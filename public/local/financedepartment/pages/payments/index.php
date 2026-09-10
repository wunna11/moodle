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
 * Payments: search for a student to see their fee records and jump
 * straight to recording a payment against one, or view the most
 * recently recorded payments across every student. Same search-then-view
 * shape as pages/installments/index.php - see that page's docblock.
 *
 * Full per-fee-record payment history lives on pages/feerecords/view.php
 * (its "Payments" mini-table, wired in for Step 7.7) rather than being
 * duplicated here - this page is only the entry point for finding a fee
 * record and a "recent activity" feed, same role installments/index.php
 * plays relative to installments/view.php.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feepayment_manager;
use local_financedepartment\feerecord_manager;
use local_hrdepartment\student_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
if (!access_manager::can_manage('local/financedepartment:recordpayments')
        && !access_manager::can_manage('local/financedepartment:managerefunds')) {
    throw new required_capability_exception($context, 'local/financedepartment:recordpayments', 'nopermissions', '');
}

$studentid = optional_param('studentid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/payments/index.php', [
    'studentid' => $studentid, 'search' => $search,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('payments', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('payments');

echo html_writer::start_div('local-financedepartment-payments');

echo local_financedepartment_render_page_hero(
    get_string('payments', 'local_financedepartment'),
    get_string('paymentsdesc', 'local_financedepartment')
);

// Student search bar.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $PAGE->url->out_omit_querystring(),
    'class' => 'findept-filter-bar',
]);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudent', 'local_financedepartment'),
    'class' => 'findept-filter-text',
]);
echo html_writer::tag('button', get_string('filter', 'local_financedepartment'), [
    'type' => 'submit',
    'class' => 'btn btn-primary findept-filter-submit',
]);
echo html_writer::link(
    new moodle_url('/local/financedepartment/pages/payments/index.php'),
    get_string('reset', 'local_financedepartment'),
    ['class' => 'findept-filter-reset']
);
echo html_writer::end_tag('form');

if ($studentid) {
    // A student is selected - show their fee records, each with a
    // "Record payment" shortcut and a link to its full detail/history.
    $student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0]);
    if (!$student) {
        throw new moodle_exception('invaliduser', 'error', new moodle_url('/local/financedepartment/pages/payments/index.php'));
    }

    echo html_writer::tag(
        'h3',
        get_string('viewfeerecordsfor', 'local_financedepartment', fullname($student) . ' (' . $student->email . ')'),
        ['class' => 'findept-section-title']
    );

    $records = feerecord_manager::get_for_student($studentid);

    if (empty($records)) {
        echo local_financedepartment_render_empty_state(get_string('nofeerecords', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('feestructure', 'local_financedepartment'),
            get_string('balance', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('actions'),
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-payments-table';

        foreach ($records as $record) {
            $actions = [];
            if (access_manager::can_manage('local/financedepartment:recordpayments')) {
                $actions[] = html_writer::link(
                    new moodle_url('/local/financedepartment/pages/payments/create.php', ['feerecordid' => $record->id]),
                    get_string('recordpayment', 'local_financedepartment')
                );
            }
            $actions[] = html_writer::link(
                new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $record->id]),
                get_string('viewfeerecord', 'local_financedepartment')
            );

            $table->data[] = [
                format_string($record->categoryname) . ' - ' . s($record->academicyear),
                local_financedepartment_format_money($record->balance),
                local_financedepartment_feerecord_status_badge($record),
                implode(' | ', $actions),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else if ($search !== '') {
    // Search results - pick a student.
    $matches = student_manager::get_students($search, 0, '', 0, 20);

    if (empty($matches)) {
        echo local_financedepartment_render_empty_state(get_string('nostudentsfound', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [get_string('fullname'), get_string('email'), ''];
        $table->attributes['class'] = 'generaltable local-financedepartment-payments-table';

        foreach ($matches as $match) {
            $selecturl = new moodle_url('/local/financedepartment/pages/payments/index.php', ['studentid' => $match->id]);
            $table->data[] = [
                format_string($match->fullname),
                s($match->email),
                html_writer::link($selecturl, get_string('viewfeerecords', 'local_financedepartment')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else {
    // Default view: most recently recorded payments/refunds across all students.
    echo html_writer::tag('h3', get_string('recentpayments', 'local_financedepartment'), ['class' => 'findept-section-title']);

    $recent = feepayment_manager::get_recent(10);

    if (empty($recent)) {
        echo local_financedepartment_render_empty_state(get_string('nopaymentsyet', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('student', 'local_financedepartment'),
            get_string('category', 'local_financedepartment'),
            get_string('paymenttype', 'local_financedepartment'),
            get_string('amount', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('when', 'local_financedepartment'),
            '',
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-payments-table';

        foreach ($recent as $payment) {
            $studenturl = new moodle_url('/local/financedepartment/pages/payments/index.php', ['studentid' => $payment->studentid]);
            $viewurl = new moodle_url('/local/financedepartment/pages/payments/view.php', ['id' => $payment->id]);

            $table->data[] = [
                html_writer::link($studenturl, format_string($payment->fullname)),
                format_string($payment->categoryname) . ' - ' . s($payment->academicyear),
                local_financedepartment_payment_type_badge($payment->paymenttype),
                local_financedepartment_format_money($payment->amount),
                local_financedepartment_payment_status_badge($payment->status),
                userdate($payment->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
                html_writer::link($viewurl, get_string('view')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
}

echo html_writer::end_div();

echo $OUTPUT->footer();
