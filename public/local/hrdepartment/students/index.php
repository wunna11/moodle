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
 * Students directory: search/filter every student (any Moodle user
 * holding the "student" role in at least one course) and see their
 * enrolled courses. See local_hrdepartment\student_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\output\students_directory;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managestudents');

$search = optional_param('search', '', PARAM_TEXT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/students/index.php', [
    'search' => $search, 'courseid' => $courseid, 'status' => $status, 'page' => $page,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('students', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$renderer = $PAGE->get_renderer('local_hrdepartment');

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('students');

echo $OUTPUT->heading(get_string('studentsdirectory', 'local_hrdepartment'));

$directory = new students_directory($search, $courseid, $status, $page, $PAGE->url);
echo $renderer->render_students_directory($directory);

echo $OUTPUT->footer();
