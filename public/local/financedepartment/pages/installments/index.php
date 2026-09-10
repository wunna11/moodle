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
 * Installments: search for a student to see their fee records and each
 * one's installment plan status (none/active/cancelled), or jump
 * straight to creating a new plan. Same search-then-view shape as
 * pages/feerecords/index.php (Step 7.3) - see that page's docblock for
 * why this isn't a full searchable all-students table (that's Step
 * 7.9's job).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\feerecord_manager;
use local_financedepartment\installmentplan_manager;
use local_hrdepartment\student_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:manageinstallments');

$studentid = optional_param('studentid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/installments/index.php', [
    'studentid' => $studentid, 'search' => $search,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('installmentplans', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('installments');

echo html_writer::start_div('local-financedepartment-installments');

echo local_financedepartment_render_page_hero(
    get_string('installmentplans', 'local_financedepartment'),
    get_string('installmentplansdesc', 'local_financedepartment'),
    [[
        'url' => new moodle_url('/local/financedepartment/pages/installments/create.php'),
        'label' => get_string('createinstallmentplan', 'local_financedepartment'),
        'icon' => 'fa-plus',
    ]]
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
    new moodle_url('/local/financedepartment/pages/installments/index.php'),
    get_string('reset', 'local_financedepartment'),
    ['class' => 'findept-filter-reset']
);
echo html_writer::end_tag('form');

if ($studentid) {
    // A student is selected - show their fee records and each one's plan status.
    $student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0]);
    if (!$student) {
        throw new moodle_exception('invaliduser', 'error', new moodle_url('/local/financedepartment/pages/installments/index.php'));
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
            get_string('installmentplan', 'local_financedepartment'),
            get_string('actions'),
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-installment-table';

        foreach ($records as $record) {
            $plan = installmentplan_manager::get_for_feerecord($record->id);

            if (!$plan) {
                $planstatus = get_string('noinstallmentplan', 'local_financedepartment');
                $action = html_writer::link(
                    new moodle_url('/local/financedepartment/pages/installments/create.php', ['feerecordid' => $record->id]),
                    get_string('createplan', 'local_financedepartment')
                );
            } else {
                $planstatus = local_financedepartment_installmentplan_status_badge($plan->status);
                $action = html_writer::link(
                    new moodle_url('/local/financedepartment/pages/installments/view.php', ['id' => $plan->id]),
                    get_string('viewinstallmentplan', 'local_financedepartment')
                );
            }

            $table->data[] = [
                format_string($record->categoryname) . ' - ' . s($record->academicyear),
                local_financedepartment_format_money($record->balance),
                $planstatus,
                $action,
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
        $table->attributes['class'] = 'generaltable local-financedepartment-installment-table';

        foreach ($matches as $match) {
            $selecturl = new moodle_url('/local/financedepartment/pages/installments/index.php', ['studentid' => $match->id]);
            $table->data[] = [
                format_string($match->fullname),
                s($match->email),
                html_writer::link($selecturl, get_string('viewinstallmentplans', 'local_financedepartment')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else {
    // Default view: most recently created installment plans across all students.
    echo html_writer::tag('h3', get_string('recentinstallmentplans', 'local_financedepartment'), ['class' => 'findept-section-title']);

    $recent = installmentplan_manager::get_recent(10);

    if (empty($recent)) {
        echo local_financedepartment_render_empty_state(get_string('noinstallmentplansyet', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('student', 'local_financedepartment'),
            get_string('category', 'local_financedepartment'),
            get_string('installmentnumber', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('when', 'local_financedepartment'),
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-installment-table';

        foreach ($recent as $plan) {
            $studenturl = new moodle_url('/local/financedepartment/pages/installments/index.php', ['studentid' => $plan->studentid]);
            $viewurl = new moodle_url('/local/financedepartment/pages/installments/view.php', ['id' => $plan->id]);

            $table->data[] = [
                html_writer::link($studenturl, format_string($plan->fullname)),
                html_writer::link($viewurl, format_string($plan->categoryname) . ' - ' . s($plan->academicyear)),
                $plan->numberofinstallments,
                local_financedepartment_installmentplan_status_badge($plan->status),
                userdate($plan->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
}

echo html_writer::end_div();

echo $OUTPUT->footer();
