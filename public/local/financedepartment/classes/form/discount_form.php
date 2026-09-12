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
 * Add/edit discount definition form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;

/**
 * Class discount_form
 *
 * customdata keys: discountid (int, 0 = new).
 *
 * Deliberately has no isautomatic/rulejson fields - see
 * discount_manager's class docblock for why (automatic rule evaluation
 * is deferred, every discount is manual-only for now regardless of its
 * type). A static notice element explains this on the form itself.
 */
class discount_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $discountid = $this->_customdata['discountid'] ?? 0;
        $iscreate = empty($discountid);

        $mform->addElement('text', 'name', get_string('discountname', 'local_financedepartment'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $typeoptions = [];
        foreach (constants::discount_types() as $type) {
            $typeoptions[$type] = get_string('discounttype_' . $type, 'local_financedepartment');
        }
        $mform->addElement('select', 'type', get_string('discounttype', 'local_financedepartment'), $typeoptions);
        $mform->addHelpButton('type', 'discounttype', 'local_financedepartment');

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

        // Dynamic unit hint (2026-09-12) - identical fix as
        // scholarship_form.php's amountvalue field, applied here too since
        // this form has the exact same amounttype/amountvalue pattern. See
        // that form's own comment for the full "why hideIf instead of
        // label-text JS" reasoning.
        $mform->addElement('static', 'amountvaluehint_fixed', '', get_string('amountvaluehint_fixed', 'local_financedepartment'));
        $mform->hideIf('amountvaluehint_fixed', 'amounttype', 'eq', constants::AMOUNT_TYPE_PERCENTAGE);

        $mform->addElement('static', 'amountvaluehint_percentage', '', get_string('amountvaluehint_percentage', 'local_financedepartment'));
        $mform->hideIf('amountvaluehint_percentage', 'amounttype', 'neq', constants::AMOUNT_TYPE_PERCENTAGE);

        $mform->addElement('static', 'discountautomaticnotice', '', get_string('discountautomaticnotice', 'local_financedepartment'));

        $mform->addElement('textarea', 'description', get_string('description', 'local_financedepartment'), ['rows' => 3]);
        $mform->setType('description', PARAM_TEXT);

        if (!$iscreate) {
            $mform->addElement(
                'select',
                'status',
                get_string('status', 'local_financedepartment'),
                [
                    constants::DISCOUNT_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
                    constants::DISCOUNT_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
                ]
            );
        }

        $mform->addElement('hidden', 'discountid', $discountid);
        $mform->setType('discountid', PARAM_INT);

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
        $errors = parent::validation($data, $files);

        if (!is_numeric($data['amountvalue']) || (float) $data['amountvalue'] < 0) {
            $errors['amountvalue'] = get_string('erroramountnegative', 'local_financedepartment');
        } else if ($data['amounttype'] === constants::AMOUNT_TYPE_PERCENTAGE && (float) $data['amountvalue'] > 100) {
            $errors['amountvalue'] = get_string('errorpercentagerange', 'local_financedepartment');
        }

        if (!in_array($data['type'], constants::discount_types(), true)) {
            $errors['type'] = get_string('required');
        }

        return $errors;
    }
}
