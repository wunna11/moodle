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
 * Self-service: a student prepares and submits their own leave request,
 * choosing which of their own course teachers should review it. Distinct
 * from leave/edit.php, which is the HR/staff-facing "log a request on a
 * student's behalf" form. See local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\form\student_leave_apply_form;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

require_capability(student_leave_manager::CAP_APPLYOWN, $context);

if (!student_leave_manager::is_student((int) $USER->id)) {
    throw new moodle_exception(
        'notastudentnoaccess',
        'local_hrdepartment',
        new moodle_url('/local/hrdepartment/leave/index.php')
    );
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/apply.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('applyforleave', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

$form = new student_leave_apply_form($PAGE->url, [
    'studentid' => (int) $USER->id,
    'studentdisplay' => fullname($USER) . ' (' . $USER->email . ')',
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/hrdepartment/leave/myrequests.php'));
}

if ($data = $form->get_data()) {
    // Defence in depth beyond the form's own validation(): never trust a
    // submitted approverid without re-checking it server-side against
    // this student's actual teachers.
    if (!student_leave_manager::is_teacher_of_student((int) $data->approverid, (int) $USER->id)) {
        throw new coding_exception('The selected approver is not a teacher of this student.');
    }

    $data->studentid = (int) $USER->id;
    $newid = student_leave_manager::create_application($data, (int) $USER->id);

    redirect(
        new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $newid]),
        get_string('leaverequestsubmitted', 'local_hrdepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave-form');

echo html_writer::start_div('hrdept-form-hero');
echo html_writer::div(
    html_writer::tag('i', '', ['class' => 'icon fa fa-calendar-plus', 'aria-hidden' => 'true']),
    'hrdept-form-hero-icon'
);
echo html_writer::div(
    html_writer::tag('h2', get_string('applyforleave', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-title']) .
    html_writer::tag('p', get_string('applyforleavesubtitle', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-subtitle'])
);
echo html_writer::end_div();

echo html_writer::start_div('hrdept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
