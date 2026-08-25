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
 * Approve/reject a scholarship request.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class scholarshiprequestreview_form
 *
 * customdata keys: requestid (int), decision ('approve'|'reject'),
 * suggestedamount (float - the request's own requestedamount, used to
 * pre-fill the amount field on an approval so the reviewer only has to
 * type something different if they actually want to override it).
 *
 * The amountapproved element is only added for an 'approve' decision -
 * a rejection never touches the fee record, so there is nothing to
 * amount-override.
 */
class scholarshiprequestreview_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $decision = $this->_customdata['decision'];
        $suggestedamount = $this->_customdata['suggestedamount'] ?? 0;

        if ($decision === 'approve') {
            $mform->addElement('text', 'amountapproved', get_string('approvedamount', 'local_financedepartment'));
            $mform->setType('amountapproved', PARAM_FLOAT);
            $mform->setDefault('amountapproved', $suggestedamount);
            $mform->addRule('amountapproved', get_string('required'), 'required', null, 'client');
            $mform->addHelpButton('amountapproved', 'approvedamount', 'local_financedepartment');
        }

        $mform->addElement(
            'textarea',
            'reviewnote',
            get_string('reviewnote', 'local_financedepartment'),
            ['rows' => 3]
        );
        $mform->setType('reviewnote', PARAM_TEXT);
        if ($decision === 'reject') {
            $mform->addRule('reviewnote', get_string('required'), 'required', null, 'client');
        }

        $mform->addElement('hidden', 'requestid', $this->_customdata['requestid']);
        $mform->setType('requestid', PARAM_INT);

        $mform->addElement('hidden', 'decision', $decision);
        $mform->setType('decision', PARAM_ALPHA);

        $label = $decision === 'approve'
            ? get_string('approve', 'local_financedepartment')
            : get_string('reject', 'local_financedepartment');
        $this->add_action_buttons(true, $label);
    }

    /**
     * Server-side validation: an approval amount must be non-negative.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['decision'] === 'approve' && (!is_numeric($data['amountapproved']) || (float) $data['amountapproved'] < 0)) {
            $errors['amountapproved'] = get_string('erroramountnegative', 'local_financedepartment');
        }

        return $errors;
    }
}
