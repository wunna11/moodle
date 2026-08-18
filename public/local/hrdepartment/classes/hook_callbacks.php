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
 * Hook callbacks for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class hook_callbacks
 */
class hook_callbacks {

    /**
     * Adds "Apply for leave" (students) and/or "Leave requests to
     * review" (reviewing teachers) to the top-right account/user menu
     * (the dropdown reached by clicking your avatar, next to Profile,
     * Grades, Calendar, Preferences, Log out) - direct, always-visible
     * shortcuts that don't depend on finding the "HR Department" top nav
     * link first.
     *
     * Added 2026-08-17 because relying on the "HR Department" top nav
     * link + "Leave" tab to find the self-service form was not
     * discoverable enough in practice: a student instead found their way
     * to leave/edit.php (the pre-existing HR/staff-only "log a request
     * on a student's behalf" form) and hit a "you do not currently have
     * permissions" error for local/hrdepartment:managestudentleave -
     * that page is intentionally HR/Admin-only and was never meant for
     * students.
     *
     * Extended the same day for teachers: a teacher chosen as the
     * approving teacher on a student's self-service request
     * (student_leave_manager::CAP_APPLYOWN, leave/apply.php) holds none
     * of the plugin's capabilities and had no menu entry, no nav tab,
     * and no listing page at all - only a direct leave/view.php?id=X
     * link would work, so the request they'd been asked to approve was
     * effectively invisible to them in the UI. See
     * student_leave_manager::is_approver() and leave/myapprovals.php.
     *
     * @param \core_user\hook\extend_user_menu $hook
     */
    public static function extend_user_menu(\core_user\hook\extend_user_menu $hook): void {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return;
        }

        $context = \context_system::instance();
        $userid = (int) $USER->id;

        if (has_capability(student_leave_manager::CAP_APPLYOWN, $context)
                && student_leave_manager::is_student($userid)) {
            $item = new \stdClass();
            $item->itemtype = 'link';
            $item->url = new \moodle_url('/local/hrdepartment/leave/apply.php');
            $item->title = get_string('applyforleave', 'local_hrdepartment');
            $item->titleidentifier = 'applyforleave,local_hrdepartment';
            $item->pix = 'i/calendar';

            $hook->add_navitem($item);
        }

        if (student_leave_manager::is_approver($userid)) {
            $item = new \stdClass();
            $item->itemtype = 'link';
            $item->url = new \moodle_url('/local/hrdepartment/leave/myapprovals.php');
            $item->title = get_string('myapprovals', 'local_hrdepartment');
            $item->titleidentifier = 'myapprovals,local_hrdepartment';
            $item->pix = 'i/calendar';

            $hook->add_navitem($item);
        }
    }
}
