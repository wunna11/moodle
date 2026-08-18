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
 * Assigns a lecturer to a Moodle course (creates the hrdep_courseassign
 * record and syncs real Moodle enrolment/role assignment).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\course_assignment_manager;
use local_hrdepartment\form\courseassign_form;
use local_hrdepartment\lecturer_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managelecturers');

$lecturer = lecturer_manager::get_lecturer($id);
if (!$lecturer) {
    throw new moodle_exception('errorlecturernotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/lecturer/index.php'));
}

$returnurl = new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/lecturer/courseassign.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('assigncourse', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$form = new courseassign_form($PAGE->url, ['employeeid' => $id]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    [$assignid, $enrolsynced, $warning] = course_assignment_manager::assign($id, $data, $USER->id);

    if ($enrolsynced) {
        redirect($returnurl, get_string('courseassigned', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect(
            $returnurl,
            get_string('courseassignedwithwarning', 'local_hrdepartment', $warning),
            null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('lecturers');

echo $OUTPUT->heading(get_string('assigncourseto', 'local_hrdepartment', $lecturer->fullname));

$form->display();

echo $OUTPUT->footer();
