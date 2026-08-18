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
 * Self-service: the leave requests a teacher has personally been chosen
 * as the approving teacher for (see student_leave_manager::CAP_APPLYOWN,
 * leave/apply.php, and can_review_application()). This is the landing
 * page for a teacher who holds none of the HR capabilities
 * (CAP_MANAGE/CAP_VIEW/CAP_APPLYOWN) but has been picked by at least one
 * student as their approver - previously such a teacher had no way to
 * find their pending approvals in the UI at all, and had to be given a
 * direct leave/view.php?id=X link. See local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

if (!student_leave_manager::can_manage() && !student_leave_manager::can_view()
        && !student_leave_manager::is_approver((int) $USER->id)) {
    // Not HR/Admin/global-Approver and never chosen as an approving
    // teacher on any application: nothing here for this user.
    require_capability(student_leave_manager::CAP_VIEW, $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/myapprovals.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('myapprovals', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('myapprovals', 'local_hrdepartment'),
    get_string('myapprovalssubtitle', 'local_hrdepartment')
);

$rows = student_leave_manager::get_application_rows(['approverid' => (int) $USER->id]);

if (empty($rows)) {
    echo local_hrdepartment_render_empty_state(
        get_string('nomyapprovals', 'local_hrdepartment'),
        'fa-clipboard-check'
    );
} else {
    $table = new html_table();
    $table->head = [
        get_string('student', 'local_hrdepartment'),
        get_string('leavetype', 'local_hrdepartment'),
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
            html_writer::link($viewurl, $row->fullname),
            format_string($row->leavetypename),
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
