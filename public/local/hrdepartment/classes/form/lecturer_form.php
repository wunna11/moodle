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
 * Add/edit lecturer form.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_hrdepartment\constants;
use local_hrdepartment\department_helper;
use local_hrdepartment\lecturer_manager;

/**
 * Class lecturer_form
 *
 * customdata keys: employeeid (int, 0 = new), currentuserid (int|null).
 */
class lecturer_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $employeeid = $this->_customdata['employeeid'] ?? 0;
        $currentuserid = $this->_customdata['currentuserid'] ?? null;
        $currentuserdisplay = $this->_customdata['currentuserdisplay'] ?? '';
        $iscreate = empty($employeeid);

        $mform->addElement('header', 'linkedaccount', get_string('linkedaccount', 'local_hrdepartment'));

        if ($iscreate) {
            $useroptions = lecturer_manager::get_eligible_users();
            $mform->addElement(
                'autocomplete',
                'userid',
                get_string('linkedmoodleuser', 'local_hrdepartment'),
                $useroptions,
                ['noselectionstring' => get_string('choosedots')]
            );
            $mform->addRule('userid', get_string('required'), 'required', null, 'client');
        } else {
            $mform->addElement('static', 'useridstatic', get_string('linkedmoodleuser', 'local_hrdepartment'), $currentuserdisplay);
            $mform->addElement('hidden', 'userid', $currentuserid);
            $mform->setType('userid', PARAM_INT);
        }

        $mform->addElement('header', 'profiledetails', get_string('profiledetails', 'local_hrdepartment'));

        $mform->addElement('text', 'employeecode', get_string('employeecode', 'local_hrdepartment'));
        $mform->setType('employeecode', PARAM_ALPHANUMEXT);
        $mform->addRule('employeecode', get_string('required'), 'required', null, 'client');

        $mform->addElement(
            'autocomplete',
            'departmentid',
            get_string('department', 'local_hrdepartment'),
            department_helper::get_options(),
            ['tags' => true, 'noselectionstring' => get_string('choosedots')]
        );

        $mform->addElement('text', 'designation', get_string('designation', 'local_hrdepartment'));
        $mform->setType('designation', PARAM_TEXT);

        $mform->addElement(
            'autocomplete',
            'reportsto',
            get_string('reportsto', 'local_hrdepartment'),
            lecturer_manager::get_potential_managers($employeeid ?: null),
            ['noselectionstring' => get_string('none')]
        );

        $mform->addElement(
            'select',
            'employmentstatus',
            get_string('employmentstatus', 'local_hrdepartment'),
            [
                constants::EMPLOYMENT_STATUS_ACTIVE => get_string('status_active', 'local_hrdepartment'),
                constants::EMPLOYMENT_STATUS_INACTIVE => get_string('status_inactive', 'local_hrdepartment'),
                constants::EMPLOYMENT_STATUS_TERMINATED => get_string('status_terminated', 'local_hrdepartment'),
            ]
        );

        $mform->addElement('date_selector', 'joindate', get_string('joindate', 'local_hrdepartment'), ['optional' => true]);

        $mform->addElement('text', 'phone', get_string('phone', 'local_hrdepartment'));
        $mform->setType('phone', PARAM_TEXT);

        $mform->addElement('text', 'emergencycontact', get_string('emergencycontact', 'local_hrdepartment'));
        $mform->setType('emergencycontact', PARAM_TEXT);

        $mform->addElement('textarea', 'address', get_string('address', 'local_hrdepartment'), ['rows' => 3]);
        $mform->setType('address', PARAM_TEXT);

        $mform->addElement('header', 'academicdetails', get_string('academicdetails', 'local_hrdepartment'));

        $mform->addElement('text', 'qualification', get_string('qualification', 'local_hrdepartment'));
        $mform->setType('qualification', PARAM_TEXT);

        $mform->addElement('text', 'specialization', get_string('specialization', 'local_hrdepartment'));
        $mform->setType('specialization', PARAM_TEXT);

        $mform->addElement('hidden', 'employeeid', $employeeid);
        $mform->setType('employeeid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: employee code uniqueness and, for new
     * lecturers, that the chosen user is not already linked to an
     * employee record.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $employeeid = (int) $data['employeeid'];
        $code = trim($data['employeecode']);

        $params = ['employeecode' => $code];
        $sql = 'employeecode = :employeecode';
        if ($employeeid) {
            $sql .= ' AND id <> :id';
            $params['id'] = $employeeid;
        }
        if ($code !== '' && $DB->record_exists_select('hrdep_employee', $sql, $params)) {
            $errors['employeecode'] = get_string('errorcodeinuse', 'local_hrdepartment');
        }

        if (empty($employeeid) && !empty($data['userid'])) {
            if ($DB->record_exists('hrdep_employee', ['userid' => (int) $data['userid']])) {
                $errors['userid'] = get_string('erroruseralreadylinked', 'local_hrdepartment');
            }
        }

        return $errors;
    }
}
