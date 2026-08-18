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
 * Sortable, searchable, paginated lecturer listing.
 *
 * UNUSED as of the Lecturers directory redesign: lecturer/index.php now
 * renders local_hrdepartment\output\lecturers_directory (a card grid with
 * a department + status filter bar, matching the Students directory)
 * instead of this table_sql class. Left in place rather than deleted -
 * remove it in a future cleanup pass if nothing else starts using it.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use local_hrdepartment\constants;
use local_hrdepartment\user_account_sync;
use moodle_url;

/**
 * Class lecturers_table
 */
class lecturers_table extends \table_sql {

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param string $search
     */
    public function __construct(string $uniqueid, string $search = '') {
        parent::__construct($uniqueid);

        $this->define_columns([
            'fullname', 'employeecode', 'departmentname', 'designation',
            'qualification', 'specialization', 'employmentstatus', 'actions',
        ]);
        $this->define_headers([
            get_string('fullname'),
            get_string('employeecode', 'local_hrdepartment'),
            get_string('department', 'local_hrdepartment'),
            get_string('designation', 'local_hrdepartment'),
            get_string('qualification', 'local_hrdepartment'),
            get_string('specialization', 'local_hrdepartment'),
            get_string('employmentstatus', 'local_hrdepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'lastname', SORT_ASC);
        $this->no_sorting('qualification');
        $this->no_sorting('specialization');
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-hrdepartment-lecturers-table');

        $fields = "e.id, e.userid, e.employeecode, e.designation, e.employmentstatus,
                   u.firstname, u.lastname,
                   d.name AS departmentname,
                   ld.qualification, ld.specialization";
        $from = "{hrdep_employee} e
                 JOIN {user} u ON u.id = e.userid
            LEFT JOIN {hrdep_department} d ON d.id = e.departmentid
            LEFT JOIN {hrdep_lecturerdetails} ld ON ld.employeeid = e.id";

        $where = 'e.type = :type';
        $params = ['type' => constants::EMPLOYEE_TYPE_LECTURER];

        if ($search !== '') {
            $where .= ' AND (' . $this->get_db()->sql_like('u.firstname', ':search1', false) . '
                          OR ' . $this->get_db()->sql_like('u.lastname', ':search2', false) . '
                          OR ' . $this->get_db()->sql_like('e.employeecode', ':search3', false) . ')';
            $like = '%' . $this->get_db()->sql_like_escape($search) . '%';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);
    }

    /**
     * Convenience accessor to the global $DB used for sql_like().
     *
     * @return \moodle_database
     */
    protected function get_db(): \moodle_database {
        global $DB;
        return $DB;
    }

    /**
     * Renders the fullname column as a link to the lecturer's profile.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_fullname($row): string {
        $url = new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $row->id]);
        return \html_writer::link($url, fullname($row));
    }

    /**
     * Renders the employment status column as a coloured badge.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_employmentstatus($row): string {
        // The stored employmentstatus (active/inactive/terminated) can
        // drift from reality if the Moodle account was suspended or
        // unsuspended directly via Site administration > Users, bypassing
        // this plugin. Show a single Active/Suspended label driven by the
        // live Moodle account state, rather than the stored HR status.
        $active = !user_account_sync::is_account_suspended($row->userid);

        if ($active) {
            return \html_writer::span(get_string('status_active', 'local_hrdepartment'), 'badge badge-success');
        }

        return \html_writer::span(get_string('status_suspended', 'local_hrdepartment'), 'badge badge-danger');
    }

    /**
     * Renders the department column, falling back to a dash when unset.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_departmentname($row): string {
        return $row->departmentname !== null ? format_string($row->departmentname) : '-';
    }

    /**
     * Renders the row action links (view, edit, assign course, deactivate/reactivate).
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        global $OUTPUT;

        $actions = [];

        $actions[] = \html_writer::link(
            new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $row->id]),
            get_string('view')
        );
        $actions[] = \html_writer::link(
            new moodle_url('/local/hrdepartment/lecturer/edit.php', ['id' => $row->id]),
            get_string('edit')
        );
        $actions[] = \html_writer::link(
            new moodle_url('/local/hrdepartment/lecturer/courseassign.php', ['id' => $row->id]),
            get_string('assigncourse', 'local_hrdepartment')
        );

        // Match the Active/Suspended label in col_employmentstatus(): base
        // the action offered on the live Moodle account state, not just
        // the stored HR status.
        if (!user_account_sync::is_account_suspended($row->userid)) {
            $actions[] = \html_writer::link(
                new moodle_url('/local/hrdepartment/lecturer/delete.php', ['id' => $row->id]),
                get_string('deactivate', 'local_hrdepartment')
            );
        } else {
            $actions[] = \html_writer::link(
                new moodle_url('/local/hrdepartment/lecturer/delete.php', ['id' => $row->id, 'reactivate' => 1]),
                get_string('reactivate', 'local_hrdepartment')
            );
        }

        return implode(' | ', $actions);
    }
}
