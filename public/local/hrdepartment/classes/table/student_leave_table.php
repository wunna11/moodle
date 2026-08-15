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
 * Sortable, searchable, paginated listing of leave-marked mod_attendance
 * records, for the Student Leave Lookup page. Read-only, like the rest
 * of this section - see local_hrdepartment\student_leave_manager.
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

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param string $search student name/email search
     * @param int $courseid 0 = any (subject to $courseids restriction)
     * @param int[]|null $courseids restrict to these course ids, or null for no restriction
     */
    public function __construct(string $uniqueid, string $search = '', int $courseid = 0, ?array $courseids = null) {
        parent::__construct($uniqueid);

        $this->define_columns([
            'fullname', 'coursename', 'sessdate', 'remarks', 'actions',
        ]);
        $this->define_headers([
            get_string('student', 'local_hrdepartment'),
            get_string('course', 'local_hrdepartment'),
            get_string('attendancedate', 'local_hrdepartment'),
            get_string('remarks', 'local_hrdepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'sessdate', SORT_DESC);
        $this->no_sorting('actions');
        $this->no_sorting('coursename');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-hrdepartment-student-leave-table');

        global $DB;

        $fields = "l.id, u.id AS studentid, u.firstname, u.lastname,
                   a.course AS courseid, c.shortname, c.fullname AS coursefullname,
                   s.sessdate, l.remarks";
        $from = "{attendance_log} l
                 JOIN {attendance_sessions} s ON s.id = l.sessionid
                 JOIN {attendance} a ON a.id = s.attendanceid
                 JOIN {course} c ON c.id = a.course
                 JOIN {attendance_statuses} st ON st.id = l.statusid
                 JOIN {user} u ON u.id = l.studentid";

        $where = 'LOWER(st.description) = LOWER(:leavelabel)';
        $params = ['leavelabel' => student_leave_manager::get_leave_status_label()];

        if ($courseids !== null) {
            if (empty($courseids)) {
                $courseids = [0];
            }
            [$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $where .= " AND a.course $insql";
            $params += $inparams;
        }

        if ($courseid) {
            $where .= ' AND a.course = :courseid';
            $params['courseid'] = $courseid;
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
     * Renders the fullname column as a link to the record detail view.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_fullname($row): string {
        $url = new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]);
        return \html_writer::link($url, fullname($row));
    }

    /**
     * Renders the course column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_coursename($row): string {
        return $row->shortname . ': ' . format_string($row->coursefullname);
    }

    /**
     * Renders the session date column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_sessdate($row): string {
        return userdate($row->sessdate, get_string('strftimedatefullshort', 'langconfig'));
    }

    /**
     * Renders the remarks column, falling back to a dash when unset.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_remarks($row): string {
        return $row->remarks !== null && $row->remarks !== '' ? format_string($row->remarks) : '-';
    }

    /**
     * Renders the row action link (view detail).
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        return \html_writer::link(
            new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $row->id]),
            get_string('view')
        );
    }
}
