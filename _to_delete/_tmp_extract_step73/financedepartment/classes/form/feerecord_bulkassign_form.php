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
 * Bulk-assign a fee structure to every student in a course category.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_financedepartment\feerecord_manager;
use local_financedepartment\feestructure_manager;

/**
 * Class feerecord_bulkassign_form
 *
 * Deliberately two plain selects rather than one filtered-by-category
 * dynamic select (which would need JS/ajax) - the category and fee
 * structure choices are cross-checked server-side in validation()
 * instead, same trade-off local_hrdepartment makes elsewhere for
 * simple admin forms.
 */
class feerecord_bulkassign_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement(
            'select',
            'categoryid',
            get_string('category', 'local_financedepartment'),
            feestructure_manager::get_category_options()
        );
        $mform->addRule('categoryid', get_string('required'), 'required', null, 'client');

        $mform->addElement(
            'select',
            'feestructureid',
            get_string('feestructure', 'local_financedepartment'),
            feerecord_manager::get_feestructure_options()
        );
        $mform->addRule('feestructureid', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('feestructureid', 'bulkassignfeestructure', 'local_financedepartment');

        $this->add_action_buttons(true, get_string('bulkassign', 'local_financedepartment'));
    }

    /**
     * Server-side validation: the chosen fee structure must actually
     * belong to the chosen category (the two selects aren't
     * JS-filtered - see class docblock).
     *
     * @param array $data
     * @param array $files
     * @return array errors keyed by element name
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        $categoryid = (int) $data['categoryid'];
        $feestructureid = (int) $data['feestructureid'];

        if ($categoryid && $feestructureid) {
            $matches = $DB->record_exists('financedep_feestructure', [
                'id' => $feestructureid,
                'categoryid' => $categoryid,
            ]);
            if (!$matches) {
                $errors['feestructureid'] = get_string('errorfeestructurecategorymismatch', 'local_financedepartment');
            }
        }

        return $errors;
    }
}
