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
 * Leave record detail: one leave-marked mod_attendance record. Read-only
 * - there is nothing to approve/reject/edit here, since the mark itself
 * lives in and is only changed via mod_attendance. See
 * local_hrdepartment\student_leave_manager.
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

$record = student_leave_manager::get_leave_record($id);
if (!$record) {
    throw new moodle_exception('errorrecordnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/lookup.php'));
}

$isself = (int) $record->studentid === (int) $USER->id;
if (!$isself && !student_leave_manager::can_view_course_leave((int) $record->courseid)) {
    require_capability('local/hrdepartment:manageleave', $context);
} else if ($isself) {
    require_capability('local/hrdepartment:viewownleave', $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leaverequestdetail', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('leave');
echo $OUTPUT->tabtree($tabs, 'leave');

echo $OUTPUT->heading(get_string('leaverequestdetail', 'local_hrdepartment'));

$dateformat = get_string('strftimedatefullshort', 'langconfig');

echo html_writer::start_div('card mb-3');
echo html_writer::start_div('card-body');

$rows = [
    [get_string('student', 'local_hrdepartment'), $record->fullname . ' (' . $record->email . ')'],
    [get_string('course', 'local_hrdepartment'), $record->shortname . ': ' . format_string($record->fullname)],
    [get_string('attendancedate', 'local_hrdepartment'), userdate($record->sessdate, $dateformat)],
    [get_string('status', 'local_hrdepartment'), s($record->statusdescription) . ' (' . s($record->acronym) . ')'],
    [get_string('remarks', 'local_hrdepartment'), $record->remarks !== null && $record->remarks !== ''
        ? format_string($record->remarks) : '-'],
    [get_string('recordedby', 'local_hrdepartment'), $record->takenbyfullname ?? '-'],
    [get_string('recordedat', 'local_hrdepartment'), $record->timetaken ? userdate($record->timetaken, $dateformat) : '-'],
];

$table = new html_table();
$table->attributes['class'] = 'table table-sm mb-0';
foreach ($rows as [$label, $value]) {
    $table->data[] = [html_writer::tag('strong', $label), $value];
}
echo html_writer::table($table);

echo html_writer::end_div();
echo html_writer::end_div();

if (!empty($record->cmid)) {
    echo html_writer::link(
        new moodle_url('/mod/attendance/report.php', ['id' => $record->cmid]),
        get_string('openinattendanceactivity', 'local_hrdepartment'),
        ['class' => 'btn btn-secondary btn-sm']
    );
}

echo $OUTPUT->footer();
