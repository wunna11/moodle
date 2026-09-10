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
 * Record a payment, or a refund, against a fee record.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;
use local_financedepartment\feerecord_manager;
use local_financedepartment\installmentplan_manager;

/**
 * Class feepayment_form
 *
 * One form handles both "record a payment" and "record a refund" -
 * customdata['mode'] ('payment' or 'refund') only changes the submit
 * button label and validation.php's refund-specific check; every field
 * is identical, matching this plugin's established "one form, customdata
 * switches behaviour" shape (installmentplan_form for create/reschedule,
 * discountrequestreview_form for approve/reject).
 *
 * customdata keys:
 * - feerecordid (int, required) - always fixed/preset, shown read-only.
 *   Unlike installmentplan_form's create mode, this is never a free
 *   autocomplete: a payment only ever gets recorded starting from a
 *   specific fee record's own page (pages/feerecords/view.php's "Record
 *   payment" action) or a specific installment row
 *   (pages/installments/view.php's per-row "Record payment" link) - see
 *   [[financedepartment-schema]] project memory.
 * - mode ('payment'|'refund')
 * - presetinstallmentschedid (int, optional) - pre-selects (but does not
 *   lock) the installment link dropdown, when reached from
 *   pages/installments/view.php's per-row link.
 *
 * 2026-09-09 scope decision confirmed with the user before building:
 * linking to a specific installment is OPTIONAL in both modes - the
 * dropdown always includes a "not linked to any installment" choice, and
 * only lists schedule rows that are not already fully PAID (there is
 * nothing useful to pay/refund against a row with nothing owing, though
 * a refund can still be recorded with no installment link at all if
 * needed).
 */
class feepayment_form extends \moodleform {

    /** @var float rounding tolerance (MMK) allowed when comparing a refund amount against what's actually been paid. */
    const REFUND_TOLERANCE = 0.01;

    /** @var string[] accepted supporting-document file extensions. */
    const ATTACHMENT_TYPES = ['.pdf', '.jpg', '.jpeg', '.png'];

    /** @var int max number of supporting-document files (2026-09-10, per user request - multiple files). */
    const ATTACHMENT_MAXFILES = 5;

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $feerecordid = (int) $this->_customdata['feerecordid'];
        $mode = $this->_customdata['mode'];
        $presetinstallmentschedid = (int) ($this->_customdata['presetinstallmentschedid'] ?? 0);

        $feerecord = feerecord_manager::get($feerecordid);
        $label = $feerecord
            ? fullname($feerecord) . ' - ' . format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear)
                . ' (' . get_string('balance', 'local_financedepartment') . ': '
                . self::format_mmk($feerecord->balance) . ')'
            : '';
        $mform->addElement('static', 'feerecorddisplay', get_string('feerecord', 'local_financedepartment'), $label);
        $mform->addElement('hidden', 'feerecordid', $feerecordid);
        $mform->setType('feerecordid', PARAM_INT);

        $scheduleoptions = [0 => get_string('paymentnotlinked', 'local_financedepartment')];
        foreach (installmentplan_manager::get_schedule_for_feerecord($feerecordid) as $schedrow) {
            if ($schedrow->status === constants::INSTALLMENT_STATUS_PAID) {
                continue;
            }
            $scheduleoptions[$schedrow->id] = get_string(
                'installmentoptionlabel',
                'local_financedepartment',
                (object) [
                    'number' => $schedrow->installmentnumber,
                    'duedate' => userdate($schedrow->duedate, get_string('strftimedatetimeshort', 'core_langconfig')),
                    'amount' => self::format_mmk($schedrow->amount),
                    'paid' => self::format_mmk($schedrow->paidamount),
                ]
            );
        }
        $mform->addElement(
            'select',
            'installmentschedid',
            get_string('linkedinstallment', 'local_financedepartment'),
            $scheduleoptions
        );
        $mform->setType('installmentschedid', PARAM_INT);
        $mform->addHelpButton('installmentschedid', 'linkedinstallment', 'local_financedepartment');
        if ($presetinstallmentschedid && isset($scheduleoptions[$presetinstallmentschedid])) {
            $mform->setDefault('installmentschedid', $presetinstallmentschedid);
        }

        $mform->addElement('text', 'amount', get_string('amount', 'local_financedepartment'));
        $mform->setType('amount', PARAM_FLOAT);
        $mform->addRule('amount', get_string('required'), 'required', null, 'client');

        $mform->addElement('date_selector', 'paymentdate', get_string('paymentdate', 'local_financedepartment'));
        $mform->setDefault('paymentdate', time());

        $mform->addElement(
            'select',
            'paymentmethod',
            get_string('paymentmethod', 'local_financedepartment'),
            [
                '' => get_string('choosedots'),
                'cash' => get_string('paymentmethod_cash', 'local_financedepartment'),
                'banktransfer' => get_string('paymentmethod_banktransfer', 'local_financedepartment'),
                'mobilebanking' => get_string('paymentmethod_mobilebanking', 'local_financedepartment'),
                'other' => get_string('paymentmethod_other', 'local_financedepartment'),
            ]
        );
        $mform->setType('paymentmethod', PARAM_ALPHA);

        $mform->addElement('textarea', 'notes', get_string('notes', 'local_financedepartment'), ['rows' => 3]);
        $mform->setType('notes', PARAM_TEXT);

        // Optional supporting-document attachment (2026-09-10, per user
        // request) - multiple files, same filemanager/ATTACHMENT_MAXFILES
        // shape as scholarshiprequest_form/discountrequest_form. Saved by
        // pages/payments/create.php into this plugin's OWN 'payment'
        // filearea after the payment/refund row is created (the itemid is
        // the new financedep_feepayment id), served back via
        // local_financedepartment_pluginfile(). Deliberately optional, no
        // addRule('required') - matching every other attachment field in
        // this plugin.
        $mform->addElement(
            'filemanager',
            'attachment',
            get_string('paymentattachment', 'local_financedepartment'),
            null,
            [
                'subdirs' => 0,
                'maxfiles' => self::ATTACHMENT_MAXFILES,
                'accepted_types' => self::ATTACHMENT_TYPES,
            ]
        );
        $mform->addHelpButton('attachment', 'paymentattachment', 'local_financedepartment');

        $mform->addElement('hidden', 'mode', $mode);
        $mform->setType('mode', PARAM_ALPHA);

        $label = $mode === constants::PAYMENT_TYPE_REFUND
            ? get_string('recordrefund', 'local_financedepartment')
            : get_string('recordpayment', 'local_financedepartment');
        $this->add_action_buttons(true, $label);
    }

    /**
     * Formats an MMK amount inline, without calling
     * local_financedepartment_format_money() (a lib.php function) - see
     * feerecord_manager::get_feestructure_options()'s docblock (and this
     * plugin's CRITICAL RULE) for why form classes never call it
     * directly: definition() runs during __construct(), before lib.php
     * is guaranteed loaded. Identical logic to
     * scholarshiprequest_form::format_mmk() / discountrequest_form's
     * equivalent - kept as a small per-form copy rather than a shared
     * helper, matching this plugin's established pattern for this exact
     * fix (see [[financedepartment-schema]] project memory, Post-deploy
     * fix #1). **2026-09-10: this exact bug recurred here (definition()
     * called local_financedepartment_format_money() directly, 3 call
     * sites) - fixed the same way. If a NEW form class is added to this
     * plugin, copy this helper into it too rather than reaching for the
     * lib.php function.**
     *
     * @param float|int|string $amount
     * @return string
     */
    protected static function format_mmk($amount): string {
        $amount = (float) $amount;
        $decimals = (abs($amount - round($amount)) > 0.001) ? 2 : 0;

        return number_format($amount, $decimals) . ' MMK';
    }

    /**
     * Server-side validation. Both modes: amount must be a positive
     * number, and if an installment is linked, it must actually belong
     * to this fee record. Refund mode only: the refund amount must not
     * exceed what has actually been paid so far - on the fee record as a
     * whole, and (if linked) on that installment specifically - so a
     * refund can never push paidamount negative in a way that hides a
     * data-entry mistake (feerecord_manager::add_payment_amount() would
     * silently clamp it to zero otherwise).
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $amount = $data['amount'] ?? null;
        if (!is_numeric($amount) || (float) $amount <= 0) {
            $errors['amount'] = get_string('erroramountnegative', 'local_financedepartment');
            return $errors;
        }
        $amount = (float) $amount;

        $feerecordid = (int) ($data['feerecordid'] ?? 0);
        $feerecord = feerecord_manager::get($feerecordid);

        $installmentschedid = (int) ($data['installmentschedid'] ?? 0);
        $schedrow = $installmentschedid ? installmentplan_manager::get_schedule_row($installmentschedid) : false;
        if ($installmentschedid && !$schedrow) {
            $errors['installmentschedid'] = get_string('errorinstallmentnotforrecord', 'local_financedepartment');
        }

        if ($data['mode'] === constants::PAYMENT_TYPE_REFUND) {
            if ($feerecord && (float) $amount > (float) $feerecord->paidamount + self::REFUND_TOLERANCE) {
                $errors['amount'] = get_string('errorrefundexceedspaid', 'local_financedepartment');
            } else if ($schedrow && (float) $amount > (float) $schedrow->paidamount + self::REFUND_TOLERANCE) {
                $errors['amount'] = get_string('errorrefundexceedsinstallmentpaid', 'local_financedepartment');
            }
        }

        return $errors;
    }
}
