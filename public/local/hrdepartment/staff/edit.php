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
 * Add/edit a staff profile.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\form\staff_form;
use local_hrdepartment\staff_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managestaff');

$staff = null;
if ($id) {
    $staff = staff_manager::get_staff($id);
    if (!$staff) {
        throw new moodle_exception('errorstaffnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/staff/index.php'));
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/staff/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id ? get_string('editstaffmember', 'local_hrdepartment') : get_string('addstaffmember', 'local_hrdepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$form = new staff_form($PAGE->url, [
    'employeeid' => $id,
    'currentuserid' => $staff->userid ?? null,
    'currentuserdisplay' => $staff ? ($staff->fullname . ' (' . $staff->email . ')') : '',
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/hrdepartment/staff/index.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        staff_manager::update($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/hrdepartment/staff/view.php', ['id' => $id]),
            get_string('changessaved'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        $newid = staff_manager::create($data, $USER->id);
        redirect(
            new moodle_url('/local/hrdepartment/staff/view.php', ['id' => $newid]),
            get_string('staffcreated', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($staff) {
    $form->set_data([
        'employeeid' => $staff->id,
        'userid' => $staff->userid,
        'employeecode' => $staff->employeecode,
        'departmentid' => $staff->departmentid,
        'designation' => $staff->designation,
        'employmentstatus' => $staff->employmentstatus,
        'phone' => $staff->phone,
        'address' => $staff->address,
        'emergencycontact' => $staff->emergencycontact,
        'joindate' => $staff->joindate,
    ]);
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('staff');

$subtitle = $id
    ? get_string('editstaffmemberdesc', 'local_hrdepartment')
    : get_string('addstaffmemberdesc', 'local_hrdepartment');

echo html_writer::start_div('local-hrdepartment-staff-form');

echo html_writer::start_div('hrdept-form-hero');
echo html_writer::tag('div', '<i class="icon fa fa-user-tie" aria-hidden="true"></i>', ['class' => 'hrdept-form-hero-icon']);
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
