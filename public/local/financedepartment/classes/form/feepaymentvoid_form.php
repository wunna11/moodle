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
 * Void an existing payment or refund.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class feepaymentvoid_form
 *
 * Unlike installmentplan_manager::cancel() (a plain $OUTPUT->confirm(),
 * no reason needed - see pages/installments/cancel.php), voiding a
 * payment always requires a typed reason - feepayment_manager::
 * void_payment()'s docblock calls this "required, always logged and
 * stored", so it is enforced here in validation() rather than left
 * optional the way discountrequestreview_form's reviewnote is for an
 * approval. customdata keys: paymentid (int), paymentsummary (string,
 * already-safe HTML - the read-only summary of what's being voided).
 */
class feepaymentvoid_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $paymentid = (int) $this->_customdata['paymentid'];
        $paymentsummary = $this->_customdata['paymentsummary'] ?? '';

        $mform->addElement('static', 'paymentsummary', get_string('payment', 'local_financedepartment'), $paymentsummary);

        $mform->addElement('textarea', 'voidreason', get_string('voidreason', 'local_financedepartment'), ['rows' => 3]);
        $mform->setType('voidreason', PARAM_TEXT);
        $mform->addRule('voidreason', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('voidreason', 'voidreason', 'local_financedepartment');

        $mform->addElement('hidden', 'paymentid', $paymentid);
        $mform->setType('paymentid', PARAM_INT);

        $this->add_action_buttons(true, get_string('voidpayment', 'local_financedepartment'));
    }

    /**
     * Server-side validation: a void reason is always required (client-
     * side 'required' rule above is not enough on its own - see this
     * class's docblock).
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (trim((string) ($data['voidreason'] ?? '')) === '') {
            $errors['voidreason'] = get_string('required');
        }

        return $errors;
    }
}
