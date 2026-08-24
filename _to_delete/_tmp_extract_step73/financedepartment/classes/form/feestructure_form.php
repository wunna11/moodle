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
 * Add/edit fee structure form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;
use local_financedepartment\feestructure_manager;

/**
 * Class feestructure_form
 *
 * customdata keys: feestructureid (int, 0 = new).
 */
class feestructure_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $feestructureid = $this->_customdata['feestructureid'] ?? 0;
        $iscreate = empty($feestructureid);

        $mform->addElement(
            'select',
            'categoryid',
            get_string('category', 'local_financedepartment'),
            feestructure_manager::get_category_options()
        );
        $mform->addRule('categoryid', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'academicyear', get_string('academicyear', 'local_financedepartment'));
        $mform->setType('academicyear', PARAM_TEXT);
        $mform->addRule('academicyear', get_string('required'), 'required', null, 'client');
        $mform->addRule('academicyear', get_string('erroracademicyeartoolong', 'local_financedepartment'), 'maxlength', 20, 'client');
        $mform->addHelpButton('academicyear', 'academicyear', 'local_financedepartment');

        $mform->addElement('text', 'amount', get_string('amount', 'local_financedepartment'));
        $mform->setType('amount', PARAM_FLOAT);
        $mform->addRule('amount', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('amount', 'amount', 'local_financedepartment');

        $mform->addElement('textarea', 'description', get_string('description', 'local_financedepartment'), ['rows' => 3]);
        $mform->setType('description', PARAM_TEXT);

        if (!$iscreate) {
            $mform->addElement(
                'select',
                'status',
                get_string('status', 'local_financedepartment'),
                [
                    constants::FEESTRUCTURE_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
                    constants::FEESTRUCTURE_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
                ]
            );
        }

        $mform->addElement('hidden', 'feestructureid', $feestructureid);
        $mform->setType('feestructureid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: amount must be a non-negative number, and
     * no other ACTIVE fee structure may already exist for the same
     * category + academic year.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!is_numeric($data['amount']) || (float) $data['amount'] < 0) {
            $errors['amount'] = get_string('erroramountnegative', 'local_financedepartment');
        }

        $categoryid = (int) $data['categoryid'];
        if ($categoryid && !$DB->record_exists('course_categories', ['id' => $categoryid])) {
            $errors['categoryid'] = get_string('required');
        }

        $academicyear = trim($data['academicyear']);
        $feestructureid = (int) $data['feestructureid'];
        if ($academicyear !== '' && $categoryid
                && feestructure_manager::has_active_duplicate($categoryid, $academicyear, $feestructureid)) {
            $errors['academicyear'] = get_string('errorduplicatefeestructure', 'local_financedepartment');
        }

        return $errors;
    }
}
