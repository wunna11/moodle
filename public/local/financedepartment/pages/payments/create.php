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
 * Record a payment (or, with the higher-trust `managerefunds`
 * capability, a refund) against a fee record. Always reached from a
 * specific fee record's own context - pages/feerecords/view.php's
 * "Record payment" action, or pages/installments/view.php's per-row
 * "Record payment" link (which additionally presets the installment
 * link) - never a free-standing form with its own fee-record picker, see
 * classes/form/feepayment_form.php's docblock.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\feepayment_manager;
use local_financedepartment\feerecord_manager;
use local_financedepartment\form\feepayment_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$feerecordid = required_param('feerecordid', PARAM_INT);
$installmentschedid = optional_param('installmentschedid', 0, PARAM_INT);
$mode = optional_param('mode', constants::PAYMENT_TYPE_PAYMENT, PARAM_ALPHA);
if (!in_array($mode, [constants::PAYMENT_TYPE_PAYMENT, constants::PAYMENT_TYPE_REFUND], true)) {
    $mode = constants::PAYMENT_TYPE_PAYMENT;
}

$context = context_system::instance();
$requiredcapability = $mode === constants::PAYMENT_TYPE_REFUND
    ? 'local/financedepartment:managerefunds'
    : 'local/financedepartment:recordpayments';
access_manager::require_manage($requiredcapability);

$feerecord = feerecord_manager::get($feerecordid);
if (!$feerecord) {
    throw new moodle_exception(
        'errorfeerecordnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/payments/index.php')
    );
}
if ($feerecord->status === constants::FEE_STATUS_CANCELLED) {
    throw new moodle_exception(
        'errorfeerecordcancelled',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $feerecordid])
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $feerecordid]);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/payments/create.php', [
    'feerecordid' => $feerecordid,
    'installmentschedid' => $installmentschedid,
    'mode' => $mode,
]));
$PAGE->set_pagelayout('standard');
$title = $mode === constants::PAYMENT_TYPE_REFUND
    ? get_string('recordrefund', 'local_financedepartment')
    : get_string('recordpayment', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$form = new feepayment_form($PAGE->url, [
    'feerecordid' => $feerecordid,
    'mode' => $mode,
    'presetinstallmentschedid' => $installmentschedid,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    if ($mode === constants::PAYMENT_TYPE_REFUND) {
        $newid = feepayment_manager::record_refund($data, $USER->id);
        $notice = get_string('refundrecorded', 'local_financedepartment');
    } else {
        $newid = feepayment_manager::record_payment($data, $USER->id);
        $notice = get_string('paymentrecorded', 'local_financedepartment');
    }

    // Optional supporting-document attachment(s), 2026-09-10 - saved into
    // this plugin's own 'payment' filearea (shared by both payment and
    // refund rows, distinguished by the row's own paymenttype - see
    // feepayment_form's docblock and local_financedepartment_pluginfile()
    // in lib.php). Must match feepayment_form::ATTACHMENT_MAXFILES exactly,
    // same pattern as pages/scholarshiprequests/submit.php.
    if (!empty($data->attachment)) {
        file_save_draft_area_files(
            $data->attachment,
            $context->id,
            'local_financedepartment',
            'payment',
            $newid,
            ['subdirs' => 0, 'maxfiles' => feepayment_form::ATTACHMENT_MAXFILES]
        );
    }

    redirect($returnurl, $notice, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('payments');

echo html_writer::start_div('local-financedepartment-payments-form');

echo local_financedepartment_render_back_link($returnurl, get_string('backtofeerecord', 'local_financedepartment'));

$subtitle = format_string($feerecord->fullname) . ' - ' . format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear);
echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
