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
 * Assign a lecturer to a Moodle course form.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\course_assignment_manager;

/**
 * Class courseassign_form
 *
 * customdata keys: employeeid (int).
 */
class courseassign_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $employeeid = $this->_customdata['employeeid'];

        $mform->addElement(
            'autocomplete',
            'courseid',
            get_string('course', 'local_hrdepartment'),
            course_assignment_manager::get_course_options(),
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('courseid', get_string('required'), 'required', null, 'client');

        $roles = course_assignment_manager::get_assignable_roles();
        $mform->addElement('select', 'roleid', get_string('role', 'local_hrdepartment'), $roles);
        $mform->addRule('roleid', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'local_hrdepartment'), ['optional' => true]);
        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'local_hrdepartment'), ['optional' => true]);

        $mform->addElement('hidden', 'employeeid', $employeeid);
        $mform->setType('employeeid', PARAM_INT);

        $this->add_action_buttons(true, get_string('assigncourse', 'local_hrdepartment'));
    }

    /**
     * Ensures the end date, if set, is not before the start date, and
     * that the lecturer doesn't already have an active assignment for
     * the chosen course (re-assigning after a properly ended stint is
     * still fine - only a second *active* row is blocked).
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['startdate']) && !empty($data['enddate']) && $data['enddate'] < $data['startdate']) {
            $errors['enddate'] = get_string('errorenddatebeforestart', 'local_hrdepartment');
        }

        if (!empty($data['courseid'])
            && course_assignment_manager::has_active_assignment((int) $data['employeeid'], (int) $data['courseid'])) {
            $errors['courseid'] = get_string('errorduplicateassignment', 'local_hrdepartment');
        }

        return $errors;
    }
}
