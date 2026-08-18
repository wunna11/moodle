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
 * View a lecturer's profile and course assignment history.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\lecturer_manager;
use local_hrdepartment\output\lecturer_profile;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managelecturers');

$lecturer = lecturer_manager::get_lecturer($id);
if (!$lecturer) {
    throw new moodle_exception('errorlecturernotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/lecturer/index.php'));
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($lecturer->fullname);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$renderer = $PAGE->get_renderer('local_hrdepartment');

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('lecturers');

echo $renderer->render_lecturer_profile(new lecturer_profile($lecturer));

echo $OUTPUT->footer();
