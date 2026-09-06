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
use local_hrdepartment\student_manager;

/**
 * Class discountrequest_form
 *
 * Mirrors scholarshiprequest_form's shape exactly (student/fee record/
 * item/description/optional attachment all on one self-contained page) -
 * see that form's docblock for the full rationale. The one structural
 * difference: the discountid autocomplete lists every ACTIVE discount
 * regardless of category (discount_manager::get_active_options()),
 * since a discount has no category restriction to narrow it down (see
 * discount_manager's class docblock) - validation() still enforces the
 * fee-record-belongs-to-student and no-duplicate-pending-request checks.
 *
 * customdata keys: presetstudentid (int, optional).
 */
class discountrequest_form extends \moodleform {

    /** @var int cap on the studentid autocomplete - see feerecord_form's own constant for the same caveat. */
    const MAX_STUDENT_OPTIONS = 500;

    /** @var string[] accepted supporting-document file extensions. */
    const ATTACHMENT_TYPES = ['.pdf', '.jpg', '.jpeg', '.png'];

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $presetstudentid = (int) ($this->_customdata['presetstudentid'] ?? 0);

        $students = student_manager::get_students('', 0, 'active', 0, self::MAX_STUDENT_OPTIONS);
        $studentoptions = [];
        foreach ($students as $student) {
            $studentoptions[$student->id] = $student->fullname . ' (' . $student->email . ')';
        }

        $mform->addElement(
            'autocomplete',
            'studentid',
            get_string('student', 'local_financedepartment'),
            $studentoptions,
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('studentid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('studentid', 'student', 'local_financedepartment');
        if ($presetstudentid) {
            $mform->setDefault('studentid', $presetstudentid);
        }

        $mform->addElement(
            'autocomplete',
            'feerecordid',
            get_string('feerecord', 'local_financedepartment'),
            feerecord_manager::get_active_options(),
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
                'maxfiles' => 1,
                'accepted_types' => self::ATTACHMENT_TYPES,
            ]
        );
        $mform->addHelpButton('attachment', 'attachment', 'local_financedepartment');
        // Deliberately optional, same choice as scholarshiprequest_form's own attachment field.

        $this->add_action_buttons(true, get_string('submitrequest', 'local_financedepartment'));
    }

    /**
     * Server-side validation: the student must exist, the fee record
     * must belong to that student and not be cancelled, the discount
     * must be active, and there must not already be a pending/approved
     * request for the same pairing.
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

        if (!$studentid || !$DB->record_exists('user', ['id' => $studentid, 'deleted' => 0])) {
            $errors['studentid'] = get_string('required');
        }

        $feerecord = $feerecordid ? $DB->get_record('financedep_feerecord', ['id' => $feerecordid]) : false;
        if (!$feerecord) {
            $errors['feerecordid'] = get_string('errorfeerecordnotfound', 'local_financedepartment');
        } else if (empty($errors['studentid']) && (int) $feerecord->studentid !== $studentid) {
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
