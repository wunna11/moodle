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
 * Sortable, filterable, paginated fee-status listing across EVERY
 * student - Step 7.9's "finance staff list view: all students' fee
 * status, searchable/filterable by category, year, status". Previously
 * only a per-student search-then-view flow existed
 * (pages/feerecords/index.php, whose own docblock explicitly named this
 * table as "Step 7.9's job").
 *
 * Extends \core_table\sql_table - see feestructure_table.php's docblock
 * for why (this Moodle checkout's core class relocation).
 *
 * The status filter deliberately matches what
 * local_financedepartment_feerecord_status_badge() DISPLAYS, not the raw
 * financedep_feerecord.status column - "Overdue" is a live-computed
 * state (Step 7.8) that is never persisted to that column, so filtering
 * by it means checking for an unpaid/partially-paid record with at
 * least one overdue installment schedule line on its ACTIVE plan (see
 * the overdue EXISTS subquery below), and filtering by "Unpaid"/
 * "Partially paid" means excluding records that are actually overdue -
 * otherwise the same record could appear to match two different status
 * filters at once, or a record shown as "Overdue" in the Status column
 * could fail to show up when filtering by Overdue. This mirrors
 * feerecord_manager::display_status()'s exact logic in SQL form.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use moodle_url;
use local_financedepartment\constants;

/**
 * Class feerecord_table
 */
class feerecord_table extends \core_table\sql_table {

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param int $categoryid 0 = any category
     * @param string $academicyear '' = any academic year
     * @param string $status '' = any status, otherwise one of constants::FEE_STATUS_* (as DISPLAYED, see class docblock)
     * @param string $search '' = no filter, otherwise matched against the student's name/email
     */
    public function __construct(
        string $uniqueid,
        int $categoryid = 0,
        string $academicyear = '',
        string $status = '',
        string $search = ''
    ) {
        parent::__construct($uniqueid);

        $this->define_columns([
            'lastname', 'categoryname', 'academicyear', 'totalamount', 'balance', 'status', 'actions',
        ]);
        $this->define_headers([
            get_string('student', 'local_financedepartment'),
            get_string('category', 'local_financedepartment'),
            get_string('academicyear', 'local_financedepartment'),
            get_string('totalamount', 'local_financedepartment'),
            get_string('balance', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'lastname', SORT_ASC);
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-financedepartment-feerecord-table');

        $db = $this->get_db();

        $fields = 'r.*, u.firstname, u.lastname, u.email, f.academicyear, cc.name AS categoryname';
        $from = '{financedep_feerecord} r
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid';

        $where = '1 = 1';
        $params = [];

        if ($categoryid) {
            $where .= ' AND f.categoryid = :categoryid';
            $params['categoryid'] = $categoryid;
        }

        if ($academicyear !== '') {
            $where .= ' AND ' . $db->sql_like('f.academicyear', ':academicyear', false);
            $params['academicyear'] = '%' . $db->sql_like_escape($academicyear) . '%';
        }

        if ($search !== '') {
            $fullnamesql = $db->sql_concat('u.firstname', "' '", 'u.lastname');
            $where .= ' AND (' . $db->sql_like($fullnamesql, ':searchname', false)
                . ' OR ' . $db->sql_like('u.email', ':searchemail', false) . ')';
            $params['searchname'] = '%' . $db->sql_like_escape($search) . '%';
            $params['searchemail'] = '%' . $db->sql_like_escape($search) . '%';
        }

        if ($status !== '') {
            [$statuswhere, $statusparams] = $this->build_status_where($status);
            $where .= ' AND ' . $statuswhere;
            $params = array_merge($params, $statusparams);
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);
    }

    /**
     * Convenience accessor to the global $DB used for sql_like()/sql_concat().
     *
     * @return \moodle_database
     */
    protected function get_db(): \moodle_database {
        global $DB;
        return $DB;
    }

    /**
     * Builds the WHERE fragment + params for one DISPLAYED status filter
     * value - see this class's docblock for why "overdue" needs a
     * subquery instead of a plain r.status = :status comparison, and why
     * "unpaid"/"partiallypaid" must explicitly EXCLUDE an overdue record
     * to stay consistent with feerecord_manager::display_status().
     *
     * @param string $status one of constants::FEE_STATUS_*
     * @return array [string $where, array $params]
     */
    protected function build_status_where(string $status): array {
        $db = $this->get_db();

        $overduesql = "EXISTS (
                SELECT 1
                  FROM {financedep_installmentplan} ip
                  JOIN {financedep_installmentsched} sc ON sc.installmentplanid = ip.id
                 WHERE ip.feerecordid = r.id
                   AND ip.status = :ipactive
                   AND sc.status IN (:schedpending, :schedpartial)
                   AND sc.duedate < :nowtime
            )";
        $overdueparams = [
            'ipactive' => constants::INSTALLMENTPLAN_STATUS_ACTIVE,
            'schedpending' => constants::INSTALLMENT_STATUS_PENDING,
            'schedpartial' => constants::INSTALLMENT_STATUS_PARTIALLY_PAID,
            'nowtime' => time(),
        ];

        if ($status === constants::FEE_STATUS_OVERDUE) {
            [$insql, $inparams] = $db->get_in_or_equal(
                [constants::FEE_STATUS_UNPAID, constants::FEE_STATUS_PARTIALLY_PAID],
                SQL_PARAMS_NAMED,
                'ovstatus'
            );
            return ["(r.status $insql AND $overduesql)", array_merge($inparams, $overdueparams)];
        }

        if ($status === constants::FEE_STATUS_UNPAID || $status === constants::FEE_STATUS_PARTIALLY_PAID) {
            return ["(r.status = :fstatus AND NOT $overduesql)", array_merge(['fstatus' => $status], $overdueparams)];
        }

        // Fully paid / cancelled - plain equality, never affected by the
        // live-computed overdue state.
        return ['r.status = :fstatus', ['fstatus' => $status]];
    }

    /**
     * Renders the student column: full name + email, linked to the
     * itemised statement (view.php) - now reachable by a
     * viewallrecords-only report viewer too, see access_manager::
     * require_manage_any()'s docblock.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_lastname($row): string {
        $url = new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $row->id]);
        return \html_writer::link($url, format_string(fullname($row)) . ' (' . s($row->email) . ')');
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
     * Renders the academic year column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_academicyear($row): string {
        return s($row->academicyear);
    }

    /**
     * Renders the total amount column, MMK-formatted.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_totalamount($row): string {
        return local_financedepartment_format_money($row->totalamount);
    }

    /**
     * Renders the balance column, MMK-formatted.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_balance($row): string {
        return local_financedepartment_format_money($row->balance);
    }

    /**
     * Renders the status column as a coloured badge - the DISPLAYED
     * (live-computed overdue-aware) status, same helper every other page
     * uses. Never badge $row->status directly.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_status($row): string {
        return local_financedepartment_feerecord_status_badge($row);
    }

    /**
     * Renders the row action link (view the itemised statement).
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        return \html_writer::link(
            new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $row->id]),
            get_string('view')
        );
    }
}
