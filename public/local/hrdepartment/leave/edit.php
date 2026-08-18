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
 * Log a new student leave request, or edit one that's still pending.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\constants;
use local_hrdepartment\form\student_leave_form;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);
$prefillstudentid = optional_param('studentid', 0, PARAM_INT);

$context = context_system::instance();

$application = null;
if ($id) {
    $application = student_leave_manager::get_application($id);
    if (!$application) {
        throw new moodle_exception('errorapplicationnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/lookup.php'));
    }
    if (!student_leave_manager::can_manage((int) $application->studentid)) {
        throw new required_capability_exception($context, student_leave_manager::CAP_MANAGE, 'nopermissions', '');
    }
    if ($application->status !== constants::LEAVE_STATUS_PENDING) {
        redirect(
            new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id]),
            get_string('erroreditnotpending', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
} else {
    // New application: without a pre-chosen student, this can only be
    // gated globally (a delegated per-student Approver has nothing to
    // scope the check to yet).
    require_capability(student_leave_manager::CAP_MANAGE, $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/edit.php', ['id' => $id, 'studentid' => $prefillstudentid]));
$PAGE->set_pagelayout('standard');
$title = $id ? get_string('editleaverequest', 'local_hrdepartment') : get_string('logleaverequest', 'local_hrdepartment');
$PAGE->set_title($title);
$PAGE->set_heading(student_leave_manager::get_page_heading());

$form = new student_leave_form($PAGE->url, [
    'applicationid' => $id,
    'studentid' => $application->studentid ?? ($prefillstudentid ?: null),
    'studentdisplay' => $application ? ($application->studentfullname . ' (' . $application->studentemail . ')') : '',
    'iscreate' => empty($id),
]);

if ($form->is_cancelled()) {
    redirect($id
        ? new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id])
        : new moodle_url('/local/hrdepartment/leave/lookup.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        student_leave_manager::update_application($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id]),
            get_string('leaverequestupdated', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        if (!student_leave_manager::can_manage((int) $data->studentid)) {
            throw new required_capability_exception($context, student_leave_manager::CAP_MANAGE, 'nopermissions', '');
        }
        $newid = student_leave_manager::create_application($data, $USER->id);
        redirect(
            new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $newid]),
            get_string('leaverequestcreated', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($application) {
    $form->set_data([
        'applicationid' => $application->id,
        'studentid' => $application->studentid,
        'leavetypeid' => $application->leavetypeid,
        'courseid' => $application->courseid,
        'startdate' => $application->startdate,
        'enddate' => $application->enddate,
        'reason' => $application->reason,
    ]);
} else if ($prefillstudentid) {
    $form->set_data(['studentid' => $prefillstudentid]);
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
    html_writer::tag('h2', $title, ['class' => 'hrdept-form-hero-title']) .
    html_writer::tag('p', get_string(
        $id ? 'editleaverequestsubtitle' : 'logleaverequestsubtitle',
        'local_hrdepartment'
    ), ['class' => 'hrdept-form-hero-subtitle'])
);
echo html_writer::end_div();

echo html_writer::start_div('hrdept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
