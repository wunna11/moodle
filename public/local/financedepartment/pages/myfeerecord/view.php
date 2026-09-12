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
 * View one of the logged-in student's own fee records, read-only:
 * balance breakdown, payment history, and installment schedule (if a
 * plan exists). No edit/cancel/record-payment/void actions anywhere on
 * this page - those stay exclusively on the finance-staff admin pages
 * (pages/feerecords/view.php, pages/payments/*.php,
 * pages/installments/*.php). Added 2026-09-10, see
 * pages/myfeerecord/index.php's docblock for the full context.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feepayment_manager;
use local_financedepartment\feerecord_manager;
use local_financedepartment\installmentplan_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
require_capability('local/financedepartment:viewownfeerecord', $context);

$feerecord = feerecord_manager::get($id);
if (!$feerecord) {
    throw new moodle_exception(
        'errorfeerecordnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/myfeerecord/index.php')
    );
}

// Ownership check - viewownfeerecord only ever means the viewer's OWN
// record. A finance-staff viewer with managefeerecords is additionally
// allowed through (harmless - they already see everything on the admin
// pages), but a plain student can never open another student's "my fee
// record" page this way.
if ((int) $feerecord->studentid !== (int) $USER->id
        && !access_manager::can_manage('local/financedepartment:managefeerecords')) {
    throw new required_capability_exception($context, 'local/financedepartment:viewownfeerecord', 'nopermissions', '');
}

$heading = format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/myfeerecord/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($heading);
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('myfeerecord');

echo html_writer::start_div('local-financedepartment-myfeerecord-view');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/myfeerecord/index.php'),
    get_string('backtomyfeerecords', 'local_financedepartment')
);

echo local_financedepartment_render_page_hero($heading, get_string('feerecorddetails', 'local_financedepartment'));

// Details card.
echo html_writer::start_div('findept-detail-card');

$rows = [
    [get_string('feestructure', 'local_financedepartment'), format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear)],
    [get_string('totalamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->totalamount)],
    [get_string('scholarshipamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->scholarshipamount)],
    [get_string('discountamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->discountamount)],
    [get_string('paidamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->paidamount)],
    [get_string('balance', 'local_financedepartment'), local_financedepartment_format_money($feerecord->balance)],
    [get_string('status', 'local_financedepartment'), local_financedepartment_feerecord_status_badge($feerecord)],
];

echo html_writer::start_tag('dl', ['class' => 'findept-detail-list']);
foreach ($rows as [$label, $value]) {
    echo html_writer::tag('dt', $label);
    echo html_writer::tag('dd', $value);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div();

// Payment history - read-only, no void/edit links (those are
// finance-staff-only, on pages/payments/*.php which this viewer cannot
// open anyway).
echo html_writer::tag('h3', get_string('payments', 'local_financedepartment'), ['class' => 'findept-section-title']);

$payments = feepayment_manager::get_for_feerecord($id);

if (empty($payments)) {
    echo local_financedepartment_render_empty_state(get_string('nopaymentsyet', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('receiptnumber', 'local_financedepartment'),
        get_string('paymenttype', 'local_financedepartment'),
        get_string('amount', 'local_financedepartment'),
        get_string('linkedinstallment', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
        get_string('when', 'local_financedepartment'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-payments-table';

    foreach ($payments as $payment) {
        $table->data[] = [
            s($payment->receiptnumber),
            local_financedepartment_payment_type_badge($payment->paymenttype),
            local_financedepartment_format_money($payment->amount),
            $payment->installmentnumber
                ? get_string('installmentnumbershort', 'local_financedepartment', $payment->installmentnumber)
                : get_string('paymentnotlinked', 'local_financedepartment'),
            local_financedepartment_payment_status_badge($payment->status),
            userdate($payment->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

// Installment schedule - read-only, no "Record payment" shortcut links
// (finance-staff-only). Shows the schedule for whatever plan exists
// regardless of its status (active or cancelled), same as
// pages/installments/view.php - schedule rows are kept for history, per
// installmentplan_manager::cancel()'s own docblock.
echo html_writer::tag('h3', get_string('installmentschedule', 'local_financedepartment'), ['class' => 'findept-section-title']);

$plan = installmentplan_manager::get_for_feerecord($id);
$schedule = $plan ? installmentplan_manager::get_schedule($plan->id) : [];

if (empty($schedule)) {
    echo local_financedepartment_render_empty_state(get_string('noinstallmentplansyet', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('installmentnumber', 'local_financedepartment'),
        get_string('amount', 'local_financedepartment'),
        get_string('duedate', 'local_financedepartment'),
        get_string('paidamount', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-installment-schedule-table';

    foreach ($schedule as $sched) {
        $table->data[] = [
            $sched->installmentnumber,
            local_financedepartment_format_money($sched->amount),
            userdate($sched->duedate, get_string('strftimedatetimeshort', 'core_langconfig')),
            local_financedepartment_format_money($sched->paidamount),
            local_financedepartment_installment_status_badge($sched),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
