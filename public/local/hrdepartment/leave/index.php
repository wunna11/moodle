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
 * Leave Overview / Dashboard for the student leave request/approval
 * workflow. See local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$canmanage = student_leave_manager::can_manage();
$canview = student_leave_manager::can_view();

if (!$canmanage && !$canview) {
    // Not HR/Admin and not a delegated Approver: a plain student holding
    // only local/hrdepartment:applyownleave belongs on the self-service
    // "My leave requests" page instead of this HR-facing org-wide
    // overview, which would otherwise expose every student's leave data.
    if (has_capability(student_leave_manager::CAP_APPLYOWN, $context) && student_leave_manager::is_student((int) $USER->id)) {
        redirect(new moodle_url('/local/hrdepartment/leave/myrequests.php'));
    }

    // A teacher chosen as the approver on at least one self-service
    // application holds none of the capabilities above either - route
    // them to "Leave requests to review" instead of this org-wide
    // overview.
    if (student_leave_manager::is_approver((int) $USER->id)) {
        redirect(new moodle_url('/local/hrdepartment/leave/myapprovals.php'));
    }

    require_capability(student_leave_manager::CAP_VIEW, $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leave', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('leaveoverview', 'local_hrdepartment'),
    get_string('leaveoverviewsubtitle', 'local_hrdepartment')
);

echo html_writer::start_div('hrdept-quicklink-grid');
if ($canmanage) {
    echo local_hrdepartment_render_quicklink(
        new moodle_url('/local/hrdepartment/leave/edit.php'),
        get_string('logleaverequest', 'local_hrdepartment'),
        'fa-calendar-plus'
    );
}
echo local_hrdepartment_render_quicklink(
    new moodle_url('/local/hrdepartment/leave/lookup.php'),
    get_string('leaverequests', 'local_hrdepartment'),
    'fa-list-alt'
);
if ($canmanage) {
    echo local_hrdepartment_render_quicklink(
        new moodle_url('/local/hrdepartment/leave/types.php'),
        get_string('leavetypes', 'local_hrdepartment'),
        'fa-tags'
    );
    echo local_hrdepartment_render_quicklink(
        new moodle_url('/local/hrdepartment/leave/balance.php'),
        get_string('leavebalances', 'local_hrdepartment'),
        'fa-balance-scale'
    );
}
echo local_hrdepartment_render_quicklink(
    new moodle_url('/local/hrdepartment/leave/reports.php'),
    get_string('leavereports', 'local_hrdepartment'),
    'fa-chart-bar'
);
echo html_writer::end_div();

$summary = student_leave_manager::get_dashboard_summary();

echo html_writer::start_div('hrdept-stats-strip');
echo local_hrdepartment_render_stat_card(
    (string) $summary->pending,
    get_string('pendingleaverequests', 'local_hrdepartment'),
    'hrdept-stat-pending',
    'fa-hourglass-half'
);
echo local_hrdepartment_render_stat_card(
    (string) $summary->onleavetoday,
    get_string('onleavetoday', 'local_hrdepartment'),
    'hrdept-stat-onleave',
    'fa-user-clock'
);
echo local_hrdepartment_render_stat_card(
    (string) $summary->approvedthismonth,
    get_string('leaveapprovedthismonth', 'local_hrdepartment'),
    'hrdept-stat-approved',
    'fa-calendar-check'
);
echo local_hrdepartment_render_stat_card(
    (string) $summary->total,
    get_string('totalleaverecords', 'local_hrdepartment'),
    'hrdept-stat-total',
    'fa-calendar-alt'
);
echo html_writer::end_div();

$recent = student_leave_manager::get_recent_applications();

echo html_writer::start_div('hrdept-summary-card mb-3');
echo html_writer::div(
    html_writer::span(
        html_writer::tag('i', '', ['class' => 'icon fa fa-clock', 'aria-hidden' => 'true']) .
        get_string('recentleaverecords', 'local_hrdepartment'),
        'hrdept-summary-card-title'
    ),
    'hrdept-summary-card-header'
);

if (empty($recent)) {
    echo local_hrdepartment_render_empty_state(
        get_string('norecentleaverecords', 'local_hrdepartment'),
        'fa-calendar-check'
    );
} else {
    $dateformat = get_string('strftimedatefullshort', 'langconfig');

    echo html_writer::start_div('hrdept-activity-list');
    foreach ($recent as $row) {
        $viewurl = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]);
        $initials = mb_strtoupper(mb_substr($row->firstname, 0, 1) . mb_substr($row->lastname, 0, 1));

        $meta = format_string($row->leavetypename) . ' &middot; '
            . userdate($row->startdate, $dateformat) . ' &ndash; ' . userdate($row->enddate, $dateformat);

        echo html_writer::link(
            $viewurl,
            html_writer::span($initials, 'hrdept-activity-avatar') .
            html_writer::span(
                html_writer::span(s($row->fullname), 'hrdept-activity-name') .
                html_writer::span($meta, 'hrdept-activity-meta'),
                'hrdept-activity-body'
            ) .
            local_hrdepartment_leave_status_badge($row->status) .
            html_writer::span($row->totaldays . ' ' . get_string('totaldays', 'local_hrdepartment'), 'hrdept-activity-days'),
            ['class' => 'hrdept-activity-item']
        );
    }
    echo html_writer::end_div();
}
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
