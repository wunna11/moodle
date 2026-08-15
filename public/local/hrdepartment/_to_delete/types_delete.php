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
 * Deletes a student leave type. Blocked if any leave application or
 * balance already references it - deactivate it instead in that case.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
require_capability('local/hrdepartment:managestudentleave', $context);

$type = student_leave_manager::get_leave_type($id);
if (!$type) {
    throw new moodle_exception('errorleavetypenotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/types.php'));
}

$returnurl = new moodle_url('/local/hrdepartment/leave/types.php');
$actionurl = new moodle_url('/local/hrdepartment/leave/types_delete.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

if ($confirm && confirm_sesskey()) {
    if (!student_leave_manager::delete_leave_type($id)) {
        redirect($returnurl, get_string('errorleavetypeinuse', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_ERROR);
    }
    redirect($returnurl, get_string('leavetypedeleted', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('leave');
echo $OUTPUT->tabtree($tabs, 'leave');

echo $OUTPUT->confirm(
    get_string('confirmdeleteleavetype', 'local_hrdepartment', format_string($type->name)),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
