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
 * Cancel an installment plan. Never touches the fee record's balance -
 * see installmentplan_manager's class docblock.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\installmentplan_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:manageinstallments');

$plan = installmentplan_manager::get($id);
if (!$plan) {
    throw new moodle_exception(
        'errorinstallmentplannotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/installments/index.php')
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/installments/view.php', ['id' => $id]);
$actionurl = new moodle_url('/local/financedepartment/pages/installments/cancel.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(access_manager::get_display_name());
$PAGE->set_heading(access_manager::get_display_name());

if ($confirm && confirm_sesskey()) {
    installmentplan_manager::cancel($id, $USER->id);

    redirect($returnurl, get_string('installmentplancancelled', 'local_financedepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('installments');

echo $OUTPUT->confirm(
    get_string('confirmcancelinstallmentplan', 'local_financedepartment'),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
