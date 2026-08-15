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
 * Add/edit a student leave type.
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
 * Class student_leavetype_form
 *
 * customdata keys: id (int, 0 = new).
 */
class student_leavetype_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $id = $this->_customdata['id'] ?? 0;

        $mform->addElement('text', 'name', get_string('leavetypename', 'local_hrdepartment'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'description', get_string('leavetypedescription', 'local_hrdepartment'), ['rows' => 2]);
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('text', 'maxdaysperyear', get_string('maxdaysperyear', 'local_hrdepartment'));
        $mform->setType('maxdaysperyear', PARAM_FLOAT);
        $mform->setDefault('maxdaysperyear', 0);

        $mform->addElement('advcheckbox', 'requiresapproval', get_string('requiresapproval', 'local_hrdepartment'));
        $mform->setDefault('requiresapproval', 1);

        $mform->addElement('advcheckbox', 'active', get_string('active', 'local_hrdepartment'));
        $mform->setDefault('active', 1);

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: leave type name uniqueness.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $id = (int) $data['id'];
        $name = trim($data['name']);

        if ($name !== '' && student_leave_manager::leave_type_name_in_use($name, $id)) {
            $errors['name'] = get_string('errorleavetypenameinuse', 'local_hrdepartment');
        }

        return $errors;
    }
}
