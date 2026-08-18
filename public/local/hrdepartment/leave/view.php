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
 * Leave application detail, with review/edit/cancel actions where
 * applicable. See local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();

$application = student_leave_manager::get_application($id);
if (!$application) {
    throw new moodle_exception('errorapplicationnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/lookup.php'));
}

$canmanage = student_leave_manager::can_manage((int) $application->studentid);
$canreview = student_leave_manager::can_review_application($application);
$isownrequest = ((int) $USER->id === (int) $application->studentid);

if (!$canmanage && !$canreview && !$isownrequest && !student_leave_manager::can_view((int) $application->studentid)) {
    throw new required_capability_exception($context, student_leave_manager::CAP_VIEW, 'nopermissions', '');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leaverequestdetail', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

$dateformat = get_string('strftimedatefullshort', 'langconfig');

$initials = mb_strtoupper(mb_substr($application->studentfirstname, 0, 1) . mb_substr($application->studentlastname, 0, 1));
$herosubtitle = format_string($application->leavetypename) . ($application->courseid
    ? ' &middot; ' . $application->courseshortname . ': ' . format_string($application->coursefullname)
    : '');

echo html_writer::start_div('hrdept-detail-hero hrdept-detail-hero-' . $application->status);
echo html_writer::div($initials, 'hrdept-detail-hero-avatar');
echo html_writer::div(
    html_writer::tag('h2', s($application->studentfullname), ['class' => 'hrdept-detail-hero-title']) .
    html_writer::tag('p', $herosubtitle, ['class' => 'hrdept-detail-hero-subtitle']),
    'hrdept-detail-hero-text'
);
echo html_writer::span(
    get_string('status_' . $application->status, 'local_hrdepartment'),
    'hrdept-detail-hero-badge'
);
echo html_writer::end_div();

$rows = [
    [get_string('student', 'local_hrdepartment'), s($application->studentfullname) . ' (' . s($application->studentemail) . ')'],
    [get_string('startdate', 'local_hrdepartment'), userdate($application->startdate, $dateformat)],
    [get_string('enddate', 'local_hrdepartment'), userdate($application->enddate, $dateformat)],
    [get_string('totaldays', 'local_hrdepartment'), $application->totaldays],
    [get_string('reason', 'local_hrdepartment'), $application->reason !== null && $application->reason !== ''
        ? format_string($application->reason) : '-'],
    [get_string('submittedby', 'local_hrdepartment'), $application->submittedbyfullname],
    [get_string('approver', 'local_hrdepartment'), $application->approverfullname ?? get_string('noapproverassigned', 'local_hrdepartment')],
    [get_string('reviewedby', 'local_hrdepartment'), $application->reviewedbyfullname ?? '-'],
    [get_string('reviewnote', 'local_hrdepartment'), $application->reviewnote !== null && $application->reviewnote !== ''
        ? format_string($application->reviewnote) : '-'],
];

echo html_writer::start_div('hrdept-detail-grid');
foreach ($rows as [$label, $value]) {
    echo html_writer::div(
        html_writer::span($label, 'hrdept-detail-label') .
        html_writer::span($value, 'hrdept-detail-value'),
        'hrdept-detail-row'
    );
}
echo html_writer::end_div();

if ($canmanage || $canreview || $isownrequest) {
    if ($application->status === 'pending') {
        echo html_writer::start_div('hrdept-detail-actions');

        // Approve/reject: HR/Admin, a delegated per-student Approver, or
        // the specific teacher this student chose when applying (see
        // student_leave_manager::can_review_application()) - never the
        // student themselves, reviewing their own request.
        if ($canreview) {
            $approveurl = new moodle_url('/local/hrdepartment/leave/review.php', [
                'id' => $id, 'decision' => 'approved', 'sesskey' => sesskey(),
            ]);
            echo html_writer::link($approveurl, get_string('approve', 'local_hrdepartment'), ['class' => 'btn btn-success mr-2 mb-2']);

            $rejecturl = new moodle_url('/local/hrdepartment/leave/review.php', [
                'id' => $id, 'decision' => 'rejected', 'sesskey' => sesskey(),
            ]);
            echo html_writer::link($rejecturl, get_string('reject', 'local_hrdepartment'), ['class' => 'btn btn-danger mr-2 mb-2']);
        }

        // Editing the application's own details stays HR/Admin-only - a
        // reviewing teacher's role is to approve/reject, not rewrite it.
        if ($canmanage) {
            echo html_writer::link(
                new moodle_url('/local/hrdepartment/leave/edit.php', ['id' => $id]),
                get_string('edit'),
                ['class' => 'btn btn-outline-secondary mr-2 mb-2']
            );
        }

        // Cancel/withdraw: HR/Admin, or the student withdrawing their
        // own request.
        if ($canmanage || $isownrequest) {
            echo html_writer::link(
                new moodle_url('/local/hrdepartment/leave/cancel.php', ['id' => $id]),
                get_string('cancel'),
                ['class' => 'btn btn-outline-secondary mr-2 mb-2']
            );
        }

        echo html_writer::end_div();
    } else if ($application->status === 'approved' && ($canmanage || $isownrequest)) {
        echo html_writer::div(
            html_writer::link(
                new moodle_url('/local/hrdepartment/leave/cancel.php', ['id' => $id]),
                get_string('cancelleaverequest', 'local_hrdepartment'),
                ['class' => 'btn btn-outline-secondary mb-2']
            ),
            'hrdept-detail-actions'
        );
    }
}

echo html_writer::end_div();

echo $OUTPUT->footer();
