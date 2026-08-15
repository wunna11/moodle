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
 * Marks (or amends) student attendance for one course on one day. Reopening
 * a day that already has records pre-fills the form from
 * hrdep_studentattendance rather than starting blank, so this page doubles
 * as both "take attendance" and "edit attendance".
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\constants;
use local_hrdepartment\form\mark_attendance_form;
use local_hrdepartment\student_attendance_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$datestr = optional_param('date', '', PARAM_TEXT);

$context = context_system::instance();

if (!student_attendance_manager::can_manage_course_attendance($courseid)) {
    // Not an HR/admin and not this course's assigned lecturer - throws a
    // standard "not allowed" exception the same way require_capability()
    // would, since no single system-wide capability covers "my course".
    require_capability('local/hrdepartment:manageattendance', $context);
}

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

// attendancedate is stored as a plain midnight timestamp (matching
// hrdep_attendance's existing convention elsewhere in the plugin), not a
// user-timezone-aware one - "midnight" here parses in PHP's default
// timezone, same as dashboard_helper's strtotime('midnight') calls.
$date = $datestr !== '' ? strtotime($datestr . ' midnight') : strtotime('midnight');
if ($date === false) {
    $date = strtotime('midnight');
}

$returnurl = new moodle_url('/local/hrdepartment/attendance/index.php', ['courseid' => $courseid]);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/attendance/mark.php', [
    'courseid' => $courseid,
    'date' => date('Y-m-d', $date),
]));
$PAGE->set_pagelayout('standard');
$title = get_string('markattendance', 'local_hrdepartment') . ': ' . $course->shortname;
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$students = student_attendance_manager::get_enrolled_students($courseid);

if (empty($students)) {
    echo $OUTPUT->header();
    $tabs = local_hrdepartment_get_tabs('attendance');
    echo $OUTPUT->tabtree($tabs, 'attendance');
    echo $OUTPUT->heading($title);
    echo $OUTPUT->notification(get_string('nostudentsenrolled', 'local_hrdepartment'), 'info');
    echo $OUTPUT->footer();
    exit;
}

$existing = student_attendance_manager::get_attendance_for_date($courseid, $date);

$form = new mark_attendance_form($PAGE->url, [
    'courseid' => $courseid,
    'coursename' => $course->shortname . ': ' . format_string($course->fullname),
    'date' => $date,
    'datestring' => userdate($date, get_string('strftimedatefullshort', 'langconfig')),
    'students' => $students,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $entries = [];
    foreach ($students as $student) {
        $statusfield = 'status_' . $student->id;
        $remarksfield = 'remarks_' . $student->id;
        $entries[$student->id] = [
            'status' => $data->$statusfield ?? constants::ATTENDANCE_PRESENT,
            'remarks' => $data->$remarksfield ?? '',
        ];
    }

    student_attendance_manager::save_bulk($courseid, $date, $entries, $USER->id);

    redirect(
        new moodle_url('/local/hrdepartment/attendance/index.php', ['courseid' => $courseid]),
        get_string('attendancesaved', 'local_hrdepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Pre-fill from any records already saved for this course/day.
$prefill = ['courseid' => $courseid, 'date' => $date];
foreach ($students as $student) {
    $record = $existing[$student->id] ?? null;
    $prefill['status_' . $student->id] = $record->status ?? constants::ATTENDANCE_PRESENT;
    $prefill['remarks_' . $student->id] = $record->remarks ?? '';
}
$form->set_data($prefill);

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('attendance');
echo $OUTPUT->tabtree($tabs, 'attendance');

echo $OUTPUT->heading($title);

$form->display();

echo $OUTPUT->footer();
