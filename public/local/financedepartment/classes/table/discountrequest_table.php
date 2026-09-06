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
 * Sortable, filterable, paginated discount request listing.
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
 * Class discountrequest_table
 *
 * Deliberately has no $candelete/delete-row-action support, unlike
 * scholarshiprequest_table - discount requests don't have a delete
 * feature yet, see discountrequest_manager's class docblock.
 */
class discountrequest_table extends \core_table\sql_table {

    /** @var bool whether the current user can approve/reject a pending row (controls the actions column). */
    protected $canapprove;

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param string $status '' = any status
     * @param int $studentid 0 = any student
     * @param string $search free-text match against the student's name/email
     * @param bool $canapprove whether to show approve/reject row actions
     */
    public function __construct(
        string $uniqueid,
        string $status,
        int $studentid,
        string $search,
        bool $canapprove
    ) {
        parent::__construct($uniqueid);

        $this->canapprove = $canapprove;

        $this->define_columns([
            'student', 'discountname', 'discounttype', 'requestedamount', 'approvedamount', 'status', 'timecreated', 'actions',
        ]);
        $this->define_headers([
            get_string('student', 'local_financedepartment'),
            get_string('discount', 'local_financedepartment'),
            get_string('discounttype', 'local_financedepartment'),
            get_string('requestedamount', 'local_financedepartment'),
            get_string('approvedamount', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('when', 'local_financedepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'timecreated', SORT_DESC);
        $this->no_sorting('requestedamount');
        $this->no_sorting('approvedamount');
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-financedepartment-discountrequest-table');

        $fields = 'q.id, q.studentid, q.feerecordid, q.discountid, q.requestedamount, q.approvedamount,
                   q.status, q.timecreated, q.requestedby,
                   u.firstname, u.lastname, u.email,
                   d.name AS discountname, d.type AS discounttype';
        $from = '{financedep_discountreq} q
                   JOIN {user} u ON u.id = q.studentid
                   JOIN {financedep_discount} d ON d.id = q.discountid';

        $where = '1 = 1';
        $params = [];

        if ($status !== '') {
            $where .= ' AND q.status = :status';
            $params['status'] = $status;
        }

        if ($studentid) {
            $where .= ' AND q.studentid = :studentid';
            $params['studentid'] = $studentid;
        }

        if ($search !== '') {
            global $DB;
            $where .= ' AND (' . $DB->sql_like('u.firstname', ':search1', false) . '
                          OR ' . $DB->sql_like('u.lastname', ':search2', false) . '
                          OR ' . $DB->sql_like('u.email', ':search3', false) . ')';
            $params['search1'] = '%' . $DB->sql_like_escape($search) . '%';
            $params['search2'] = '%' . $DB->sql_like_escape($search) . '%';
            $params['search3'] = '%' . $DB->sql_like_escape($search) . '%';
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);
    }

    /**
     * Renders the student column, linked back to this same list filtered
     * to just this student.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_student($row): string {
        $url = new moodle_url('/local/financedepartment/pages/discountrequests/index.php', ['studentid' => $row->studentid]);
        return \html_writer::link($url, format_string(fullname($row)));
    }

    /**
     * Renders the discount name column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_discountname($row): string {
        return format_string($row->discountname);
    }

    /**
     * Renders the discount type column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_discounttype($row): string {
        return get_string('discounttype_' . $row->discounttype, 'local_financedepartment');
    }

    /**
     * Renders the requested-amount column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_requestedamount($row): string {
        return local_financedepartment_format_money($row->requestedamount);
    }

    /**
     * Renders the approved-amount column - only meaningful once a
     * decision has been made.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_approvedamount($row): string {
        return $row->approvedamount !== null ? local_financedepartment_format_money($row->approvedamount) : '-';
    }

    /**
     * Renders the status column as a coloured badge.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_status($row): string {
        return local_financedepartment_discountrequest_status_badge($row->status);
    }

    /**
     * Renders the submitted-date column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_timecreated($row): string {
        return userdate($row->timecreated, get_string('strftimedatetimeshort', 'core_langconfig'));
    }

    /**
     * Renders the row action links: always a view link, plus
     * approve/reject shortcuts when the row is still PENDING, the
     * current user can approve, and the current user did not submit
     * this request themselves (self-approval guard, built in from day
     * one - see discountrequest_manager::approve()'s docblock).
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        global $USER;

        $actions = [
            \html_writer::link(
                new moodle_url('/local/financedepartment/pages/discountrequests/view.php', ['id' => $row->id]),
                get_string('view')
            ),
        ];

        $isownrequest = (int) $row->requestedby === (int) $USER->id;

        if ($this->canapprove && $row->status === \local_financedepartment\constants::REQUEST_STATUS_PENDING && !$isownrequest) {
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/discountrequests/review.php', ['id' => $row->id, 'decision' => 'approve']),
                get_string('approve', 'local_financedepartment')
            );
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/discountrequests/review.php', ['id' => $row->id, 'decision' => 'reject']),
                get_string('reject', 'local_financedepartment')
            );
        }

        return implode(' | ', $actions);
    }
}
