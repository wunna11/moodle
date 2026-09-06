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
 * Add/edit an organisational department.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\department_manager;

/**
 * Class department_form
 *
 * customdata keys: id (int, 0 = new), currentname (string, the
 * department's current stored name - only meaningful when editing,
 * used to decide whether the name field should be locked, see
 * department_manager::PROTECTED_NAMES).
 */
class department_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $id = $this->_customdata['id'] ?? 0;
        $currentname = $this->_customdata['currentname'] ?? '';
        $isprotected = $id && department_manager::is_protected($currentname);

        if ($isprotected) {
            // This department's name is used elsewhere for access
            // control (see department_manager::PROTECTED_NAMES) -
            // renaming it here would silently break that rule, so the
            // name is shown read-only instead of editable. The hidden
            // field keeps the current name in the submitted data;
            // department_manager::update() also refuses to change it
            // as a second line of defence.
            $mform->addElement(
                'static',
                'nameprotected',
                get_string('departmentname', 'local_hrdepartment'),
                format_string($currentname) . \html_writer::tag(
                    'span',
                    get_string('departmentnameprotected', 'local_hrdepartment'),
                    ['class' => 'text-muted d-block small']
                )
            );
            $mform->addElement('hidden', 'name', $currentname);
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->addElement('text', 'name', get_string('departmentname', 'local_hrdepartment'));
            $mform->setType('name', PARAM_TEXT);
            $mform->addRule('name', get_string('required'), 'required', null, 'client');
        }

        $mform->addElement('text', 'code', get_string('departmentcode', 'local_hrdepartment'));
        $mform->setType('code', PARAM_ALPHANUMEXT);

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: name and code uniqueness (both
     * case-insensitive).
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $id = (int) $data['id'];
        $name = trim($data['name']);
        $code = trim($data['code'] ?? '');

        if ($name !== '' && department_manager::name_in_use($name, $id)) {
            $errors['name'] = get_string('errordepartmentnameinuse', 'local_hrdepartment');
        }

        if ($code !== '' && department_manager::code_in_use($code, $id)) {
            $errors['code'] = get_string('errordepartmentcodeinuse', 'local_hrdepartment');
        }

        return $errors;
    }
}
