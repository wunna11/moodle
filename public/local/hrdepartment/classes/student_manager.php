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
 * Read-only students directory: every Moodle user holding the "student"
 * role in at least one course, with their enrolled courses.
 *
 * This plugin does not store its own list of students or enrolments - it
 * reads straight from core Moodle data (mdl_user, mdl_role_assignments,
 * mdl_context, mdl_course), the same "there's already a real system for
 * this" pattern used by Attendance and Leave (see the
 * hrdepartment-entity-scope note): a student's course list here is
 * whatever the site's own enrolment/role assignments say it is, nothing
 * plugin-owned.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class student_manager
 */
class student_manager {

    /** @var int default number of student cards per page. */
    const PAGE_SIZE = 12;

    /**
     * Builds the shared WHERE clause + params used by both
     * count_students() and get_students(): every non-deleted, non-guest
     * user holding the "student" role in at least one real course,
     * optionally narrowed by course, account status, and a name/email
     * search.
     *
     * @param string $search name/email search, '' = any
     * @param int $courseid 0 = any course, otherwise must hold the student role in this course
     * @param string $status '' = any, 'active' = not suspended, 'suspended' = suspended
     * @return array [string $where, array $params]
     */
    protected static function build_where(string $search, int $courseid, string $status): array {
        global $DB, $CFG;

        $params = [
            'rolestudent' => 'student',
            'coursecontextlevel' => CONTEXT_COURSE,
            'siteid' => SITEID,
            'guestid' => $CFG->siteguest ?? 1,
        ];

        $existssql = "EXISTS (
                        SELECT 1
                          FROM {role_assignments} ra
                          JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
                          JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
                         WHERE ra.userid = u.id AND ctx.instanceid <> :siteid";
        if ($courseid) {
            $existssql .= ' AND ctx.instanceid = :courseid';
            $params['courseid'] = $courseid;
        }
        $existssql .= ')';

        $where = "u.deleted = 0 AND u.id <> :guestid AND $existssql";

        if ($status === 'active') {
            $where .= ' AND u.suspended = 0';
        } else if ($status === 'suspended') {
            $where .= ' AND u.suspended = 1';
        }

        if ($search !== '') {
            $like = '%' . $DB->sql_like_escape($search) . '%';
            $where .= ' AND (' . $DB->sql_like('u.firstname', ':search1', false) . '
                          OR ' . $DB->sql_like('u.lastname', ':search2', false) . '
                          OR ' . $DB->sql_like('u.email', ':search3', false) . ')';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
        }

        return [$where, $params];
    }

    /**
     * Counts students matching the given filters, for pagination.
     *
     * @param string $search
     * @param int $courseid
     * @param string $status
     * @return int
     */
    public static function count_students(string $search, int $courseid, string $status): int {
        global $DB;

        [$where, $params] = self::build_where($search, $courseid, $status);

        return (int) $DB->count_records_sql("SELECT COUNT(u.id) FROM {user} u WHERE $where", $params);
    }

    /**
     * Returns one page of students matching the given filters, each with
     * an enrolled-course *count* attached (see get_course_counts_for_users()).
     * Used by the directory list/grid, which links out to view_student()'s
     * page for the full course list rather than showing it inline - see
     * that method's docblock for why.
     *
     * @param string $search
     * @param int $courseid
     * @param string $status
     * @param int $page zero-based page number
     * @param int $perpage
     * @return \stdClass[] user_picture::fields() columns, plus fullname and coursecount
     */
    public static function get_students(string $search, int $courseid, string $status, int $page, int $perpage): array {
        global $DB;

        [$where, $params] = self::build_where($search, $courseid, $status);

        $fields = \user_picture::fields('u', ['city', 'country', 'suspended']);

        $sql = "SELECT $fields
                  FROM {user} u
                 WHERE $where
              ORDER BY u.lastname ASC, u.firstname ASC";

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        if (empty($records)) {
            return [];
        }

        $counts = self::get_course_counts_for_users(array_keys($records));

        $students = [];
        foreach ($records as $record) {
            $record->fullname = fullname($record);
            $record->coursecount = $counts[$record->id] ?? 0;
            $students[] = $record;
        }

        return $students;
    }

    /**
     * Returns a single student (must hold the "student" role in at least
     * one course, same scope as the directory listing - otherwise this
     * returns null rather than exposing an arbitrary user's profile
     * fields to whoever holds managestudents) with their full enrolled
     * course list attached, for the Student profile page.
     *
     * @param int $userid
     * @return \stdClass|null user_picture::fields() columns, plus fullname and courses, or null if not found/not a student
     */
    public static function get_student(int $userid): ?\stdClass {
        global $DB;

        $fields = \user_picture::fields('u', ['city', 'country', 'suspended', 'lastaccess']);

        $record = $DB->get_record_sql(
            "SELECT $fields FROM {user} u WHERE u.id = :id AND u.deleted = 0",
            ['id' => $userid]
        );

        if (!$record) {
            return null;
        }

        $courses = self::get_courses_for_users([$userid])[$userid] ?? [];
        if (empty($courses)) {
            // Not enrolled as a student anywhere - out of this
            // directory's scope, same as build_where()'s EXISTS check.
            return null;
        }

        $record->fullname = fullname($record);
        $record->courses = $courses;

        return $record;
    }

    /**
     * Returns how many courses each of the given users holds the
     * "student" role in, grouped by userid - a single batched COUNT
     * query for the directory list, which only needs the number (see
     * get_students()); get_courses_for_users() is for when the full
     * course list is actually needed (the Student profile page).
     *
     * @param int[] $userids
     * @return array userid => int course count
     */
    public static function get_course_counts_for_users(array $userids): array {
        global $DB;

        if (empty($userids)) {
            return [];
        }

        [$insql, $inparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uid');
        $params = array_merge($inparams, [
            'rolestudent' => 'student',
            'coursecontextlevel' => CONTEXT_COURSE,
            'siteid' => SITEID,
        ]);

        $sql = "SELECT ra.userid, COUNT(DISTINCT c.id) AS coursecount
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
                  JOIN {course} c ON c.id = ctx.instanceid AND c.id <> :siteid
                  JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
                 WHERE ra.userid $insql
              GROUP BY ra.userid";

        $records = $DB->get_records_sql($sql, $params);

        $counts = [];
        foreach ($records as $record) {
            $counts[$record->userid] = (int) $record->coursecount;
        }

        return $counts;
    }

    /**
     * Returns every course each of the given users holds the "student"
     * role in, grouped by userid - a single batched query rather than
     * one per student card. Used by get_student() for the profile page's
     * full course list.
     *
     * @param int[] $userids
     * @return array userid => \stdClass[] (id, shortname, fullname), ordered by course fullname
     */
    public static function get_courses_for_users(array $userids): array {
        global $DB;

        if (empty($userids)) {
            return [];
        }

        [$insql, $inparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uid');
        $params = array_merge($inparams, [
            'rolestudent' => 'student',
            'coursecontextlevel' => CONTEXT_COURSE,
            'siteid' => SITEID,
        ]);

        $sql = "SELECT ra.id AS raid, ra.userid, c.id AS courseid, c.shortname, c.fullname
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
                  JOIN {course} c ON c.id = ctx.instanceid AND c.id <> :siteid
                  JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
                 WHERE ra.userid $insql
              ORDER BY c.fullname ASC";

        $records = $DB->get_records_sql($sql, $params);

        $bystudent = [];
        foreach ($records as $record) {
            $bystudent[$record->userid][] = (object) [
                'id' => $record->courseid,
                'shortname' => $record->shortname,
                'fullname' => $record->fullname,
            ];
        }

        return $bystudent;
    }

    /**
     * Returns organisation-wide student stats for the directory's stat
     * strip and the Dashboard preview card: total distinct students,
     * total course enrolments (as student), courses with at least one
     * student, and the active/suspended account split.
     *
     * @return \stdClass
     */
    public static function get_summary_stats(): \stdClass {
        global $DB;

        $params = [
            'rolestudent' => 'student',
            'coursecontextlevel' => CONTEXT_COURSE,
            'siteid' => SITEID,
        ];

        $stats = new \stdClass();

        $stats->totalstudents = (int) $DB->count_records_sql(
            "SELECT COUNT(DISTINCT u.id)
               FROM {user} u
               JOIN {role_assignments} ra ON ra.userid = u.id
               JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
               JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
               JOIN {course} c ON c.id = ctx.instanceid AND c.id <> :siteid
              WHERE u.deleted = 0",
            $params
        );

        $stats->totalenrolments = (int) $DB->count_records_sql(
            "SELECT COUNT(ra.id)
               FROM {role_assignments} ra
               JOIN {user} u ON u.id = ra.userid
               JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
               JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
               JOIN {course} c ON c.id = ctx.instanceid AND c.id <> :siteid
              WHERE u.deleted = 0",
            $params
        );

        $stats->courseswithstudents = (int) $DB->count_records_sql(
            "SELECT COUNT(DISTINCT c.id)
               FROM {course} c
               JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :coursecontextlevel
               JOIN {role_assignments} ra ON ra.contextid = ctx.id
               JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
               JOIN {user} u ON u.id = ra.userid AND u.deleted = 0
              WHERE c.id <> :siteid",
            $params
        );

        $stats->suspendedstudents = (int) $DB->count_records_sql(
            "SELECT COUNT(DISTINCT u.id)
               FROM {user} u
               JOIN {role_assignments} ra ON ra.userid = u.id
               JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
               JOIN {role} r ON r.id = ra.roleid AND r.shortname = :rolestudent
               JOIN {course} c ON c.id = ctx.instanceid AND c.id <> :siteid
              WHERE u.deleted = 0 AND u.suspended = 1",
            $params
        );

        $stats->activestudents = $stats->totalstudents - $stats->suspendedstudents;

        return $stats;
    }
}
