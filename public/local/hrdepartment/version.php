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
 * Version file for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->component = 'local_hrdepartment';

// 2026-09-12, v2026090602/0.7.0: fixed a site-wide crash reported on a
// production deploy ("Table hrdep_studentleaveapp does not exist",
// thrown from student_leave_manager::is_approver(), called unconditionally
// on every logged-in page load by hook_callbacks::extend_user_menu() and
// by lib.php's local_hrdepartment_extend_navigation()).
//
// Root cause: db/install.xml was never kept in sync with db/upgrade.php.
// The student-leave tables (hrdep_studentleavetype/-leaveapp/
// -leavebalance, plus hrdep_studentleaveapp.approverid) were only ever
// created by upgrade.php's incremental steps (savepoints 2026081400,
// 2026081600, 2026081900) - a site that INCREMENTALLY UPGRADED through
// those versions got them for free and never noticed anything wrong. A
// site where this plugin was FRESHLY installed instead (a new site, or
// installing the plugin for the first time) uses install.xml alone as
// the complete schema and stamps the plugin straight to the current
// version - Moodle does not replay upgrade.php's steps on a fresh
// install - so hrdep_studentleaveapp (and its sibling tables) never got
// created there at all, while every logged-in page's account-menu
// hook kept querying it unconditionally and crashed.
//
// Two-part fix:
// 1. student_leave_manager::is_approver() now guards with
//    $DB->get_manager()->table_exists() and returns false instead of
//    querying a table that might not exist - this is the fix that
//    protects any site regardless of how it got into this state, and
//    can never regress even if install.xml drifts again in future.
// 2. db/install.xml now includes all three tables in their FINAL
//    current shape (i.e. matching upgrade.php's state after every step
//    through 2026081900), so a FUTURE fresh install gets the complete
//    schema immediately and never hits this at all.
//
// This does NOT retroactively fix a site that is ALREADY installed with
// the incomplete schema (its plugin version in mdl_config_plugins is
// already stamped at/above 2026081400, so Moodle will not re-run
// upgrade.php's table-creation steps just because install.xml changed) -
// that site's three missing tables must be created directly; see the
// standalone repair script provided alongside this fix.
//
// See hrdepartment-studentleave-schema-fix memory for the full
// investigation.
// 2026-09-12, v2026090603/0.7.1: the user reported that after logging
// in, the top navbar showed local_financedepartment's "Scholarship"
// entry but never an "HR Department" entry for HR staff, and asked that
// a site admin see BOTH "HR Department" and "Finance" in the top bar.
// Root cause: this plugin's classic extend_navigation() (lib.php) only
// ever populates the site nav tree/side drawer on this Moodle version,
// never the TOP primary nav bar - local_financedepartment already hit
// and fixed this exact gap for itself on 2026-09-10 (see its db/hooks.php
// docblock) via a core\hook\navigation\primary_extend hook callback,
// but this plugin never got the equivalent fix. Added
// classes/hooks/navigation/primary_extend.php + a matching db/hooks.php
// registration (alongside the pre-existing extend_user_menu one) so "HR
// Department" now shows in the top bar too - gated on
// access_manager::can_access_hr_department() (HR staff or site admin),
// DELIBERATELY narrower than extend_navigation()'s own broader
// self-service cancontent check, since a plain student must keep seeing
// ONLY local_financedepartment's "Scholarship" entry, not this one - see
// that plugin's matching same-day exclusion in its own
// can_view_navigation_entry(). No schema/capability/lang-string change.
// Needs a Moodle cache purge to take effect (new hook registration).
$plugin->version   = 2026090603;
$plugin->requires  = 2024042200; // Moodle 4.4+.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.7.1';
$plugin->dependencies = [
    'mod_attendance' => ANY_VERSION,
];
