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
 * Access control for the HR Department feature as a whole - the org-wide
 * Dashboard, and every management-side section (Lecturers, Staff,
 * Students, Attendance management, Leave management, Payroll).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class access_manager
 *
 * Implements the "who is HR" rule (added 2026-08-17, widened same day to
 * cover every management-side section, not just the Dashboard):
 *
 *   (hrdep_employee Staff record whose department is "HR")
 *   OR  (Moodle site administrator)
 *
 * =>  full access to every HR Department management feature: the
 *     org-wide Dashboard, Lecturers, Staff, Students, Attendance
 *     management, Leave management, and Payroll (once built).
 *
 * "Role is staff" is decided by this plugin's own hrdep_employee.type
 * field (constants::EMPLOYEE_TYPE_STAFF), NOT a separate Moodle role
 * assignment. A first version (2026-08-17, same day) checked a Moodle
 * role with shortname "staff" instead - confirmed via a live diagnostic
 * (local/hrdepartment/debug_access.php) that no such role existed on
 * the site at all (zero role assignments for the test account), while
 * the hrdep_employee record was already correct (type=staff,
 * department=HR). Switched to reading the employee record directly:
 * works with zero extra Moodle admin setup, and matches what "Staff"
 * already means everywhere else in this plugin. If a real Moodle role
 * requirement is wanted later, it needs to be created and assigned in
 * Site administration > Users > Permissions first - this class won't
 * silently start requiring one again.
 *
 * This can't be expressed as a plain Moodle capability, because a
 * capability has no way to condition on a custom field's value (the
 * employee's department) - so it lives here as a runtime check instead.
 * Two entry points:
 *
 * - can_access_hr_department(): the rule on its own. Used as a drop-in
 *   replacement for every former has_capability('local/hrdepartment:
 *   managedashboard', ...) call site (index.php's dashboard-vs-self-
 *   service branch, lib.php's navigation visibility,
 *   student_leave_manager::is_leave_attendance_only_role()'s "is this a
 *   manager" check).
 * - can_manage($capability): the rule OR'd with the plugin's normal
 *   per-section capability (managelecturers/managestaff/managestudents/
 *   manageattendance/managepayroll - see db/access.php), so a role that
 *   already grants one of those capabilities keeps working exactly as
 *   before, and the new Staff+HR rule (or a site admin) additionally
 *   gets in everywhere too. Used as a drop-in replacement for every
 *   has_capability()/require_capability() call site gating those five
 *   capabilities across lecturer/*.php, staff/*.php, students/*.php,
 *   attendance/*.php, lib.php's tab visibility, and
 *   student_leave_manager::can_manage()'s global (studentid = 0) branch
 *   for Leave.
 *
 * Every one of the plugin's own manage* capability definitions is left
 * in place in db/access.php purely for its display name/description in
 * the Define roles UI and for backwards compatibility with any role
 * that already grants it - has_capability() on those capabilities is no
 * longer called directly from any page; go through can_manage() instead.
 */
class access_manager {

    /** @var string hrdep_department.name value that grants access to a Staff-type employee. */
    const HR_DEPARTMENT_NAME = 'HR';

    /**
     * Whether $userid may access the HR Department feature's management
     * side (the org-wide Dashboard, and everywhere else that used to
     * gate on local/hrdepartment:managedashboard).
     *
     * True for a Moodle site administrator, or for a user who has an
     * hrdep_employee Staff record whose department is named "HR".
     *
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_access_hr_department(int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        return self::is_staff_in_hr_department($userid);
    }

    /**
     * Drop-in replacement for
     * has_capability($capability, context_system::instance(), $userid)
     * for any of this plugin's manage* capabilities
     * (managelecturers/managestaff/managestudents/manageattendance/
     * managepayroll/managestudentleave). Grants access if the user holds
     * $capability the normal Moodle way (so existing role setups keep
     * working unchanged), OR satisfies can_access_hr_department() (the
     * Staff+HR rule, or a site admin) even without that capability
     * assigned via any role.
     *
     * @param string $capability e.g. 'local/hrdepartment:managestaff'
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_manage(string $capability, int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (self::can_access_hr_department($userid)) {
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
     * @param string $capability e.g. 'local/hrdepartment:managestaff'
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
     * Whether $userid has an hrdep_employee record of type "staff" (see
     * constants::EMPLOYEE_TYPE_STAFF) whose department is named "HR"
     * (case-insensitive).
     *
     * @param int $userid
     * @return bool
     */
    protected static function is_staff_in_hr_department(int $userid): bool {
        global $DB;

        $sql = "SELECT 1
                  FROM {hrdep_employee} e
                  JOIN {hrdep_department} d ON d.id = e.departmentid
                 WHERE e.userid = :userid
                   AND e.type = :type
                   AND " . $DB->sql_equal('d.name', ':deptname', false);

        return $DB->record_exists_sql($sql, [
            'userid' => $userid,
            'type' => constants::EMPLOYEE_TYPE_STAFF,
            'deptname' => self::HR_DEPARTMENT_NAME,
        ]);
    }
}
