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
 * Business logic for organisational department management
 * (departments/*.php) - create/rename/delete hrdep_department rows.
 *
 * Added 2026-09-06 alongside a new local/hrdepartment:managedepartments
 * capability - see access_manager::can_manage_departments()'s docblock
 * for why this is deliberately gated differently (more strictly) than
 * every other manage* capability in this plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class department_manager
 */
class department_manager {

    /**
     * @var string[] department names (matched case-insensitively) that
     * an access-control rule somewhere keys off of by exact name match:
     * this plugin's own access_manager::HR_DEPARTMENT_NAME ("HR"), and
     * local_financedepartment\access_manager::FINANCE_DEPARTMENT_NAME
     * ("Finance"). Renaming or deleting one of these rows would silently
     * strip access from every staff member of that department, with no
     * error shown anywhere - see is_protected(), delete()'s guard, and
     * department_form's read-only name field for a protected row.
     *
     * This list is a manually maintained cross-plugin reference, NOT
     * auto-discovered - if another plugin ever adds its own
     * department-name-keyed access rule, that name must be added here
     * too, or this guard silently stops covering it.
     */
    const PROTECTED_NAMES = ['HR', 'Finance'];

    /**
     * Returns every department, ordered by name, each with an
     * `employeecount` (staff + lecturers currently assigned to it).
     *
     * @return \stdClass[]
     */
    public static function get_all(): array {
        global $DB;

        $sql = "SELECT d.id, d.name, d.code, d.parentid, d.timecreated, d.timemodified,
                       COUNT(e.id) AS employeecount
                  FROM {hrdep_department} d
             LEFT JOIN {hrdep_employee} e ON e.departmentid = d.id
              GROUP BY d.id, d.name, d.code, d.parentid, d.timecreated, d.timemodified
              ORDER BY d.name ASC";

        return array_values($DB->get_records_sql($sql));
    }

    /**
     * Returns one department, or false if it doesn't exist.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        return $DB->get_record('hrdep_department', ['id' => $id]);
    }

    /**
     * Whether $name (case-insensitive, trimmed) is one of the protected
     * department names an access rule keys off of - see PROTECTED_NAMES.
     *
     * @param string $name
     * @return bool
     */
    public static function is_protected(string $name): bool {
        foreach (self::PROTECTED_NAMES as $protected) {
            if (strcasecmp(trim($name), $protected) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a department with this name (case-insensitive) already
     * exists.
     *
     * @param string $name
     * @param int $excludeid department id to ignore (when editing)
     * @return bool
     */
    public static function name_in_use(string $name, int $excludeid = 0): bool {
        global $DB;

        $select = 'LOWER(name) = LOWER(:name)';
        $params = ['name' => $name];
        if ($excludeid) {
            $select .= ' AND id <> :excludeid';
            $params['excludeid'] = $excludeid;
        }

        return $DB->record_exists_select('hrdep_department', $select, $params);
    }

    /**
     * Whether a department with this code (case-insensitive) already
     * exists. A null/empty code never counts as "in use" - many
     * departments can have no code.
     *
     * @param string $code
     * @param int $excludeid department id to ignore (when editing)
     * @return bool
     */
    public static function code_in_use(string $code, int $excludeid = 0): bool {
        global $DB;

        if ($code === '') {
            return false;
        }

        $select = 'code IS NOT NULL AND LOWER(code) = LOWER(:code)';
        $params = ['code' => $code];
        if ($excludeid) {
            $select .= ' AND id <> :excludeid';
            $params['excludeid'] = $excludeid;
        }

        return $DB->record_exists_select('hrdep_department', $select, $params);
    }

    /**
     * Whether any hrdep_employee (staff or lecturer) is currently
     * assigned to this department.
     *
     * @param int $id
     * @return bool
     */
    public static function has_employees(int $id): bool {
        global $DB;

        return $DB->record_exists('hrdep_employee', ['departmentid' => $id]);
    }

    /**
     * Whether any other department has this one set as its parent.
     * parentid exists in the schema but no UI sets it away from 0 yet -
     * this guard is defensive, in case it's ever populated another way.
     *
     * @param int $id
     * @return bool
     */
    public static function has_children(int $id): bool {
        global $DB;

        return $DB->record_exists('hrdep_department', ['parentid' => $id]);
    }

    /**
     * Whether this department can't be deleted because something still
     * references it (employees or, defensively, sub-departments).
     *
     * @param int $id
     * @return bool
     */
    public static function is_in_use(int $id): bool {
        return self::has_employees($id) || self::has_children($id);
    }

    /**
     * Creates a new department.
     *
     * @param \stdClass $data form data (name, code)
     * @return int the new department id
     */
    public static function create(\stdClass $data): int {
        global $DB;

        $now = time();

        $record = new \stdClass();
        $record->name = trim($data->name);
        $record->code = !empty($data->code) ? trim($data->code) : null;
        $record->parentid = 0;
        $record->timecreated = $now;
        $record->timemodified = $now;

        return (int) $DB->insert_record('hrdep_department', $record);
    }

    /**
     * Updates an existing department's name/code.
     *
     * A protected department's name (see PROTECTED_NAMES) is never
     * changed here even if the submitted data disagrees -
     * department_form renders it read-only for a protected row, but
     * this is defence-in-depth against a tampered request bypassing
     * that.
     *
     * @param int $id
     * @param \stdClass $data form data (name, code)
     * @return void
     */
    public static function update(int $id, \stdClass $data): void {
        global $DB;

        $existing = $DB->get_record('hrdep_department', ['id' => $id], '*', MUST_EXIST);

        $record = new \stdClass();
        $record->id = $id;
        $record->name = self::is_protected($existing->name) ? $existing->name : trim($data->name);
        $record->code = !empty($data->code) ? trim($data->code) : null;
        $record->timemodified = time();

        $DB->update_record('hrdep_department', $record);
    }

    /**
     * Deletes a department. Refuses (returns false, deletes nothing) if
     * the department is protected (see PROTECTED_NAMES) or still in use
     * (see is_in_use()) - departments are never force-deleted out from
     * under an access rule or an employee record.
     *
     * @param int $id
     * @return bool true if deleted, false if refused
     */
    public static function delete(int $id): bool {
        global $DB;

        $department = $DB->get_record('hrdep_department', ['id' => $id]);
        if (!$department) {
            return false;
        }

        if (self::is_protected($department->name) || self::is_in_use($id)) {
            return false;
        }

        $DB->delete_records('hrdep_department', ['id' => $id]);

        return true;
    }
}
