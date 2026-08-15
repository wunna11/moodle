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
 * Library callbacks for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Adds the HR Department entry to the main Moodle navigation for users
 * who have at least dashboard access, or their own self-service
 * capabilities (attendance, leave, payroll).
 *
 * @param global_navigation $nav
 */
function local_hrdepartment_extend_navigation(global_navigation $nav) {
    global $PAGE;

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $context = context_system::instance();

    $cancontent = has_capability('local/hrdepartment:managedashboard', $context)
        || has_capability('local/hrdepartment:viewownattendance', $context)
        || has_capability('local/hrdepartment:viewownleave', $context)
        || has_capability('local/hrdepartment:viewownpayroll', $context);

    if (!$cancontent) {
        return;
    }

    $url = new moodle_url('/local/hrdepartment/index.php');
    $name = get_string('pluginname', 'local_hrdepartment');

    $node = $nav->add(
        $name,
        $url,
        navigation_node::TYPE_CUSTOM,
        $name,
        'local_hrdepartment',
        new pix_icon('i/report', '')
    );
    $node->showinflatnavigation = true;
}

/**
 * Builds the shared section tab bar (Dashboard | Lecturers | Staff |
 * Attendance | Leave | Payroll) shown at the top of every HR Department
 * page, filtered to the sections the current user has access to.
 *
 * @param string $selected the tab identifier to mark as active
 * @return \tabobject[]
 */
function local_hrdepartment_get_tabs(string $selected): array {
    $context = context_system::instance();
    $tabs = [];

    $tabs[] = new tabobject(
        'dashboard',
        (new moodle_url('/local/hrdepartment/index.php'))->out(false),
        get_string('dashboard', 'local_hrdepartment')
    );

    if (has_capability('local/hrdepartment:managelecturers', $context)) {
        $tabs[] = new tabobject(
            'lecturers',
            (new moodle_url('/local/hrdepartment/lecturer/index.php'))->out(false),
            get_string('lecturers', 'local_hrdepartment')
        );
    }

    if (has_capability('local/hrdepartment:managestaff', $context)) {
        $tabs[] = new tabobject(
            'staff',
            (new moodle_url('/local/hrdepartment/staff/index.php'))->out(false),
            get_string('staff', 'local_hrdepartment')
        );
    }

    if (has_capability('local/hrdepartment:manageattendance', $context)
        || has_capability('local/hrdepartment:viewownattendance', $context)) {
        $tabs[] = new tabobject(
            'attendance',
            (new moodle_url('/local/hrdepartment/attendance/index.php'))->out(false),
            get_string('attendance', 'local_hrdepartment')
        );
    }

    if (has_capability('local/hrdepartment:manageleave', $context)
        || has_capability('local/hrdepartment:viewownleave', $context)) {
        $tabs[] = new tabobject(
            'leave',
            (new moodle_url('/local/hrdepartment/leave/index.php'))->out(false),
            get_string('leave', 'local_hrdepartment')
        );
    }

    if (has_capability('local/hrdepartment:managepayroll', $context)
        || has_capability('local/hrdepartment:viewownpayroll', $context)) {
        $tabs[] = new tabobject(
            'payroll',
            (new moodle_url('/local/hrdepartment/payroll/index.php'))->out(false),
            get_string('payroll', 'local_hrdepartment')
        );
    }

    return $tabs;
}
