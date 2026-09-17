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
 * Phase 1 of the access-model migration - see project memory
 * hrdepartment-access-migration-plan.md for the full plan.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class role_sync_manager
 *
 * Dual-writes a real Moodle system-context role assignment whenever a
 * Staff-type hrdep_employee record's department or employment status
 * changes, in ADDITION to the existing access_manager::can_manage()
 * employee-record check - this class does NOT change what can_manage()
 * grants (that is Phase 2 of the migration). Its only job right now is
 * to keep real role_assignments rows in step with hrdep_employee, so
 * Phase 2's cutover has correct data to read from the moment it ships.
 *
 * Only Staff-type records (constants::EMPLOYEE_TYPE_STAFF) are synced -
 * Lecturer-type records were never part of the "who is HR/Finance" rule
 * (see access_manager::is_staff_in_hr_department() and
 * local_financedepartment\access_manager's mirror of it), so they are
 * left untouched here.
 *
 * Called from staff_manager::create()/update()/set_employment_status()
 * after each write. Deliberately silent/best-effort throughout: a
 * missing role definition (Phase 0 not done yet on this site) or a
 * missing department must never break the calling staff_manager
 * operation, since Phase 2 hasn't switched anything over to depend on
 * this data existing yet.
 */
class role_sync_manager {

    /**
     * @var string[] hrdep_department.name (case-insensitive, trimmed)
     * => the Moodle role shortname a Staff-type employee in that
     * department should hold at system context.
     *
     * This mapping is intentionally hardcoded and kept separate from
     * department_manager::PROTECTED_NAMES - that list protects
     * department ROWS from rename/delete, a different concern from this
     * class's ROLE mapping. The two must be kept in step by hand, the
     * same way department_manager's own docblock already warns for
     * local_financedepartment's FINANCE_DEPARTMENT_NAME.
     *
     * The roles named here must already exist (created manually via
     * Site administration > Users > Permissions > Define roles - see
     * Phase 0 of the migration plan). This class never creates a role
     * definition itself.
     */
    const DEPARTMENT_ROLE_MAP = [
        'HR' => 'hrdepartmentstaff',
        'Finance' => 'financedepartmentstaff',
    ];

    /**
     * Re-syncs the Moodle system-context role assignment for one
     * hrdep_employee record to match its CURRENT department + status.
     * Always derives the correct end state from what's in the database
     * right now (rather than diffing against a previous state), so it's
     * safe to call unconditionally after every create()/update()/
     * set_employment_status() call on a Staff-type record - unassigns
     * any managed role (see DEPARTMENT_ROLE_MAP) this user shouldn't
     * currently hold, and assigns the one they should, if any.
     *
     * A non-active employment status (inactive/terminated) always
     * results in every managed role being unassigned, mirroring
     * access_manager::is_staff_in_hr_department() implicitly requiring
     * the record to represent a real, current staff member (that check
     * doesn't filter on status today - this is deliberately stricter,
     * since a role assignment is a standing grant that persists outside
     * this plugin's own pages, unlike the live can_manage() query).
     *
     * Does nothing for a Lecturer-type record, a record with no
     * department, a department that doesn't match DEPARTMENT_ROLE_MAP,
     * or a mapped role shortname that doesn't exist on this site yet.
     *
     * @param int $employeeid
     * @return void
     */
    public static function sync_for_employee(int $employeeid): void {
        global $DB;

        $employee = $DB->get_record('hrdep_employee', ['id' => $employeeid]);
        if (!$employee || $employee->type !== constants::EMPLOYEE_TYPE_STAFF) {
            return;
        }

        $targetname = null;
        $isactive = $employee->employmentstatus === constants::EMPLOYMENT_STATUS_ACTIVE;

        if ($isactive && $employee->departmentid) {
            $department = $DB->get_record('hrdep_department', ['id' => $employee->departmentid]);
            if ($department) {
                foreach (self::DEPARTMENT_ROLE_MAP as $name => $shortname) {
                    if (strcasecmp(trim($department->name), $name) === 0) {
                        $targetname = $name;
                        break;
                    }
                }
            }
        }

        $context = \context_system::instance();
        $targetshortname = $targetname !== null ? self::DEPARTMENT_ROLE_MAP[$targetname] : null;

        // Unassign every managed role this user shouldn't currently hold.
        foreach (self::DEPARTMENT_ROLE_MAP as $shortname) {
            if ($shortname === $targetshortname) {
                continue;
            }
            $role = $DB->get_record('role', ['shortname' => $shortname]);
            if ($role && $DB->record_exists('role_assignments', [
                'roleid' => $role->id,
                'userid' => $employee->userid,
                'contextid' => $context->id,
            ])) {
                role_unassign($role->id, $employee->userid, $context->id);
            }
        }

        // Assign the target role, if any and not already held.
        if ($targetshortname !== null) {
            $role = $DB->get_record('role', ['shortname' => $targetshortname]);
            if ($role && !$DB->record_exists('role_assignments', [
                'roleid' => $role->id,
                'userid' => $employee->userid,
                'contextid' => $context->id,
            ])) {
                role_assign($role->id, $employee->userid, $context->id);
            }
        }
    }
}
