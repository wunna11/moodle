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
 * Leave requests: search/filter every student leave application. See
 * local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;
use local_hrdepartment\table\student_leave_table;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$canmanage = student_leave_manager::can_manage();
if (!$canmanage) {
    require_capability(student_leave_manager::CAP_VIEW, $context);
}

$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$leavetypeid = optional_param('leavetypeid', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/lookup.php', [
    'search' => $search, 'status' => $status, 'leavetypeid' => $leavetypeid,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leaverequests', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('leaverequests', 'local_hrdepartment'),
    get_string('leaverequestssubtitle', 'local_hrdepartment'),
    $canmanage ? [[
        'url' => new moodle_url('/local/hrdepartment/leave/edit.php'),
        'label' => get_string('logleaverequest', 'local_hrdepartment'),
        'icon' => 'fa-calendar-plus',
    ]] : []
);

echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'hrdept-filter-bar']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudentplaceholder', 'local_hrdepartment'),
    'class' => 'form-control mr-2 mb-2',
]);

$statusoptions = [
    '' => get_string('allstatuses', 'local_hrdepartment'),
    'pending' => get_string('status_pending', 'local_hrdepartment'),
    'approved' => get_string('status_approved', 'local_hrdepartment'),
    'rejected' => get_string('status_rejected', 'local_hrdepartment'),
    'cancelled' => get_string('status_cancelled', 'local_hrdepartment'),
];
echo html_writer::select($statusoptions, 'status', $status, null, ['class' => 'form-control mr-2 mb-2']);

$leavetypeoptions = [0 => get_string('allleavetypes', 'local_hrdepartment')] + student_leave_manager::get_leave_type_options();
echo html_writer::select($leavetypeoptions, 'leavetypeid', $leavetypeid, null, ['class' => 'form-control mr-2 mb-2']);

echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('filter', 'local_hrdepartment'), 'class' => 'btn btn-secondary mb-2']);
echo html_writer::end_tag('form');

echo html_writer::start_div('hrdept-table-card');
$table = new student_leave_table('local-hrdepartment-student-leave', $search, $status, $leavetypeid, $canmanage);
$table->define_baseurl($PAGE->url);
$table->out(20, true);
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
