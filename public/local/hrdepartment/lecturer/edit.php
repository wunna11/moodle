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
 * Add/edit a lecturer profile.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\form\lecturer_form;
use local_hrdepartment\lecturer_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managelecturers');

$lecturer = null;
if ($id) {
    $lecturer = lecturer_manager::get_lecturer($id);
    if (!$lecturer) {
        throw new moodle_exception('errorlecturernotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/lecturer/index.php'));
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/lecturer/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id ? get_string('editlecturer', 'local_hrdepartment') : get_string('addlecturer', 'local_hrdepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$form = new lecturer_form($PAGE->url, [
    'employeeid' => $id,
    'currentuserid' => $lecturer->userid ?? null,
    'currentuserdisplay' => $lecturer ? ($lecturer->fullname . ' (' . $lecturer->email . ')') : '',
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/hrdepartment/lecturer/index.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        lecturer_manager::update($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $id]),
            get_string('changessaved'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        $newid = lecturer_manager::create($data, $USER->id);
        redirect(
            new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $newid]),
            get_string('lecturercreated', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($lecturer) {
    $form->set_data([
        'employeeid' => $lecturer->id,
        'userid' => $lecturer->userid,
        'employeecode' => $lecturer->employeecode,
        'departmentid' => $lecturer->departmentid,
        'designation' => $lecturer->designation,
        'employmentstatus' => $lecturer->employmentstatus,
        'phone' => $lecturer->phone,
        'address' => $lecturer->address,
        'emergencycontact' => $lecturer->emergencycontact,
        'joindate' => $lecturer->joindate,
        'qualification' => $lecturer->qualification,
        'specialization' => $lecturer->specialization,
    ]);
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('lecturers');

$subtitle = $id
    ? get_string('editlecturerdesc', 'local_hrdepartment')
    : get_string('addlecturerdesc', 'local_hrdepartment');

echo html_writer::start_div('local-hrdepartment-lecturer-form');

echo html_writer::start_div('hrdept-form-hero');
echo html_writer::tag('div', '<i class="icon fa fa-chalkboard-teacher" aria-hidden="true"></i>', ['class' => 'hrdept-form-hero-icon']);
echo html_writer::start_div('hrdept-form-hero-text');
echo html_writer::tag('h2', $title, ['class' => 'hrdept-form-hero-title']);
echo html_writer::tag('p', $subtitle, ['class' => 'hrdept-form-hero-subtitle']);
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::start_div('hrdept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
