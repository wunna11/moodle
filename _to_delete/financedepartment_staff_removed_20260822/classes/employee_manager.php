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
 * Business logic for finance staff (financedep_employee) management.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class employee_manager
 *
 * Deliberately minimal - unlike local_hrdepartment\staff_manager, a
 * financedep_employee has no department, no employee type, and no
 * course-assignment concept. It exists purely to answer one question:
 * does this Moodle user count as Finance staff, per
 * classes/access_manager.php's can_access_finance_department() rule?
 * This is a prerequisite page, not one of the doc's numbered Task 7
 * steps - see [[financedepartment-schema]] project memory.
 */
class employee_manager {

    /** @var int default number of finance staff rows per page. */
    const PAGE_SIZE = 20;

    /**
     * Builds the shared WHERE clause + params used by count() and
     * get_list(): every financedep_employee row, optionally narrowed by
     * status and a name/email/employee-code search.
     *
     * @param string $search name/email/employee code search, '' = any
     * @param string $status '' = any, otherwise one of constants::EMPLOYEE_STATUS_*
     * @return array [string $where, array $params]
     */
    protected static function build_where(string $search, string $status): array {
        global $DB;

        $where = '1 = 1';
        $params = [];

        if ($status !== '') {
            $where .= ' AND e.status = :status';
            $params['status'] = $status;
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
     * Counts finance staff matching the given filters, for pagination.
     *
     * @param string $search
     * @param string $status
     * @return int
     */
    public static function count(string $search, string $status): int {
        global $DB;

        [$where, $params] = self::build_where($search, $status);

        $sql = "SELECT COUNT(e.id)
                  FROM {financedep_employee} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE $where";

        return (int) $DB->count_records_sql($sql, $params);
    }

    /**
     * Returns one page of finance staff matching the given filters.
     *
     * @param string $search
     * @param string $status
     * @param int $page zero-based page number
     * @param int $perpage
     * @return \stdClass[] id, employeecode, designation, status, userid, plus fullname/email
     */
    public static function get_list(string $search, string $status, int $page = 0, int $perpage = self::PAGE_SIZE): array {
        global $DB;

        [$where, $params] = self::build_where($search, $status);

        $sql = "SELECT e.id, e.userid, e.employeecode, e.designation, e.status,
                       u.firstname, u.lastname, u.email
                  FROM {financedep_employee} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE $where
              ORDER BY u.lastname ASC, u.firstname ASC";

        $records = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        $list = [];
        foreach ($records as $record) {
            $record->fullname = fullname($record);
            $list[] = $record;
        }

        return $list;
    }

    /**
     * Returns one finance staff record with its linked user's name/email
     * joined in, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT e.*, u.firstname, u.lastname, u.email
                  FROM {financedep_employee} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE e.id = :id";
        $record = $DB->get_record_sql($sql, ['id' => $id]);

        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);

        return $record;
    }

    /**
     * Creates a new finance staff record.
     *
     * @param \stdClass $data form data: userid, employeecode, designation
     * @param int $usermodified
     * @return int the new financedep_employee id
     */
    public static function create(\stdClass $data, int $usermodified): int {
        global $DB;

        $now = time();

        $employee = new \stdClass();
        $employee->userid = (int) $data->userid;
        $employee->employeecode = trim($data->employeecode);
        $employee->designation = $data->designation ?? '';
        $employee->status = constants::EMPLOYEE_STATUS_ACTIVE;
        $employee->timecreated = $now;
        $employee->timemodified = $now;
        $employee->usermodified = $usermodified;

        return $DB->insert_record('financedep_employee', $employee);
    }

    /**
     * Updates an existing finance staff record. The linked Moodle userid
     * is intentionally never changed here.
     *
     * @param int $id
     * @param \stdClass $data form data: employeecode, designation, status
     * @param int $usermodified
     * @return void
     */
    public static function update(int $id, \stdClass $data, int $usermodified): void {
        global $DB;

        $employee = new \stdClass();
        $employee->id = $id;
        $employee->employeecode = trim($data->employeecode);
        $employee->designation = $data->designation ?? '';
        $employee->status = $data->status ?? constants::EMPLOYEE_STATUS_ACTIVE;
        $employee->timemodified = time();
        $employee->usermodified = $usermodified;

        $DB->update_record('financedep_employee', $employee);
    }

    /**
     * Sets a finance staff record's status (deactivate/reactivate).
     *
     * @param int $id
     * @param string $status one of constants::EMPLOYEE_STATUS_*
     * @param int $usermodified
     * @return void
     */
    public static function set_status(int $id, string $status, int $usermodified): void {
        global $DB;

        $DB->update_record('financedep_employee', (object) [
            'id' => $id,
            'status' => $status,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);
    }

    /**
     * Returns Moodle users eligible to be linked as a new finance staff
     * member: confirmed, not deleted, not suspended, and not already
     * linked to a financedep_employee record (unless it's the current
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
                   AND (NOT EXISTS (SELECT 1 FROM {financedep_employee} e WHERE e.userid = u.id) $keepsql)
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
