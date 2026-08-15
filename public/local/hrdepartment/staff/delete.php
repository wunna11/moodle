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
 * Deactivate or reactivate a staff member. Employees are never
 * hard-deleted since attendance/leave/payroll history references them;
 * deactivation (employmentstatus = terminated) preserves that history.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\constants;
use local_hrdepartment\staff_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$reactivate = optional_param('reactivate', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
require_capability('local/hrdepartment:managestaff', $context);

$staff = staff_manager::get_staff($id);
if (!$staff) {
    throw new moodle_exception('errorstaffnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/staff/index.php'));
}

$returnurl = new moodle_url('/local/hrdepartment/staff/view.php', ['id' => $id]);
$actionurl = new moodle_url('/local/hrdepartment/staff/delete.php', ['id' => $id, 'reactivate' => $reactivate]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

if ($confirm && confirm_sesskey()) {
    $newstatus = $reactivate ? constants::EMPLOYMENT_STATUS_ACTIVE : constants::EMPLOYMENT_STATUS_TERMINATED;
    staff_manager::set_employment_status($id, $newstatus, $USER->id);

    $message = $reactivate
        ? get_string('staffreactivated', 'local_hrdepartment')
        : get_string('staffdeactivated', 'local_hrdepartment');
    redirect($returnurl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('staff');
echo $OUTPUT->tabtree($tabs, 'staff');

$confirmstring = $reactivate
    ? get_string('confirmreactivate', 'local_hrdepartment', $staff->fullname)
    : get_string('confirmdeactivate', 'local_hrdepartment', $staff->fullname);

echo $OUTPUT->confirm(
    $confirmstring,
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
