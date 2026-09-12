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
 * Create a new installment plan for a fee record.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\installmentplan_manager;
use local_financedepartment\form\installmentplan_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$feerecordid = optional_param('feerecordid', 0, PARAM_INT);
$numinstallments = optional_param('numinstallments', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:manageinstallments');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/installments/create.php', [
    'feerecordid' => $feerecordid,
    'numinstallments' => $numinstallments,
]));
$PAGE->set_pagelayout('standard');
$title = get_string('createinstallmentplan', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$returnurl = new moodle_url('/local/financedepartment/pages/installments/index.php');

// 2026-09-08 (user request): "how many installments?" is now an explicit
// first step, chosen up front, instead of always starting the form at a
// fixed 3 rows and relying on repeatedly clicking "Add another
// installment". Once a valid count is picked (carried via the
// numinstallments URL param), the real form below renders exactly that
// many rows from the start - see installmentplan_form's docblock.
$haschosencount = $numinstallments >= installmentplan_form::MIN_REPEATS
    && $numinstallments <= installmentplan_form::MAX_REPEATS;

$form = null;
if ($haschosencount) {
    $form = new installmentplan_form($PAGE->url, [
        'planid' => 0,
        'presetfeerecordid' => $feerecordid,
        'repeatcount' => $numinstallments,
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

        $planid = installmentplan_manager::create((int) $data->feerecordid, $installments, $USER->id);

        redirect(
            new moodle_url('/local/financedepartment/pages/installments/view.php', ['id' => $planid]),
            get_string('installmentplancreated', 'local_financedepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('installments');

echo html_writer::start_div('local-financedepartment-installments-form');

echo local_financedepartment_render_back_link($returnurl, get_string('backtoinstallments', 'local_financedepartment'));

echo local_financedepartment_render_page_hero($title, get_string('createinstallmentplandesc', 'local_financedepartment'));

echo html_writer::start_div('findept-form-card');

if (!$haschosencount) {
    // Step 1: pick how many installments before the real form appears.
    echo html_writer::tag('p', get_string('numinstallmentsdesc', 'local_financedepartment'), ['class' => 'findept-form-intro']);

    echo html_writer::start_tag('form', [
        'method' => 'get',
        'action' => new moodle_url('/local/financedepartment/pages/installments/create.php'),
        'class' => 'findept-filter-bar',
    ]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'feerecordid', 'value' => $feerecordid]);

    $countoptions = [];
    for ($i = installmentplan_form::MIN_REPEATS; $i <= installmentplan_form::MAX_REPEATS; $i++) {
        $countoptions[$i] = $i;
    }
    echo html_writer::tag(
        'label',
        get_string('numinstallments', 'local_financedepartment'),
        ['for' => 'id_numinstallments']
    );
    echo html_writer::select(
        $countoptions,
        'numinstallments',
        installmentplan_form::DEFAULT_REPEATS,
        false,
        ['id' => 'id_numinstallments', 'class' => 'findept-filter-select']
    );
    echo html_writer::tag('button', get_string('continue'), [
        'type' => 'submit',
        'class' => 'btn btn-primary findept-filter-submit',
    ]);
    echo html_writer::end_tag('form');
} else {
    $form->display();
}

echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
