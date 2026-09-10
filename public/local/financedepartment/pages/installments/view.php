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
 * View one installment plan's schedule and history.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\audit_manager;
use local_financedepartment\constants;
use local_financedepartment\installmentplan_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

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

$heading = format_string($plan->fullname) . ' - ' . format_string($plan->categoryname) . ' - ' . s($plan->academicyear);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/installments/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($heading);
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('installments');

echo html_writer::start_div('local-financedepartment-installments-view');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/installments/index.php', ['studentid' => $plan->studentid]),
    get_string('backtoinstallments', 'local_financedepartment')
);

$actions = [];
$isactive = $plan->status === constants::INSTALLMENTPLAN_STATUS_ACTIVE;
if ($isactive) {
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/installments/edit.php', ['id' => $id]),
        'label' => get_string('rescheduleinstallmentplan', 'local_financedepartment'),
        'icon' => 'fa-calendar',
    ];
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/installments/cancel.php', ['id' => $id]),
        'label' => get_string('cancel'),
        'icon' => 'fa-ban',
    ];
}

echo local_financedepartment_render_page_hero(
    $heading,
    get_string('installmentplan', 'local_financedepartment'),
    $actions
);

// Details card.
echo html_writer::start_div('findept-detail-card');

$rows = [
    [get_string('student', 'local_financedepartment'), format_string($plan->fullname) . ' (' . s($plan->email) . ')'],
    [get_string('category', 'local_financedepartment'), format_string($plan->categoryname) . ' - ' . s($plan->academicyear)],
    [get_string('totalamount', 'local_financedepartment'), local_financedepartment_format_money($plan->totalamount)],
    [get_string('balance', 'local_financedepartment'), local_financedepartment_format_money($plan->feerecordbalance)],
    [get_string('installmentnumber', 'local_financedepartment'), $plan->numberofinstallments],
    [get_string('status', 'local_financedepartment'), local_financedepartment_installmentplan_status_badge($plan->status)],
];

echo html_writer::start_tag('dl', ['class' => 'findept-detail-list']);
foreach ($rows as [$label, $value]) {
    echo html_writer::tag('dt', $label);
    echo html_writer::tag('dd', $value);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div();

// Schedule.
echo html_writer::tag('h3', get_string('installmentschedule', 'local_financedepartment'), ['class' => 'findept-section-title']);

$schedule = installmentplan_manager::get_schedule($id);

if (empty($schedule)) {
    echo local_financedepartment_render_empty_state(get_string('noinstallmentplansyet', 'local_financedepartment'));
} else {
    $canrecordpayments = access_manager::can_manage('local/financedepartment:recordpayments');

    $table = new html_table();
    $table->head = [
        get_string('installmentnumber', 'local_financedepartment'),
        get_string('amount', 'local_financedepartment'),
        get_string('duedate', 'local_financedepartment'),
        get_string('paidamount', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
        '',
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-installment-schedule-table';

    foreach ($schedule as $sched) {
        // "Record payment" shortcut per row - Step 7.7. Only offered
        // while the row still has something owing on it (a fully PAID
        // row has nothing left to record against), and only for an
        // ACTIVE plan (a cancelled plan's schedule is kept for history
        // only - see installmentplan_manager::cancel()'s docblock).
        $action = '';
        if ($canrecordpayments
                && $sched->status !== constants::INSTALLMENT_STATUS_PAID
                && $isactive) {
            $action = html_writer::link(
                new moodle_url('/local/financedepartment/pages/payments/create.php', [
                    'feerecordid' => $plan->feerecordid,
                    'installmentschedid' => $sched->id,
                ]),
                get_string('recordpayment', 'local_financedepartment')
            );
        }

        $table->data[] = [
            $sched->installmentnumber,
            local_financedepartment_format_money($sched->amount),
            userdate($sched->duedate, get_string('strftimedatetimeshort', 'core_langconfig')),
            local_financedepartment_format_money($sched->paidamount),
            local_financedepartment_installment_status_badge($sched),
            $action,
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

// History.
echo html_writer::tag('h3', get_string('installmentplanhistory', 'local_financedepartment'), ['class' => 'findept-section-title']);

$history = audit_manager::get_history(constants::AUDIT_ENTITY_INSTALLMENTPLAN, $id);

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
            $changelines[] = get_string('historyinstallmentplancreated', 'local_financedepartment', $new['numberofinstallments'] ?? 0);
        } else if ($entry->action === constants::AUDIT_ACTION_EDIT) {
            $changelines[] = get_string('historyinstallmentplanupdated', 'local_financedepartment', $new['numberofinstallments'] ?? 0);
        } else if ($entry->action === constants::AUDIT_ACTION_CANCEL) {
            $changelines[] = get_string('historyinstallmentplancancelled', 'local_financedepartment');
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
