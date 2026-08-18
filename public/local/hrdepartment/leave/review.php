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
 * Approves or rejects a pending student leave request. Posted to from
 * leave/view.php; kept deliberately simple as a single-step action,
 * matching the confirm-and-go pattern used elsewhere in this plugin
 * (e.g. staff/delete.php).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\constants;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();
require_sesskey();

$id = required_param('id', PARAM_INT);
$decision = required_param('decision', PARAM_ALPHA);

$context = context_system::instance();

if (!in_array($decision, [constants::LEAVE_STATUS_APPROVED, constants::LEAVE_STATUS_REJECTED], true)) {
    throw new coding_exception('Invalid leave review decision.');
}

$application = student_leave_manager::get_application($id);
if (!$application) {
    throw new moodle_exception('errorapplicationnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/lookup.php'));
}

if (!student_leave_manager::can_review_application($application)) {
    throw new required_capability_exception($context, student_leave_manager::CAP_MANAGE, 'nopermissions', '');
}

$returnurl = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id]);

if ($application->status !== constants::LEAVE_STATUS_PENDING) {
    redirect($returnurl, get_string('errorreviewnotpending', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_ERROR);
}

student_leave_manager::review_application($id, $decision, $USER->id);

$message = $decision === constants::LEAVE_STATUS_APPROVED
    ? get_string('leaveapproved', 'local_hrdepartment')
    : get_string('leaverejected', 'local_hrdepartment');

redirect($returnurl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
