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
 * Reactivates a previously ended lecturer course assignment: restores
 * the hrdep_courseassign status to active and re-syncs the Moodle
 * enrolment/role, provided the assignment is still valid (course still
 * exists, end date hasn't genuinely elapsed) and the owning lecturer is
 * currently active.
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
$actionurl = new moodle_url('/local/hrdepartment/lecturer/coursereactivate.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('reactivateassignment', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

if ($confirm && confirm_sesskey()) {
    $result = course_assignment_manager::reactivate_assignment($id, $USER->id);

    if ($result['ok']) {
        redirect(
            $returnurl,
            get_string('assignmentreactivated', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            $returnurl,
            get_string('assignmentreactivatedwithwarning', 'local_hrdepartment', $result['warning']),
            null,
            \core\output\notification::NOTIFY_WARNING
        );
    }
}

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('lecturers');

echo $OUTPUT->confirm(
    get_string('confirmreactivateassignment', 'local_hrdepartment'),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
