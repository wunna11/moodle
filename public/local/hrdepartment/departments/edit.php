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
 * Add/edit a department.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\department_manager;
use local_hrdepartment\form\department_form;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage_departments();

$department = null;
if ($id) {
    $department = department_manager::get($id);
    if (!$department) {
        throw new moodle_exception(
            'errordepartmentnotfound',
            'local_hrdepartment',
            new moodle_url('/local/hrdepartment/departments/index.php')
        );
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/departments/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id ? get_string('editdepartment', 'local_hrdepartment') : get_string('adddepartment', 'local_hrdepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$form = new department_form($PAGE->url, [
    'id' => $id,
    'currentname' => $department->name ?? '',
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/hrdepartment/departments/index.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        department_manager::update($id, $data);
    } else {
        department_manager::create($data);
    }
    redirect(
        new moodle_url('/local/hrdepartment/departments/index.php'),
        get_string('departmentsaved', 'local_hrdepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

if ($department) {
    $form->set_data([
        'id' => $department->id,
        'name' => $department->name,
        'code' => $department->code,
    ]);
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('departments');

echo html_writer::start_div('local-hrdepartment-departments-form');

echo html_writer::start_div('hrdept-form-hero');
echo html_writer::div(
    html_writer::tag('i', '', ['class' => 'icon fa fa-sitemap', 'aria-hidden' => 'true']),
    'hrdept-form-hero-icon'
);
echo html_writer::div(
    html_writer::tag('h2', $title, ['class' => 'hrdept-form-hero-title']) .
    html_writer::tag('p', get_string(
        $id ? 'editdepartmentsubtitle' : 'adddepartmentsubtitle',
        'local_hrdepartment'
    ), ['class' => 'hrdept-form-hero-subtitle'])
);
echo html_writer::end_div();

echo html_writer::start_div('hrdept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
