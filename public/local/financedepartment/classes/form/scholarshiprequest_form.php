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
 * Submit a scholarship request/nomination.
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
use local_financedepartment\scholarshiprequest_manager;
use local_hrdepartment\student_manager;

/**
 * Class scholarshiprequest_form
 *
 * REWRITTEN 2026-08-24: originally this form only had feerecordid/
 * scholarshipid/justification, with the student fixed via a hidden
 * field set by a separate "search for a student first" page
 * (pages/scholarshiprequests/pick.php). The user asked for the student
 * to be a real, selectable field directly on this form instead - so
 * pick.php was retired and this form now includes its own studentid
 * autocomplete (same MAX_STUDENT_OPTIONS/500-cap pattern as
 * feerecord_form's own studentid field), plus an optional supporting-
 * document upload and a renamed "Description" field (was
 * "Justification" - same underlying DB column/element name
 * `justification`, since renaming the column would need a schema
 * upgrade step for a purely cosmetic change; only the visible label
 * changed).
 *
 * customdata keys: presetstudentid (int, optional - pre-selects the
 * studentid autocomplete when this form is reached via a direct link
 * that already names a student, e.g. an old bookmarked
 * submit.php?studentid=X URL; the field is still fully editable).
 *
 * The feerecordid autocomplete lists every non-cancelled fee record
 * SYSTEM-WIDE (feerecord_manager::get_active_options(), each labelled
 * with its own student's name) rather than being scoped to whichever
 * student is chosen above - this plain moodleform has no JS to
 * re-populate a dependent select when another field changes. Likewise
 * scholarshipid lists every ACTIVE scholarship regardless of category.
 * validation() is what actually enforces that the three choices are a
 * real, allowed combination (feerecord belongs to the chosen student,
 * scholarship is eligible for the fee record's category, no duplicate
 * pending/approved request) - same server-side-validate-the-match
 * pattern as feerecord_bulkassign_form.
 */
class scholarshiprequest_form extends \moodleform {

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
            'scholarshipid',
            get_string('scholarship', 'local_financedepartment'),
            $this->get_scholarship_options(),
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('scholarshipid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('scholarshipid', 'requestscholarship', 'local_financedepartment');

        $mform->addElement(
            'textarea',
            'justification',
            get_string('description', 'local_financedepartment'),
            ['rows' => 4]
        );
        $mform->setType('justification', PARAM_TEXT);
        $mform->addRule('justification', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('justification', 'requestdescription', 'local_financedepartment');

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
        // Deliberately optional - no addRule('required') here, per the
        // user's explicit choice (2026-08-24 AskUserQuestion).

        $this->add_action_buttons(true, get_string('submitrequest', 'local_financedepartment'));
    }

    /**
     * Every ACTIVE scholarship, id => "name (category - amount)".
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
                : self::format_mmk($amount);
            $options[$record->id] = format_string($record->name) . ' (' . format_string($record->categoryname) . ' - ' . $amountlabel . ')';
        }

        return $options;
    }

    /**
     * Formats an MMK amount inline, without calling
     * local_financedepartment_format_money() (a lib.php function) - see
     * feerecord_manager::get_feestructure_options()'s docblock for why
     * form classes never call it directly (definition() runs during
     * __construct(), before lib.php is guaranteed loaded).
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
     * Server-side validation: the student must exist, the fee record
     * must belong to that student, the scholarship must actually be
     * eligible for that fee record's category
     * (scholarshiprequest_manager::is_eligible() - the real
     * program-restriction enforcement point), and there must not
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
        $scholarshipid = (int) $data['scholarshipid'];

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
