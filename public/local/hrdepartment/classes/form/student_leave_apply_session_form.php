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
 * Self-service "Apply for leave" form, session-scope variant: a student
 * requests leave against 1+ specific mod_attendance session(s) of one of
 * their own courses, all on the single calendar day chosen on the prior
 * step (leave/apply.php's course+date picker), instead of a whole day.
 * See student_leave_apply_form (the day-scope sibling this mirrors) and
 * local_hrdepartment\student_leave_manager::create_application().
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\constants;
use local_hrdepartment\student_leave_manager;

/**
 * Class student_leave_apply_session_form
 *
 * customdata keys: studentid (int), studentdisplay (string), courseid (int),
 * coursedisplay (string), sessiondate (int timestamp), sessiondatedisplay (string),
 * sessionoptions (array sessionid => label).
 */
class student_leave_apply_session_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $studentid = (int) $this->_customdata['studentid'];
        $studentdisplay = $this->_customdata['studentdisplay'] ?? '';
        $courseid = (int) $this->_customdata['courseid'];
        $coursedisplay = $this->_customdata['coursedisplay'] ?? '';
        $sessiondatedisplay = $this->_customdata['sessiondatedisplay'] ?? '';
        $sessionoptions = $this->_customdata['sessionoptions'] ?? [];

        $mform->addElement('static', 'studentidstatic', get_string('student', 'local_hrdepartment'), $studentdisplay);
        $mform->addElement('hidden', 'studentid', $studentid);
        $mform->setType('studentid', PARAM_INT);

        $mform->addElement('static', 'coursestatic', get_string('course', 'local_hrdepartment'), $coursedisplay);
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('static', 'sessiondatestatic', get_string('sessiondate', 'local_hrdepartment'), $sessiondatedisplay);
        $mform->addElement('hidden', 'sessiondate', $this->_customdata['sessiondate']);
        $mform->setType('sessiondate', PARAM_INT);

        $mform->addElement('hidden', 'leavescope', constants::LEAVE_SCOPE_SESSION);
        $mform->setType('leavescope', PARAM_ALPHA);

        $mform->addElement(
            'select',
            'sessionids',
            get_string('selectsessions', 'local_hrdepartment'),
            $sessionoptions,
            ['multiple' => true, 'size' => max(2, min(6, count($sessionoptions)))]
        );
        $mform->addRule('sessionids', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('sessionids', 'selectsessions', 'local_hrdepartment');

        $mform->addElement(
            'select',
            'leavetypeid',
            get_string('leavetype', 'local_hrdepartment'),
            student_leave_manager::get_leave_type_options()
        );
        $mform->addRule('leavetypeid', get_string('required'), 'required', null, 'client');

        $approveroptions = student_leave_manager::get_teacher_options_for_student($studentid);
        $mform->addElement(
            'select',
            'approverid',
            get_string('selectapprover', 'local_hrdepartment'),
            [0 => get_string('choosedots')] + $approveroptions
        );
        $mform->addRule('approverid', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'reason', get_string('reason', 'local_hrdepartment'), ['rows' => 3]);
        $mform->setType('reason', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('applyforleave', 'local_hrdepartment'));
    }

    /**
     * Server-side validation: at least one session chosen, every chosen
     * session must actually belong to this exact course+date (never
     * trust the select element's options alone - the submitted value
     * could have been tampered with), and the chosen approver must
     * actually be a teacher of one of this student's own courses.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $allowed = array_keys($this->_customdata['sessionoptions'] ?? []);
        $chosen = array_map('intval', (array) ($data['sessionids'] ?? []));

        if (empty($chosen)) {
            $errors['sessionids'] = get_string('errornosessionsselected', 'local_hrdepartment');
        } else if (!empty(array_diff($chosen, array_map('intval', $allowed)))) {
            $errors['sessionids'] = get_string('errorinvalidsessionselected', 'local_hrdepartment');
        }

        if (empty($data['approverid'])
                || !student_leave_manager::is_teacher_of_student((int) $data['approverid'], (int) $data['studentid'])) {
            $errors['approverid'] = get_string('errorapprovernotteacher', 'local_hrdepartment');
        }

        return $errors;
    }
}
