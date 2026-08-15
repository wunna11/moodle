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
 * Reports & Export: a filtered report of leave-marked mod_attendance
 * records with a CSV export, built from the same query as the Student
 * Leave Lookup table so they always agree. Read-only - see
 * local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\course_assignment_manager;
use local_hrdepartment\student_attendance_manager;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
$canviewall = has_capability('local/hrdepartment:manageleave', $context);
$manageablecourses = student_attendance_manager::get_manageable_courses((int) $USER->id, $canviewall);

if (!$canviewall && empty($manageablecourses)) {
    require_capability('local/hrdepartment:manageleave', $context);
}

$courseidsrestriction = $canviewall ? null : array_keys($manageablecourses);

$courseid = optional_param('courseid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$datefromraw = optional_param('datefrom', '', PARAM_TEXT);
$datetoraw = optional_param('dateto', '', PARAM_TEXT);
$datefrom = $datefromraw !== '' ? strtotime($datefromraw . ' 00:00:00') : 0;
$dateto = $datetoraw !== '' ? strtotime($datetoraw . ' 23:59:59') : 0;
$export = optional_param('export', '', PARAM_ALPHA);

$filters = [
    'courseids' => $courseidsrestriction,
    'courseid' => $courseid,
    'search' => $search,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
];

$rows = student_leave_manager::get_leave_rows($filters);

if ($export === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=student-leave-report.csv');

    $out = fopen('php://output', 'w');
    fputcsv($out, [
        get_string('student', 'local_hrdepartment'),
        get_string('course', 'local_hrdepartment'),
        get_string('attendancedate', 'local_hrdepartment'),
        get_string('remarks', 'local_hrdepartment'),
    ]);

    $dateformat = get_string('strftimedatefullshort', 'langconfig');
    foreach ($rows as $row) {
        fputcsv($out, [
            $row->fullname,
            $row->shortname . ': ' . $row->fullname,
            userdate($row->sessdate, $dateformat),
            $row->remarks,
        ]);
    }
    fclose($out);
    exit;
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/reports.php', [
    'courseid' => $courseid, 'search' => $search, 'datefrom' => $datefromraw, 'dateto' => $datetoraw,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leavereports', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('leave');
echo $OUTPUT->tabtree($tabs, 'leave');

echo $OUTPUT->heading(get_string('leavereports', 'local_hrdepartment'));

echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'form-inline mb-3']);

echo html_writer::empty_tag('input', [
    'type' => 'text', 'name' => 'search', 'value' => $search,
    'placeholder' => get_string('searchstudentplaceholder', 'local_hrdepartment'), 'class' => 'form-control mr-2 mb-2',
]);

$courseoptions = [0 => get_string('allcourses', 'local_hrdepartment')];
$courseoptions += $canviewall ? course_assignment_manager::get_course_options() : $manageablecourses;
echo html_writer::select($courseoptions, 'courseid', $courseid, null, ['class' => 'form-control mr-2 mb-2']);

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
    echo $OUTPUT->notification(get_string('noleaverecordsfound', 'local_hrdepartment'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('student', 'local_hrdepartment'),
        get_string('course', 'local_hrdepartment'),
        get_string('attendancedate', 'local_hrdepartment'),
        get_string('remarks', 'local_hrdepartment'),
    ];
    $table->attributes['class'] = 'generaltable';

    $dateformat = get_string('strftimedatefullshort', 'langconfig');

    foreach ($rows as $row) {
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

echo $OUTPUT->footer();
