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
 * Create a new installment plan, or reschedule an existing one.
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
 * Class installmentplan_form
 *
 * One form handles both create and reschedule, the same "customdata
 * switches behaviour" shape as scholarshiprequestreview_form (create vs
 * approve/reject). customdata keys:
 * - planid (int, 0 = create mode, >0 = reschedule mode for that plan)
 * - presetfeerecordid (int, optional, create mode only)
 * - repeatcount (int, optional, BOTH modes as of the 2026-09-08 follow-up
 *   below - how many installment rows to render. In reschedule mode the
 *   caller passes the plan's current schedule count so every existing
 *   row gets its own row here; in create mode pages/installments/create.php
 *   passes whatever count the user picked in its own "how many
 *   installments?" step. Clamped to [MIN_REPEATS, MAX_REPEATS], defaults
 *   to DEFAULT_REPEATS if omitted entirely.)
 *
 * In create mode, feerecordid is a real autocomplete (any non-cancelled
 * fee record, feerecord_manager::get_active_options()). In reschedule
 * mode, feerecordid is a fixed hidden field (the plan's feerecordid
 * cannot be changed by rescheduling) shown instead as read-only static
 * text - see pages/installments/edit.php for how it pre-fills the
 * existing schedule's amount/duedate arrays via set_data().
 *
 * 2026-09-08 scope decision (user asked via AskUserQuestion): each
 * installment's amount and due date is entered MANUALLY by finance
 * staff, via Moodle's core repeat_elements() feature (an "Add another
 * installment" button, no custom JS needed) - NOT auto-split evenly
 * across a chosen count. validation() enforces that the entered amounts
 * sum to the fee record's CURRENT balance (totalamount - scholarshipamount
 * - discountamount, from feerecord_manager::get()) - see
 * installmentplan_manager's class docblock for why this plugin never
 * auto-splits.
 *
 * 2026-09-08, same-day follow-up (user reported the create form "felt
 * stuck at 3"): `repeatcount` customdata now ALSO drives create mode,
 * not just reschedule - pages/installments/create.php asks "how many
 * installments?" as an explicit first step (a plain GET selector, see
 * that page) and passes the chosen count in as `repeatcount`, so this
 * form renders exactly that many rows from the start. The
 * "Add another installment" repeat_elements() button still lets staff
 * add more beyond that on the same page if needed - the count is a
 * starting point, not a hard cap either way.
 */
class installmentplan_form extends \moodleform {

    /** @var int lowest selectable installment count in the create-mode "how many?" step. */
    const MIN_REPEATS = 1;

    /** @var int highest selectable installment count in the create-mode "how many?" step. */
    const MAX_REPEATS = 36;

    /** @var int how many installment rows to render if no repeatcount customdata is given at all (defensive fallback only - pages/installments/create.php always supplies one). */
    const DEFAULT_REPEATS = 3;

    /** @var float rounding tolerance (MMK) allowed between the entered sum and the fee record's balance. */
    const SUM_TOLERANCE = 0.01;

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $planid = (int) ($this->_customdata['planid'] ?? 0);
        $presetfeerecordid = (int) ($this->_customdata['presetfeerecordid'] ?? 0);
        $iscreate = empty($planid);

        if ($iscreate) {
            $mform->addElement(
                'autocomplete',
                'feerecordid',
                get_string('feerecord', 'local_financedepartment'),
                feerecord_manager::get_active_options(),
                ['noselectionstring' => get_string('choosedots')]
            );
            $mform->addRule('feerecordid', get_string('required'), 'required', null, 'client');
            $mform->addHelpButton('feerecordid', 'installmentplanfeerecord', 'local_financedepartment');
            if ($presetfeerecordid) {
                $mform->setDefault('feerecordid', $presetfeerecordid);
            }
        } else {
            // Reschedule mode - the fee record is fixed, shown read-only.
            $feerecordid = (int) ($this->_customdata['presetfeerecordid'] ?? 0);
            $feerecord = $feerecordid ? feerecord_manager::get($feerecordid) : false;
            $label = $feerecord
                ? fullname($feerecord) . ' - ' . format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear)
                : '';
            $mform->addElement('static', 'feerecorddisplay', get_string('feerecord', 'local_financedepartment'), $label);
            $mform->addElement('hidden', 'feerecordid', $feerecordid);
            $mform->setType('feerecordid', PARAM_INT);
        }

        $repeatcount = max(
            self::MIN_REPEATS,
            min(self::MAX_REPEATS, (int) ($this->_customdata['repeatcount'] ?? self::DEFAULT_REPEATS))
        );

        $repeatarray = [];
        $repeatarray[] = $mform->createElement('text', 'installmentamount', get_string('installmentamount', 'local_financedepartment'));
        $repeatarray[] = $mform->createElement('date_selector', 'installmentduedate', get_string('duedate', 'local_financedepartment'));

        $repeateloptions = [];
        $repeateloptions['installmentamount']['type'] = PARAM_FLOAT;
        $repeateloptions['installmentduedate']['type'] = PARAM_INT;

        $this->repeat_elements(
            $repeatarray,
            $repeatcount,
            $repeateloptions,
            'installment_repeats',
            'installment_add_fields',
            1,
            get_string('addinstallment', 'local_financedepartment'),
            true
        );

        $mform->addElement('hidden', 'planid', $planid);
        $mform->setType('planid', PARAM_INT);

        $submitlabel = $iscreate
            ? get_string('createinstallmentplan', 'local_financedepartment')
            : get_string('rescheduleinstallmentplan', 'local_financedepartment');
        $this->add_action_buttons(true, $submitlabel);
    }

    /**
     * Server-side validation. Create mode: the fee record must exist,
     * not be cancelled, and not already have an ACTIVE plan. Reschedule
     * mode skips that existence check (the plan being edited IS the
     * active one). Both modes: at least one installment row must have a
     * positive amount, and the sum of every entered amount must equal
     * the fee record's current balance (within SUM_TOLERANCE).
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $iscreate = empty((int) ($data['planid'] ?? 0));
        $feerecordid = (int) ($data['feerecordid'] ?? 0);
        $feerecord = $feerecordid ? feerecord_manager::get($feerecordid) : false;

        if ($iscreate) {
            if (!$feerecord) {
                $errors['feerecordid'] = get_string('errorfeerecordnotfound', 'local_financedepartment');
            } else if ($feerecord->status === constants::FEE_STATUS_CANCELLED) {
                $errors['feerecordid'] = get_string('errorfeerecordcancelled', 'local_financedepartment');
            } else if (installmentplan_manager::has_active_plan($feerecordid)) {
                $errors['feerecordid'] = get_string('errorinstallmentplanexists', 'local_financedepartment');
            }
        }

        $sum = 0.0;
        $count = 0;
        $amounts = $data['installmentamount'] ?? [];
        foreach ($amounts as $index => $amount) {
            if ($amount === '' || $amount === null) {
                continue;
            }
            if (!is_numeric($amount) || (float) $amount <= 0) {
                $errors["installmentamount[$index]"] = get_string('erroramountnegative', 'local_financedepartment');
                continue;
            }
            $count++;
            $sum += (float) $amount;
        }

        if ($count === 0) {
            $errors['installmentamount[0]'] = get_string('errorinstallmentnonefilled', 'local_financedepartment');
        } else if ($feerecord && empty($errors['feerecordid'])) {
            $balance = (float) $feerecord->balance;
            if (abs($sum - $balance) > self::SUM_TOLERANCE) {
                $errors['installmentamount[0]'] = get_string(
                    'errorinstallmentsumamountmismatch',
                    'local_financedepartment',
                    (object) [
                        'sum' => number_format($sum, 2),
                        'balance' => number_format($balance, 2),
                    ]
                );
            }
        }

        return $errors;
    }
}
