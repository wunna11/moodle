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
 * Sortable, filterable, paginated scholarship definition listing.
 *
 * Extends \core_table\sql_table - see feestructure_table.php's docblock
 * for why (this Moodle checkout's core class relocation, verified in
 * [[moodle-legacy-classes]] project memory).
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
 * Class scholarship_table
 */
class scholarship_table extends \core_table\sql_table {

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param int $categoryid 0 = any category
     * @param string $status '' = any status
     */
    public function __construct(string $uniqueid, int $categoryid = 0, string $status = '') {
        parent::__construct($uniqueid);

        $this->define_columns([
            'name', 'categoryname', 'amountvalue', 'status', 'timemodified', 'actions',
        ]);
        $this->define_headers([
            get_string('scholarshipname', 'local_financedepartment'),
            get_string('category', 'local_financedepartment'),
            get_string('amountvalue', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('lastupdated', 'local_financedepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'name', SORT_ASC);
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-financedepartment-scholarship-table');

        $fields = 's.id, s.name, s.categoryid, s.amounttype, s.amountvalue, s.status, s.timemodified, cc.name AS categoryname';
        $from = '{financedep_scholarship} s JOIN {course_categories} cc ON cc.id = s.categoryid';

        $where = '1 = 1';
        $params = [];

        if ($categoryid) {
            $where .= ' AND s.categoryid = :categoryid';
            $params['categoryid'] = $categoryid;
        }

        if ($status !== '') {
            $where .= ' AND s.status = :status';
            $params['status'] = $status;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);
    }

    /**
     * Renders the name column, linked to the scholarship's detail/history page.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_name($row): string {
        $url = new moodle_url('/local/financedepartment/pages/scholarships/view.php', ['id' => $row->id]);
        return \html_writer::link($url, format_string($row->name));
    }

    /**
     * Renders the category column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_categoryname($row): string {
        return format_string($row->categoryname);
    }

    /**
     * Renders the amount column - MMK or a percentage, depending on
     * amounttype.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_amountvalue($row): string {
        $amount = (float) $row->amountvalue;

        if ($row->amounttype === \local_financedepartment\constants::AMOUNT_TYPE_PERCENTAGE) {
            return rtrim(rtrim(number_format($amount, 2), '0'), '.') . '%';
        }

        return local_financedepartment_format_money($amount);
    }

    /**
     * Renders the status column as a coloured badge. Scholarship
     * statuses reuse the same active/inactive shape as fee structures,
     * but with their own lang strings/constants (constants::SCHOLARSHIP_STATUS_*)
     * since they're a semantically different field.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_status($row): string {
        $variant = ($row->status === \local_financedepartment\constants::SCHOLARSHIP_STATUS_ACTIVE) ? 'success' : 'secondary';

        return \html_writer::span(
            get_string('status_' . $row->status, 'local_financedepartment'),
            'badge badge-' . $variant
        );
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
            new moodle_url('/local/financedepartment/pages/scholarships/view.php', ['id' => $row->id]),
            get_string('view')
        );
        $actions[] = \html_writer::link(
            new moodle_url('/local/financedepartment/pages/scholarships/edit.php', ['id' => $row->id]),
            get_string('edit')
        );

        if ($row->status === \local_financedepartment\constants::SCHOLARSHIP_STATUS_ACTIVE) {
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/scholarships/deactivate.php', ['id' => $row->id]),
                get_string('deactivate', 'local_financedepartment')
            );
        } else {
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/scholarships/deactivate.php', ['id' => $row->id, 'reactivate' => 1]),
                get_string('reactivate', 'local_financedepartment')
            );
        }

        return implode(' | ', $actions);
    }
}
