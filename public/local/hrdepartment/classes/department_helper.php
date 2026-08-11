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
 * Shared department lookups, used by both the lecturer and staff forms.
 *
 * Deliberately a class method rather than a lib.php function: lib.php is
 * only guaranteed to be loaded once Moodle builds the page navigation
 * (inside $OUTPUT->header()), but form definitions run during form
 * construction, before header() is called. A lib.php global here would
 * be undefined at that point.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class department_helper
 */
class department_helper {

    /**
     * Returns department options for select/autocomplete menus, keyed by id.
     *
     * @return array
     */
    public static function get_options(): array {
        global $DB;

        $departments = $DB->get_records('hrdep_department', null, 'name ASC', 'id, name');

        $options = [];
        foreach ($departments as $department) {
            $options[$department->id] = $department->name;
        }

        return $options;
    }
}
