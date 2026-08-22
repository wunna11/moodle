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
 * Add/edit a finance staff member.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\employee_manager;
use local_financedepartment\form\employee_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managestaff');

$employee = null;
if ($id) {
    $employee = employee_manager::get($id);
    if (!$employee) {
        throw new moodle_exception(
            'errorfinancestaffnotfound',
            'local_financedepartment',
            new moodle_url('/local/financedepartment/pages/staff/index.php')
        );
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/staff/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id
    ? get_string('editfinancestaff', 'local_financedepartment')
    : get_string('addfinancestaff', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

$form = new employee_form($PAGE->url, [
    'employeeid' => $id,
    'currentuserid' => $employee->userid ?? null,
    'currentuserdisplay' => $employee ? ($employee->fullname . ' (' . $employee->email . ')') : '',
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/financedepartment/pages/staff/index.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        employee_manager::update($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/staff/index.php'),
            get_string('changessaved'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        employee_manager::create($data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/staff/index.php'),
            get_string('financestaffcreated', 'local_financedepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($employee) {
    $form->set_data([
        'employeeid' => $employee->id,
        'userid' => $employee->userid,
        'employeecode' => $employee->employeecode,
        'designation' => $employee->designation,
        'status' => $employee->status,
    ]);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('staff');

$subtitle = $id
    ? get_string('editfinancestaffdesc', 'local_financedepartment')
    : get_string('addfinancestaffdesc', 'local_financedepartment');

echo html_writer::start_div('local-financedepartment-staff-form');

echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
