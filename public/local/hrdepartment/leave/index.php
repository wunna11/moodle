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
 * Leave Overview / Dashboard: a read-only report on top of the site's
 * existing mod_attendance activity data - a student is "on leave" when a
 * lecturer marks them with the site's configured leave status while
 * taking attendance. This plugin never records leave itself; that action
 * happens in mod_attendance. See local_hrdepartment\student_leave_manager.
 *
 * Users who can view leave for at least one course (HR/admins holding
 * local/hrdepartment:manageleave, or lecturers with an active
 * hrdep_courseassign) see the org-wide/course summary here. Everyone
 * else (a plain student) sees their own leave history instead.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_attendance_manager;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$canviewall = has_capability('local/hrdepartment:manageleave', $context);
$manageablecourses = student_attendance_manager::get_manageable_courses((int) $USER->id, $canviewall);
$canviewany = $canviewall || !empty($manageablecourses);

if (!$canviewany) {
    require_capability('local/hrdepartment:viewownleave', $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leave', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('leave');
echo $OUTPUT->tabtree($tabs, 'leave');

echo $OUTPUT->heading(get_string('leaveoverview', 'local_hrdepartment'));

if ($canviewany) {

    $courseidsarray = $canviewall ? null : array_keys($manageablecourses);

    echo html_writer::start_div('d-flex flex-wrap mb-3');
    echo html_writer::link(
        new moodle_url('/local/hrdepartment/leave/lookup.php'),
        get_string('leavelookup', 'local_hrdepartment'),
        ['class' => 'btn btn-outline-secondary mr-2 mb-2']
    );
    echo html_writer::link(
        new moodle_url('/local/hrdepartment/leave/reports.php'),
        get_string('leavereports', 'local_hrdepartment'),
        ['class' => 'btn btn-outline-secondary mr-2 mb-2']
    );
    echo html_writer::end_div();

    $summary = student_leave_manager::get_dashboard_summary($courseidsarray);

    echo html_writer::start_div('row');
    $cards = [
        ['label' => get_string('activeleavetoday', 'local_hrdepartment'), 'value' => $summary->today],
        ['label' => get_string('leavethismonth', 'local_hrdepartment'), 'value' => $summary->thismonth],
        ['label' => get_string('totalleaverecords', 'local_hrdepartment'), 'value' => $summary->total],
    ];
    foreach ($cards as $card) {
        echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
        echo html_writer::div(
            html_writer::div($card['value'], 'display-6') .
            html_writer::div($card['label'], 'text-muted small text-uppercase'),
            'card card-body h-100'
        );
        echo html_writer::end_div();
    }
    echo html_writer::end_div();

    if (!empty($summary->bycourse)) {
        echo html_writer::start_div('card mb-3');
        echo html_writer::div(get_string('bycourseleave', 'local_hrdepartment'), 'card-header');
        echo html_writer::start_div('card-body');
        foreach ($summary->bycourse as $row) {
            echo html_writer::span(
                $row->shortname . ': ' . format_string($row->fullname) . ' - ' . $row->total,
                'badge badge-secondary mr-2 mb-1'
            );
        }
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    $recent = student_leave_manager::get_recent_leave_records($courseidsarray);

    echo html_writer::start_div('card mb-3');
    echo html_writer::div(get_string('recentleaverecords', 'local_hrdepartment'), 'card-header');

    if (empty($recent)) {
        echo html_writer::div(get_string('norecentleaverecords', 'local_hrdepartment'), 'card-body text-muted');
    } else {
        $table = new html_table();
        $table->head = [
            get_string('student', 'local_hrdepartment'),
            get_string('course', 'local_hrdepartment'),
            get_string('attendancedate', 'local_hrdepartment'),
            get_string('remarks', 'local_hrdepartment'),
        ];
        $table->attributes['class'] = 'generaltable mb-0';

        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        foreach ($recent as $row) {
            $viewurl = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->logid]);
            $table->data[] = [
                html_writer::link($viewurl, $row->fullname),
                $row->shortname . ': ' . format_string($row->fullname),
                userdate($row->sessdate, $dateformat),
                $row->remarks !== null && $row->remarks !== '' ? format_string($row->remarks) : '-',
            ];
        }

        echo html_writer::table($table);
    }
    echo html_writer::end_div();

} else {

    // Self-service: the logged-in user's own leave-marked attendance
    // records across every course.
    $mine = student_leave_manager::get_leave_rows(['studentid' => (int) $USER->id]);

    if (empty($mine)) {
        echo $OUTPUT->notification(get_string('norecentleaverecords', 'local_hrdepartment'), 'info');
    } else {
        $table = new html_table();
        $table->head = [
            get_string('course', 'local_hrdepartment'),
            get_string('attendancedate', 'local_hrdepartment'),
            get_string('remarks', 'local_hrdepartment'),
        ];
        $table->attributes['class'] = 'generaltable local-hrdepartment-my-leave';

        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        foreach ($mine as $row) {
            $table->data[] = [
                $row->shortname . ': ' . format_string($row->fullname),
                userdate($row->sessdate, $dateformat),
                $row->remarks !== null && $row->remarks !== '' ? format_string($row->remarks) : '-',
            ];
        }

        echo html_writer::table($table);
    }
}

echo $OUTPUT->footer();
