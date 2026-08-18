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
 * Self-service "Apply for leave" form: a student prepares and submits
 * their own leave request, choosing which of their own course teachers
 * should review it. Distinct from student_leave_form, which is the
 * HR/staff-facing "log a request on a student's behalf" form (no
 * approver picker - HR/Admin/any delegated Approver can review those).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\student_leave_manager;

/**
 * Class student_leave_apply_form
 *
 * customdata keys: studentid (int, always the logged-in user), studentdisplay (string).
 */
class student_leave_apply_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $studentid = (int) $this->_customdata['studentid'];
        $studentdisplay = $this->_customdata['studentdisplay'] ?? '';

        $mform->addElement('static', 'studentidstatic', get_string('student', 'local_hrdepartment'), $studentdisplay);
        $mform->addElement('hidden', 'studentid', $studentid);
        $mform->setType('studentid', PARAM_INT);

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
            student_leave_manager::get_course_options_for_student($studentid),
            ['noselectionstring' => get_string('none')]
        );

        $approveroptions = student_leave_manager::get_teacher_options_for_student($studentid);
        $mform->addElement(
            'select',
            'approverid',
            get_string('selectapprover', 'local_hrdepartment'),
            [0 => get_string('choosedots')] + $approveroptions
        );
        $mform->addRule('approverid', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'local_hrdepartment'));
        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'local_hrdepartment'));

        $mform->addElement('textarea', 'reason', get_string('reason', 'local_hrdepartment'), ['rows' => 3]);
        $mform->setType('reason', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('applyforleave', 'local_hrdepartment'));
    }

    /**
     * Server-side validation: end date not before start date, and the
     * chosen approver must actually be a teacher of one of this
     * student's own courses (never trust the select element's options
     * alone - the submitted value could have been tampered with).
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

        if (empty($data['approverid'])
                || !student_leave_manager::is_teacher_of_student((int) $data['approverid'], (int) $data['studentid'])) {
            $errors['approverid'] = get_string('errorapprovernotteacher', 'local_hrdepartment');
        }

        return $errors;
    }
}
