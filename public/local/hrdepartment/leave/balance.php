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
 * Leave Balance Management: search for a student, pick an academic year,
 * and view/adjust their allocated leave days per leave type.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$studentid = optional_param('studentid', 0, PARAM_INT);

$canmanage = student_leave_manager::can_manage($studentid);
if (!$canmanage && !student_leave_manager::can_view($studentid)) {
    throw new required_capability_exception($context, student_leave_manager::CAP_VIEW, 'nopermissions', '');
}

$academicyear = optional_param('academicyear', student_leave_manager::current_academic_year(), PARAM_ALPHANUMEXT);
$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/balance.php', [
    'studentid' => $studentid, 'academicyear' => $academicyear, 'search' => $search,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leavebalances', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('leavebalances', 'local_hrdepartment'),
    get_string('leavebalancessubtitle', 'local_hrdepartment')
);

echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'hrdept-filter-bar']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudentplaceholder', 'local_hrdepartment'),
    'class' => 'form-control mr-2 mb-2',
]);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('search'), 'class' => 'btn btn-secondary mb-2']);
echo html_writer::end_tag('form');

if ($search !== '' && !$studentid) {
    $matches = student_leave_manager::search_students($search, 20);
    if (empty($matches)) {
        echo local_hrdepartment_render_empty_state(
            get_string('nostudentsfound', 'local_hrdepartment'),
            'fa-user-slash'
        );
    } else {
        $table = new html_table();
        $table->head = [get_string('student', 'local_hrdepartment'), ''];
        $table->attributes['class'] = 'generaltable';
        foreach ($matches as $match) {
            $selecturl = new moodle_url('/local/hrdepartment/leave/balance.php', [
                'studentid' => $match->id, 'academicyear' => $academicyear,
            ]);
            $table->data[] = [
                fullname($match) . ' (' . $match->email . ')',
                html_writer::link($selecturl, get_string('view')),
            ];
        }
        echo local_hrdepartment_render_table_card(html_writer::table($table));
    }
}

if ($studentid) {
    // Re-check now that we have a concrete student, so a delegated
    // per-student Approver (who couldn't be checked before we knew which
    // student they were searching for) is correctly granted access here.
    $canmanage = student_leave_manager::can_manage($studentid);
    if (!$canmanage && !student_leave_manager::can_view($studentid)) {
        throw new required_capability_exception($context, student_leave_manager::CAP_VIEW, 'nopermissions', '');
    }

    $student = core_user::get_user($studentid);
    if (!$student || $student->deleted) {
        throw new moodle_exception('invaliduser');
    }

    echo html_writer::start_div('hrdept-subheader');
    echo html_writer::tag('h2', s(fullname($student)) . ' (' . s($student->email) . ')', ['class' => 'hrdept-subheader-title']);
    echo html_writer::end_div();

    echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'hrdept-filter-bar']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'studentid', 'value' => $studentid]);
    echo html_writer::tag('label', get_string('academicyear', 'local_hrdepartment'), ['class' => 'mr-2']);
    echo html_writer::select(
        student_leave_manager::get_academic_year_options(),
        'academicyear',
        $academicyear,
        null,
        ['class' => 'form-control mr-2']
    );
    echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('filter', 'local_hrdepartment'), 'class' => 'btn btn-secondary']);
    echo html_writer::end_tag('form');

    $balances = student_leave_manager::get_balances_for_student($studentid, $academicyear);

    $table = new html_table();
    $table->head = [
        get_string('leavetype', 'local_hrdepartment'),
        get_string('allocated', 'local_hrdepartment'),
        get_string('used', 'local_hrdepartment'),
        get_string('remaining', 'local_hrdepartment'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($balances as $balance) {
        $actions = '-';
        if ($canmanage) {
            $editurl = new moodle_url('/local/hrdepartment/leave/balance_edit.php', [
                'studentid' => $studentid, 'leavetypeid' => $balance->leavetypeid, 'academicyear' => $academicyear,
            ]);
            $actions = html_writer::link($editurl, get_string('setallocation', 'local_hrdepartment'));
        }

        $table->data[] = [
            format_string($balance->leavetypename),
            $balance->allocated,
            $balance->used,
            $balance->remaining,
            $actions,
        ];
    }

    if (empty($balances)) {
        echo local_hrdepartment_render_empty_state(
            get_string('nobalancesforstudent', 'local_hrdepartment'),
            'fa-balance-scale'
        );
    } else {
        echo local_hrdepartment_render_table_card(html_writer::table($table));
    }
} else if ($search === '') {
    echo local_hrdepartment_render_empty_state(
        get_string('pickastudent', 'local_hrdepartment'),
        'fa-user-graduate'
    );
}

echo html_writer::end_div();

echo $OUTPUT->footer();
