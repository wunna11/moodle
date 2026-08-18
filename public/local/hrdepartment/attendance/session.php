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
 * The attendance record list for one session/day: read straight from
 * mod_attendance's attendance_log, no editing. Only students who were
 * actually marked appear, matching exactly what the Attendance activity
 * recorded.
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

$sessionid = required_param('sessionid', PARAM_INT);

$context = context_system::instance();

$session = student_attendance_manager::get_session($sessionid);
if (!$session) {
    throw new moodle_exception('errorsessionnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/attendance/index.php'));
}

if (!student_attendance_manager::can_view_course_attendance($session->courseid)) {
    access_manager::require_manage('local/hrdepartment:manageattendance');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/attendance/session.php', ['sessionid' => $sessionid]));
$PAGE->set_pagelayout('standard');
$dateformat = get_string('strftimedatefullshort', 'langconfig');
$title = $session->shortname . ' - ' . userdate($session->sessdate, $dateformat);
$PAGE->set_title($title);
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('attendance');

echo html_writer::start_div('local-hrdepartment-attendance');

echo local_hrdepartment_render_subheader(
    $title,
    format_string($session->attendancename),
    new moodle_url('/local/hrdepartment/attendance/course.php', ['courseid' => $session->courseid]),
    get_string('backtosessions', 'local_hrdepartment')
);

if (!empty($session->cmid)) {
    echo html_writer::div(
        html_writer::link(
            new moodle_url('/mod/attendance/report.php', ['id' => $session->cmid]),
            get_string('openinattendanceactivity', 'local_hrdepartment'),
            ['class' => 'btn btn-secondary btn-sm']
        ),
        'mb-3'
    );
}

$records = student_attendance_manager::get_session_records($sessionid);

if (empty($records)) {
    echo local_hrdepartment_render_empty_state(
        get_string('norecordsforsession', 'local_hrdepartment'),
        'fa-users-slash'
    );
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = [
    get_string('student', 'local_hrdepartment'),
    get_string('status', 'local_hrdepartment'),
    get_string('remarks', 'local_hrdepartment'),
    get_string('recordedby', 'local_hrdepartment'),
    get_string('recordedat', 'local_hrdepartment'),
];
$table->attributes['class'] = 'generaltable local-hrdepartment-attendance-records';

foreach ($records as $record) {
    $studentname = fullname((object) ['firstname' => $record->firstname, 'lastname' => $record->lastname]);
    $studentlink = html_writer::link(
        new moodle_url('/local/hrdepartment/attendance/view.php', [
            'studentid' => $record->studentid,
            'courseid' => $session->courseid,
        ]),
        $studentname
    );

    $recordedby = ($record->takenbyfirstname !== null)
        ? fullname((object) ['firstname' => $record->takenbyfirstname, 'lastname' => $record->takenbylastname])
        : '-';

    $table->data[] = [
        $studentlink,
        s($record->statusdescription) . ' (' . s($record->acronym) . ')',
        $record->remarks !== null && $record->remarks !== '' ? format_string($record->remarks) : '-',
        $recordedby,
        $record->timetaken ? userdate($record->timetaken, get_string('strftimedatetimeshort', 'langconfig')) : '-',
    ];
}

echo local_hrdepartment_render_table_card(html_writer::table($table));

echo html_writer::end_div();

echo $OUTPUT->footer();
