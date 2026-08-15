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
 * Read-only integration with the site's mod_attendance activity, for the
 * Leave section.
 *
 * This plugin does not run a leave request/approval workflow of its own
 * - the user already marks a student "on leave" as an attendance status
 * (e.g. "Leave" or "Excused") when taking attendance in the site's
 * mod_attendance activity, the same way student_attendance_manager
 * already reads that data for the Attendance section. A "leave record"
 * here is simply an attendance_log row whose status description matches
 * the configured leave label (local_hrdepartment/leavestatuslabel,
 * default "Leave"). Nothing here ever writes to a mod_attendance table.
 *
 * See the hrdepartment-entity-scope project memory for why this
 * replaced the original self-contained write-capable design.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class student_leave_manager
 */
class student_leave_manager {

    /**
     * Returns the configured status description that counts as "on
     * leave" (site setting local_hrdepartment/leavestatuslabel, default
     * "Leave").
     *
     * @return string
     */
    public static function get_leave_status_label(): string {
        $label = get_config('local_hrdepartment', 'leavestatuslabel');
        return $label !== false && $label !== '' ? $label : 'Leave';
    }

    /**
     * Whether the current user can view leave reports for a specific
     * course: either they hold local/hrdepartment:manageleave (HR/admin,
     * any course), or hrdep_courseassign says they're the course's
     * active lecturer. Mirrors
     * student_attendance_manager::can_view_course_attendance() exactly,
     * since "who can see what course's data" is the same question for
     * both sections.
     *
     * @param int $courseid
     * @return bool
     */
    public static function can_view_course_leave(int $courseid): bool {
        global $USER;

        if (has_capability('local/hrdepartment:manageleave', \context_system::instance())) {
            return true;
        }

        $manageable = student_attendance_manager::get_manageable_courses((int) $USER->id, false);

        return array_key_exists($courseid, $manageable);
    }

    /**
     * Returns every course (optionally restricted to a set of course
     * ids) that has at least one leave-marked attendance record, with a
     * record count and the most recent leave date.
     *
     * @param int[]|null $courseids restrict to these course ids, or null for no restriction
     * @return \stdClass[] id, shortname, fullname, leavecount, lastleavedate
     */
    public static function get_courses_with_leave(?array $courseids): array {
        global $DB;

        $where = "LOWER(st.description) = LOWER(:leavelabel)";
        $params = ['leavelabel' => self::get_leave_status_label()];

        if ($courseids !== null) {
            if (empty($courseids)) {
                return [];
            }
            [$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $where .= " AND a.course $insql";
            $params += $inparams;
        }

        $sql = "SELECT c.id, c.shortname, c.fullname,
                       COUNT(l.id) AS leavecount,
                       MAX(s.sessdate) AS lastleavedate
                  FROM {attendance_log} l
                  JOIN {attendance_sessions} s ON s.id = l.sessionid
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {attendance_statuses} st ON st.id = l.statusid
                  JOIN {course} c ON c.id = a.course
                 WHERE $where
              GROUP BY c.id, c.shortname, c.fullname
              ORDER BY c.fullname ASC";

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Returns dashboard summary stats for the Leave Overview page:
     * leave-marked records today, this month, and in total, optionally
     * restricted to a set of courses (for a lecturer who can only see
     * their own courses).
     *
     * @param int[]|null $courseids restrict to these course ids, or null for no restriction
     * @return \stdClass
     */
    public static function get_dashboard_summary(?array $courseids): \stdClass {
        global $DB;

        $summary = new \stdClass();

        if ($courseids !== null && empty($courseids)) {
            $summary->today = 0;
            $summary->thismonth = 0;
            $summary->total = 0;
            $summary->bycourse = [];
            return $summary;
        }

        $where = "LOWER(st.description) = LOWER(:leavelabel)";
        $params = ['leavelabel' => self::get_leave_status_label()];
        $courserestriction = '';

        if ($courseids !== null) {
            [$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $courserestriction = " AND a.course $insql";
            $params += $inparams;
        }

        $today = strtotime('midnight');
        $tomorrow = $today + DAYSECS;
        $monthstart = strtotime('first day of this month midnight');

        $basefrom = "{attendance_log} l
                       JOIN {attendance_sessions} s ON s.id = l.sessionid
                       JOIN {attendance} a ON a.id = s.attendanceid
                       JOIN {attendance_statuses} st ON st.id = l.statusid";

        $summary->today = (int) $DB->count_records_sql(
            "SELECT COUNT(l.id) FROM $basefrom WHERE $where$courserestriction AND s.sessdate >= :todaystart AND s.sessdate < :todayend",
            $params + ['todaystart' => $today, 'todayend' => $tomorrow]
        );

        $summary->thismonth = (int) $DB->count_records_sql(
            "SELECT COUNT(l.id) FROM $basefrom WHERE $where$courserestriction AND s.sessdate >= :monthstart",
            $params + ['monthstart' => $monthstart]
        );

        $summary->total = (int) $DB->count_records_sql(
            "SELECT COUNT(l.id) FROM $basefrom WHERE $where$courserestriction",
            $params
        );

        $sql = "SELECT c.id, c.shortname, c.fullname, COUNT(l.id) AS total
                  FROM $basefrom
                  JOIN {course} c ON c.id = a.course
                 WHERE $where$courserestriction
              GROUP BY c.id, c.shortname, c.fullname
              ORDER BY total DESC";
        $summary->bycourse = array_values($DB->get_records_sql($sql, $params));

        return $summary;
    }

    /**
     * Returns the most recent leave-marked records, for the Leave
     * Overview quick-glance list.
     *
     * @param int[]|null $courseids restrict to these course ids, or null for no restriction
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_recent_leave_records(?array $courseids, int $limit = 8): array {
        $rows = self::get_leave_rows(['courseids' => $courseids], $limit);
        return $rows;
    }

    /**
     * Runs a filtered query over leave-marked attendance_log rows,
     * shared by the Student Leave Lookup table, the Leave Overview
     * recent list, and the Reports & Export page so they all stay
     * consistent.
     *
     * @param array $filters keys: search, courseid, studentid, courseids (int[]|null restriction), datefrom, dateto (all optional)
     * @param int $limit 0 = no limit
     * @return \stdClass[] logid, studentid, firstname, lastname, email, courseid, shortname, fullname, sessdate, remarks, takenbyfirstname, takenbylastname
     */
    public static function get_leave_rows(array $filters, int $limit = 0): array {
        global $DB;

        $where = ['LOWER(st.description) = LOWER(:leavelabel)'];
        $params = ['leavelabel' => self::get_leave_status_label()];

        if (array_key_exists('courseids', $filters) && $filters['courseids'] !== null) {
            if (empty($filters['courseids'])) {
                return [];
            }
            [$insql, $inparams] = $DB->get_in_or_equal($filters['courseids'], SQL_PARAMS_NAMED, 'course');
            $where[] = "a.course $insql";
            $params += $inparams;
        }

        if (!empty($filters['courseid'])) {
            $where[] = 'a.course = :courseid';
            $params['courseid'] = (int) $filters['courseid'];
        }

        if (!empty($filters['studentid'])) {
            $where[] = 'u.id = :studentid';
            $params['studentid'] = (int) $filters['studentid'];
        }

        if (!empty($filters['search'])) {
            $like = '%' . $DB->sql_like_escape($filters['search']) . '%';
            $where[] = '(' . $DB->sql_like('u.firstname', ':search1', false) . '
                     OR ' . $DB->sql_like('u.lastname', ':search2', false) . '
                     OR ' . $DB->sql_like('u.email', ':search3', false) . ')';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
        }

        if (!empty($filters['datefrom'])) {
            $where[] = 's.sessdate >= :datefrom';
            $params['datefrom'] = (int) $filters['datefrom'];
        }

        if (!empty($filters['dateto'])) {
            $where[] = 's.sessdate <= :dateto';
            $params['dateto'] = (int) $filters['dateto'];
        }

        $wheresql = implode(' AND ', $where);

        $sql = "SELECT l.id AS logid, u.id AS studentid, u.firstname, u.lastname, u.email,
                       a.course AS courseid, c.shortname, c.fullname,
                       s.id AS sessionid, s.sessdate,
                       l.remarks, l.timetaken,
                       tu.firstname AS takenbyfirstname, tu.lastname AS takenbylastname
                  FROM {attendance_log} l
                  JOIN {attendance_sessions} s ON s.id = l.sessionid
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {course} c ON c.id = a.course
                  JOIN {attendance_statuses} st ON st.id = l.statusid
                  JOIN {user} u ON u.id = l.studentid
             LEFT JOIN {user} tu ON tu.id = l.takenby
                 WHERE $wheresql
              ORDER BY s.sessdate DESC";

        $records = $DB->get_records_sql($sql, $params, 0, $limit ?: 0);

        $rows = [];
        foreach ($records as $record) {
            $record->fullname = fullname($record);
            $rows[] = $record;
        }

        return $rows;
    }

    /**
     * Returns one leave record's detail, or false if it doesn't exist or
     * isn't actually marked with the leave status (e.g. the underlying
     * mark was changed directly in mod_attendance after the link was
     * generated).
     *
     * @param int $logid
     * @return \stdClass|false
     */
    public static function get_leave_record(int $logid) {
        global $DB;

        $sql = "SELECT l.id AS logid, u.id AS studentid, u.firstname, u.lastname, u.email,
                       a.course AS courseid, c.shortname, c.fullname,
                       s.id AS sessionid, s.sessdate, s.description AS sessiondescription,
                       st.acronym, st.description AS statusdescription,
                       l.remarks, l.timetaken,
                       tu.firstname AS takenbyfirstname, tu.lastname AS takenbylastname,
                       cm.id AS cmid
                  FROM {attendance_log} l
                  JOIN {attendance_sessions} s ON s.id = l.sessionid
                  JOIN {attendance} a ON a.id = s.attendanceid
                  JOIN {course} c ON c.id = a.course
                  JOIN {attendance_statuses} st ON st.id = l.statusid
                  JOIN {user} u ON u.id = l.studentid
                  JOIN {modules} m ON m.name = 'attendance'
                  JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id
             LEFT JOIN {user} tu ON tu.id = l.takenby
                 WHERE l.id = :logid AND LOWER(st.description) = LOWER(:leavelabel)";

        $record = $DB->get_record_sql($sql, ['logid' => $logid, 'leavelabel' => self::get_leave_status_label()]);
        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);
        $record->takenbyfullname = $record->takenbyfirstname !== null
            ? fullname((object) ['firstname' => $record->takenbyfirstname, 'lastname' => $record->takenbylastname])
            : null;

        return $record;
    }
}
