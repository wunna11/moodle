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
 * Assign/edit a fee record form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\feerecord_manager;
use local_hrdepartment\student_manager;

/**
 * Class feerecord_form
 *
 * customdata keys: feerecordid (int, 0 = new).
 *
 * Both studentid and feestructureid are editable on edit as well as
 * create - Step 7.3 explicitly asks to "edit a mistakenly-assigned fee
 * record", and the mistake could be either the wrong student or the
 * wrong fee structure, so neither field is locked the way
 * local_hrdepartment linked-user fields sometimes are elsewhere in this
 * codebase.
 */
class feerecord_form extends \moodleform {

    /**
     * Maximum number of students loaded into the studentid autocomplete.
     * This plugin depends on local_hrdepartment (see version.php), so
     * student search reuses \local_hrdepartment\student_manager rather
     * than duplicating a second "who is a student" query here - but that
     * class's get_students() still returns a plain in-memory list, not
     * an ajax-backed search, so this caps how many load onto the page at
     * once. Fine for a small/medium site; if the student list grows past
     * this, this field needs to become an ajax-backed selector instead.
     */
    const MAX_STUDENT_OPTIONS = 500;

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $feerecordid = $this->_customdata['feerecordid'] ?? 0;

        $students = student_manager::get_students('', 0, 'active', 0, self::MAX_STUDENT_OPTIONS);
        $useroptions = [];
        foreach ($students as $student) {
            $useroptions[$student->id] = $student->fullname . ' (' . $student->email . ')';
        }

        $mform->addElement(
            'autocomplete',
            'studentid',
            get_string('student', 'local_financedepartment'),
            $useroptions,
            ['noselectionstring' => get_string('choosedots')]
        );
        $mform->addRule('studentid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('studentid', 'student', 'local_financedepartment');

        $mform->addElement(
            'select',
            'feestructureid',
            get_string('feestructure', 'local_financedepartment'),
            feerecord_manager::get_feestructure_options()
        );
        $mform->addRule('feestructureid', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'feerecordid', $feerecordid);
        $mform->setType('feerecordid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: student and fee structure must exist, and
     * the student must not already hold a non-cancelled fee record for
     * the same fee structure.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $studentid = (int) $data['studentid'];
        $feestructureid = (int) $data['feestructureid'];
        $feerecordid = (int) $data['feerecordid'];

        if (!$studentid || !$DB->record_exists('user', ['id' => $studentid, 'deleted' => 0])) {
            $errors['studentid'] = get_string('required');
        }

        if (!$feestructureid || !$DB->record_exists('financedep_feestructure', ['id' => $feestructureid])) {
            $errors['feestructureid'] = get_string('required');
        }

        if ($studentid && $feestructureid
                && feerecord_manager::has_active_assignment($studentid, $feestructureid, $feerecordid)) {
            $errors['feestructureid'] = get_string('errorduplicatefeerecord', 'local_financedepartment');
        }

        return $errors;
    }
}
