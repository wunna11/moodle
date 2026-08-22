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
 * Deactivate or reactivate a finance staff member. Never hard-deleted,
 * since fee structures/records may have been created or modified by
 * this account (usermodified references) - deactivation just removes
 * their finance access.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\employee_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$reactivate = optional_param('reactivate', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managestaff');

$employee = employee_manager::get($id);
if (!$employee) {
    throw new moodle_exception(
        'errorfinancestaffnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/staff/index.php')
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/staff/index.php');
$actionurl = new moodle_url('/local/financedepartment/pages/staff/deactivate.php', ['id' => $id, 'reactivate' => $reactivate]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_financedepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

if ($confirm && confirm_sesskey()) {
    $newstatus = $reactivate ? constants::EMPLOYEE_STATUS_ACTIVE : constants::EMPLOYEE_STATUS_INACTIVE;
    employee_manager::set_status($id, $newstatus, $USER->id);

    $message = $reactivate
        ? get_string('financestaffreactivated', 'local_financedepartment')
        : get_string('financestaffdeactivated', 'local_financedepartment');
    redirect($returnurl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('staff');

$confirmstring = $reactivate
    ? get_string('confirmreactivatefinancestaff', 'local_financedepartment', $employee->fullname)
    : get_string('confirmdeactivatefinancestaff', 'local_financedepartment', $employee->fullname);

echo $OUTPUT->confirm(
    $confirmstring,
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
