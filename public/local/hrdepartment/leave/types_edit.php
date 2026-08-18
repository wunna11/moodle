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
 * Add/edit a student leave type.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\form\student_leavetype_form;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
require_capability(student_leave_manager::CAP_MANAGE, $context);

$type = null;
if ($id) {
    $type = student_leave_manager::get_leave_type($id);
    if (!$type) {
        throw new moodle_exception('errorleavetypenotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/types.php'));
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/types_edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id ? get_string('editleavetype', 'local_hrdepartment') : get_string('addleavetype', 'local_hrdepartment');
$PAGE->set_title($title);
$PAGE->set_heading(student_leave_manager::get_page_heading());

$form = new student_leavetype_form($PAGE->url, ['id' => $id]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/hrdepartment/leave/types.php'));
}

if ($data = $form->get_data()) {
    student_leave_manager::save_leave_type($data);
    redirect(
        new moodle_url('/local/hrdepartment/leave/types.php'),
        get_string('leavetypesaved', 'local_hrdepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

if ($type) {
    $form->set_data([
        'id' => $type->id,
        'name' => $type->name,
        'description' => $type->description,
        'maxdaysperyear' => $type->maxdaysperyear,
        'requiresapproval' => $type->requiresapproval,
        'active' => $type->active,
    ]);
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave-form');

echo html_writer::start_div('hrdept-form-hero');
echo html_writer::div(
    html_writer::tag('i', '', ['class' => 'icon fa fa-tags', 'aria-hidden' => 'true']),
    'hrdept-form-hero-icon'
);
echo html_writer::div(
    html_writer::tag('h2', $title, ['class' => 'hrdept-form-hero-title']) .
    html_writer::tag('p', get_string(
        $id ? 'editleavetypesubtitle' : 'addleavetypesubtitle',
        'local_hrdepartment'
    ), ['class' => 'hrdept-form-hero-subtitle'])
);
echo html_writer::end_div();

echo html_writer::start_div('hrdept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
