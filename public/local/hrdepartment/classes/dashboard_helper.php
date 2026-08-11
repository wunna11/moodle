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
 * Data access helper for HR dashboard metrics.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class dashboard_helper
 *
 * Pure data-access methods (no output/markup) used to build both the
 * organisation-wide dashboard and the personal "My HR" snapshot. Later
 * modules (attendance, leave, payroll) reuse these queries rather than
 * re-implementing them.
 */
class dashboard_helper {

    /**
     * Returns active/inactive employee counts split by type.
     *
     * @return \stdClass
     */
    public static function get_employee_counts(): \stdClass {
        global $DB;

        $counts = new \stdClass();
        $counts->lecturers = $DB->count_records('hrdep_employee', [
            'type' => constants::EMPLOYEE_TYPE_LECTURER,
            'employmentstatus' => constants::EMPLOYMENT_STATUS_ACTIVE,
        ]);
        $counts->staff = $DB->count_records('hrdep_employee', [
            'type' => constants::EMPLOYEE_TYPE_STAFF,
            'employmentstatus' => constants::EMPLOYMENT_STATUS_ACTIVE,
        ]);
        $counts->total = $counts->lecturers + $counts->staff;
        $counts->inactive = $DB->count_records_select(
            'hrdep_employee',
            'employmentstatus <> :active',
            ['active' => constants::EMPLOYMENT_STATUS_ACTIVE]
        );

        return $counts;
    }

    /**
     * Returns payroll totals and status breakdown for a period.
     *
     * @param string|null $period YYYY-MM, defaults to the current month.
     * @return \stdClass
     */
    public static function get_payroll_summary(?string $period = null): \stdClass {
        global $DB;

        $period = $period ?? date('Y-m', time());

        $sql = "SELECT COUNT(id) AS numrecords,
                       COALESCE(SUM(basesalary), 0) AS basesalary,
                       COALESCE(SUM(allowances), 0) AS allowances,
                       COALESCE(SUM(deductions), 0) AS deductions,
                       COALESCE(SUM(netsalary), 0) AS netsalary
                  FROM {hrdep_payroll}
                 WHERE period = :period";
        $summary = $DB->get_record_sql($sql, ['period' => $period]);
        $summary->period = $period;

        $summary->pending = $DB->count_records('hrdep_payroll', [
            'period' => $period,
            'paymentstatus' => constants::PAYROLL_STATUS_PENDING,
        ]);
        $summary->processed = $DB->count_records('hrdep_payroll', [
            'period' => $period,
            'paymentstatus' => constants::PAYROLL_STATUS_PROCESSED,
        ]);
        $summary->paid = $DB->count_records('hrdep_payroll', [
            'period' => $period,
            'paymentstatus' => constants::PAYROLL_STATUS_PAID,
        ]);

        return $summary;
    }

    /**
     * Returns organisation-wide leave stats: pending approvals, approved
     * this month, and employees marked on-leave today.
     *
     * @return \stdClass
     */
    public static function get_leave_summary(): \stdClass {
        global $DB;

        $summary = new \stdClass();
        $summary->pending = $DB->count_records('hrdep_leaveapplication', [
            'status' => constants::LEAVE_STATUS_PENDING,
        ]);

        $monthstart = strtotime('first day of this month midnight');
        $summary->approvedthismonth = $DB->count_records_select(
            'hrdep_leaveapplication',
            'status = :status AND timemodified >= :monthstart',
            ['status' => constants::LEAVE_STATUS_APPROVED, 'monthstart' => $monthstart]
        );

        $today = strtotime('midnight');
        $summary->onleavetoday = $DB->count_records('hrdep_attendance', [
            'attendancedate' => $today,
            'status' => constants::ATTENDANCE_LEAVE,
        ]);

        return $summary;
    }

    /**
     * Returns today's attendance breakdown by status.
     *
     * @param int|null $date Midnight timestamp of the day to summarise, defaults to today.
     * @return \stdClass
     */
    public static function get_attendance_summary(?int $date = null): \stdClass {
        global $DB;

        $date = $date ?? strtotime('midnight');

        $summary = new \stdClass();
        foreach (constants::attendance_statuses() as $status) {
            $summary->$status = $DB->count_records('hrdep_attendance', [
                'attendancedate' => $date,
                'status' => $status,
            ]);
        }
        $summary->totalmarked = array_sum(get_object_vars($summary));

        return $summary;
    }

    /**
     * Returns the most recent pending leave applications, for a
     * manager's quick-glance action list.
     *
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_recent_leave_requests(int $limit = 5): array {
        global $DB;

        $sql = "SELECT la.id, la.startdate, la.enddate, la.totaldays, la.status, la.timecreated,
                       lt.name AS leavetypename,
                       e.id AS employeeid, u.firstname, u.lastname
                  FROM {hrdep_leaveapplication} la
                  JOIN {hrdep_employee} e ON e.id = la.employeeid
                  JOIN {user} u ON u.id = e.userid
                  JOIN {hrdep_leavetype} lt ON lt.id = la.leavetypeid
                 WHERE la.status = :status
              ORDER BY la.timecreated DESC";
        $records = $DB->get_records_sql($sql, ['status' => constants::LEAVE_STATUS_PENDING], 0, $limit);

        $requests = [];
        foreach ($records as $record) {
            $record->fullname = fullname($record);
            $requests[] = $record;
        }

        return $requests;
    }

    /**
     * Returns the hrdep_employee record linked to a Moodle user, if any.
     *
     * @param int $userid
     * @return \stdClass|false
     */
    public static function get_employee_for_user(int $userid) {
        global $DB;

        return $DB->get_record('hrdep_employee', ['userid' => $userid]);
    }

    /**
     * Builds a personal HR snapshot for self-service users: attendance
     * this month, leave balances/pending applications, and the latest
     * payslip. Returns null if the user has no employee record yet.
     *
     * @param int $userid
     * @return \stdClass|null
     */
    public static function get_my_snapshot(int $userid): ?\stdClass {
        global $DB;

        $employee = self::get_employee_for_user($userid);
        if (!$employee) {
            return null;
        }

        $snapshot = new \stdClass();
        $snapshot->employee = $employee;

        $monthstart = strtotime('first day of this month midnight');
        $sql = "SELECT status, COUNT(id) AS total
                  FROM {hrdep_attendance}
                 WHERE employeeid = :employeeid AND attendancedate >= :monthstart
              GROUP BY status";
        $rows = $DB->get_records_sql($sql, ['employeeid' => $employee->id, 'monthstart' => $monthstart]);

        $snapshot->attendance = array_fill_keys(constants::attendance_statuses(), 0);
        foreach ($rows as $row) {
            $snapshot->attendance[$row->status] = (int) $row->total;
        }

        $year = (int) date('Y');
        $sql = "SELECT lb.id, lt.name AS leavetypename, lb.allocated, lb.used, lb.remaining
                  FROM {hrdep_leavebalance} lb
                  JOIN {hrdep_leavetype} lt ON lt.id = lb.leavetypeid
                 WHERE lb.employeeid = :employeeid AND lb.year = :year
              ORDER BY lt.name ASC";
        $snapshot->leavebalances = array_values($DB->get_records_sql($sql, [
            'employeeid' => $employee->id,
            'year' => $year,
        ]));

        $snapshot->pendingleave = $DB->count_records('hrdep_leaveapplication', [
            'employeeid' => $employee->id,
            'status' => constants::LEAVE_STATUS_PENDING,
        ]);

        $payrolls = $DB->get_records(
            'hrdep_payroll',
            ['employeeid' => $employee->id],
            'period DESC',
            '*',
            0,
            1
        );
        $snapshot->latestpayroll = $payrolls ? reset($payrolls) : null;

        return $snapshot;
    }
}
