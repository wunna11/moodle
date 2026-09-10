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
 * Sortable, filterable, paginated scholarship request listing - added
 * 2026-08-24 so browsing requests no longer requires typing a student's
 * name first (the original pages/scholarshiprequests/index.php only
 * showed anything once you searched for a student, or showed PENDING
 * requests only for an approver). See pages/scholarshiprequests/index.php.
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
 * Class scholarshiprequest_table
 */
class scholarshiprequest_table extends \core_table\sql_table {

    /** @var bool whether the current user can approve/reject a pending row (controls the actions column). */
    protected $canapprove;

    /** @var bool whether the current user can delete a row (controls the actions column). */
    protected $candelete;

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param string $status '' = any status
     * @param int $studentid 0 = any student
     * @param string $search free-text match against the student's name/email
     * @param bool $canapprove whether to show approve/reject row actions
     * @param bool $candelete whether to show a delete row action (added 2026-08-24)
     */
    public function __construct(
        string $uniqueid,
        string $status,
        int $studentid,
        string $search,
        bool $canapprove,
        bool $candelete = false
    ) {
        parent::__construct($uniqueid);

        $this->canapprove = $canapprove;
        $this->candelete = $candelete;

        $this->define_columns([
            'student', 'categoryname', 'scholarshipname', 'requestedamount', 'approvedamount', 'status', 'timecreated', 'actions',
        ]);
        $this->define_headers([
            get_string('student', 'local_financedepartment'),
            get_string('category', 'local_financedepartment'),
            get_string('scholarship', 'local_financedepartment'),
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
        $this->set_attribute('class', 'generaltable local-financedepartment-scholarshiprequest-table');

        // LEFT JOINs for the fee-record/category chain (changed
        // 2026-09-10, v2026091004/0.8.0): feerecordid is now nullable -
        // a request submitted through the current form never has one
        // (see scholarshiprequest_manager's class docblock) - only a
        // legacy pre-change row still has a real feerecordid/category.
        // s.amounttype/s.amountvalue added so col_requestedamount() can
        // render a meaningful message for a null (percentage-type)
        // requestedamount instead of just calling format_money(null).
        $fields = 'q.id, q.studentid, q.feerecordid, q.scholarshipid, q.requestedamount, q.approvedamount,
                   q.status, q.timecreated, q.requestedby,
                   u.firstname, u.lastname, u.email,
                   s.name AS scholarshipname, s.status AS scholarshipstatus, s.amounttype, s.amountvalue,
                   cc.name AS categoryname, fs.academicyear';
        $from = '{financedep_scholarshipreq} q
                   JOIN {user} u ON u.id = q.studentid
                   JOIN {financedep_scholarship} s ON s.id = q.scholarshipid
              LEFT JOIN {financedep_feerecord} fr ON fr.id = q.feerecordid
              LEFT JOIN {financedep_feestructure} fs ON fs.id = fr.feestructureid
              LEFT JOIN {course_categories} cc ON cc.id = fs.categoryid';

        // Deleted requests are soft-deleted (status = DELETED) and are
        // NEVER shown through this table regardless of the $status
        // filter - see scholarshiprequest_manager::delete()'s docblock.
        // They remain reachable directly via view.php?id= and stay in
        // financedep_auditlog for history.
        $where = 'q.status != :excludedeleted';
        $params = ['excludedeleted' => \local_financedepartment\constants::REQUEST_STATUS_DELETED];

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
     * to just this student (via the studentid param), so a reviewer can
     * see one student's full request history in one click.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_student($row): string {
        $url = new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', ['studentid' => $row->studentid]);
        return \html_writer::link($url, format_string(fullname($row)));
    }

    /**
     * Renders the category column. Returns "-" for a request with no
     * linked fee record (the normal case going forward, since
     * v2026091004/0.8.0 removed the fee-record field from submission -
     * see scholarshiprequest_manager's class docblock) - only a legacy
     * pre-change row still has a real category/academicyear here.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_categoryname($row): string {
        if ($row->categoryname === null) {
            return '-';
        }

        return format_string($row->categoryname) . ' - ' . s($row->academicyear);
    }

    /**
     * Renders the scholarship column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_scholarshipname($row): string {
        return format_string($row->scholarshipname);
    }

    /**
     * Renders the requested-amount column. Null (a percentage-type
     * scholarship with no fee record to compute a base amount against -
     * see scholarshiprequest_manager::compute_suggested_amount()'s
     * docblock, v2026091004/0.8.0) renders the scholarship's raw
     * percentage instead of a computed MMK figure.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_requestedamount($row): string {
        if ($row->requestedamount === null) {
            $percent = rtrim(rtrim(number_format((float) $row->amountvalue, 2), '0'), '.');
            return get_string('requestedamountpercentagebased', 'local_financedepartment', $percent);
        }

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
        return local_financedepartment_scholarshiprequest_status_badge($row->status);
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
     * approve/reject shortcuts when the row is still PENDING and the
     * current user can approve.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        global $USER;

        $actions = [
            \html_writer::link(
                new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $row->id]),
                get_string('view')
            ),
        ];

        // Self-approval fix (2026-09-06): never show approve/reject to
        // the same user who submitted the request - see
        // scholarshiprequest_manager::approve()'s docblock and
        // review.php's matching server-side check (the actual
        // enforcement point; this just keeps the buttons from
        // appearing only to error out).
        $isownrequest = (int) $row->requestedby === (int) $USER->id;

        if ($this->canapprove && $row->status === \local_financedepartment\constants::REQUEST_STATUS_PENDING && !$isownrequest) {
            // Approve is hidden (not Reject) once the underlying
            // scholarship has been deactivated - added 2026-09-06, see
            // scholarshiprequest_manager::approve()'s docblock. A
            // reviewer can still reject such a request to clear it out
            // of the pending queue.
            if ($row->scholarshipstatus === \local_financedepartment\constants::SCHOLARSHIP_STATUS_ACTIVE) {
                $actions[] = \html_writer::link(
                    new moodle_url('/local/financedepartment/pages/scholarshiprequests/review.php', ['id' => $row->id, 'decision' => 'approve']),
                    get_string('approve', 'local_financedepartment')
                );
            }
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/scholarshiprequests/review.php', ['id' => $row->id, 'decision' => 'reject']),
                get_string('reject', 'local_financedepartment')
            );
        }

        // Delete is offered for any status this table can show (deleted
        // rows themselves are already excluded from every query above,
        // so $row->status here is always pending/approved/rejected) -
        // see scholarshiprequest_manager::delete()'s docblock for what
        // happens to an approved row's fee record balance on delete.
        if ($this->candelete) {
            $actions[] = \html_writer::link(
                new moodle_url('/local/financedepartment/pages/scholarshiprequests/delete.php', ['id' => $row->id]),
                get_string('delete'),
                ['class' => 'text-danger']
            );
        }

        return implode(' | ', $actions);
    }
}
