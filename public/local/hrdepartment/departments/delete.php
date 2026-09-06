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
 * Deletes a department. Refused (with a specific error message) if the
 * department name is protected (see department_manager::PROTECTED_NAMES)
 * or still has staff/lecturers (or sub-departments) assigned to it - see
 * department_manager::delete().
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\department_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
access_manager::require_manage_departments();

$department = department_manager::get($id);
if (!$department) {
    throw new moodle_exception(
        'errordepartmentnotfound',
        'local_hrdepartment',
        new moodle_url('/local/hrdepartment/departments/index.php')
    );
}

$returnurl = new moodle_url('/local/hrdepartment/departments/index.php');
$actionurl = new moodle_url('/local/hrdepartment/departments/delete.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

if ($confirm && confirm_sesskey()) {
    if (!department_manager::delete($id)) {
        $reason = department_manager::is_protected($department->name)
            ? get_string('errordepartmentprotected', 'local_hrdepartment')
            : get_string('errordepartmentinuse', 'local_hrdepartment');
        redirect($returnurl, $reason, null, \core\output\notification::NOTIFY_ERROR);
    }
    redirect($returnurl, get_string('departmentdeleted', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('departments');

echo html_writer::start_div('local-hrdepartment-departments');

echo $OUTPUT->confirm(
    get_string('confirmdeletedepartment', 'local_hrdepartment', format_string($department->name)),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo html_writer::end_div();

echo $OUTPUT->footer();
