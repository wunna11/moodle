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
 * Add/edit fee structure form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\constants;
use local_financedepartment\feestructure_manager;

/**
 * Class feestructure_form
 *
 * customdata keys: feestructureid (int, 0 = new).
 */
class feestructure_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $feestructureid = $this->_customdata['feestructureid'] ?? 0;
        $iscreate = empty($feestructureid);

        $mform->addElement(
            'select',
            'categoryid',
            get_string('category', 'local_financedepartment'),
            feestructure_manager::get_category_options()
        );
        $mform->addRule('categoryid', get_string('required'), 'required', null, 'client');

        // 2026-09-12: replaced the old single free-text "academic year"
        // field with a From/To year-range picker, plus a "custom label"
        // fallback for a period that isn't a plain year range (e.g. one
        // described by specific months) - the user explicitly raised
        // that case when asking for this change. The underlying stored
        // value (financedep_feestructure.academicyear, a char(20)) is
        // UNCHANGED - see compose_academicyear()/get_data()/set_data()
        // below, which convert between the picker/custom UI and that
        // single stored string, so every other manager/table/export that
        // reads academicyear as free text needs no changes at all.
        $currentyear = (int) date('Y');
        $yearoptions = [];
        for ($year = $currentyear - 5; $year <= $currentyear + 10; $year++) {
            $yearoptions[$year] = (string) $year;
        }

        $mform->addElement('select', 'academicyearfrom', get_string('academicyearfrom', 'local_financedepartment'), $yearoptions);
        $mform->setType('academicyearfrom', PARAM_INT);
        $mform->setDefault('academicyearfrom', $currentyear);

        $mform->addElement('select', 'academicyearto', get_string('academicyearto', 'local_financedepartment'), $yearoptions);
        $mform->setType('academicyearto', PARAM_INT);
        $mform->setDefault('academicyearto', $currentyear + 1);
        $mform->addHelpButton('academicyearto', 'academicyear', 'local_financedepartment');

        $mform->addElement('advcheckbox', 'academicyearcustom', '', get_string('academicyearcustom', 'local_financedepartment'));
        $mform->setType('academicyearcustom', PARAM_INT);

        $mform->addElement('text', 'academicyeartext', get_string('academicyeartext', 'local_financedepartment'));
        $mform->setType('academicyeartext', PARAM_TEXT);
        $mform->addRule('academicyeartext', get_string('erroracademicyeartoolong', 'local_financedepartment'), 'maxlength', 20, 'client');

        $mform->hideIf('academicyearfrom', 'academicyearcustom', 'checked');
        $mform->hideIf('academicyearto', 'academicyearcustom', 'checked');
        $mform->hideIf('academicyeartext', 'academicyearcustom', 'notchecked');

        $mform->addElement('text', 'amount', get_string('amount', 'local_financedepartment'));
        $mform->setType('amount', PARAM_FLOAT);
        $mform->addRule('amount', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('amount', 'amount', 'local_financedepartment');

        $mform->addElement('textarea', 'description', get_string('description', 'local_financedepartment'), ['rows' => 3]);
        $mform->setType('description', PARAM_TEXT);

        if (!$iscreate) {
            $mform->addElement(
                'select',
                'status',
                get_string('status', 'local_financedepartment'),
                [
                    constants::FEESTRUCTURE_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
                    constants::FEESTRUCTURE_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
                ]
            );
        }

        $mform->addElement('hidden', 'feestructureid', $feestructureid);
        $mform->setType('feestructureid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Server-side validation: amount must be a non-negative number, and
     * no other ACTIVE fee structure may already exist for the same
     * category + academic year.
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!is_numeric($data['amount']) || (float) $data['amount'] < 0) {
            $errors['amount'] = get_string('erroramountnegative', 'local_financedepartment');
        }

        $categoryid = (int) $data['categoryid'];
        if ($categoryid && !$DB->record_exists('course_categories', ['id' => $categoryid])) {
            $errors['categoryid'] = get_string('required');
        }

        if (!empty($data['academicyearcustom'])) {
            if (trim($data['academicyeartext'] ?? '') === '') {
                $errors['academicyeartext'] = get_string('required');
            }
        } else if ((int) ($data['academicyearfrom'] ?? 0) > (int) ($data['academicyearto'] ?? 0)) {
            $errors['academicyearto'] = get_string('erroracademicyearrange', 'local_financedepartment');
        }

        $academicyear = self::compose_academicyear($data);
        $feestructureid = (int) $data['feestructureid'];
        if ($academicyear !== '' && $categoryid
                && feestructure_manager::has_active_duplicate($categoryid, $academicyear, $feestructureid)) {
            $errors['academicyearcustom'] = get_string('errorduplicatefeestructure', 'local_financedepartment');
        }

        return $errors;
    }

    /**
     * Composes the single free-text academicyear value that gets stored
     * (financedep_feestructure.academicyear) from either the From/To year
     * picker (e.g. 2026 + 2027 -> "2026-2027", or 2026 + 2026 -> just
     * "2026") or the custom-label fallback, matching whichever the
     * academicyearcustom checkbox selects. Shared by validation() (to
     * duplicate-check the value that will actually be saved) and
     * get_data() (to hand callers the same 'academicyear' property this
     * form always returned before this UI change).
     *
     * @param array $data raw submitted form data
     * @return string
     */
    protected static function compose_academicyear(array $data): string {
        if (!empty($data['academicyearcustom'])) {
            return trim((string) ($data['academicyeartext'] ?? ''));
        }

        $from = (int) ($data['academicyearfrom'] ?? 0);
        $to = (int) ($data['academicyearto'] ?? 0);
        if (!$from || !$to) {
            return '';
        }

        return $from === $to ? (string) $from : "{$from}-{$to}";
    }

    /**
     * Overridden so every existing caller (feestructure_manager::
     * create()/update(), which read $data->academicyear directly) keeps
     * working unchanged - the picker/custom UI's several form fields are
     * composed back into that one property here.
     *
     * @return \stdClass|null
     */
    public function get_data() {
        $data = parent::get_data();
        if ($data !== null) {
            $data->academicyear = self::compose_academicyear((array) $data);
        }
        return $data;
    }

    /**
     * Overridden so editing an existing fee structure re-populates the
     * From/To picker (or the custom-label fallback) from the single
     * stored academicyear string, instead of the picker always resetting
     * to today's default years.
     *
     * @param \stdClass|array $defaultvalues
     * @return void
     */
    public function set_data($defaultvalues) {
        $values = is_object($defaultvalues) ? (array) $defaultvalues : $defaultvalues;

        if (array_key_exists('academicyear', $values) && trim((string) $values['academicyear']) !== '') {
            $value = trim((string) $values['academicyear']);
            if (preg_match('/^(\d{4})-(\d{4})$/', $value, $matches)) {
                $values['academicyearfrom'] = (int) $matches[1];
                $values['academicyearto'] = (int) $matches[2];
                $values['academicyearcustom'] = 0;
                $values['academicyeartext'] = '';
            } else if (preg_match('/^\d{4}$/', $value)) {
                $values['academicyearfrom'] = (int) $value;
                $values['academicyearto'] = (int) $value;
                $values['academicyearcustom'] = 0;
                $values['academicyeartext'] = '';
            } else {
                $values['academicyearcustom'] = 1;
                $values['academicyeartext'] = $value;
            }
        }

        parent::set_data($values);
    }
}
