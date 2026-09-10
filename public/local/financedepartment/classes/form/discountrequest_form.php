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
 * Submit a manual/hardship discount request.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;
use local_financedepartment\discount_manager;
use local_financedepartment\discountrequest_manager;
use local_financedepartment\feerecord_manager;

/**
 * Class discountrequest_form
 *
 * REWRITTEN AGAIN 2026-09-09, mirroring scholarshiprequest_form's own
 * rewrite the same day - see that form's docblock for the full
 * rationale. Submitting a discount request is now student self-service:
 * the studentid field is gone, the student is always the logged-in
 * viewer (pages/discountrequests/submit.php passes it in as customdata
 * `studentid`), and feerecordid is scoped to that student's own fee
 * records only (feerecord_manager::get_active_options_for_student()).
 * db/access.php's new local/financedepartment:submitdiscountrequest
 * capability gates who can reach this form.
 *
 * The one structural difference from scholarshiprequest_form: the
 * discountid autocomplete lists every ACTIVE discount regardless of
 * category (discount_manager::get_active_options()), since a discount
 * has no category restriction to narrow it down (see discount_manager's
 * class docblock) - validation() still enforces the
 * fee-record-belongs-to-student and no-duplicate-pending-request checks.
 *
 * customdata keys: studentid (int, required - always $USER->id, set by
 * submit.php).
 */
class discountrequest_form extends \moodleform {

    /** @var string[] accepted supporting-document file extensions. */
    const ATTACHMENT_TYPES = ['.pdf', '.jpg', '.jpeg', '.png'];

    /** @var int max number of supporting-document files (raised from 1 to 5, 2026-09-10, per user request - see scholarshiprequest_form's matching change). */
    const ATTACHMENT_MAXFILES = 5;

    /**
     * Form definition.
     */
    public function definition() {
        global $USER;

        $mform = $this->_form;
        $studentid = (int) ($this->_customdata['studentid'] ?? 0);

        // The student is always the logged-in viewer - see
        // scholarshiprequest_form::definition()'s matching block for the
        // full rationale (same pattern, mirrored here).
        $mform->addElement('static', 'studentiddisplay', get_string('student', 'local_financedepartment'), fullname($USER));
        $mform->addElement('hidden', 'studentid', $studentid);
        $mform->setType('studentid', PARAM_INT);

        $mform->addElement(
            'autocomplete',
            'feerecordid',
            get_string('feerecord', 'local_financedepartment'),
            feerecord_manager::get_active_options_for_student($studentid),
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('feerecordid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('feerecordid', 'requestfeerecord', 'local_financedepartment');

        $mform->addElement(
            'autocomplete',
            'discountid',
            get_string('discount', 'local_financedepartment'),
            discount_manager::get_active_options(),
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('discountid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('discountid', 'requestdiscount', 'local_financedepartment');

        $mform->addElement(
            'textarea',
            'justification',
            get_string('description', 'local_financedepartment'),
            ['rows' => 4]
        );
        $mform->setType('justification', PARAM_TEXT);
        $mform->addRule('justification', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('justification', 'requestdiscountdescription', 'local_financedepartment');

        $mform->addElement(
            'filemanager',
            'attachment',
            get_string('attachment', 'local_financedepartment'),
            null,
            [
                'subdirs' => 0,
                'maxfiles' => self::ATTACHMENT_MAXFILES,
                'accepted_types' => self::ATTACHMENT_TYPES,
            ]
        );
        $mform->addHelpButton('attachment', 'attachment', 'local_financedepartment');
        // Deliberately optional, same choice as scholarshiprequest_form's own attachment field.

        $this->add_action_buttons(true, get_string('submitrequest', 'local_financedepartment'));
    }

    /**
     * Server-side validation: the fee record must belong to $USER (the
     * hidden studentid field is never user-editable, but is still
     * re-checked here defensively rather than trusted blindly) and not
     * be cancelled, the discount must be active, and there must not
     * already be a pending/approved request for the same pairing.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $studentid = (int) $data['studentid'];
        $feerecordid = (int) $data['feerecordid'];
        $discountid = (int) $data['discountid'];

        $feerecord = $feerecordid ? $DB->get_record('financedep_feerecord', ['id' => $feerecordid]) : false;
        if (!$feerecord) {
            $errors['feerecordid'] = get_string('errorfeerecordnotfound', 'local_financedepartment');
        } else if ((int) $feerecord->studentid !== $studentid) {
            $errors['feerecordid'] = get_string('errorfeerecordwrongstudent', 'local_financedepartment');
        } else if ($feerecord->status === constants::FEE_STATUS_CANCELLED) {
            $errors['feerecordid'] = get_string('errorfeerecordcancelled', 'local_financedepartment');
        }

        if ($discountid && !$DB->record_exists('financedep_discount', ['id' => $discountid, 'status' => constants::DISCOUNT_STATUS_ACTIVE])) {
            $errors['discountid'] = get_string('errordiscountnotfound', 'local_financedepartment');
        }

        if (empty($errors['feerecordid']) && empty($errors['discountid'])) {
            if (!discountrequest_manager::is_eligible($discountid, $feerecordid)) {
                $errors['discountid'] = get_string('errordiscountnoteligible', 'local_financedepartment');
            } else if (discountrequest_manager::has_pending_request($feerecordid, $discountid)) {
                $errors['discountid'] = get_string('errordiscountpending', 'local_financedepartment');
            }
        }

        return $errors;
    }
}
