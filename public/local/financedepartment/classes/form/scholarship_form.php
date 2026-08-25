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
 * Add/edit scholarship definition form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;
use local_financedepartment\scholarship_manager;

/**
 * Class scholarship_form
 *
 * customdata keys: scholarshipid (int, 0 = new).
 */
class scholarship_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $scholarshipid = $this->_customdata['scholarshipid'] ?? 0;
        $iscreate = empty($scholarshipid);

        $mform->addElement('text', 'name', get_string('scholarshipname', 'local_financedepartment'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement(
            'select',
            'categoryid',
            get_string('category', 'local_financedepartment'),
            scholarship_manager::get_category_options()
        );
        $mform->addRule('categoryid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('categoryid', 'scholarshipcategory', 'local_financedepartment');

        $mform->addElement(
            'select',
            'amounttype',
            get_string('amounttype', 'local_financedepartment'),
            [
                constants::AMOUNT_TYPE_FIXED => get_string('amounttype_fixed', 'local_financedepartment'),
                constants::AMOUNT_TYPE_PERCENTAGE => get_string('amounttype_percentage', 'local_financedepartment'),
            ]
        );

        $mform->addElement('text', 'amountvalue', get_string('amountvalue', 'local_financedepartment'));
        $mform->setType('amountvalue', PARAM_FLOAT);
        $mform->addRule('amountvalue', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('amountvalue', 'amountvalue', 'local_financedepartment');

        $mform->addElement('textarea', 'description', get_string('description', 'local_financedepartment'), ['rows' => 3]);
        $mform->setType('description', PARAM_TEXT);

        if (!$iscreate) {
            $mform->addElement(
                'select',
                'status',
                get_string('status', 'local_financedepartment'),
                [
                    constants::SCHOLARSHIP_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
                    constants::SCHOLARSHIP_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
                ]
            );
        }

        $mform->addElement('hidden', 'scholarshipid', $scholarshipid);
        $mform->setType('scholarshipid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: amount must be non-negative, and a
     * percentage amounttype must be 0-100.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!is_numeric($data['amountvalue']) || (float) $data['amountvalue'] < 0) {
            $errors['amountvalue'] = get_string('erroramountnegative', 'local_financedepartment');
        } else if ($data['amounttype'] === constants::AMOUNT_TYPE_PERCENTAGE && (float) $data['amountvalue'] > 100) {
            $errors['amountvalue'] = get_string('errorpercentagerange', 'local_financedepartment');
        }

        $categoryid = (int) $data['categoryid'];
        if ($categoryid && !$DB->record_exists('course_categories', ['id' => $categoryid])) {
            $errors['categoryid'] = get_string('required');
        }

        return $errors;
    }
}
