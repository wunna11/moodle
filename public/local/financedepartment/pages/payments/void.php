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
 * Void an existing payment or refund. Requires the higher-trust
 * `managerefunds` capability - see feepayment_manager::void_payment()'s
 * docblock and db/access.php.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\feepayment_manager;
use local_financedepartment\form\feepaymentvoid_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managerefunds');

$payment = feepayment_manager::get($id);
if (!$payment) {
    throw new moodle_exception(
        'errorpaymentnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/payments/index.php')
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/payments/view.php', ['id' => $id]);

if ($payment->status !== constants::PAYMENT_STATUS_ACTIVE) {
    redirect($returnurl, get_string('errorpaymentnotactive', 'local_financedepartment'), null, \core\output\notification::NOTIFY_WARNING);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/payments/void.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = get_string('voidpayment', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

// Built before $OUTPUT->header() below (the form must be constructed
// before header() so is_cancelled()/get_data() can redirect cleanly
// with no output sent yet), so this cannot call
// local_financedepartment_payment_type_badge() or
// local_financedepartment_format_money() directly - lib.php is not
// guaranteed loaded yet at this point, the same CRITICAL RULE that
// applies to moodleform classes (see feepayment_form::format_mmk()'s
// docblock; this bug on 2026-09-10 was the page-script equivalent of
// that same mistake). Reimplemented inline instead, using only core
// Moodle functions (html_writer, get_string) and the autoloaded
// constants class, both safe before header()/before lib.php is
// included.
$paymenttypevariant = ($payment->paymenttype === constants::PAYMENT_TYPE_REFUND) ? 'warning' : 'info';
$paymenttypebadge = html_writer::span(
    get_string('paymenttype_' . $payment->paymenttype, 'local_financedepartment'),
    'badge badge-' . $paymenttypevariant
);
$paymentamountdecimals = (abs($payment->amount - round($payment->amount)) > 0.001) ? 2 : 0;
$paymentamountformatted = number_format($payment->amount, $paymentamountdecimals) . ' MMK';

$summary = format_string($payment->fullname) . ' - '
    . $paymenttypebadge . ' '
    . $paymentamountformatted
    . ' (' . s($payment->receiptnumber) . ')';

$form = new feepaymentvoid_form($PAGE->url, [
    'paymentid' => $id,
    'paymentsummary' => $summary,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    feepayment_manager::void_payment($id, $data->voidreason, $USER->id);

    redirect($returnurl, get_string('paymentvoided', 'local_financedepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('payments');

echo html_writer::start_div('local-financedepartment-payments-form');

echo local_financedepartment_render_back_link($returnurl, get_string('backtopayment', 'local_financedepartment'));

echo local_financedepartment_render_page_hero($title, get_string('voidpaymentdesc', 'local_financedepartment'));

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
