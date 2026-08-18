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
 * Admin settings for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_hrdepartment',
        get_string('pluginname', 'local_hrdepartment')
    );

    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_hrdepartment/currency',
        get_string('setting_currency', 'local_hrdepartment'),
        get_string('setting_currency_desc', 'local_hrdepartment'),
        'USD',
        PARAM_ALPHA
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_hrdepartment/payrollapprovalrequired',
        get_string('setting_payrollapprovalrequired', 'local_hrdepartment'),
        get_string('setting_payrollapprovalrequired_desc', 'local_hrdepartment'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_hrdepartment/notifyleavedecision',
        get_string('setting_notifyleavedecision', 'local_hrdepartment'),
        get_string('setting_notifyleavedecision_desc', 'local_hrdepartment'),
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_hrdepartment/defaultannualleavedays',
        get_string('setting_defaultannualleavedays', 'local_hrdepartment'),
        get_string('setting_defaultannualleavedays_desc', 'local_hrdepartment'),
        18,
        PARAM_INT
    ));

    // Note: the 'leavestatuslabel' setting that used to appear here
    // configured the read-only mod_attendance-sourced Leave report.
    // That report was superseded 2026-08-15 by the self-contained
    // student leave request/approval workflow (see
    // local_hrdepartment\student_leave_manager), which has no such
    // setting - removed rather than left dangling/unused.
}
