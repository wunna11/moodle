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
 * Log/edit a student leave request.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\course_assignment_manager;
use local_hrdepartment\student_leave_manager;

/**
 * Class student_leave_form
 *
 * customdata keys: applicationid (int, 0 = new), studentid (int|null),
 * studentdisplay (string), iscreate (bool).
 */
class student_leave_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $applicationid = $this->_customdata['applicationid'] ?? 0;
        $studentid = $this->_customdata['studentid'] ?? null;
        $studentdisplay = $this->_customdata['studentdisplay'] ?? '';
        $iscreate = !empty($this->_customdata['iscreate']);

        if ($iscreate) {
            $mform->addElement(
                'autocomplete',
                'studentid',
                get_string('student', 'local_hrdepartment'),
                student_leave_manager::get_student_options($studentid),
                ['noselectionstring' => get_string('choosedots')]
            );
            $mform->addRule('studentid', get_string('required'), 'required', null, 'client');
        } else {
            $mform->addElement('static', 'studentidstatic', get_string('student', 'local_hrdepartment'), $studentdisplay);
            $mform->addElement('hidden', 'studentid', $studentid);
            $mform->setType('studentid', PARAM_INT);
        }

        $mform->addElement(
            'select',
            'leavetypeid',
            get_string('leavetype', 'local_hrdepartment'),
            student_leave_manager::get_leave_type_options()
        );
        $mform->addRule('leavetypeid', get_string('required'), 'required', null, 'client');

        $mform->addElement(
            'autocomplete',
            'courseid',
            get_string('course', 'local_hrdepartment'),
            course_assignment_manager::get_course_options(),
            ['noselectionstring' => get_string('none')]
        );

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'local_hrdepartment'));
        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'local_hrdepartment'));

        $mform->addElement('textarea', 'reason', get_string('reason', 'local_hrdepartment'), ['rows' => 3]);
        $mform->setType('reason', PARAM_TEXT);

        $mform->addElement('hidden', 'applicationid', $applicationid);
        $mform->setType('applicationid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: end date not before start date, and the
     * chosen student must actually hold the student role.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['enddate']) && !empty($data['startdate']) && $data['enddate'] < $data['startdate']) {
            $errors['enddate'] = get_string('errorenddatebeforestart', 'local_hrdepartment');
        }

        if (!empty($data['studentid']) && !student_leave_manager::is_student((int) $data['studentid'])) {
            $errors['studentid'] = get_string('errornotastudent', 'local_hrdepartment');
        }

        return $errors;
    }
}
