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
 * Sortable, filterable, paginated student attendance record listing.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use local_hrdepartment\constants;
use moodle_url;

/**
 * Class student_attendance_table
 */
class student_attendance_table extends \table_sql {

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param array|null $courseids restrict to these course ids, or null for no course restriction
     * @param int $filtercourseid single-course filter (0 = no filter), applied on top of $courseids
     * @param string $filterstatus one of constants::attendance_statuses(), or '' for no filter
     * @param int $filterstudentid restrict to one student (0 = no filter)
     */
    public function __construct(
        string $uniqueid,
        ?array $courseids,
        int $filtercourseid = 0,
        string $filterstatus = '',
        int $filterstudentid = 0
    ) {
        parent::__construct($uniqueid);

        $this->define_columns([
            'studentname', 'coursename', 'attendancedate', 'status', 'remarks', 'recordedbyname', 'actions',
        ]);
        $this->define_headers([
            get_string('student', 'local_hrdepartment'),
            get_string('course', 'local_hrdepartment'),
            get_string('attendancedate', 'local_hrdepartment'),
            get_string('status', 'local_hrdepartment'),
            get_string('remarks', 'local_hrdepartment'),
            get_string('recordedby', 'local_hrdepartment'),
            get_string('actions'),
        ]);

        $this->sortable(true, 'attendancedate', SORT_DESC);
        $this->no_sorting('remarks');
        $this->no_sorting('actions');
        $this->collapsible(false);
        $this->set_attribute('class', 'generaltable local-hrdepartment-studentattendance-table');

        $fields = "sa.id, sa.studentid, sa.courseid, sa.attendancedate, sa.status, sa.remarks,
                   su.firstname AS studentfirstname, su.lastname AS studentlastname,
                   c.shortname, c.fullname,
                   ru.firstname AS recordedbyfirstname, ru.lastname AS recordedbylastname";
        $from = "{hrdep_studentattendance} sa
                 JOIN {user} su ON su.id = sa.studentid
                 JOIN {course} c ON c.id = sa.courseid
                 JOIN {user} ru ON ru.id = sa.recordedby";

        $where = '1 = 1';
        $params = [];

        if ($courseids !== null) {
            if (empty($courseids)) {
                // No manageable courses at all - guarantee zero rows rather
                // than accidentally matching everything.
                $where = '1 = 0';
            } else {
                [$insql, $inparams] = $this->get_db()->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
                $where .= " AND sa.courseid $insql";
                $params += $inparams;
            }
        }

        if ($filtercourseid) {
            $where .= ' AND sa.courseid = :filtercourseid';
            $params['filtercourseid'] = $filtercourseid;
        }

        if ($filterstatus !== '') {
            $where .= ' AND sa.status = :filterstatus';
            $params['filterstatus'] = $filterstatus;
        }

        if ($filterstudentid) {
            $where .= ' AND sa.studentid = :filterstudentid';
            $params['filterstudentid'] = $filterstudentid;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);
    }

    /**
     * Convenience accessor to the global $DB used for get_in_or_equal().
     *
     * @return \moodle_database
     */
    protected function get_db(): \moodle_database {
        global $DB;
        return $DB;
    }

    /**
     * Renders the student column as a link to their attendance history.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_studentname($row): string {
        $name = fullname((object) ['firstname' => $row->studentfirstname, 'lastname' => $row->studentlastname]);
        $url = new moodle_url('/local/hrdepartment/attendance/view.php', [
            'studentid' => $row->studentid,
            'courseid' => $row->courseid,
        ]);

        return \html_writer::link($url, $name);
    }

    /**
     * Renders the course column as a link to the course itself.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_coursename($row): string {
        $url = new moodle_url('/course/view.php', ['id' => $row->courseid]);

        return \html_writer::link($url, $row->shortname . ': ' . format_string($row->fullname));
    }

    /**
     * Renders the attendance date column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_attendancedate($row): string {
        return userdate($row->attendancedate, get_string('strftimedatefullshort', 'langconfig'));
    }

    /**
     * Renders the status column as a coloured badge.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_status($row): string {
        $classmap = [
            constants::ATTENDANCE_PRESENT => 'badge-success',
            constants::ATTENDANCE_ABSENT => 'badge-danger',
            constants::ATTENDANCE_LEAVE => 'badge-info',
            constants::ATTENDANCE_HALFDAY => 'badge-warning',
        ];
        $class = $classmap[$row->status] ?? 'badge-secondary';

        return \html_writer::span(get_string('attendance_' . $row->status, 'local_hrdepartment'), 'badge ' . $class);
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
     * Renders the recorded-by column.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_recordedbyname($row): string {
        return fullname((object) ['firstname' => $row->recordedbyfirstname, 'lastname' => $row->recordedbylastname]);
    }

    /**
     * Renders the row action link: re-opens that day's roster for editing.
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_actions($row): string {
        // A plain machine-readable Y-m-d for the URL param, not a
        // user-facing display string - userdate()'s locale formatting has
        // no place here, mark.php just needs to parse it back with
        // strtotime().
        $url = new moodle_url('/local/hrdepartment/attendance/mark.php', [
            'courseid' => $row->courseid,
            'date' => date('Y-m-d', $row->attendancedate),
        ]);

        return \html_writer::link($url, get_string('edit'));
    }
}
