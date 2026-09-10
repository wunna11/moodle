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
use local_financedepartment\scholarshiprequest_manager;

/**
 * Class scholarshiprequest_form
 *
 * CHANGED 2026-09-10 (v2026091004/0.8.0): the user reported the fee
 * record field shouldn't need to be filled in at all when a student
 * submits a scholarship request - it's gone entirely. Confirmed via
 * AskUserQuestion: no fee record linkage anywhere in this workflow any
 * more (financedep_scholarshipreq.feerecordid is now nullable and
 * always stored null by scholarshiprequest_manager::submit()), and the
 * old category-eligibility restriction is removed too (any ACTIVE
 * scholarship may now be requested by any eligible student - finance
 * staff use their own judgement at review time). See
 * scholarshiprequest_manager's own class docblock for the full
 * rationale and the backward-compatibility handling for requests
 * submitted before this change. The optional attachment now also
 * accepts up to 5 files (was 1), per the same request.
 *
 * REWRITTEN AGAIN 2026-09-09: this form used to be reached only by
 * finance staff (managescholarships), who picked a target student via a
 * studentid autocomplete - meaning a student could never nominate
 * themselves, only finance staff could nominate on their behalf. The
 * user reported this was backwards: a scholarship request should be
 * something a STUDENT submits for themselves. The studentid field is
 * now gone entirely - the student is always the logged-in viewer
 * (pages/scholarshiprequests/submit.php passes it in as customdata
 * `studentid`, rendered as a hidden field plus a read-only static
 * display, never editable). See [[financedepartment-schema]] project
 * memory for the full history; db/access.php's new
 * local/financedepartment:submitscholarshiprequest capability is what
 * gates who can even reach this form.
 *
 * Earlier revision (2026-08-24) kept for context: originally this form
 * only had feerecordid/scholarshipid/justification, with the student
 * fixed via a hidden field set by a separate "search for a student
 * first" page (pages/scholarshiprequests/pick.php, long since retired).
 * That revision added the optional supporting-document upload and
 * renamed "Justification" to "Description" (same underlying DB column/
 * element name `justification` - only the visible label changed).
 *
 * customdata keys: studentid (int, required - always $USER->id, set by
 * submit.php), presetscholarshipid (int, optional, added 2026-09-10 -
 * pre-selects the scholarshipid element when arriving from
 * pages/scholarships/browse.php's "Request this" link; 0 means no
 * preselection).
 *
 * validation() enforces that the scholarship is ACTIVE and there isn't
 * already a pending/approved request for the same student+scholarship
 * pairing (scholarshiprequest_manager::has_pending_request()).
 */
class scholarshiprequest_form extends \moodleform {

    /** @var string[] accepted supporting-document file extensions. */
    const ATTACHMENT_TYPES = ['.pdf', '.jpg', '.jpeg', '.png'];

    /** @var int max number of supporting-document files (raised from 1 to 5, 2026-09-10, per user request). */
    const ATTACHMENT_MAXFILES = 5;

    /**
     * Form definition.
     */
    public function definition() {
        global $USER;

        $mform = $this->_form;
        $studentid = (int) ($this->_customdata['studentid'] ?? 0);

        // The student is always the logged-in viewer - shown as a
        // read-only static field for clarity, carried as a hidden field
        // so get_data()->studentid still comes through unchanged for
        // scholarshiprequest_manager::submit() (see that method's
        // docblock: it reads $data->studentid directly).
        $mform->addElement('static', 'studentiddisplay', get_string('student', 'local_financedepartment'), fullname($USER));
        $mform->addElement('hidden', 'studentid', $studentid);
        $mform->setType('studentid', PARAM_INT);

        $scholarshipoptions = $this->get_scholarship_options();
        $mform->addElement(
            'autocomplete',
            'scholarshipid',
            get_string('scholarship', 'local_financedepartment'),
            $scholarshipoptions,
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('scholarshipid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('scholarshipid', 'requestscholarship', 'local_financedepartment');

        // Optional preselection from pages/scholarships/browse.php's
        // "Request this" link (2026-09-10) - only applied if that
        // scholarship id is actually one of the options just built above,
        // so a stale/invalid link never silently selects nothing visible.
        $presetscholarshipid = (int) ($this->_customdata['presetscholarshipid'] ?? 0);
        if ($presetscholarshipid && array_key_exists($presetscholarshipid, $scholarshipoptions)) {
            $mform->setDefault('scholarshipid', $presetscholarshipid);
        }

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
                'maxfiles' => self::ATTACHMENT_MAXFILES,
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
     * Server-side validation: the scholarship must be ACTIVE, and there
     * must not already be a pending/approved request for the same
     * student+scholarship pairing. CHANGED 2026-09-10 (v2026091004/
     * 0.8.0): no fee record to validate any more (the field is gone -
     * see this class's own docblock), and the old category-eligibility
     * check (scholarshiprequest_manager::is_eligible(), since removed)
     * is no longer performed.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $studentid = (int) $data['studentid'];
        $scholarshipid = (int) $data['scholarshipid'];

        if ($scholarshipid && !$DB->record_exists('financedep_scholarship', ['id' => $scholarshipid, 'status' => constants::SCHOLARSHIP_STATUS_ACTIVE])) {
            $errors['scholarshipid'] = get_string('errorscholarshipnotfound', 'local_financedepartment');
        }

        // Already-paid guard, added 2026-09-10 per the user's explicit
        // request - see scholarshiprequest_manager::has_paid_in_category()'s
        // own docblock for the full rationale. Checked against the
        // scholarship's own categoryid (not a fee record - requests no
        // longer carry one, see this form's class docblock).
        if (empty($errors['scholarshipid']) && $scholarshipid) {
            $categoryid = (int) $DB->get_field('financedep_scholarship', 'categoryid', ['id' => $scholarshipid]);
            if ($categoryid && scholarshiprequest_manager::has_paid_in_category($studentid, $categoryid)) {
                $errors['scholarshipid'] = get_string('errorscholarshipalreadypaid', 'local_financedepartment');
            }
        }

        if (empty($errors['scholarshipid']) && scholarshiprequest_manager::has_pending_request($studentid, $scholarshipid)) {
            $errors['scholarshipid'] = get_string('errorscholarshippending', 'local_financedepartment');
        }

        return $errors;
    }
}
