<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Read-only integration with the site's mod_attendance activity.
 *
 * This plugin does not take or store attendance itself - staff already
 * mark it the normal way, in the Attendance activity inside each course
 * (mod_attendance's own attendance/attendance_sessions/attendance_log/
 * attendance_statuses tables). This class only *reads* that data and
 * reshapes it for the HR Department views: Course -> Day/session ->
 * record list. Nothing here ever writes to a mod_attendance table.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class student_attendance_manager
 */
class student_attendance_manager {

    /**
     * Returns the courses a user may view student attendance reports
     * for: every course (for an HR admin holding manageattendance), or
     * just the courses they're actively assigned to teach per
     * hrdep_courseassign (for a lecturer who doesn't hold that
     * system-wide capability). This mirrors the plugin's existing
     * "who teaches what" source of truth rather than checking Moodle
     * course-level roles directly.
     *
     * @param int $userid
     * @param bool $isadmin true if the user holds local/hrdepartment:manageattendance
     * @return array courseid => "Shortname: Fullname"
     */
    public static function get_manageable_courses(int $userid, bool $isadmin): array {
        if ($isadmin) {
            return course_assignment_manager::get_course_options();
        }

        $employee = dashboard_helper::get_employee_for_user($userid);
        if (!$employee || $employee->type !== constants::EMPLOYEE_TYPE_LECTURER) {
            return [];
        }

        $options = [];
        foreach (course_assignment_manager::get_assignments_for_employee($employee->id) as $assignment) {
            if ($assignment->status === constants::COURSEASSIGN_STATUS_ACTIVE) {
                $options[$assignment->courseid] = $assignment->shortname . ': ' . format_string($assignment->fullname);
            }
        }

        return $options;
    }

    /**
     * Whether the current user can view attendance reports for a
     * specific course: either they hold local/hrdepartment:manageattendance
     * (HR/admin, any course), or hrdep_courseassign says they're the
     * course's active lecturer.
     *
     * @param int $courseid
     * @return bool
     */
    public static function can_view_course_attendance(int $courseid): bool {
        global $USER;

        if (has_capability('local/hrdepartment:manageattendance', \context_system::instance())) {
            return true;
        }

        $manageable = self::get_manageable_courses((int) $USER->id, false);

        return array_key_exists($courseid, $manageable);
    }

    /**
     * Returns every course (optionally restricted to a set of course
     * ids) that has at least one mod_attendance session recorded, with a
     * session count and the most recent session date - the "Course"
     * level of the Course -> Day -> records view.
     *
     * @param int[]|null $courseids restrict to these course ids, or null for no restriction
     * @return \stdClass[] id, shortname, fullname, sessioncount, lastsessiondate
     */
    public static function get_courses_with_attendance(?array $courseids): array {
        global $DB;

        $where = '1 = 1';
        $params = [];

        if ($courseids !== null) {
            if (empty($courseids)) {
                return [];
            }
            [$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $where = "a.course $insql";
            $params = $inparams;
        }

        $sql = "SELECT c.id, c.shortname, c.fullname,
                       COUNT(DISTINCT s.id) AS sessioncount,
                       MAX(s.sessdate) AS lastsessiondate
                  FROM {attendance} a
                  JOIN {course} c ON c.id = a.course
                  JOIN {attendance_sessions} s ON s.attendanceid = a.id
                 WHERE $where
              GROUP BY c.id, c.shortname, c.fullname
              ORDER BY c.fullname ASC";

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Returns the day/session listing for one course - the "Day 1, Day
     * 2, ..." level - across every Attendance activity instance in that
     * course (a course can have more than one), most recent first.
     *
     * @param int $courseid
     * @return \stdClass[] sessionid, sessdate, description, attendanceid, attendancename, cmid, totalmarked
     */
    public static function get_sessions_for_course(int $courseid): array {
        global $DB;

        $sql = "SELECT s.id AS sessionid, s.sessdate, s.description, s.descriptionformat, s.duration,
                       a.id AS attendanceid, a.name AS attendancename,
                       cm.id AS cmid,
                       COUNT(l.id) AS totalmarked
                  FROM {attendance_sessions} s
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {modules} m ON m.name = 'attendance'
                  JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id
             LEFT JOIN {attendance_log} l ON l.sessionid = s.id
                 WHERE a.course = :courseid
              GROUP BY s.id, s.sessdate, s.description, s.descriptionformat, s.duration,
                       a.id, a.name, cm.id
              ORDER BY s.sessdate DESC";

        return array_values($DB->get_records_sql($sql, ['courseid' => $courseid]));
    }

    /**
     * Returns one session's header details (date, course, activity name)
     * needed to render the records page and check access, or false if
     * the session doesn't exist.
     *
     * @param int $sessionid
     * @return \stdClass|false sessionid, sessdate, description, courseid, shortname, fullname, attendancename, cmid
     */
    public static function get_session(int $sessionid) {
        global $DB;

        $sql = "SELECT s.id AS sessionid, s.sessdate, s.description, s.descriptionformat,
                       a.course AS courseid, a.name AS attendancename,
                       c.shortname, c.fullname,
                       cm.id AS cmid
                  FROM {attendance_sessions} s
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {course} c ON c.id = a.course
                  JOIN {modules} m ON m.name = 'attendance'
                  JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id
                 WHERE s.id = :sessionid";

        return $DB->get_record_sql($sql, ['sessionid' => $sessionid]);
    }

    /**
     * Returns the recorded attendance for one session - the actual
     * record list for that "Day". Only students who were actually
     * marked appear (mod_attendance only writes an attendance_log row
     * once someone is marked), which is correct for a read-only report:
     * this mirrors exactly what was recorded, nothing more.
     *
     * @param int $sessionid
     * @return \stdClass[] logid, studentid, firstname, lastname, email, acronym, statusdescription, statusgrade, remarks, timetaken, takenbyfirstname, takenbylastname
     */
    public static function get_session_records(int $sessionid): array {
        global $DB;

        $sql = "SELECT l.id AS logid, l.studentid, u.firstname, u.lastname, u.email,
                       st.acronym, st.description AS statusdescription, st.grade AS statusgrade,
                       l.remarks, l.timetaken,
                       tu.firstname AS takenbyfirstname, tu.lastname AS takenbylastname
                  FROM {attendance_log} l
                  JOIN {user} u ON u.id = l.studentid
                  JOIN {attendance_statuses} st ON st.id = l.statusid
             LEFT JOIN {user} tu ON tu.id = l.takenby
                 WHERE l.sessionid = :sessionid
              ORDER BY u.lastname ASC, u.firstname ASC";

        return array_values($DB->get_records_sql($sql, ['sessionid' => $sessionid]));
    }

    /**
     * Returns a status-count summary for a student, optionally scoped to
     * one course, used for both the HR/lecturer "view student" page and
     * the student's own self-service summary. Grouped by the site's own
     * configured status acronym/description rather than a fixed set,
     * since mod_attendance lets each Attendance activity define its own
     * statuses.
     *
     * @param int $studentid
     * @param int|null $courseid
     * @return \stdClass[] acronym, description, total - ordered by acronym
     */
    public static function get_student_status_summary(int $studentid, ?int $courseid = null): array {
        global $DB;

        $where = 'l.studentid = :studentid';
        $params = ['studentid' => $studentid];
        if ($courseid) {
            $where .= ' AND a.course = :courseid';
            $params['courseid'] = $courseid;
        }

        $sql = "SELECT st.acronym, st.description, COUNT(l.id) AS total
                  FROM {attendance_log} l
                  JOIN {attendance_sessions} s ON s.id = l.sessionid
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {attendance_statuses} st ON st.id = l.statusid
                 WHERE $where
              GROUP BY st.acronym, st.description
              ORDER BY st.acronym ASC";

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Returns a student's individual attendance records (their own rows
     * from attendance_log across every session), optionally scoped to
     * one course - used for the per-student history page.
     *
     * @param int $studentid
     * @param int|null $courseid
     * @return \stdClass[] logid, sessdate, description, courseid, shortname, fullname, acronym, statusdescription, remarks
     */
    public static function get_student_records(int $studentid, ?int $courseid = null): array {
        global $DB;

        $where = 'l.studentid = :studentid';
        $params = ['studentid' => $studentid];
        if ($courseid) {
            $where .= ' AND a.course = :courseid';
            $params['courseid'] = $courseid;
        }

        $sql = "SELECT l.id AS logid, s.id AS sessionid, s.sessdate, s.description,
                       a.course AS courseid, c.shortname, c.fullname,
                       st.acronym, st.description AS statusdescription,
                       l.remarks
                  FROM {attendance_log} l
                  JOIN {attendance_sessions} s ON s.id = l.sessionid
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {course} c ON c.id = a.course
                  JOIN {attendance_statuses} st ON st.id = l.statusid
                 WHERE $where
              ORDER BY s.sessdate DESC";

        return array_values($DB->get_records_sql($sql, $params));
    }
}
