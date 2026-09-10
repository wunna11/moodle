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
     * Every local/financedepartment:* capability that marks someone as
     * finance STAFF (management/approval/reporting), as opposed to the
     * three plain per-user self-service capabilities (viewownfeerecord,
     * submitscholarshiprequest, submitdiscountrequest) every logged-in
     * user gets by default. Used by is_finance_staff_viewer() below -
     * keep this in sync with db/access.php whenever a new manage/approve/
     * report-style capability is added.
     *
     * @var string[]
     */
    const MANAGEMENT_CAPABILITIES = [
        'viewfinancereports',
        'viewallrecords',
        'managefeestructures',
        'managefeerecords',
        'managescholarships',
        'approvescholarships',
        'managediscounts',
        'approvediscounts',
        'manageinstallments',
        'recordpayments',
        'managerefunds',
    ];

    /**
     * Whether the current user should see this plugin's finance-staff
     * branding ("Finance Department") as opposed to the narrower
     * self-service branding ("Scholarship") - see
     * local_financedepartment_get_display_name() in lib.php, which this
     * feeds.
     *
     * Added 2026-09-10: the user asked for the nav label and page
     * heading to say "Scholarship" for a plain student (who can only
     * ever reach the self-service pages: the landing page, the
     * scholarship catalog, and their own scholarship/discount requests)
     * while staying "Finance Department" for finance staff/admins, who
     * see the full multi-section plugin (fee structures, fee records,
     * installments, payments, ...). True for a Finance-department
     * hrdep_employee/site admin (can_access_finance_department()), OR
     * anyone holding at least one of MANAGEMENT_CAPABILITIES above -
     * the capability check covers a role granted e.g. managescholarships
     * directly without an hrdep_employee record, which
     * can_access_finance_department() alone would miss.
     *
     * @return bool
     */
    public static function is_finance_staff_viewer(): bool {
        global $USER;

        if (self::can_access_finance_department((int) $USER->id)) {
            return true;
        }

        $context = \context_system::instance();
        foreach (self::MANAGEMENT_CAPABILITIES as $capability) {
            if (has_capability('local/financedepartment:' . $capability, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The plugin's display name for the CURRENT VIEWER - "Finance
     * Department" for finance staff/admins (is_finance_staff_viewer()
     * above), "Scholarship" for a plain student who can only ever reach
     * the self-service pages (landing page, scholarship catalog, their
     * own scholarship/discount requests).
     *
     * Added 2026-09-10 per an explicit user request: a student logging
     * in only ever sees the self-service slice of this plugin, so
     * "Finance Department" (which implies fee structures/records/
     * installments/payments they can't access) reads as confusing
     * branding to them - "Scholarship" is what they actually came here
     * to do. Finance staff/admins are UNCHANGED - they still see
     * "Finance Department" everywhere, since they DO manage the full
     * multi-section plugin.
     *
     * Deliberately lives HERE, not as a lib.php helper, even though
     * every call site (the two nav entry points below, and every page's
     * $PAGE->set_title()/set_heading()/hero title) could reach a lib.php
     * function just as easily: classes/hooks/navigation/primary_extend.php
     * calls this from a hook callback dispatched during navigation setup,
     * and this file's own CRITICAL RULE (see the frontmatter) already
     * warns that lib.php's inclusion timing is not guaranteed outside of
     * page-body code running after $OUTPUT->header() - a hook callback is
     * exactly the kind of early, autoloaded call site that rule exists
     * for. access_manager.php is autoloaded like any other class with no
     * such timing risk, so this method (and is_finance_staff_viewer()
     * above) live here instead.
     *
     * Deliberately NOT used for the plain get_string('pluginname', ...)
     * calls that identify the plugin in a 403/moodle_exception message -
     * those stay the literal plugin name regardless of viewer, since
     * that's an error-page context naming the component, not user-facing
     * branding.
     *
     * @return string
     */
    public static function get_display_name(): string {
        if (self::is_finance_staff_viewer()) {
            return get_string('pluginname', 'local_financedepartment');
        }

        return get_string('pluginnamestudent', 'local_financedepartment');
    }

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
     * Whether the current user should see the Finance Department entry in
     * navigation at all - true for finance staff/admins, or for a student
     * who holds any of the self-service capabilities (view own fee
     * record, submit a scholarship/discount request).
     *
     * Added 2026-09-10 to fix a SECOND navigation-visibility bug in the
     * same week: this plugin has TWO separate navigation entry points on
     * this Moodle 5.2 site -
     *   (1) local_financedepartment_extend_navigation() (lib.php), the
     *       classic callback that populates $PAGE->navigation (the site
     *       navigation tree/drawer) - this plugin's ORIGINAL and only nav
     *       hook, in place since Step 7.2.
     *   (2) \local_financedepartment\hooks\navigation\primary_extend
     *       (new this fix), registered via db/hooks.php for Moodle's
     *       core\hook\navigation\primary_extend hook - this is what
     *       actually populates the TOP primary nav bar (Home/Dashboard/My
     *       courses/...) on this Moodle version. Investigated after the
     *       user reported the plugin still wasn't visible in that top bar
     *       even after the 2026-09-10 fix that added the two missing
     *       submit capabilities to entry point (1)'s check: traced
     *       core\navigation\output\primary::get_primary_nav() and found
     *       it reads $this->page->primarynav, NOT $PAGE->navigation -  a
     *       completely separate tree that extend_navigation() never
     *       touches. core\navigation\views\primary::initialise() (Moodle
     *       core) builds that tree itself (Home/Dashboard/My courses/Site
     *       admin) and then dispatches core\hook\navigation\primary_extend
     *       so plugins/themes can add their own nodes to it - lib/
     *       navigationlib.php also confirms the OLD global_navigation-based
     *       flat_navigation/showinflatnavigation mechanism this plugin's
     *       extend_navigation() callback relied on for "flat" visibility
     *       is deprecated as of this Moodle version, so that flag no
     *       longer does anything useful either.
     *
     * Both entry points must show the SAME node for the SAME users, so
     * the capability check is centralised here rather than duplicated -
     * duplicating it a second time is exactly how the first
     * navigation-visibility bug (fixed earlier the same day) happened.
     *
     * @return bool
     */
    public static function can_view_navigation_entry(): bool {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $context = \context_system::instance();

        return self::can_access_finance_department((int) $USER->id)
            || has_capability('local/financedepartment:viewfinancereports', $context)
            || has_capability('local/financedepartment:viewownfeerecord', $context)
            || has_capability('local/financedepartment:submitscholarshiprequest', $context)
            || has_capability('local/financedepartment:submitdiscountrequest', $context);
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
