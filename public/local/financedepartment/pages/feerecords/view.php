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
 * View one fee record's details and its assignment/edit/cancel history -
 * this already IS the itemised statement Step 7.9 asks for (fee
 * structure + scholarship + discount + payments + balance, all in one
 * view), just under its original Step 7.3 page rather than a new one.
 *
 * 2026-09-10 (Step 7.9): gate widened from managefeerecords-only to
 * ALSO accept local/financedepartment:viewallrecords, via the new
 * access_manager::require_manage_any() - matches viewallrecords' own
 * db/access.php comment ("view any student's fee statement, regardless
 * of who assigned or manages it"), previously defined since Step 7.1
 * but never actually wired to any page. A viewallrecords-only viewer
 * (no managefeerecords) can now open this page from the new
 * pages/feerecords/all.php list, but sees NO mutating action (no Edit/
 * Cancel button, and the back link routes to all.php instead of the
 * managefeerecords-gated index.php) - see the $actions/back-link
 * construction below.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\audit_manager;
use local_financedepartment\constants;
use local_financedepartment\feepayment_manager;
use local_financedepartment\feerecord_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
access_manager::require_manage_any([
    'local/financedepartment:managefeerecords',
    'local/financedepartment:viewallrecords',
]);
$canmanagefeerecords = access_manager::can_manage('local/financedepartment:managefeerecords');

$feerecord = feerecord_manager::get($id);
if (!$feerecord) {
    throw new moodle_exception(
        'errorfeerecordnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/feerecords/index.php')
    );
}

$heading = format_string($feerecord->fullname) . ' - ' . format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($heading);
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar($canmanagefeerecords ? 'feerecords' : 'allfeerecords');

echo html_writer::start_div('local-financedepartment-feerecords-view');

if ($canmanagefeerecords) {
    echo local_financedepartment_render_back_link(
        new moodle_url('/local/financedepartment/pages/feerecords/index.php', ['studentid' => $feerecord->studentid]),
        get_string('backtofeerecords', 'local_financedepartment')
    );
} else {
    echo local_financedepartment_render_back_link(
        new moodle_url('/local/financedepartment/pages/feerecords/all.php'),
        get_string('backtoallfeerecords', 'local_financedepartment')
    );
}

$actions = [];
if ($canmanagefeerecords) {
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/feerecords/edit.php', ['id' => $id]),
        'label' => get_string('edit'),
        'icon' => 'fa-pencil-alt',
    ];
}
if ($feerecord->status !== constants::FEE_STATUS_CANCELLED) {
    // Record payment/refund - Step 7.7. Refund is only offered once
    // something has actually been paid (feerecord_manager::add_payment_amount()
    // clamps paidamount at 0 anyway, but there is no point offering the
    // action at all when there is nothing to refund).
    if (access_manager::can_manage('local/financedepartment:recordpayments')) {
        $actions[] = [
            'url' => new moodle_url('/local/financedepartment/pages/payments/create.php', ['feerecordid' => $id]),
            'label' => get_string('recordpayment', 'local_financedepartment'),
            'icon' => 'fa-money',
        ];
    }
    if (access_manager::can_manage('local/financedepartment:managerefunds') && (float) $feerecord->paidamount > 0) {
        $actions[] = [
            'url' => new moodle_url('/local/financedepartment/pages/payments/create.php', ['feerecordid' => $id, 'mode' => 'refund']),
            'label' => get_string('recordrefund', 'local_financedepartment'),
            'icon' => 'fa-undo',
        ];
    }

    if ($canmanagefeerecords) {
        $actions[] = [
            'url' => new moodle_url('/local/financedepartment/pages/feerecords/cancel.php', ['id' => $id]),
            'label' => get_string('cancel'),
            'icon' => 'fa-ban',
        ];
    }
}

echo local_financedepartment_render_page_hero(
    $heading,
    get_string('feerecorddetails', 'local_financedepartment'),
    $actions
);

// Details card.
echo html_writer::start_div('findept-detail-card');

$rows = [
    [get_string('student', 'local_financedepartment'), format_string($feerecord->fullname) . ' (' . s($feerecord->email) . ')'],
    [get_string('feestructure', 'local_financedepartment'), format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear)],
    [get_string('totalamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->totalamount)],
    [get_string('scholarshipamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->scholarshipamount)],
    [get_string('discountamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->discountamount)],
    [get_string('paidamount', 'local_financedepartment'), local_financedepartment_format_money($feerecord->paidamount)],
    [get_string('balance', 'local_financedepartment'), local_financedepartment_format_money($feerecord->balance)],
    [get_string('status', 'local_financedepartment'), local_financedepartment_feerecord_status_badge($feerecord)],
    [get_string('assignedby', 'local_financedepartment'), ($assignedbyuser = \core_user::get_user($feerecord->assignedby))
        ? fullname($assignedbyuser) : get_string('unknownuser', 'local_financedepartment')],
    [get_string('assigneddate', 'local_financedepartment'), userdate($feerecord->timecreated)],
    [get_string('lastupdated', 'local_financedepartment'), userdate($feerecord->timemodified)],
];

echo html_writer::start_tag('dl', ['class' => 'findept-detail-list']);
foreach ($rows as [$label, $value]) {
    echo html_writer::tag('dt', $label);
    echo html_writer::tag('dd', $value);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div();

// Payments mini-table - Step 7.7. Full receipt detail (including void/
// history) lives on pages/payments/view.php; this is just a compact
// list, same "summary here, detail elsewhere" role installments/view.php's
// own schedule table plays relative to a fee record.
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
        '',
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-payments-table';

    foreach ($payments as $payment) {
        $viewurl = new moodle_url('/local/financedepartment/pages/payments/view.php', ['id' => $payment->id]);

        $table->data[] = [
            s($payment->receiptnumber),
            local_financedepartment_payment_type_badge($payment->paymenttype),
            local_financedepartment_format_money($payment->amount),
            $payment->installmentnumber
                ? get_string('installmentnumbershort', 'local_financedepartment', $payment->installmentnumber)
                : get_string('paymentnotlinked', 'local_financedepartment'),
            local_financedepartment_payment_status_badge($payment->status),
            userdate($payment->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
            html_writer::link($viewurl, get_string('view')),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

// History.
echo html_writer::tag('h3', get_string('feerecordhistory', 'local_financedepartment'), ['class' => 'findept-section-title']);

$history = audit_manager::get_history(constants::AUDIT_ENTITY_FEERECORD, $id);

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

    // Audited field key -> display label. Matches
    // feerecord_manager::AUDITED_FIELDS.
    $fieldlabels = [
        'studentid' => get_string('student', 'local_financedepartment'),
        'feestructureid' => get_string('feestructure', 'local_financedepartment'),
        'totalamount' => get_string('totalamount', 'local_financedepartment'),
        'status' => get_string('status', 'local_financedepartment'),
    ];

    // Resolves a studentid value from an audit log snapshot to a
    // human-readable name, falling back to the raw id if the user
    // record is gone.
    $resolvestudentname = function ($value) {
        if (!is_numeric($value)) {
            return $value;
        }
        $user = \core_user::get_user((int) $value);
        return $user ? fullname($user) : (string) $value;
    };

    // Resolves a feestructureid value to its "category - year" label,
    // falling back to the raw id if the fee structure no longer exists.
    $resolvefeestructurename = function ($value) {
        if (!is_numeric($value)) {
            return $value;
        }
        global $DB;
        $sql = "SELECT f.academicyear, cc.name AS categoryname
                  FROM {financedep_feestructure} f
                  JOIN {course_categories} cc ON cc.id = f.categoryid
                 WHERE f.id = :id";
        $record = $DB->get_record_sql($sql, ['id' => (int) $value]);
        return $record ? (format_string($record->categoryname) . ' - ' . $record->academicyear) : (string) $value;
    };

    foreach ($history as $entry) {
        $user = \core_user::get_user($entry->userid);
        $who = $user ? fullname($user) : get_string('unknownuser', 'local_financedepartment');

        $old = $entry->olddata !== null ? json_decode($entry->olddata, true) : [];
        $new = $entry->newdata !== null ? json_decode($entry->newdata, true) : [];

        $changelines = [];
        if ($entry->action === constants::AUDIT_ACTION_CREATE) {
            $changelines[] = get_string('historyassigned', 'local_financedepartment');
        } else {
            $fields = array_unique(array_merge(array_keys((array) $old), array_keys((array) $new)));
            foreach ($fields as $field) {
                $oldval = $old[$field] ?? '-';
                $newval = $new[$field] ?? '-';

                if ($field === 'totalamount') {
                    $oldval = is_numeric($oldval) ? local_financedepartment_format_money($oldval) : $oldval;
                    $newval = is_numeric($newval) ? local_financedepartment_format_money($newval) : $newval;
                } else if ($field === 'studentid') {
                    $oldval = $resolvestudentname($oldval);
                    $newval = $resolvestudentname($newval);
                } else if ($field === 'feestructureid') {
                    $oldval = $resolvefeestructurename($oldval);
                    $newval = $resolvefeestructurename($newval);
                } else if ($field === 'status') {
                    $oldval = is_string($oldval) && $oldval !== '-' ? get_string('feestatus_' . $oldval, 'local_financedepartment') : $oldval;
                    $newval = is_string($newval) && $newval !== '-' ? get_string('feestatus_' . $newval, 'local_financedepartment') : $newval;
                }

                $label = $fieldlabels[$field] ?? $field;
                $changelines[] = html_writer::tag('strong', $label) . ': ' . s((string) $oldval) . ' &rarr; ' . s((string) $newval);
            }
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
