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
 * View one payment/refund's receipt-style detail and its history.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\audit_manager;
use local_financedepartment\constants;
use local_financedepartment\feepayment_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
if (!access_manager::can_manage('local/financedepartment:recordpayments')
        && !access_manager::can_manage('local/financedepartment:managerefunds')) {
    throw new required_capability_exception($context, 'local/financedepartment:recordpayments', 'nopermissions', '');
}

$payment = feepayment_manager::get($id);
if (!$payment) {
    throw new moodle_exception(
        'errorpaymentnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/payments/index.php')
    );
}

$heading = get_string('receiptnumber', 'local_financedepartment') . ': ' . s($payment->receiptnumber);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/payments/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($heading);
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('payments');

echo html_writer::start_div('local-financedepartment-payments-view');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $payment->feerecordid]),
    get_string('backtofeerecord', 'local_financedepartment')
);

$actions = [];
if ($payment->status === constants::PAYMENT_STATUS_ACTIVE && access_manager::can_manage('local/financedepartment:managerefunds')) {
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/payments/void.php', ['id' => $id]),
        'label' => get_string('voidpayment', 'local_financedepartment'),
        'icon' => 'fa-ban',
    ];
}

echo local_financedepartment_render_page_hero(
    $heading,
    format_string($payment->fullname) . ' - ' . format_string($payment->categoryname) . ' - ' . s($payment->academicyear),
    $actions
);

// Details card.
echo html_writer::start_div('findept-detail-card');

$rows = [
    [get_string('student', 'local_financedepartment'), format_string($payment->fullname) . ' (' . s($payment->email) . ')'],
    [get_string('category', 'local_financedepartment'), format_string($payment->categoryname) . ' - ' . s($payment->academicyear)],
    [get_string('paymenttype', 'local_financedepartment'), local_financedepartment_payment_type_badge($payment->paymenttype)],
    [get_string('amount', 'local_financedepartment'), local_financedepartment_format_money($payment->amount)],
    [get_string('paymentdate', 'local_financedepartment'), userdate($payment->paymentdate)],
    [get_string('paymentmethod', 'local_financedepartment'), $payment->paymentmethod
        ? get_string('paymentmethod_' . $payment->paymentmethod, 'local_financedepartment') : '-'],
    [get_string('linkedinstallment', 'local_financedepartment'), $payment->installmentnumber
        ? get_string('installmentnumbershort', 'local_financedepartment', $payment->installmentnumber)
        : get_string('paymentnotlinked', 'local_financedepartment')],
    [get_string('status', 'local_financedepartment'), local_financedepartment_payment_status_badge($payment->status)],
    [get_string('recordedby', 'local_financedepartment'), ($recordeduser = \core_user::get_user($payment->recordedby))
        ? fullname($recordeduser) : get_string('unknownuser', 'local_financedepartment')],
    [get_string('when', 'local_financedepartment'), userdate($payment->timecreated)],
];

if (!empty($payment->notes)) {
    $rows[] = [get_string('notes', 'local_financedepartment'), format_text($payment->notes, FORMAT_PLAIN)];
}

// Optional supporting-document attachment(s), 2026-09-10 - see lib.php's
// local_financedepartment_pluginfile() for how these are served, and
// pages/payments/create.php for where they're saved. This whole block
// runs after $OUTPUT->header() above, so calling lib.php helpers here
// would be safe too, but none are needed for this part.
$fs = get_file_storage();
$paymentattachments = $fs->get_area_files($context->id, 'local_financedepartment', 'payment', $id, 'filename', false);
if (!empty($paymentattachments)) {
    $attachmentlinks = [];
    foreach ($paymentattachments as $file) {
        $fileurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            'local_financedepartment',
            'payment',
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );
        $attachmentlinks[] = html_writer::link($fileurl, s($file->get_filename()));
    }
    $rows[] = [get_string('paymentattachment', 'local_financedepartment'), implode(html_writer::empty_tag('br'), $attachmentlinks)];
}

if ($payment->status === constants::PAYMENT_STATUS_VOID) {
    $rows[] = [get_string('voidedby', 'local_financedepartment'), $payment->voidedbyfirstname
        ? s($payment->voidedbyfirstname . ' ' . $payment->voidedbylastname) : get_string('unknownuser', 'local_financedepartment')];
    $rows[] = [get_string('voidedat', 'local_financedepartment'), $payment->voidedat ? userdate($payment->voidedat) : '-'];
    $rows[] = [get_string('voidreason', 'local_financedepartment'), format_text((string) $payment->voidreason, FORMAT_PLAIN)];
}

echo html_writer::start_tag('dl', ['class' => 'findept-detail-list']);
foreach ($rows as [$label, $value]) {
    echo html_writer::tag('dt', $label);
    echo html_writer::tag('dd', $value);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div();

// History.
echo html_writer::tag('h3', get_string('paymenthistory', 'local_financedepartment'), ['class' => 'findept-section-title']);

$history = audit_manager::get_history(constants::AUDIT_ENTITY_FEEPAYMENT, $id);

if (empty($history)) {
    echo local_financedepartment_render_empty_state(get_string('nohistoryyet', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('when', 'local_financedepartment'),
        get_string('who', 'local_financedepartment'),
        get_string('action', 'local_financedepartment'),
        get_string('change', 'local_financedepartment'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-history-table';

    foreach ($history as $entry) {
        $user = \core_user::get_user($entry->userid);
        $who = $user ? fullname($user) : get_string('unknownuser', 'local_financedepartment');

        $new = $entry->newdata !== null ? json_decode($entry->newdata, true) : [];

        $changelines = [];
        if ($entry->action === constants::AUDIT_ACTION_CREATE) {
            $changelines[] = get_string(
                'historypaymentrecorded',
                'local_financedepartment',
                local_financedepartment_format_money($new['amount'] ?? 0)
            );
        } else if ($entry->action === constants::AUDIT_ACTION_REFUND) {
            $changelines[] = get_string(
                'historyrefundrecorded',
                'local_financedepartment',
                local_financedepartment_format_money($new['amount'] ?? 0)
            );
        } else if ($entry->action === constants::AUDIT_ACTION_VOID) {
            $changelines[] = get_string('historypaymentvoided', 'local_financedepartment');
        }
        if (!empty($entry->reason)) {
            $changelines[] = html_writer::tag('em', get_string('reason', 'local_financedepartment') . ': ' . s($entry->reason));
        }

        $table->data[] = [
            userdate($entry->timecreated),
            $who,
            get_string('auditaction_' . $entry->action, 'local_financedepartment'),
            implode(html_writer::empty_tag('br'), $changelines),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
