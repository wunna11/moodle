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
 * Implements the "who is HR" rule:
 *
 *   (a Moodle role holding at least one local/hrdepartment:* management
 *    capability - see MANAGEMENT_CAPABILITIES below)
 *   OR  (Moodle site administrator)
 *
 * =>  full access to every HR Department management feature: the
 *     org-wide Dashboard, Lecturers, Staff, Students, Attendance
 *     management, Leave management, and Payroll (once built).
 *
 * HISTORY: from 2026-08-17 to 2026-09-14 (v2026091301/0.8.1 and
 * earlier) this rule was instead decided by reading an hrdep_employee
 * row directly (type=staff, department="HR") rather than a Moodle role
 * assignment - see is_staff_in_hr_department() below, kept for
 * reference/rollback but no longer called. That approach was itself a
 * fallback from an even earlier attempt (same day) to check a Moodle
 * role with shortname "staff", abandoned when a live diagnostic found
 * no such role existed on the site at all.
 *
 * 2026-09-14, v2026091400/0.8.2 (Phase 1) added a real Moodle role
 * (hrdepartmentstaff, system context) kept in sync with every
 * hrdep_employee Staff record by classes/role_sync_manager.php, as
 * dual-write "shadow" data alongside the still-authoritative
 * employee-record check.
 *
 * 2026-09-14, v2026091401/0.8.3 (Phase 2, THIS cutover) flips this
 * class over to reading that role's capabilities via has_capability()
 * instead of hrdep_employee directly - see project memory
 * hrdepartment-access-migration-plan.md for the full multi-phase plan.
 * Every public method's signature is UNCHANGED, so no caller anywhere
 * in the codebase needed to change. Rollback: revert can_access_hr_
 * department()'s and can_manage()'s bodies only (git revert this
 * commit) - is_staff_in_hr_department() was deliberately left in place
 * to make that a clean one-file revert; role_sync_manager's shadow data
 * is harmless to leave in place either way.
 *
 * This still can't be expressed as a single plain Moodle capability
 * (no one capability means "is HR"), so it lives here as a runtime
 * check over several. Three entry points:
 *
 * - can_access_hr_department(): the rule on its own. Used as a drop-in
 *   replacement for every former has_capability('local/hrdepartment:
 *   managedashboard', ...) call site (index.php's dashboard-vs-self-
 *   service branch, student_leave_manager::is_leave_attendance_only_role()
 *   's "is this a manager" check).
 * - can_manage($capability): now a plain has_capability() call (site
 *   admins are already covered by Moodle's own has_capability()
 *   shortcut for them) - kept as a named wrapper rather than inlined at
 *   every call site so a future policy change again has one place to
 *   change, and so lecturer/*.php, staff/*.php, students/*.php,
 *   attendance/*.php, lib.php's tab visibility, and
 *   student_leave_manager::can_manage()'s global (studentid = 0) branch
 *   for Leave never call has_capability() directly.
 * - can_view_navigation_entry(): added 2026-09-14 (Phase 4) - whether
 *   the current user should see ANY HR Department nav entry (management
 *   or self-service), shared by both of this plugin's nav entry points
 *   (lib.php's extend_navigation() and classes/hooks/navigation/
 *   primary_extend.php) so they can't drift apart again. See that
 *   method's own docblock.
 *
 * Every one of the plugin's own manage* capability definitions is left
 * in place in db/access.php purely for its display name/description in
 * the Define roles UI and for backwards compatibility with any role
 * that already grants it - has_capability() on those capabilities is no
 * longer called directly from any page; go through can_manage() instead.
 *
 * ONE EXCEPTION, added 2026-09-06: local/hrdepartment:managedepartments
 * is gated by can_manage_departments()/require_manage_departments()
 * below, NOT can_manage()/require_manage() - see that method's own
 * docblock for why.
 */
class access_manager {

    /** @var string hrdep_department.name value historically used by is_staff_in_hr_department() (no longer called - see this class's docblock). */
    const HR_DEPARTMENT_NAME = 'HR';

    /**
     * Every local/hrdepartment:* capability that marks someone as HR
     * management staff, i.e. the exact 9 capabilities Allow-checked on
     * the hrdepartmentstaff custom role as Phase 0 of the migration
     * plan (managedepartments deliberately excluded - stays
     * Manager-role-only, see can_manage_departments(); the viewown*
     * self-service capabilities also excluded, they default to
     * everyone via the 'user' archetype). Keep in sync with db/access.php
     * and with Phase 0's role setup whenever a new manage- or view-style
     * capability is added - mirrors local_financedepartment\access_manager
     * ::MANAGEMENT_CAPABILITIES exactly, same caveat.
     *
     * @var string[]
     */
    const MANAGEMENT_CAPABILITIES = [
        'managedashboard',
        'managelecturers',
        'managestaff',
        'managestudents',
        'manageattendance',
        'managestudentleave',
        'viewstudentleave',
        'managepayroll',
        'viewallrecords',
    ];

    /**
     * Whether $userid may access the HR Department feature's management
     * side (the org-wide Dashboard, and everywhere else that used to
     * gate on local/hrdepartment:managedashboard).
     *
     * True for a Moodle site administrator, or for a user whose roles
     * grant at least one of MANAGEMENT_CAPABILITIES above - in
     * practice, on this site, that means the hrdepartmentstaff role
     * (see role_sync_manager.php), assigned automatically to every
     * active Staff-type hrdep_employee in the "HR" department, but any
     * other role granting one of those capabilities also qualifies, the
     * same as it always could have.
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

        $context = \context_system::instance();
        foreach (self::MANAGEMENT_CAPABILITIES as $capability) {
            if (has_capability('local/hrdepartment:' . $capability, $context, $userid)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Drop-in replacement for
     * has_capability($capability, context_system::instance(), $userid)
     * for any of this plugin's manage* capabilities. Since Phase 2 of
     * the migration this is a plain has_capability() call - a site
     * administrator is already covered by Moodle's own has_capability()
     * shortcut for them, so no separate is_siteadmin() check is needed
     * here either.
     *
     * Do NOT use this for local/hrdepartment:managedepartments - see
     * can_manage_departments() below, which has always been a separate,
     * plain has_capability() check.
     *
     * @param string $capability e.g. 'local/hrdepartment:managestaff'
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_manage(string $capability, int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

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
     * Whether $userid may manage departments (create/rename/delete
     * hrdep_department rows via departments/*.php).
     *
     * Deliberately NOT routed through can_manage()/can_access_hr_department():
     * every hrdep_employee Staff record in the HR department currently
     * gets a blanket grant to every OTHER manage* capability in this
     * plugin via that rule, but department rows are exactly what that
     * rule (and local_financedepartment's equivalent Finance rule) key
     * off of by exact name match - handing every HR staff member the
     * ability to rename or delete the "HR" or "Finance" row would let
     * any one of them silently lock everyone in that department out,
     * with no error shown anywhere (see department_manager::
     * PROTECTED_NAMES, which is the second, row-level line of defence).
     *
     * True only for a Moodle site administrator, or a user holding
     * local/hrdepartment:managedepartments the normal Moodle way (its
     * only default archetype grant is 'manager' - a distinct Moodle
     * role from being HR staff via hrdep_employee, so being HR staff
     * alone is not enough here, unlike every other section).
     *
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_manage_departments(int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        return has_capability('local/hrdepartment:managedepartments', \context_system::instance(), $userid);
    }

    /**
     * require_capability() equivalent for can_manage_departments() -
     * see that method's docblock for why this is separate from
     * require_manage().
     *
     * @param int $userid defaults to $USER.
     * @return void
     * @throws \required_capability_exception
     */
    public static function require_manage_departments(int $userid = 0): void {
        if (!self::can_manage_departments($userid)) {
            throw new \required_capability_exception(
                \context_system::instance(),
                'local/hrdepartment:managedepartments',
                'nopermissions',
                ''
            );
        }
    }

    /**
     * Whether the CURRENT user should see the HR Department entry in
     * navigation at all - true for HR management staff/admin
     * (can_access_hr_department()), or for a self-service-only viewer
     * (a plain student/teacher holding any of the self-service
     * attendance/leave/payroll capabilities, or a delegated leave
     * approver).
     *
     * Added 2026-09-14 (Phase 4 of the access-model migration, project
     * memory hrdepartment-access-migration-plan.md) to close the exact
     * "two nav entry points computing this same condition independently"
     * gap that caused the v2026091301/0.8.1 bug: classes/hooks/
     * navigation/primary_extend.php originally had its OWN, narrower
     * copy of this check (HR-staff/admin only) and simply never got
     * updated when self-service capabilities were added to lib.php's
     * local_hrdepartment_extend_navigation(), so a plain student had no
     * path to their own leave/attendance pages in the top nav bar at
     * all. Both of those call sites now call this method instead of
     * maintaining their own copy of the OR chain, so they can never
     * drift apart again - mirrors
     * local_financedepartment\access_manager::can_view_navigation_entry()'s
     * own reason for existing (added 2026-09-10 for the exact same
     * reason, on that plugin's own two nav entry points).
     *
     * Deliberately does NOT decide which LABEL to show ("HR Department"
     * vs "My HR") - that half of the decision still varies by call site
     * (lib.php's side-drawer entry always uses the plain "HR Department"
     * label regardless of viewer; primary_extend.php's top-bar entry
     * picks between the two based on can_access_hr_department()) and was
     * deliberately left as-is by this refactor rather than unified,
     * since doing so would be a user-facing behaviour change, not a
     * pure consolidation - flagged in project memory as a follow-up
     * worth asking the user about, not decided unilaterally here.
     *
     * @return bool
     */
    public static function can_view_navigation_entry(): bool {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $userid = (int) $USER->id;

        if (self::can_access_hr_department($userid)) {
            return true;
        }

        $context = \context_system::instance();

        return has_capability('local/hrdepartment:viewownattendance', $context, $userid)
            || student_leave_manager::can_view(0, $userid)
            || has_capability('local/hrdepartment:applyownleave', $context, $userid)
            || student_leave_manager::is_approver($userid)
            || has_capability('local/hrdepartment:viewownpayroll', $context, $userid);
    }

    /**
     * Whether $userid has an hrdep_employee record of type "staff" (see
     * constants::EMPLOYEE_TYPE_STAFF) whose department is named "HR"
     * (case-insensitive).
     *
     * NO LONGER CALLED as of Phase 2 of the access-model migration
     * (2026-09-14, v2026091401/0.8.3) - can_access_hr_department() now
     * checks MANAGEMENT_CAPABILITIES via has_capability() instead. Left
     * in place, unchanged, purely so a rollback of that method's body
     * is a clean one-file git revert; safe to delete once Phase 2 has
     * been running in production for a while with no issues.
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
