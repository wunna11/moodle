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
 * Bulk mark-attendance form: one status + remarks row per enrolled
 * student, for a single course and day.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\constants;

/**
 * Class mark_attendance_form
 *
 * customdata keys: courseid (int), coursename (string), date (int
 * timestamp), datestring (string, display only), students (stdClass[]
 * as returned by student_attendance_manager::get_enrolled_students()).
 *
 * Dynamically defines status_{studentid} and remarks_{studentid}
 * elements for every student passed in, so get_data() naturally returns
 * one status/remarks pair per student without any custom parsing.
 */
class mark_attendance_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $courseid = $this->_customdata['courseid'];
        $coursename = $this->_customdata['coursename'];
        $date = $this->_customdata['date'];
        $datestring = $this->_customdata['datestring'];
        $students = $this->_customdata['students'];

        $mform->addElement('static', 'coursestatic', get_string('course', 'local_hrdepartment'), $coursename);
        $mform->addElement('static', 'datestatic', get_string('attendancedate', 'local_hrdepartment'), $datestring);

        $statusoptions = [];
        foreach (constants::attendance_statuses() as $status) {
            $statusoptions[$status] = get_string('attendance_' . $status, 'local_hrdepartment');
        }

        foreach ($students as $student) {
            $statusname = 'status_' . $student->id;
            $remarksname = 'remarks_' . $student->id;

            $group = [];
            $group[] = $mform->createElement('select', $statusname, '', $statusoptions);
            $group[] = $mform->createElement('text', $remarksname, '', [
                'size' => 24,
                'placeholder' => get_string('remarks', 'local_hrdepartment'),
            ]);
            $mform->addGroup($group, 'group_' . $student->id, $student->fullname, [' '], false);
            $mform->setType($statusname, PARAM_ALPHA);
            $mform->setType($remarksname, PARAM_TEXT);
            $mform->setDefault($statusname, constants::ATTENDANCE_PRESENT);
        }

        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'date', $date);
        $mform->setType('date', PARAM_INT);

        $this->add_action_buttons(true, get_string('saveattendance', 'local_hrdepartment'));
    }
}
