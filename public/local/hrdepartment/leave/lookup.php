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
 * Student Leave Lookup: search every leave-marked mod_attendance record
 * by student, optionally scoped to a course. Read-only - see
 * local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\course_assignment_manager;
use local_hrdepartment\student_attendance_manager;
use local_hrdepartment\table\student_leave_table;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
$canviewall = has_capability('local/hrdepartment:manageleave', $context);
$manageablecourses = student_attendance_manager::get_manageable_courses((int) $USER->id, $canviewall);

if (!$canviewall && empty($manageablecourses)) {
    require_capability('local/hrdepartment:manageleave', $context);
}

$search = optional_param('search', '', PARAM_TEXT);
$courseid = optional_param('courseid', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/lookup.php', ['search' => $search, 'courseid' => $courseid]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leavelookup', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('leave');
echo $OUTPUT->tabtree($tabs, 'leave');

echo $OUTPUT->heading(get_string('leavelookup', 'local_hrdepartment'));

$courseidsrestriction = $canviewall ? null : array_keys($manageablecourses);

echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'form-inline mb-3']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudentplaceholder', 'local_hrdepartment'),
    'class' => 'form-control mr-2 mb-2',
]);

$courseoptions = [0 => get_string('allcourses', 'local_hrdepartment')];
$allcourses = $canviewall ? course_assignment_manager::get_course_options() : $manageablecourses;
$courseoptions += $allcourses;
echo html_writer::select($courseoptions, 'courseid', $courseid, null, ['class' => 'form-control mr-2 mb-2']);

echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('filter', 'local_hrdepartment'), 'class' => 'btn btn-secondary mb-2']);
echo html_writer::end_tag('form');

$table = new student_leave_table('local-hrdepartment-student-leave', $search, $courseid, $courseidsrestriction);
$table->define_baseurl($PAGE->url);
$table->out(20, true);

echo $OUTPUT->footer();
