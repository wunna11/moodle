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
 * One course's attendance sessions ("Day 1", "Day 2", ...), read straight
 * from mod_attendance. Each row links to that day's record list.
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

$courseid = required_param('courseid', PARAM_INT);

$context = context_system::instance();

if (!student_attendance_manager::can_view_course_attendance($courseid)) {
    access_manager::require_manage('local/hrdepartment:manageattendance');
}

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/attendance/course.php', ['courseid' => $courseid]));
$PAGE->set_pagelayout('standard');
$title = $course->shortname . ': ' . format_string($course->fullname);
$PAGE->set_title($title);
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('attendance');

echo html_writer::start_div('local-hrdepartment-attendance');

echo local_hrdepartment_render_subheader(
    $title,
    get_string('attendancesessionssubtitle', 'local_hrdepartment'),
    new moodle_url('/local/hrdepartment/attendance/index.php'),
    get_string('backtocourses', 'local_hrdepartment')
);

$sessions = student_attendance_manager::get_sessions_for_course($courseid);

if (empty($sessions)) {
    echo local_hrdepartment_render_empty_state(
        get_string('nosessionsforcourse', 'local_hrdepartment'),
        'fa-calendar-times'
    );
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = [
    get_string('attendancedate', 'local_hrdepartment'),
    get_string('attendanceactivity', 'local_hrdepartment'),
    get_string('totalmarked', 'local_hrdepartment'),
    get_string('actions'),
];
$table->attributes['class'] = 'generaltable local-hrdepartment-attendance-sessions';

$dateformat = get_string('strftimedatefullshort', 'langconfig');

foreach ($sessions as $session) {
    $recordsurl = new moodle_url('/local/hrdepartment/attendance/session.php', ['sessionid' => $session->sessionid]);

    $actions = [html_writer::link($recordsurl, get_string('viewrecords', 'local_hrdepartment'))];
    if (!empty($session->cmid)) {
        $actions[] = html_writer::link(
            new moodle_url('/mod/attendance/report.php', ['id' => $session->cmid]),
            get_string('openinattendanceactivity', 'local_hrdepartment')
        );
    }

    $table->data[] = [
        userdate($session->sessdate, $dateformat),
        format_string($session->attendancename),
        $session->totalmarked,
        implode(' | ', $actions),
    ];
}

echo local_hrdepartment_render_table_card(html_writer::table($table));

echo html_writer::end_div();

echo $OUTPUT->footer();
