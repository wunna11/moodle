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
 * Access rules for the Student 360 Profile local plugin.
 *
 * view-any (search for and open any student's aggregated profile) is
 * granted to:
 *  - a site administrator,
 *  - anyone local_hrdepartment already recognises as HR-department
 *    management staff (can_access_hr_department(), role/capability
 *    driven since that plugin's own access migration),
 *  - anyone local_financedepartment already recognises as Finance-
 *    department management staff (can_access_finance_department(),
 *    same migration), or
 *  - anyone directly holding local/student360:view (an independent way
 *    in, for a role that isn't HR/Finance staff but should still see
 *    this aggregated view).
 *
 * view-own (see only the logged-in user's own profile) is the plain
 * local/student360:viewown self-service capability, archetype user,
 * mirroring viewownfeerecord/viewownattendance in the other two
 * plugins.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_student360;

defined('MOODLE_INTERNAL') || die();

class access_manager {

    /**
     * Whether the given user (default: $USER) can search for and view
     * ANY student's aggregated 360 profile.
     *
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_view_any(int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (is_siteadmin($userid)) {
            return true;
        }

        if (\local_hrdepartment\access_manager::can_access_hr_department($userid)) {
            return true;
        }

        if (\local_financedepartment\access_manager::can_access_finance_department($userid)) {
            return true;
        }

        return has_capability('local/student360:view', \context_system::instance(), $userid);
    }

    /**
     * Drop-in require_capability()-style companion to can_view_any().
     *
     * @param int $userid defaults to $USER.
     * @return void
     * @throws \required_capability_exception
     */
    public static function require_view_any(int $userid = 0): void {
        if (!self::can_view_any($userid)) {
            throw new \required_capability_exception(\context_system::instance(), 'local/student360:view', 'nopermissions', '');
        }
    }

    /**
     * Whether the given user (default: $USER) holds the plain
     * self-service capability to view their OWN profile.
     *
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function can_view_own(int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        return has_capability('local/student360:viewown', \context_system::instance(), $userid);
    }

    /**
     * Whether $viewerid (default: $USER) may open $studentid's
     * aggregated profile - either because they can view any student's
     * profile, or because it is their own and they hold the self-service
     * capability.
     *
     * @param int $studentid the profile being opened
     * @param int $viewerid defaults to $USER.
     * @return bool
     */
    public static function can_view_profile(int $studentid, int $viewerid = 0): bool {
        global $USER;
        $viewerid = $viewerid ?: (int) $USER->id;

        if (self::can_view_any($viewerid)) {
            return true;
        }

        return $studentid === $viewerid && self::can_view_own($viewerid);
    }

    /**
     * Drop-in require_capability()-style companion to can_view_profile().
     *
     * @param int $studentid
     * @param int $viewerid defaults to $USER.
     * @return void
     * @throws \required_capability_exception
     */
    public static function require_view_profile(int $studentid, int $viewerid = 0): void {
        if (!self::can_view_profile($studentid, $viewerid)) {
            throw new \required_capability_exception(\context_system::instance(), 'local/student360:view', 'nopermissions', '');
        }
    }

    /**
     * Whether the plugin's nav entry point(s) - lib.php's
     * extend_navigation() and classes/hooks/navigation/primary_extend.php -
     * should show a node to the current user. Pulled into one shared
     * method so the two entry points can never drift apart on which
     * capabilities they check - the exact bug class
     * local_financedepartment/local_hrdepartment both hit and fixed the
     * same way (see [[hrdepartment-access-migration-plan]] project
     * memory).
     *
     * @return bool
     */
    public static function can_view_navigation_entry(): bool {
        return self::can_view_any() || self::can_view_own();
    }
}
