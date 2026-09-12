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
 * Reschedule an existing installment plan (edit amounts/due dates).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\installmentplan_manager;
use local_financedepartment\form\installmentplan_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$id = required_param('id', PARAM_INT);

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

if ($plan->status !== constants::INSTALLMENTPLAN_STATUS_ACTIVE) {
    redirect($returnurl, get_string('errorinstallmentplannotactive', 'local_financedepartment'), null, \core\output\notification::NOTIFY_WARNING);
}

$schedule = installmentplan_manager::get_schedule($id);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/installments/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = get_string('rescheduleinstallmentplan', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$form = new installmentplan_form($PAGE->url, [
    'planid' => $id,
    'presetfeerecordid' => $plan->feerecordid,
    'repeatcount' => max(1, count($schedule)),
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $installments = [];
    foreach ($data->installmentamount as $index => $amount) {
        if ($amount === '' || $amount === null || (float) $amount <= 0) {
            continue;
        }
        $installments[] = [
            'amount' => (float) $amount,
            'duedate' => (int) $data->installmentduedate[$index],
        ];
    }

    installmentplan_manager::reschedule($id, $installments, $USER->id);

    redirect(
        $returnurl,
        get_string('installmentplanupdated', 'local_financedepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Pre-fill the existing schedule.
$prefill = [
    'planid' => $id,
    'feerecordid' => $plan->feerecordid,
    'installmentamount' => [],
    'installmentduedate' => [],
];
foreach ($schedule as $index => $sched) {
    $prefill['installmentamount'][$index] = $sched->amount;
    $prefill['installmentduedate'][$index] = $sched->duedate;
}
$form->set_data($prefill);

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('installments');

echo html_writer::start_div('local-financedepartment-installments-form');

echo local_financedepartment_render_back_link($returnurl, get_string('backtoinstallmentplan', 'local_financedepartment'));

echo local_financedepartment_render_page_hero($title, get_string('rescheduleinstallmentplandesc', 'local_financedepartment'));

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
