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
 * Ends a lecturer's course assignment: marks the hrdep_courseassign
 * record as ended and suspends the Moodle enrolment/role (history is
 * preserved, only access is revoked).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\course_assignment_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
access_manager::require_manage('local/hrdepartment:managelecturers');

$assignment = $DB->get_record('hrdep_courseassign', ['id' => $id]);
if (!$assignment) {
    throw new moodle_exception('errorassignmentnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/lecturer/index.php'));
}

$returnurl = new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $assignment->employeeid]);
$actionurl = new moodle_url('/local/hrdepartment/lecturer/courseunassign.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('endassignment', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

if ($confirm && confirm_sesskey()) {
    [$enrolsynced, $warning] = course_assignment_manager::end_assignment($id, $USER->id);

    if ($enrolsynced) {
        redirect($returnurl, get_string('assignmentended', 'local_hrdepartment'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect(
            $returnurl,
            get_string('assignmentendedwithwarning', 'local_hrdepartment', $warning),
            null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('lecturers');

echo $OUTPUT->confirm(
    get_string('confirmendassignment', 'local_hrdepartment'),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
