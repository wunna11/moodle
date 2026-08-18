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
 * Sortable, searchable, paginated listing of student leave applications,
 * for the Leave requests page.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use local_hrdepartment\student_leave_manager;
use moodle_url;

/**
 * Class student_leave_table
 */
class student_leave_table extends \table_sql {

    /** @var bool whether the viewing user can manage (not just view) applications. */
    protected $canmanage;

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param string $search student name/email search
     * @param string $status status filter, '' = any
     * @param int $leavetypeid 0 = any
     * @param bool $canmanage whether to show manage actions (edit/review/cancel) vs view-only
     */
    public function __construct(string $uniqueid, string $search, string $status, int $leavetypeid, bool $canmanage) {
        parent::__construct($uniqueid);

        $this->canmanage = $canmanage;

        $this->define_columns([
            'fullname', 'leavetypename', 'approver', 'dates', 'totaldays', 'status', 'actions',
        ]);
        $this->define_headers([
            get_string('student', 'local_hrdepartment'),
            get_string('leavetype', 'local_hrdepartment'),
            get_string('approver', 'local_hrdepartment'),
            get_string('startdate', 'local_hrdepartment') . ' - ' . get_string('enddate', 'local_hrdepartment'),
            get_string('totaldays', 'local_hrdepartment'),
            get_string('status', 'local_hrdepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'timecreated', SORT_DESC);
        $this->no_sorting('dates');
        $this->no_sorting('approver');
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-hrdepartment-student-leave-table');

        global $DB;

        $fields = "a.id, a.studentid, u.firstname, u.lastname,
                   a.leavetypeid, lt.name AS leavetypename,
                   a.approverid, au.firstname AS approverfirstname, au.lastname AS approverlastname,
                   a.startdate, a.enddate, a.totaldays, a.status, a.timecreated";
        $from = "{hrdep_studentleaveapp} a
                 JOIN {user} u ON u.id = a.studentid
                 JOIN {hrdep_studentleavetype} lt ON lt.id = a.leavetypeid
            LEFT JOIN {user} au ON au.id = a.approverid";

        $where = '1 = 1';
        $params = [];

        if ($status !== '') {
            $where .= ' AND a.status = :status';
            $params['status'] = $status;
        }

        if ($leavetypeid) {
            $where .= ' AND a.leavetypeid = :leavetypeid';
            $params['leavetypeid'] = $leavetypeid;
        }

        if ($search !== '') {
            $like = '%' . $DB->sql_like_escape($search) . '%';
            $where .= ' AND (' . $DB->sql_like('u.firstname', ':search1', false) . '
                          OR ' . $DB->sql_like('u.lastname', ':search2', false) . '
                          OR ' . $DB->sql_like('u.email', ':search3', false) . ')';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);
    }

    /**
     * Renders the fullname column as a link to the application detail.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_fullname($row): string {
        $url = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]);
        return \html_writer::link($url, fullname($row));
    }

    /**
     * Renders the leave type column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_leavetypename($row): string {
        return format_string($row->leavetypename);
    }

    /**
     * Renders the approver column: the teacher this student chose to
     * review their own self-service application (leave/apply.php), or a
     * dash for HR-logged applications, which don't set one and rely on
     * the capability-based HR/Admin/delegated-Approver path instead.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_approver($row): string {
        if (empty($row->approverid)) {
            return get_string('noapproverassigned', 'local_hrdepartment');
        }

        return fullname((object) ['firstname' => $row->approverfirstname, 'lastname' => $row->approverlastname]);
    }

    /**
     * Renders the start-end date range column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_dates($row): string {
        $dateformat = get_string('strftimedatefullshort', 'langconfig');
        return userdate($row->startdate, $dateformat) . ' - ' . userdate($row->enddate, $dateformat);
    }

    /**
     * Renders the status column as a coloured badge.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_status($row): string {
        $class = [
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-secondary',
        ][$row->status] ?? 'badge-secondary';

        return \html_writer::span(get_string('status_' . $row->status, 'local_hrdepartment'), 'badge ' . $class);
    }

    /**
     * Renders the row action links (view, plus edit/review/cancel when
     * the viewing user can manage and the application is still actionable).
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        $actions = [];

        $actions[] = \html_writer::link(
            new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]),
            get_string('view')
        );

        if ($this->canmanage && $row->status === 'pending') {
            $actions[] = \html_writer::link(
                new moodle_url('/local/hrdepartment/leave/edit.php', ['id' => $row->id]),
                get_string('edit')
            );
        }

        return implode(' | ', $actions);
    }
}
