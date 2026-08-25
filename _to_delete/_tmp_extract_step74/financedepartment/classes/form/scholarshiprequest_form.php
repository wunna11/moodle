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
 * Submit a scholarship request/nomination for one student.
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
use local_financedepartment\scholarship_manager;
use local_financedepartment\scholarshiprequest_manager;

/**
 * Class scholarshiprequest_form
 *
 * customdata keys: studentid (int, required - the student this request
 * is being submitted for; picked on the previous page, same flow as
 * pages/feerecords/edit.php).
 *
 * The feerecordid select only offers this student's own non-cancelled
 * fee records, and the scholarshipid select offers every ACTIVE
 * scholarship regardless of category - which pairing is actually
 * allowed is a validation-time check
 * (scholarshiprequest_manager::is_eligible()), not a filtered list,
 * because this plain moodleform has no JS to re-populate the
 * scholarship select when the fee record changes. This mirrors how
 * feerecord_bulkassign_form validates the category/fee-structure match
 * server-side rather than filtering client-side.
 */
class scholarshiprequest_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $studentid = (int) ($this->_customdata['studentid'] ?? 0);

        $feerecordoptions = [];
        foreach (feerecord_manager::get_for_student($studentid) as $feerecord) {
            if ($feerecord->status === constants::FEE_STATUS_CANCELLED) {
                continue;
            }
            $feerecordoptions[$feerecord->id] = format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear)
                . ' (' . get_string('balance', 'local_financedepartment') . ': '
                . local_financedepartment_format_money($feerecord->balance) . ')';
        }

        $mform->addElement(
            'select',
            'feerecordid',
            get_string('feerecord', 'local_financedepartment'),
            $feerecordoptions
        );
        $mform->addRule('feerecordid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('feerecordid', 'requestfeerecord', 'local_financedepartment');

        $scholarshipoptions = [];
        foreach ($this->get_scholarship_options() as $id => $label) {
            $scholarshipoptions[$id] = $label;
        }

        $mform->addElement(
            'select',
            'scholarshipid',
            get_string('scholarship', 'local_financedepartment'),
            $scholarshipoptions
        );
        $mform->addRule('scholarshipid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('scholarshipid', 'requestscholarship', 'local_financedepartment');

        $mform->addElement(
            'textarea',
            'justification',
            get_string('justification', 'local_financedepartment'),
            ['rows' => 4]
        );
        $mform->setType('justification', PARAM_TEXT);
        $mform->addRule('justification', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('justification', 'justification', 'local_financedepartment');

        $mform->addElement('hidden', 'studentid', $studentid);
        $mform->setType('studentid', PARAM_INT);

        $this->add_action_buttons(true, get_string('submitrequest', 'local_financedepartment'));
    }

    /**
     * Every ACTIVE scholarship, id => "name (category - amount)". Shown
     * unfiltered by category since this form has no JS to react to the
     * chosen fee record - validation() is what actually enforces the
     * category match.
     *
     * @return array
     */
    protected function get_scholarship_options(): array {
        global $DB;

        $sql = "SELECT s.id, s.name, s.amounttype, s.amountvalue, cc.name AS categoryname
                  FROM {financedep_scholarship} s
                  JOIN {course_categories} cc ON cc.id = s.categoryid
                 WHERE s.status = :status
              ORDER BY s.name ASC";

        $records = $DB->get_records_sql($sql, ['status' => constants::SCHOLARSHIP_STATUS_ACTIVE]);

        $options = [];
        foreach ($records as $record) {
            $amount = (float) $record->amountvalue;
            $amountlabel = $record->amounttype === constants::AMOUNT_TYPE_PERCENTAGE
                ? (rtrim(rtrim(number_format($amount, 2), '0'), '.') . '%')
                : local_financedepartment_format_money($amount);
            $options[$record->id] = format_string($record->name) . ' (' . format_string($record->categoryname) . ' - ' . $amountlabel . ')';
        }

        return $options;
    }

    /**
     * Server-side validation: the fee record must belong to this
     * student, the scholarship must actually be eligible for that fee
     * record's category (scholarshiprequest_manager::is_eligible() -
     * the real program-restriction enforcement point), and there must
     * not already be a pending request for the same pairing.
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
        $scholarshipid = (int) $data['scholarshipid'];

        $feerecord = $feerecordid ? $DB->get_record('financedep_feerecord', ['id' => $feerecordid]) : false;
        if (!$feerecord || (int) $feerecord->studentid !== $studentid) {
            $errors['feerecordid'] = get_string('errorfeerecordnotfound', 'local_financedepartment');
        } else if ($feerecord->status === constants::FEE_STATUS_CANCELLED) {
            $errors['feerecordid'] = get_string('errorfeerecordcancelled', 'local_financedepartment');
        }

        if ($scholarshipid && !$DB->record_exists('financedep_scholarship', ['id' => $scholarshipid, 'status' => constants::SCHOLARSHIP_STATUS_ACTIVE])) {
            $errors['scholarshipid'] = get_string('errorscholarshipnotfound', 'local_financedepartment');
        }

        if (empty($errors['feerecordid']) && empty($errors['scholarshipid'])) {
            if (!scholarshiprequest_manager::is_eligible($scholarshipid, $feerecordid)) {
                $errors['scholarshipid'] = get_string('errorscholarshipnoteligible', 'local_financedepartment');
            } else if (scholarshiprequest_manager::has_pending_request($feerecordid, $scholarshipid)) {
                $errors['scholarshipid'] = get_string('errorscholarshippending', 'local_financedepartment');
            }
        }

        return $errors;
    }
}
