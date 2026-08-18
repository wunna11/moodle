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
 * Sets the allocated leave days for one student/leave type/academic year.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\form\student_leavebalance_form;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$studentid = required_param('studentid', PARAM_INT);
$leavetypeid = required_param('leavetypeid', PARAM_INT);
$academicyear = required_param('academicyear', PARAM_ALPHANUMEXT);

$context = context_system::instance();
if (!student_leave_manager::can_manage($studentid)) {
    throw new required_capability_exception($context, student_leave_manager::CAP_MANAGE, 'nopermissions', '');
}

$student = core_user::get_user($studentid);
if (!$student || $student->deleted) {
    throw new moodle_exception('invaliduser');
}

$leavetype = student_leave_manager::get_leave_type($leavetypeid);
if (!$leavetype) {
    throw new moodle_exception('errorleavetypenotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/balance.php'));
}

$balance = student_leave_manager::get_or_create_balance($studentid, $leavetypeid, $academicyear);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/balance_edit.php', [
    'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('setallocation', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

$returnurl = new moodle_url('/local/hrdepartment/leave/balance.php', [
    'studentid' => $studentid, 'academicyear' => $academicyear,
]);

$form = new student_leavebalance_form($PAGE->url, [
    'studentid' => $studentid,
    'leavetypeid' => $leavetypeid,
    'academicyear' => $academicyear,
    'leavetypename' => format_string($leavetype->name),
    'studentdisplay' => fullname($student) . ' (' . $student->email . ')',
    'used' => $balance->used,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    student_leave_manager::save_balance_allocation($studentid, $leavetypeid, $academicyear, (float) $data->allocated);
    redirect($returnurl, get_string('balanceupdated', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$form->set_data(['allocated' => $balance->allocated]);

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave-form');

echo html_writer::start_div('hrdept-form-hero');
echo html_writer::div(
    html_writer::tag('i', '', ['class' => 'icon fa fa-balance-scale', 'aria-hidden' => 'true']),
    'hrdept-form-hero-icon'
);
echo html_writer::div(
    html_writer::tag('h2', get_string('setallocation', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-title']) .
    html_writer::tag('p', get_string('setallocationsubtitle', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-subtitle'])
);
echo html_writer::end_div();

echo html_writer::start_div('hrdept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
