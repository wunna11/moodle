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
 * Business logic for lecturer profile management.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class lecturer_manager
 *
 * Encapsulates create/update/deactivate logic for hrdep_employee records
 * of type "lecturer" plus their hrdep_lecturerdetails extension row.
 * Staff management (task 4) reuses employee_manager-style logic but
 * without the academic fields; this class stays lecturer-specific.
 */
class lecturer_manager {

    /** @var int default number of lecturer cards per page. */
    const PAGE_SIZE = 12;

    /**
     * Builds the shared WHERE clause + params used by both
     * count_lecturers() and get_lecturers(): every lecturer-type
     * hrdep_employee row, optionally narrowed by department, live account
     * status, and a name/email/employee-code search. Must be used
     * together with a FROM that joins {hrdep_employee} e to {user} u.
     *
     * Status is filtered on the live Moodle account (u.suspended) rather
     * than the stored employmentstatus field, matching the Active/
     * Suspended label shown on the list and profile pages - see
     * lecturer_profile::export_for_template()'s comment on why the stored
     * status can drift from reality.
     *
     * @param string $search name/email/employee code search, '' = any
     * @param int $departmentid 0 = any department
     * @param string $status '' = any, 'active' = not suspended, 'suspended' = suspended
     * @return array [string $where, array $params]
     */
    protected static function build_where(string $search, int $departmentid, string $status): array {
        global $DB;

        $params = ['type' => constants::EMPLOYEE_TYPE_LECTURER];
        $where = 'e.type = :type';

        if ($departmentid) {
            $where .= ' AND e.departmentid = :departmentid';
            $params['departmentid'] = $departmentid;
        }

        if ($status === 'active') {
            $where .= ' AND u.suspended = 0';
        } else if ($status === 'suspended') {
            $where .= ' AND u.suspended = 1';
        }

        if ($search !== '') {
            $like = '%' . $DB->sql_like_escape($search) . '%';
            $where .= ' AND (' . $DB->sql_like('u.firstname', ':search1', false) . '
                          OR ' . $DB->sql_like('u.lastname', ':search2', false) . '
                          OR ' . $DB->sql_like('u.email', ':search3', false) . '
                          OR ' . $DB->sql_like('e.employeecode', ':search4', false) . ')';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
            $params['search4'] = $like;
        }

        return [$where, $params];
    }

    /**
     * Counts lecturers matching the given filters, for pagination.
     *
     * @param string $search
     * @param int $departmentid
     * @param string $status
     * @return int
     */
    public static function count_lecturers(string $search, int $departmentid, string $status): int {
        global $DB;

        [$where, $params] = self::build_where($search, $departmentid, $status);

        $sql = "SELECT COUNT(e.id)
                  FROM {hrdep_employee} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE $where";

        return (int) $DB->count_records_sql($sql, $params);
    }

    /**
     * Returns one page of lecturers matching the given filters, for the
     * Lecturers directory card grid.
     *
     * @param string $search
     * @param int $departmentid
     * @param string $status
     * @param int $page zero-based page number
     * @param int $perpage
     * @return \stdClass[] employeeid, employeecode, designation, employmentstatus, departmentid,
     *                     departmentname, qualification, specialization, plus
     *                     user_picture::fields() columns (userid as "id") and fullname
     */
    public static function get_lecturers(string $search, int $departmentid, string $status, int $page, int $perpage): array {
        global $DB;

        [$where, $params] = self::build_where($search, $departmentid, $status);

        $fields = \user_picture::fields('u', ['suspended']);

        $sql = "SELECT e.id AS employeeid, e.employeecode, e.designation, e.employmentstatus, e.departmentid,
                       d.name AS departmentname, ld.qualification, ld.specialization,
                       $fields
                  FROM {hrdep_employee} e
                  JOIN {user} u ON u.id = e.userid
             LEFT JOIN {hrdep_department} d ON d.id = e.departmentid
             LEFT JOIN {hrdep_lecturerdetails} ld ON ld.employeeid = e.id
                 WHERE $where
              ORDER BY u.lastname ASC, u.firstname ASC";

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        $lecturers = [];
        foreach ($records as $record) {
            $record->fullname = fullname($record);
            $lecturers[] = $record;
        }

        return $lecturers;
    }

    /**
     * Returns organisation-wide lecturer stats for the directory's stat
     * strip: total lecturers, the active/suspended account split (live
     * Moodle state, see build_where()), and how many distinct departments
     * have at least one lecturer.
     *
     * @return \stdClass
     */
    public static function get_summary_stats(): \stdClass {
        global $DB;

        $type = constants::EMPLOYEE_TYPE_LECTURER;
        $stats = new \stdClass();

        $stats->totallecturers = (int) $DB->count_records('hrdep_employee', ['type' => $type]);

        $stats->suspendedlecturers = (int) $DB->count_records_sql(
            "SELECT COUNT(e.id)
               FROM {hrdep_employee} e
               JOIN {user} u ON u.id = e.userid
              WHERE e.type = :type AND u.suspended = 1",
            ['type' => $type]
        );

        $stats->activelecturers = $stats->totallecturers - $stats->suspendedlecturers;

        $stats->departmentcount = (int) $DB->count_records_sql(
            "SELECT COUNT(DISTINCT e.departmentid)
               FROM {hrdep_employee} e
              WHERE e.type = :type AND e.departmentid IS NOT NULL",
            ['type' => $type]
        );

        return $stats;
    }

    /**
     * Returns a lecturer's combined employee + lecturer-details + user
     * record, or false if not found or not a lecturer.
     *
     * @param int $employeeid
     * @return \stdClass|false
     */
    public static function get_lecturer(int $employeeid) {
        global $DB;

        $sql = "SELECT e.*, ld.qualification, ld.specialization,
                       u.firstname, u.lastname, u.email,
                       d.name AS departmentname
                  FROM {hrdep_employee} e
                  JOIN {user} u ON u.id = e.userid
             LEFT JOIN {hrdep_lecturerdetails} ld ON ld.employeeid = e.id
             LEFT JOIN {hrdep_department} d ON d.id = e.departmentid
                 WHERE e.id = :id AND e.type = :type";
        $record = $DB->get_record_sql($sql, ['id' => $employeeid, 'type' => constants::EMPLOYEE_TYPE_LECTURER]);

        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);

        return $record;
    }

    /**
     * Creates a new lecturer (employee + lecturer-details rows).
     *
     * @param \stdClass $data form data
     * @param int $usermodified
     * @return int the new employee id
     */
    public static function create(\stdClass $data, int $usermodified): int {
        global $DB;

        $now = time();

        $employee = new \stdClass();
        $employee->userid = (int) $data->userid;
        $employee->employeecode = trim($data->employeecode);
        $employee->type = constants::EMPLOYEE_TYPE_LECTURER;
        $employee->departmentid = self::resolve_department($data->departmentid ?? null);
        $employee->designation = $data->designation ?? '';
        $employee->employmentstatus = $data->employmentstatus ?? constants::EMPLOYMENT_STATUS_ACTIVE;
        $employee->phone = $data->phone ?? '';
        $employee->address = $data->address ?? '';
        $employee->emergencycontact = $data->emergencycontact ?? '';
        $employee->joindate = $data->joindate ?? null;
        $employee->timecreated = $now;
        $employee->timemodified = $now;
        $employee->usermodified = $usermodified;

        $employeeid = $DB->insert_record('hrdep_employee', $employee);

        $details = new \stdClass();
        $details->employeeid = $employeeid;
        $details->qualification = $data->qualification ?? '';
        $details->specialization = $data->specialization ?? '';
        $details->timecreated = $now;
        $details->timemodified = $now;
        $DB->insert_record('hrdep_lecturerdetails', $details);

        return $employeeid;
    }

    /**
     * Updates an existing lecturer's employee + lecturer-details rows.
     * The linked Moodle userid is intentionally never changed here.
     *
     * @param int $employeeid
     * @param \stdClass $data form data
     * @param int $usermodified
     * @return void
     */
    public static function update(int $employeeid, \stdClass $data, int $usermodified): void {
        global $DB;

        $now = time();

        $employee = new \stdClass();
        $employee->id = $employeeid;
        $employee->employeecode = trim($data->employeecode);
        $employee->departmentid = self::resolve_department($data->departmentid ?? null);
        $employee->designation = $data->designation ?? '';
        $employee->employmentstatus = $data->employmentstatus ?? constants::EMPLOYMENT_STATUS_ACTIVE;
        $employee->phone = $data->phone ?? '';
        $employee->address = $data->address ?? '';
        $employee->emergencycontact = $data->emergencycontact ?? '';
        $employee->joindate = $data->joindate ?? null;
        $employee->timemodified = $now;
        $employee->usermodified = $usermodified;
        $DB->update_record('hrdep_employee', $employee);

        $details = $DB->get_record('hrdep_lecturerdetails', ['employeeid' => $employeeid]);
        if (!$details) {
            $details = new \stdClass();
            $details->employeeid = $employeeid;
            $details->timecreated = $now;
        }
        $details->qualification = $data->qualification ?? '';
        $details->specialization = $data->specialization ?? '';
        $details->timemodified = $now;

        if (!empty($details->id)) {
            $DB->update_record('hrdep_lecturerdetails', $details);
        } else {
            $DB->insert_record('hrdep_lecturerdetails', $details);
        }
    }

    /**
     * Sets an employee's employment status (used for deactivate/reactivate)
     * and syncs their course assignments to match: deactivating suspends
     * Moodle enrolment on active assignments (role kept, for a clean
     * restore); reactivating validates and restores them. Also suspends
     * or unsuspends the linked Moodle account itself, so a deactivated
     * lecturer can no longer log in at all.
     *
     * @param int $employeeid
     * @param string $status one of constants::EMPLOYMENT_STATUS_*
     * @param int $usermodified
     * @return array course_assignment_manager::sync_assignments_for_employee_status() results
     */
    public static function set_employment_status(int $employeeid, string $status, int $usermodified): array {
        global $DB;

        $employee = $DB->get_record('hrdep_employee', ['id' => $employeeid], '*', MUST_EXIST);

        $DB->update_record('hrdep_employee', (object) [
            'id' => $employeeid,
            'employmentstatus' => $status,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        user_account_sync::sync_suspension($employee->userid, $status);

        return course_assignment_manager::sync_assignments_for_employee_status($employeeid, $status, $usermodified);
    }

    /**
     * Resolves a department form value to a departmentid, creating a new
     * hrdep_department row on the fly if the value is a free-typed name
     * rather than an existing id (supports the tag-style autocomplete).
     *
     * @param mixed $value
     * @return int|null
     */
    public static function resolve_department($value): ?int {
        global $DB;

        if ($value === null || $value === '' || $value === '0') {
            return null;
        }

        if (is_numeric($value) && $DB->record_exists('hrdep_department', ['id' => (int) $value])) {
            return (int) $value;
        }

        $name = trim((string) $value);
        if ($name === '') {
            return null;
        }

        $existing = $DB->get_record('hrdep_department', ['name' => $name]);
        if ($existing) {
            return (int) $existing->id;
        }

        $now = time();
        return (int) $DB->insert_record('hrdep_department', (object) [
            'name' => $name,
            'code' => null,
            'parentid' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
        ]);
    }

    /**
     * Returns Moodle users eligible to be linked as a new lecturer:
     * confirmed, not deleted, not suspended, and not already linked to
     * an hrdep_employee record (unless it's the current employee being
     * edited, in which case they stay in the list).
     *
     * @param int|null $keepuserid a userid to always include (the current linked user when editing)
     * @return array userid => "Fullname (email)"
     */
    public static function get_eligible_users(?int $keepuserid = null): array {
        global $DB, $CFG;

        $params = [];
        $keepsql = '';
        if ($keepuserid) {
            $keepsql = 'OR u.id = :keepuserid';
            $params['keepuserid'] = $keepuserid;
        }

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email
                  FROM {user} u
                 WHERE u.deleted = 0 AND u.suspended = 0 AND u.confirmed = 1 AND u.id <> :guestid
                   AND (NOT EXISTS (SELECT 1 FROM {hrdep_employee} e WHERE e.userid = u.id) $keepsql)
              ORDER BY u.lastname ASC, u.firstname ASC";
        $params['guestid'] = $CFG->siteguest ?? 1;

        $users = $DB->get_records_sql($sql, $params);

        $options = [];
        foreach ($users as $user) {
            $options[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }

        return $options;
    }
}
