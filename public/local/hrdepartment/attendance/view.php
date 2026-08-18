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

use local_hrdepartment\access_manager;
use local_hrdepartment\student_attendance_manager;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$studentid = required_param('studentid', PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

$context = context_system::instance();

$isviewingself = ((int) $USER->id === $studentid) && has_capability('local/hrdepartment:viewownattendance', $context);
$canviewall = access_manager::can_manage('local/hrdepartment:manageattendance');
$canviewcourse = $courseid && student_attendance_manager::can_view_course_attendance($courseid);

if (!$isviewingself && !$canviewall && !$canviewcourse) {
    // None of the three access paths apply - fall through to the normal
    // "you don't have this capability" exception.
    access_manager::require_manage('local/hrdepartment:manageattendance');
}

$student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0], '*', MUST_EXIST);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/attendance/view.php', [
    'studentid' => $studentid,
    'courseid' => $courseid,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(fullname($student));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('attendance');

echo html_writer::start_div('local-hrdepartment-attendance');

echo local_hrdepartment_render_subheader(
    get_string('attendancehistoryfor', 'local_hrdepartment', fullname($student)),
    get_string('attendancehistorysubtitle', 'local_hrdepartment')
);

$summary = student_attendance_manager::get_student_status_summary($studentid, $courseid ?: null);

if (empty($summary)) {
    echo local_hrdepartment_render_empty_state(
        get_string('noattendancerecords', 'local_hrdepartment'),
        'fa-clipboard-check'
    );
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

$colors = ['hrdept-stat-c1', 'hrdept-stat-c2', 'hrdept-stat-c3', 'hrdept-stat-c4', 'hrdept-stat-c5'];
echo html_writer::start_div('hrdept-stats-strip');
foreach (array_values($summary) as $i => $row) {
    echo local_hrdepartment_render_stat_card(
        (string) $row->total,
        s($row->description) . ' (' . s($row->acronym) . ')',
        $colors[$i % count($colors)],
        'fa-clipboard-check'
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

echo local_hrdepartment_render_table_card(html_writer::table($table));

echo html_writer::end_div();

echo $OUTPUT->footer();
