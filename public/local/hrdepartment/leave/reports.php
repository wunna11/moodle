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
 * Reports & Export: a filtered report of student leave applications with
 * a CSV export, built from the same query as the Leave requests table so
 * they always agree. See local_hrdepartment\student_leave_manager.
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
if (!$canmanage) {
    require_capability(student_leave_manager::CAP_VIEW, $context);
}

$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$leavetypeid = optional_param('leavetypeid', 0, PARAM_INT);
$datefromraw = optional_param('datefrom', '', PARAM_TEXT);
$datetoraw = optional_param('dateto', '', PARAM_TEXT);
$datefrom = $datefromraw !== '' ? strtotime($datefromraw . ' 00:00:00') : 0;
$dateto = $datetoraw !== '' ? strtotime($datetoraw . ' 23:59:59') : 0;
$export = optional_param('export', '', PARAM_ALPHA);

$filters = [
    'search' => $search,
    'status' => $status,
    'leavetypeid' => $leavetypeid,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
];

$rows = student_leave_manager::get_application_rows($filters);

if ($export === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=student-leave-report.csv');

    $out = fopen('php://output', 'w');
    fputcsv($out, [
        get_string('student', 'local_hrdepartment'),
        get_string('leavetype', 'local_hrdepartment'),
        get_string('startdate', 'local_hrdepartment'),
        get_string('enddate', 'local_hrdepartment'),
        get_string('totaldays', 'local_hrdepartment'),
        get_string('status', 'local_hrdepartment'),
    ]);

    $dateformat = get_string('strftimedatefullshort', 'langconfig');
    foreach ($rows as $row) {
        fputcsv($out, [
            $row->fullname,
            $row->leavetypename,
            userdate($row->startdate, $dateformat),
            userdate($row->enddate, $dateformat),
            $row->totaldays,
            get_string('status_' . $row->status, 'local_hrdepartment'),
        ]);
    }
    fclose($out);
    exit;
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/reports.php', [
    'search' => $search, 'status' => $status, 'leavetypeid' => $leavetypeid,
    'datefrom' => $datefromraw, 'dateto' => $datetoraw,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leavereports', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('leavereports', 'local_hrdepartment'),
    get_string('leavereportssubtitle', 'local_hrdepartment')
);

echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'hrdept-filter-bar']);

echo html_writer::empty_tag('input', [
    'type' => 'text', 'name' => 'search', 'value' => $search,
    'placeholder' => get_string('searchstudentplaceholder', 'local_hrdepartment'), 'class' => 'form-control mr-2 mb-2',
]);

$statusoptions = [
    '' => get_string('allstatuses', 'local_hrdepartment'),
    'pending' => get_string('status_pending', 'local_hrdepartment'),
    'approved' => get_string('status_approved', 'local_hrdepartment'),
    'rejected' => get_string('status_rejected', 'local_hrdepartment'),
    'cancelled' => get_string('status_cancelled', 'local_hrdepartment'),
];
echo html_writer::select($statusoptions, 'status', $status, null, ['class' => 'form-control mr-2 mb-2']);

$leavetypeoptions = [0 => get_string('allleavetypes', 'local_hrdepartment')] + student_leave_manager::get_leave_type_options();
echo html_writer::select($leavetypeoptions, 'leavetypeid', $leavetypeid, null, ['class' => 'form-control mr-2 mb-2']);

echo html_writer::tag('label', get_string('datefrom', 'local_hrdepartment'), ['class' => 'mr-1']);
echo html_writer::empty_tag('input', [
    'type' => 'date', 'name' => 'datefrom', 'value' => $datefromraw, 'class' => 'form-control mr-2 mb-2',
]);
echo html_writer::tag('label', get_string('dateto', 'local_hrdepartment'), ['class' => 'mr-1']);
echo html_writer::empty_tag('input', [
    'type' => 'date', 'name' => 'dateto', 'value' => $datetoraw, 'class' => 'form-control mr-2 mb-2',
]);

echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('filter', 'local_hrdepartment'), 'class' => 'btn btn-secondary mb-2']);

$exporturl = new moodle_url($PAGE->url, ['export' => 'csv']);
echo html_writer::link($exporturl, get_string('exportcsv', 'local_hrdepartment'), ['class' => 'btn btn-outline-secondary ml-2 mb-2']);

echo html_writer::end_tag('form');

if (empty($rows)) {
    echo local_hrdepartment_render_empty_state(
        get_string('noleaverecordsfound', 'local_hrdepartment'),
        'fa-chart-bar'
    );
} else {
    $table = new html_table();
    $table->head = [
        get_string('student', 'local_hrdepartment'),
        get_string('leavetype', 'local_hrdepartment'),
        get_string('startdate', 'local_hrdepartment'),
        get_string('enddate', 'local_hrdepartment'),
        get_string('totaldays', 'local_hrdepartment'),
        get_string('status', 'local_hrdepartment'),
    ];
    $table->attributes['class'] = 'generaltable';

    $dateformat = get_string('strftimedatefullshort', 'langconfig');

    foreach ($rows as $row) {
        $viewurl = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]);
        $table->data[] = [
            html_writer::link($viewurl, $row->fullname),
            format_string($row->leavetypename),
            userdate($row->startdate, $dateformat),
            userdate($row->enddate, $dateformat),
            $row->totaldays,
            local_hrdepartment_leave_status_badge($row->status),
        ];
    }

    echo local_hrdepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
