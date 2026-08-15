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
 * Business logic for general staff profile management.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class staff_manager
 *
 * Mirrors lecturer_manager, but for hrdep_employee records of type
 * "staff": the same core employment fields, with no academic fields
 * (qualification/specialization) and no course assignment capability -
 * staff aren't assigned to teach courses per the plugin's scope.
 */
class staff_manager {

    /**
     * Returns a staff member's combined employee + user record, or
     * false if not found or not of type staff.
     *
     * @param int $employeeid
     * @return \stdClass|false
     */
    public static function get_staff(int $employeeid) {
        global $DB;

        $sql = "SELECT e.*, u.firstname, u.lastname, u.email,
                       d.name AS departmentname
                  FROM {hrdep_employee} e
                  JOIN {user} u ON u.id = e.userid
             LEFT JOIN {hrdep_department} d ON d.id = e.departmentid
                 WHERE e.id = :id AND e.type = :type";
        $record = $DB->get_record_sql($sql, ['id' => $employeeid, 'type' => constants::EMPLOYEE_TYPE_STAFF]);

        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);

        return $record;
    }

    /**
     * Creates a new staff employee record.
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
        $employee->type = constants::EMPLOYEE_TYPE_STAFF;
        $employee->departmentid = self::resolve_department($data->departmentid ?? null);
        $employee->designation = $data->designation ?? '';
        $employee->reportsto = !empty($data->reportsto) ? (int) $data->reportsto : null;
        $employee->employmentstatus = $data->employmentstatus ?? constants::EMPLOYMENT_STATUS_ACTIVE;
        $employee->phone = $data->phone ?? '';
        $employee->address = $data->address ?? '';
        $employee->emergencycontact = $data->emergencycontact ?? '';
        $employee->joindate = $data->joindate ?? null;
        $employee->timecreated = $now;
        $employee->timemodified = $now;
        $employee->usermodified = $usermodified;

        return $DB->insert_record('hrdep_employee', $employee);
    }

    /**
     * Updates an existing staff member's employee record. The linked
     * Moodle userid is intentionally never changed here.
     *
     * @param int $employeeid
     * @param \stdClass $data form data
     * @param int $usermodified
     * @return void
     */
    public static function update(int $employeeid, \stdClass $data, int $usermodified): void {
        global $DB;

        $employee = new \stdClass();
        $employee->id = $employeeid;
        $employee->employeecode = trim($data->employeecode);
        $employee->departmentid = self::resolve_department($data->departmentid ?? null);
        $employee->designation = $data->designation ?? '';
        $employee->reportsto = !empty($data->reportsto) ? (int) $data->reportsto : null;
        $employee->employmentstatus = $data->employmentstatus ?? constants::EMPLOYMENT_STATUS_ACTIVE;
        $employee->phone = $data->phone ?? '';
        $employee->address = $data->address ?? '';
        $employee->emergencycontact = $data->emergencycontact ?? '';
        $employee->joindate = $data->joindate ?? null;
        $employee->timemodified = time();
        $employee->usermodified = $usermodified;

        $DB->update_record('hrdep_employee', $employee);
    }

    /**
     * Sets an employee's employment status (used for deactivate/reactivate),
     * suspends/unsuspends the linked Moodle account to match, and syncs
     * their course assignments. Staff members ordinarily have no course
     * assignments, so that part returns an empty result set for them -
     * it's still called for consistency with lecturer_manager and in
     * case a staff record ever does pick one up (e.g. a promotion to a
     * teaching role handled elsewhere).
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
     * Returns Moodle users eligible to be linked as a new staff member:
     * confirmed, not deleted, not suspended, and not already linked to
     * an hrdep_employee record of any type (unless it's the current
     * employee being edited).
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

    /**
     * Returns active employees (of any type) who could act as a manager
     * (reportsto), excluding the given employee itself.
     *
     * @param int|null $excludeemployeeid
     * @return array employeeid => fullname (employeecode)
     */
    public static function get_potential_managers(?int $excludeemployeeid = null): array {
        global $DB;

        $params = ['active' => constants::EMPLOYMENT_STATUS_ACTIVE];
        $excludesql = '';
        if ($excludeemployeeid) {
            $excludesql = 'AND e.id <> :excludeid';
            $params['excludeid'] = $excludeemployeeid;
        }

        $sql = "SELECT e.id, e.employeecode, u.firstname, u.lastname
                  FROM {hrdep_employee} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE e.employmentstatus = :active $excludesql
              ORDER BY u.lastname ASC, u.firstname ASC";
        $records = $DB->get_records_sql($sql, $params);

        $options = [];
        foreach ($records as $record) {
            $options[$record->id] = fullname($record) . ' (' . $record->employeecode . ')';
        }

        return $options;
    }
}
