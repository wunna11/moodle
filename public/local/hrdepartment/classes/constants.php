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
 * Shared constants used across the HR Department plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

/**
 * Class constants
 *
 * Centralises the enum-like string values used across the hrdep_* tables
 * so every module (dashboard, attendance, leave, payroll, ...) references
 * the same literals instead of re-declaring them.
 */
class constants {

    /** @var string Employee type: lecturer. */
    const EMPLOYEE_TYPE_LECTURER = 'lecturer';

    /** @var string Employee type: staff. */
    const EMPLOYEE_TYPE_STAFF = 'staff';

    /** @var string Employment status: active. */
    const EMPLOYMENT_STATUS_ACTIVE = 'active';

    /** @var string Employment status: inactive. */
    const EMPLOYMENT_STATUS_INACTIVE = 'inactive';

    /** @var string Employment status: terminated. */
    const EMPLOYMENT_STATUS_TERMINATED = 'terminated';

    /** @var string Course assignment status: active. */
    const COURSEASSIGN_STATUS_ACTIVE = 'active';

    /** @var string Course assignment status: ended. */
    const COURSEASSIGN_STATUS_ENDED = 'ended';

    /** @var string Attendance status: present. */
    const ATTENDANCE_PRESENT = 'present';

    /** @var string Attendance status: absent. */
    const ATTENDANCE_ABSENT = 'absent';

    /** @var string Attendance status: leave. */
    const ATTENDANCE_LEAVE = 'leave';

    /** @var string Attendance status: half-day. */
    const ATTENDANCE_HALFDAY = 'halfday';

    /** @var string Leave application status: pending. */
    const LEAVE_STATUS_PENDING = 'pending';

    /** @var string Leave application status: approved. */
    const LEAVE_STATUS_APPROVED = 'approved';

    /** @var string Leave application status: rejected. */
    const LEAVE_STATUS_REJECTED = 'rejected';

    /** @var string Leave application status: cancelled. */
    const LEAVE_STATUS_CANCELLED = 'cancelled';

    /** @var string Payroll payment status: pending. */
    const PAYROLL_STATUS_PENDING = 'pending';

    /** @var string Payroll payment status: processed. */
    const PAYROLL_STATUS_PROCESSED = 'processed';

    /** @var string Payroll payment status: paid. */
    const PAYROLL_STATUS_PAID = 'paid';

    /** @var string Payroll item type: allowance. */
    const PAYROLL_ITEM_ALLOWANCE = 'allowance';

    /** @var string Payroll item type: deduction. */
    const PAYROLL_ITEM_DEDUCTION = 'deduction';

    /**
     * Returns the list of valid employee types.
     *
     * @return string[]
     */
    public static function employee_types(): array {
        return [
            self::EMPLOYEE_TYPE_LECTURER,
            self::EMPLOYEE_TYPE_STAFF,
        ];
    }

    /**
     * Returns the list of valid attendance statuses.
     *
     * @return string[]
     */
    public static function attendance_statuses(): array {
        return [
            self::ATTENDANCE_PRESENT,
            self::ATTENDANCE_ABSENT,
            self::ATTENDANCE_LEAVE,
            self::ATTENDANCE_HALFDAY,
        ];
    }

    /**
     * Returns the list of valid leave application statuses.
     *
     * @return string[]
     */
    public static function leave_statuses(): array {
        return [
            self::LEAVE_STATUS_PENDING,
            self::LEAVE_STATUS_APPROVED,
            self::LEAVE_STATUS_REJECTED,
            self::LEAVE_STATUS_CANCELLED,
        ];
    }

    /**
     * Returns the list of valid payroll payment statuses.
     *
     * @return string[]
     */
    public static function payroll_statuses(): array {
        return [
            self::PAYROLL_STATUS_PENDING,
            self::PAYROLL_STATUS_PROCESSED,
            self::PAYROLL_STATUS_PAID,
        ];
    }
}
