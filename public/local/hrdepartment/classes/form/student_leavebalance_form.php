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
 * Sets the allocated leave days for one student/leave type/academic year.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class student_leavebalance_form
 *
 * customdata keys: studentid, leavetypeid, academicyear, leavetypename,
 * studentdisplay, used, remaining.
 */
class student_leavebalance_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $studentid = $this->_customdata['studentid'];
        $leavetypeid = $this->_customdata['leavetypeid'];
        $academicyear = $this->_customdata['academicyear'];

        $mform->addElement('static', 'studentdisplay', get_string('student', 'local_hrdepartment'), $this->_customdata['studentdisplay']);
        $mform->addElement('static', 'leavetypedisplay', get_string('leavetype', 'local_hrdepartment'), $this->_customdata['leavetypename']);
        $mform->addElement('static', 'academicyeardisplay', get_string('academicyear', 'local_hrdepartment'), $academicyear);
        $mform->addElement('static', 'useddisplay', get_string('used', 'local_hrdepartment'), (string) $this->_customdata['used']);

        $mform->addElement('text', 'allocated', get_string('allocateddays', 'local_hrdepartment'));
        $mform->setType('allocated', PARAM_FLOAT);
        $mform->addRule('allocated', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'studentid', $studentid);
        $mform->setType('studentid', PARAM_INT);
        $mform->addElement('hidden', 'leavetypeid', $leavetypeid);
        $mform->setType('leavetypeid', PARAM_INT);
        $mform->addElement('hidden', 'academicyear', $academicyear);
        $mform->setType('academicyear', PARAM_ALPHANUMEXT);

        $this->add_action_buttons();
    }
}
