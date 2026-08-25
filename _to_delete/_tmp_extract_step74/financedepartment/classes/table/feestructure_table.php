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
 * Sortable, filterable, paginated fee structure listing.
 *
 * Extends \core_table\sql_table rather than the legacy global
 * \table_sql. On this Moodle checkout the real implementation lives at
 * lib/table/classes/{sql_table,flexible_table}.php under the core_table
 * namespace - lib/tablelib.php itself now only holds the legacy
 * TABLE_VAR_/TABLE_P_/TABLE_SHOW_ALL_PAGE_SIZE constants (still
 * require_once'd below for those). The bare \table_sql/\flexible_table/
 * \html_writer/\core_user names still work too - confirmed via
 * lib/db/legacyclasses.php + a class_alias() call at the bottom of each
 * relocated class's file, Moodle's own BC mechanism for exactly this
 * kind of core rename - so this is a style choice (prefer the current
 * namespaced class in new code), not a correctness requirement. See
 * [[financedepartment-schema]] project memory for how this was verified.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use moodle_url;

/**
 * Class feestructure_table
 */
class feestructure_table extends \core_table\sql_table {

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param int $categoryid 0 = any category
     * @param string $academicyear '' = any academic year
     * @param string $status '' = any status
     */
    public function __construct(string $uniqueid, int $categoryid = 0, string $academicyear = '', string $status = '') {
        parent::__construct($uniqueid);

        $this->define_columns([
            'categoryname', 'academicyear', 'amount', 'status', 'timemodified', 'actions',
        ]);
        $this->define_headers([
            get_string('category', 'local_financedepartment'),
            get_string('academicyear', 'local_financedepartment'),
            get_string('amount', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('lastupdated', 'local_financedepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'academicyear', SORT_DESC);
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-financedepartment-feestructure-table');

        $fields = 'f.id, f.categoryid, f.academicyear, f.amount, f.status, f.timemodified, cc.name AS categoryname';
        $from = '{financedep_feestructure} f JOIN {course_categories} cc ON cc.id = f.categoryid';

        $where = '1 = 1';
        $params = [];

        if ($categoryid) {
            $where .= ' AND f.categoryid = :categoryid';
            $params['categoryid'] = $categoryid;
        }

        if ($academicyear !== '') {
            $where .= ' AND ' . $this->get_db()->sql_like('f.academicyear', ':academicyear', false);
            $params['academicyear'] = '%' . $this->get_db()->sql_like_escape($academicyear) . '%';
        }

        if ($status !== '') {
            $where .= ' AND f.status = :status';
            $params['status'] = $status;
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
     * Renders the category column, format_string()'d and linked to the
     * fee structure's detail/history page.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_categoryname($row): string {
        $url = new moodle_url('/local/financedepartment/pages/fees/view.php', ['id' => $row->id]);
        return \html_writer::link($url, format_string($row->categoryname));
    }

    /**
     * Renders the amount column, MMK-formatted.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_amount($row): string {
        return local_financedepartment_format_money($row->amount);
    }

    /**
     * Renders the status column as a coloured badge.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_status($row): string {
        return local_financedepartment_feestructure_status_badge($row->status);
    }

    /**
     * Renders the last-updated column as a user date.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_timemodified($row): string {
        return userdate($row->timemodified, get_string('strftimedatetimeshort', 'core_langconfig'));
    }

    /**
     * Renders the row action links (view, edit, deactivate/reactivate).
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        $actions = [];

        $actions[] = \html_writer::link(
            new moodle_url('/local/financedepartment/pages/fees/view.php', ['id' => $row->id]),
            get_string('view')
        );
        $actions[] = \html_writer::link(
            new moodle_url('/local/financedepartment/pages/fees/edit.php', ['id' => $row->id]),
            get_string('edit')
        );

        if ($row->status === \local_financedepartment\constants::FEESTRUCTURE_STATUS_ACTIVE) {
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/fees/deactivate.php', ['id' => $row->id]),
                get_string('deactivate', 'local_financedepartment')
            );
        } else {
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/fees/deactivate.php', ['id' => $row->id, 'reactivate' => 1]),
                get_string('reactivate', 'local_financedepartment')
            );
        }

        return implode(' | ', $actions);
    }
}
