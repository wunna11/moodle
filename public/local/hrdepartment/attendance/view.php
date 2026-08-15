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
 * A single student's attendance history, read from mod_attendance: a
 * status summary plus their full record listing, optionally scoped to
 * one course.
 *
 * Reachable by: an HR/admin (manageattendance, any student), the
 * student's own course lecturer (for that course only), or the student
 * themselves viewing their own history.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_attendance_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$studentid = required_param('studentid', PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

$context = context_system::instance();

$isviewingself = ((int) $USER->id === $studentid) && has_capability('local/hrdepartment:viewownattendance', $context);
$canviewall = has_capability('local/hrdepartment:manageattendance', $context);
$canviewcourse = $courseid && student_attendance_manager::can_view_course_attendance($courseid);

if (!$isviewingself && !$canviewall && !$canviewcourse) {
    // None of the three access paths apply - fall through to the normal
    // "you don't have this capability" exception.
    require_capability('local/hrdepartment:manageattendance', $context);
}

$student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0], '*', MUST_EXIST);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/attendance/view.php', [
    'studentid' => $studentid,
    'courseid' => $courseid,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(fullname($student));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('attendance');
echo $OUTPUT->tabtree($tabs, 'attendance');

echo $OUTPUT->heading(get_string('attendancehistoryfor', 'local_hrdepartment', fullname($student)));

$summary = student_attendance_manager::get_student_status_summary($studentid, $courseid ?: null);

if (empty($summary)) {
    echo $OUTPUT->notification(get_string('noattendancerecords', 'local_hrdepartment'), 'info');
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::start_div('local-hrdepartment-attendance-summary d-flex flex-wrap mb-3');
foreach ($summary as $row) {
    echo html_writer::div(
        html_writer::div($row->total, 'h4 mb-0') .
        html_writer::div(s($row->description) . ' (' . s($row->acronym) . ')', 'text-muted small'),
        'card p-3 mr-2 mb-2 text-center'
    );
}
echo html_writer::end_div();

// Every access path validated above already narrows the result set
// correctly on its own (own-studentid always used below, plus a
// single already-permission-checked course filter when set), so no
// additional course-list restriction is needed here.
$records = student_attendance_manager::get_student_records($studentid, $courseid ?: null);

$table = new html_table();
$table->head = [
    get_string('course', 'local_hrdepartment'),
    get_string('attendancedate', 'local_hrdepartment'),
    get_string('status', 'local_hrdepartment'),
    get_string('remarks', 'local_hrdepartment'),
];
$table->attributes['class'] = 'generaltable local-hrdepartment-attendance-history';

$dateformat = get_string('strftimedatefullshort', 'langconfig');

foreach ($records as $record) {
    $table->data[] = [
        $record->shortname . ': ' . format_string($record->fullname),
        userdate($record->sessdate, $dateformat),
        s($record->statusdescription) . ' (' . s($record->acronym) . ')',
        $record->remarks !== null && $record->remarks !== '' ? format_string($record->remarks) : '-',
    ];
}

echo html_writer::table($table);

echo $OUTPUT->footer();
