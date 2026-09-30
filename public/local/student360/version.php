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
 * Version details for the Student 360 Profile local plugin.
 *
 * This plugin is a pure read-only AGGREGATOR - it owns no database
 * tables of its own. It hard-depends on local_hrdepartment (student
 * search + attendance + leave data) and local_financedepartment (fee
 * record + scholarship + discount data) and calls their manager classes
 * directly (no web-service/API layer), the same pattern
 * local_financedepartment already uses to reuse
 * local_hrdepartment\student_manager for its own student search - see
 * [[financedepartment-schema]] project memory.
 *
 * Access rule (classes/access_manager.php): view-any (search + open any
 * student's profile) is granted to anyone local_hrdepartment or
 * local_financedepartment already recognise as department staff (their
 * own can_access_hr_department()/can_access_finance_department(), both
 * role/capability-driven post-migration), OR a site administrator, OR
 * the plain local/student360:view capability directly. view-own is the
 * student's self-service capability to see only their own aggregated
 * profile.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_student360';
$plugin->version   = 2026091800;
$plugin->requires  = 2024042200; // Moodle 4.4+.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.1.0';
$plugin->dependencies = [
    'local_hrdepartment' => 2026091602,
    'local_financedepartment' => 2026091601,
];
