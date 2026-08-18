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
 * Cancels a student leave request (pending or already-approved). If it
 * had been approved, the days are returned to the student's balance.
 * Available to HR/Admin (can_manage()) or to the student themselves
 * withdrawing their own request - not to a reviewing teacher who only
 * holds can_review_application() for it.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();

$application = student_leave_manager::get_application($id);
if (!$application) {
    throw new moodle_exception('errorapplicationnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/lookup.php'));
}

$isownrequest = ((int) $USER->id === (int) $application->studentid);

if (!student_leave_manager::can_manage((int) $application->studentid) && !$isownrequest) {
    throw new required_capability_exception($context, student_leave_manager::CAP_MANAGE, 'nopermissions', '');
}

$returnurl = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id]);
$actionurl = new moodle_url('/local/hrdepartment/leave/cancel.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

if ($confirm && confirm_sesskey()) {
    student_leave_manager::cancel_application($id, $USER->id);
    redirect($returnurl, get_string('leavecancelled', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo $OUTPUT->confirm(
    get_string('confirmcancelleave', 'local_hrdepartment', $application->studentfullname),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo html_writer::end_div();

echo $OUTPUT->footer();
