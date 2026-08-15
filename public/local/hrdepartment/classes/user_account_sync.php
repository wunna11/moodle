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
 * Keeps a Moodle user account's suspended flag in sync with the linked
 * hrdep_employee record's employment status.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class user_account_sync
 *
 * Shared by lecturer_manager and staff_manager: deactivating an employee
 * (inactive/terminated) should mean they can no longer log in, and
 * reactivating should restore that ability. This mirrors exactly what
 * Moodle's own Site administration > Users > "Suspend" action does
 * (admin/user.php) - same $user->suspended flag, same session kill, same
 * update path via user_update_user() - rather than reinventing account
 * suspension.
 */
class user_account_sync {

    /**
     * Suspends or unsuspends a Moodle user account to match an
     * employment status change. Never touches a site admin account or
     * the account of the person triggering the change, so HR
     * deactivation can't lock out an administrator or the user clicking
     * the button - the same guard admin/user.php's suspend action uses.
     *
     * @param int $userid
     * @param string $employmentstatus one of constants::EMPLOYMENT_STATUS_*
     * @return void
     */
    public static function sync_suspension(int $userid, string $employmentstatus): void {
        global $DB, $USER, $CFG;

        require_once($CFG->dirroot . '/user/lib.php');

        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0]);
        if (!$user) {
            return;
        }

        if (is_siteadmin($user) || (int) $USER->id === (int) $user->id) {
            return;
        }

        $shouldbesuspended = in_array($employmentstatus, [
            constants::EMPLOYMENT_STATUS_INACTIVE,
            constants::EMPLOYMENT_STATUS_TERMINATED,
        ], true) ? 1 : 0;

        if ((int) $user->suspended === $shouldbesuspended) {
            return;
        }

        $user->suspended = $shouldbesuspended;

        if ($shouldbesuspended) {
            // Kick out any live session immediately, same as admin/user.php.
            \core\session\manager::destroy_user_sessions($user->id);
        }

        user_update_user($user, false);
    }

    /**
     * Whether a Moodle user account is *currently* suspended, read live
     * rather than trusting hrdep_employee.employmentstatus. An account
     * can be suspended (or unsuspended) directly via Site administration
     * > Users, bypassing this plugin entirely, so the stored HR status
     * can drift out of sync with the real account state.
     *
     * @param int $userid
     * @return bool
     */
    public static function is_account_suspended(int $userid): bool {
        global $DB;

        return (bool) $DB->get_field('user', 'suspended', ['id' => $userid]);
    }
}
