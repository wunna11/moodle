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
 * Renderable for the organisation-wide HR dashboard.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\dashboard_helper;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class dashboard_summary
 *
 * Wraps dashboard_helper data into the flat array shape expected by the
 * local_hrdepartment/dashboard mustache template.
 */
class dashboard_summary implements renderable, templatable {

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $counts = dashboard_helper::get_employee_counts();
        $payroll = dashboard_helper::get_payroll_summary();
        $leave = dashboard_helper::get_leave_summary();
        $attendance = dashboard_helper::get_attendance_summary();
        $recentleave = dashboard_helper::get_recent_leave_requests(5);

        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        $recentleavedata = [];
        foreach ($recentleave as $request) {
            $recentleavedata[] = [
                'fullname' => $request->fullname,
                'leavetypename' => $request->leavetypename,
                'startdate' => userdate($request->startdate, $dateformat),
                'enddate' => userdate($request->enddate, $dateformat),
                'totaldays' => $request->totaldays,
                'url' => (new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $request->id]))->out(false),
            ];
        }

        return [
            'lecturercount' => $counts->lecturers,
            'staffcount' => $counts->staff,
            'totalemployees' => $counts->total,
            'inactiveemployees' => $counts->inactive,

            'payrollperiod' => $payroll->period,
            'currency' => get_config('local_hrdepartment', 'currency') ?: 'USD',
            'payrollbase' => number_format((float) $payroll->basesalary, 2),
            'payrollallowances' => number_format((float) $payroll->allowances, 2),
            'payrolldeductions' => number_format((float) $payroll->deductions, 2),
            'payrollnet' => number_format((float) $payroll->netsalary, 2),
            'payrollpending' => $payroll->pending,
            'payrollprocessed' => $payroll->processed,
            'payrollpaid' => $payroll->paid,

            'leavepending' => $leave->pending,
            'leaveapprovedthismonth' => $leave->approvedthismonth,
            'onleavetoday' => $leave->onleavetoday,

            'attendancepresent' => $attendance->present,
            'attendanceabsent' => $attendance->absent,
            'attendanceleave' => $attendance->leave,
            'attendancehalfday' => $attendance->halfday,
            'attendancetotalmarked' => $attendance->totalmarked,

            'hasrecentleave' => !empty($recentleavedata),
            'recentleave' => $recentleavedata,

            'lecturersurl' => (new moodle_url('/local/hrdepartment/lecturer/index.php'))->out(false),
            'staffurl' => (new moodle_url('/local/hrdepartment/staff/index.php'))->out(false),
            'attendanceurl' => (new moodle_url('/local/hrdepartment/attendance/index.php'))->out(false),
            'leaveurl' => (new moodle_url('/local/hrdepartment/leave/index.php'))->out(false),
            'payrollurl' => (new moodle_url('/local/hrdepartment/payroll/index.php'))->out(false),
        ];
    }
}
