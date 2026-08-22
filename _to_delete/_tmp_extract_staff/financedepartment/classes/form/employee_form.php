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
 * Add/edit finance staff form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;
use local_financedepartment\employee_manager;

/**
 * Class employee_form
 *
 * customdata keys: employeeid (int, 0 = new), currentuserid (int|null),
 * currentuserdisplay (string).
 */
class employee_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $employeeid = $this->_customdata['employeeid'] ?? 0;
        $currentuserid = $this->_customdata['currentuserid'] ?? null;
        $currentuserdisplay = $this->_customdata['currentuserdisplay'] ?? '';
        $iscreate = empty($employeeid);

        if ($iscreate) {
            $useroptions = employee_manager::get_eligible_users();
            $mform->addElement(
                'autocomplete',
                'userid',
                get_string('linkedmoodleuser', 'local_financedepartment'),
                $useroptions,
                ['noselectionstring' => get_string('choosedots')]
            );
            $mform->addRule('userid', get_string('required'), 'required', null, 'client');
        } else {
            $mform->addElement('static', 'useridstatic', get_string('linkedmoodleuser', 'local_financedepartment'), $currentuserdisplay);
            $mform->addElement('hidden', 'userid', $currentuserid);
            $mform->setType('userid', PARAM_INT);
        }

        $mform->addElement('text', 'employeecode', get_string('employeecode', 'local_financedepartment'));
        $mform->setType('employeecode', PARAM_ALPHANUMEXT);
        $mform->addRule('employeecode', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'designation', get_string('designation', 'local_financedepartment'));
        $mform->setType('designation', PARAM_TEXT);

        if (!$iscreate) {
            $mform->addElement(
                'select',
                'status',
                get_string('status', 'local_financedepartment'),
                [
                    constants::EMPLOYEE_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
                    constants::EMPLOYEE_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
                ]
            );
        }

        $mform->addElement('hidden', 'employeeid', $employeeid);
        $mform->setType('employeeid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: employee code uniqueness, and for a new
     * record, that the chosen user isn't already linked to one.
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
        if ($code !== '' && $DB->record_exists_select('financedep_employee', $sql, $params)) {
            $errors['employeecode'] = get_string('errorcodeinuse', 'local_financedepartment');
        }

        if (empty($employeeid) && !empty($data['userid'])) {
            if ($DB->record_exists('financedep_employee', ['userid' => (int) $data['userid']])) {
                $errors['userid'] = get_string('erroruseralreadylinked', 'local_financedepartment');
            }
        }

        return $errors;
    }
}
