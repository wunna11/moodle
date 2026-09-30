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
 * Landing page for the Student 360 Profile plugin - a staff-facing
 * search box (view-any), or a straight redirect to their own profile
 * for a self-service-only viewer (nothing for them to search for).
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_student360\access_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_student360');

$context = context_system::instance();

$canviewany = access_manager::can_view_any();
$canviewown = access_manager::can_view_own();

if (!$canviewany && !$canviewown) {
    throw new required_capability_exception($context, 'local/student360:view', 'nopermissions', '');
}

if (!$canviewany) {
    redirect(new moodle_url('/local/student360/pages/view.php', ['studentid' => $USER->id]));
}

$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/student360/pages/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_student360'));
$PAGE->set_heading(get_string('pluginname', 'local_student360'));

echo $OUTPUT->header();
echo html_writer::start_div('local-student360-index');

echo html_writer::start_div('student360-searchhero');
echo html_writer::tag('h2', get_string('pluginname', 'local_student360'), ['class' => 'student360-searchhero-title']);
echo html_writer::tag('p', get_string('searchsubtitle', 'local_student360'), ['class' => 'student360-searchhero-subtitle']);

echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => (new moodle_url('/local/student360/pages/index.php'))->out(false),
    'class' => 'student360-searchform',
]);
echo html_writer::tag('i', '', ['class' => 'fa fa-search student360-searchform-icon', 'aria-hidden' => 'true']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchplaceholder', 'local_student360'),
    'class' => 'student360-searchform-input',
]);
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('search'),
    'class' => 'student360-searchform-submit',
]);
echo html_writer::end_tag('form');
echo html_writer::end_div();

if ($search !== '') {
    $students = \local_hrdepartment\student_manager::get_students($search, 0, '', 0, 50);

    if (empty($students)) {
        echo local_student360_render_empty_state(get_string('nostudentsfound', 'local_student360'));
    } else {
        $table = new html_table();
        $table->head = [get_string('fullname'), get_string('email'), get_string('courses', 'local_student360'), ''];
        $table->data = [];
        foreach ($students as $student) {
            $table->data[] = [
                format_string($student->fullname),
                s($student->email),
                $student->coursecount,
                html_writer::link(
                    new moodle_url('/local/student360/pages/view.php', ['studentid' => $student->id]),
                    get_string('viewprofile', 'local_student360'),
                    ['class' => 'btn btn-sm btn-outline-primary']
                ),
            ];
        }
        echo local_student360_render_table_card(html_writer::table($table));
    }
}

echo html_writer::end_div();
echo $OUTPUT->footer();
