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
 * Self-service: a student's own leave requests and their status - the
 * landing page a plain student (local/hrdepartment:applyownleave only,
 * no HR/Approver capability) is redirected to from leave/index.php. See
 * local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

require_capability(student_leave_manager::CAP_APPLYOWN, $context);

if (!student_leave_manager::is_student((int) $USER->id)) {
    throw new moodle_exception(
        'notastudentnoaccess',
        'local_hrdepartment',
        new moodle_url('/local/hrdepartment/leave/index.php')
    );
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/myrequests.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('myleaverequests', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('myleaverequests', 'local_hrdepartment'),
    get_string('myleaverequestssubtitle', 'local_hrdepartment'),
    [[
        'url' => new moodle_url('/local/hrdepartment/leave/apply.php'),
        'label' => get_string('applyforleave', 'local_hrdepartment'),
        'icon' => 'fa-calendar-plus',
    ]]
);

$rows = student_leave_manager::get_application_rows(['studentid' => (int) $USER->id]);

if (empty($rows)) {
    echo local_hrdepartment_render_empty_state(
        get_string('nomyleaverequests', 'local_hrdepartment'),
        'fa-calendar-alt'
    );
} else {
    $table = new html_table();
    $table->head = [
        get_string('leavetype', 'local_hrdepartment'),
        get_string('approver', 'local_hrdepartment'),
        get_string('startdate', 'local_hrdepartment'),
        get_string('enddate', 'local_hrdepartment'),
        get_string('totaldays', 'local_hrdepartment'),
        get_string('status', 'local_hrdepartment'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable mb-0';

    $dateformat = get_string('strftimedatefullshort', 'langconfig');

    foreach ($rows as $row) {
        $viewurl = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]);
        $table->data[] = [
            format_string($row->leavetypename),
            $row->approverfullname ?? get_string('noapproverassigned', 'local_hrdepartment'),
            userdate($row->startdate, $dateformat),
            userdate($row->enddate, $dateformat),
            $row->totaldays,
            local_hrdepartment_leave_status_badge($row->status),
            html_writer::link($viewurl, get_string('view')),
        ];
    }

    echo local_hrdepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
