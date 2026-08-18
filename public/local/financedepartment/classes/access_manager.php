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
 *   (an active financedep_employee record for the user)
 *   OR  (Moodle site administrator)
 *
 * => full access to every Finance Department management feature (fee
 *    structures, fee record assignment, scholarship/discount approval,
 *    installment plans, payment recording, finance dashboard/reports).
 *
 * This is intentionally a plugin-local financedep_employee table, not a
 * reuse of local_hrdepartment's hrdep_employee - local_financedepartment
 * has no dependency on local_hrdepartment being installed. If HR and
 * Finance staff records ever need to be unified, that is a deliberate
 * follow-up, not an assumption baked in here.
 *
 * "Is finance staff" is decided by the presence of an active (status =
 * constants::EMPLOYEE_STATUS_ACTIVE) financedep_employee row for the
 * user, not a Moodle role assignment - this mirrors the pattern used by
 * local_hrdepartment\access_manager (see that plugin's class docblock)
 * of resolving "who counts as staff" from this plugin's own data instead
 * of a role that may or may not exist on the site.
 *
 * This can't be expressed as a plain Moodle capability, because a
 * capability has no way to condition on "does a financedep_employee row
 * exist for this user" - so it lives here as a runtime check instead.
 * Two entry points:
 *
 * - can_access_finance_department(): the rule on its own. Use this for
 *   simple "is this user Finance staff at all" checks (e.g. dashboard
 *   visibility, navigation).
 * - can_manage($capability): the rule OR'd with the plugin's normal
 *   per-action capability (see db/access.php), so a role that already
 *   grants one of those capabilities keeps working exactly as before,
 *   and an active finance employee (or a site admin) additionally gets
 *   in without needing any role assigned at all.
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

    /**
     * Whether $userid may access the Finance Department feature's
     * management side (fee structures, fee records, scholarships,
     * discounts, installments, payments, dashboard/reports).
     *
     * True for a Moodle site administrator, or for a user who has an
     * active financedep_employee record.
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

        return self::is_active_finance_employee($userid);
    }

    /**
     * Drop-in replacement for
     * has_capability($capability, context_system::instance(), $userid)
     * for any of this plugin's local/financedepartment:* capabilities.
     * Grants access if the user holds $capability the normal Moodle way
     * (so a role-based setup keeps working unchanged), OR satisfies
     * can_access_finance_department() (an active finance employee, or a
     * site admin) even without that capability assigned via any role.
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
     * Whether $userid has an active (status = active) financedep_employee record.
     *
     * @param int $userid
     * @return bool
     */
    protected static function is_active_finance_employee(int $userid): bool {
        global $DB;

        return $DB->record_exists('financedep_employee', [
            'userid' => $userid,
            'status' => constants::EMPLOYEE_STATUS_ACTIVE,
        ]);
    }
}
