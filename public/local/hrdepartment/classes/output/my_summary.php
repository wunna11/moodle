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
 * Renderable for the personal "My HR" self-service snapshot.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\dashboard_helper;
use renderable;
use renderer_base;
use templatable;

/**
 * Class my_summary
 *
 * Shown instead of the org-wide dashboard to users who only hold
 * self-service capabilities (own attendance/leave/payroll).
 */
class my_summary implements renderable, templatable {

    /** @var int */
    protected $userid;

    /**
     * Constructor.
     *
     * @param int $userid
     */
    public function __construct(int $userid) {
        $this->userid = $userid;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $snapshot = dashboard_helper::get_my_snapshot($this->userid);

        if (!$snapshot) {
            return ['hasprofile' => false];
        }

        $leavebalances = [];
        foreach ($snapshot->leavebalances as $balance) {
            $leavebalances[] = [
                'leavetypename' => $balance->leavetypename,
                'allocated' => $balance->allocated,
                'used' => $balance->used,
                'remaining' => $balance->remaining,
            ];
        }

        $latestpayroll = null;
        if ($snapshot->latestpayroll) {
            $payroll = $snapshot->latestpayroll;
            $latestpayroll = [
                'period' => $payroll->period,
                'netsalary' => number_format((float) $payroll->netsalary, 2),
                'currency' => get_config('local_hrdepartment', 'currency') ?: 'USD',
                'paymentstatus' => get_string('status_' . $payroll->paymentstatus, 'local_hrdepartment'),
            ];
        }

        return [
            'hasprofile' => true,
            'employeecode' => $snapshot->employee->employeecode,
            'designation' => $snapshot->employee->designation,

            'attendancepresent' => $snapshot->attendance['present'],
            'attendanceabsent' => $snapshot->attendance['absent'],
            'attendanceleave' => $snapshot->attendance['leave'],
            'attendancehalfday' => $snapshot->attendance['halfday'],

            'hasleavebalances' => !empty($leavebalances),
            'leavebalances' => $leavebalances,
            'pendingleave' => $snapshot->pendingleave,

            'haslatestpayroll' => (bool) $latestpayroll,
            'latestpayroll' => $latestpayroll,
        ];
    }
}
