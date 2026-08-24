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
 * Access control for the Finance Department feature as a whole - fee
 * structures, fee records, scholarships, discounts, installments,
 * payments, and finance reports.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class access_manager
 *
 * Implements the "who is Finance staff" rule for this plugin:
 *
 *   (hrdep_employee Staff record whose department is "Finance")
 *   OR  (Moodle site administrator)
 *
 * => full access to every Finance Department management feature (fee
 *    structures, fee record assignment, scholarship/discount approval,
 *    installment plans, payment recording, finance dashboard/reports).
 *
 * CHANGED 2026-08-22: this plugin no longer has its own staff table.
 * It originally shipped with a plugin-local `financedep_employee` table
 * (see [[financedepartment-schema]] project memory for the full history
 * of that table and the short-lived pages/staff/* UI built on top of
 * it) - deliberately NOT reusing local_hrdepartment's hrdep_employee at
 * the time, so this plugin had no dependency on local_hrdepartment
 * being installed. The user explicitly asked to reverse that decision
 * and connect the two plugins instead, once it became clear staff
 * creation was being duplicated across both plugins for no real reason.
 * This class now mirrors local_hrdepartment\access_manager's own rule
 * exactly (same query shape, same "type=staff + department name" check)
 * with `FINANCE_DEPARTMENT_NAME` in place of HR's `HR_DEPARTMENT_NAME` -
 * so local/financedepartment:* now REQUIRES local_hrdepartment to be
 * installed (see version.php's $plugin->dependencies) and finance staff
 * are created exclusively through local_hrdepartment's own
 * staff/edit.php, picking "Finance" as their department.
 *
 * "Is finance staff" is decided by an hrdep_employee row (type =
 * \local_hrdepartment\constants::EMPLOYEE_TYPE_STAFF) whose department
 * is named "Finance" (case-insensitive), not a Moodle role assignment -
 * this mirrors the pattern local_hrdepartment\access_manager uses for
 * its own "who counts as HR" rule.
 *
 * This can't be expressed as a plain Moodle capability, because a
 * capability has no way to condition on a custom field's value (the
 * employee's department) - so it lives here as a runtime check instead,
 * exactly like local_hrdepartment's version. Two entry points:
 *
 * - can_access_finance_department(): the rule on its own. Use this for
 *   simple "is this user Finance staff at all" checks (e.g. dashboard
 *   visibility, navigation).
 * - can_manage($capability): the rule OR'd with the plugin's normal
 *   per-action capability (see db/access.php), so a role that already
 *   grants one of those capabilities keeps working exactly as before,
 *   and a Finance-department hrdep_employee (or a site admin)
 *   additionally gets in without needing any role assigned at all.
 *
 * Every one of this plugin's capability definitions in db/access.php is
 * still fully defined, for its display name/description in the Define
 * roles UI and for any role that already grants it - has_capability()
 * should never be called directly from a page for a
 * local/financedepartment:* capability; go through can_manage() /
 * require_manage() instead. A direct has_capability()/require_capability()
 * call on one of these capabilities anywhere outside this class is a bug.
 */
class access_manager {

    /** @var string hrdep_department.name value that grants access to a Staff-type employee. */
    const FINANCE_DEPARTMENT_NAME = 'Finance';

    /**
     * Whether $userid may access the Finance Department feature's
     * management side (fee structures, fee records, scholarships,
     * discounts, installments, payments, dashboard/reports).
     *
     * True for a Moodle site administrator, or for a user who has a
     * local_hrdepartment Staff record whose department is named
     * "Finance".
     *
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_access_finance_department(int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        return self::is_staff_in_finance_department($userid);
    }

    /**
     * Drop-in replacement for
     * has_capability($capability, context_system::instance(), $userid)
     * for any of this plugin's local/financedepartment:* capabilities.
     * Grants access if the user holds $capability the normal Moodle way
     * (so a role-based setup keeps working unchanged), OR satisfies
     * can_access_finance_department() (a Finance-department hrdep_employee,
     * or a site admin) even without that capability assigned via any role.
     *
     * @param string $capability e.g. 'local/financedepartment:managefeestructures'
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_manage(string $capability, int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (self::can_access_finance_department($userid)) {
            return true;
        }

        return has_capability($capability, \context_system::instance(), $userid);
    }

    /**
     * Drop-in replacement for require_capability($capability,
     * context_system::instance()) using the can_manage() rule above -
     * throws the same standard "you don't have permission" exception
     * Moodle's own require_capability() would, so the error page a
     * denied user sees is unchanged.
     *
     * @param string $capability e.g. 'local/financedepartment:managefeestructures'
     * @param int $userid defaults to $USER.
     * @return void
     * @throws \required_capability_exception
     */
    public static function require_manage(string $capability, int $userid = 0): void {
        if (!self::can_manage($capability, $userid)) {
            throw new \required_capability_exception(\context_system::instance(), $capability, 'nopermissions', '');
        }
    }

    /**
     * Whether $userid has an hrdep_employee record of type "staff" whose
     * department is named "Finance" (case-insensitive). Identical query
     * shape to local_hrdepartment\access_manager::is_staff_in_hr_department(),
     * just checking FINANCE_DEPARTMENT_NAME instead of HR_DEPARTMENT_NAME.
     *
     * @param int $userid
     * @return bool
     */
    protected static function is_staff_in_finance_department(int $userid): bool {
        global $DB;

        $sql = "SELECT 1
                  FROM {hrdep_employee} e
                  JOIN {hrdep_department} d ON d.id = e.departmentid
                 WHERE e.userid = :userid
                   AND e.type = :type
                   AND " . $DB->sql_equal('d.name', ':deptname', false);

        return $DB->record_exists_sql($sql, [
            'userid' => $userid,
            'type' => \local_hrdepartment\constants::EMPLOYEE_TYPE_STAFF,
            'deptname' => self::FINANCE_DEPARTMENT_NAME,
        ]);
    }
}
