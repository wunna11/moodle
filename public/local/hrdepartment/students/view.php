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
 * Student profile: one student's details and their full enrolled-course
 * list. Linked from the Students directory card grid instead of showing
 * courses inline, so a card with 40 enrolments doesn't blow out the grid.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\output\student_profile;
use local_hrdepartment\student_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managestudents');

$id = required_param('id', PARAM_INT);

$student = student_manager::get_student($id);
if (!$student) {
    throw new moodle_exception(
        'errorstudentnotfound',
        'local_hrdepartment',
        new moodle_url('/local/hrdepartment/students/index.php')
    );
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/students/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($student->fullname);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$renderer = $PAGE->get_renderer('local_hrdepartment');

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('students');

$page = new student_profile($student);
echo $renderer->render_student_profile($page);

echo $OUTPUT->footer();
