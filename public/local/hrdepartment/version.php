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
// 2026-09-13, v2026091300/0.8.0: the user asked (in Burmese) for a
// session-scope option on the student self-service leave form
// (leave/apply.php) - a student can now request leave for either a
// whole day (unchanged) or just 1+ specific mod_attendance session(s)
// (e.g. one class period) of one of their own courses on a single day,
// then still view it back on leave/myrequests.php alongside their
// whole-day requests. Confirmed 3 scope questions via AskUserQuestion
// before building (all Recommended): (1) a session-scope request never
// deducts from hrdep_studentleavebalance - it's a pure history/
// notification record, achieved for free by always storing
// totaldays = 0 for these rows so review_application()'s existing
// balance-adjustment maths is naturally a no-op, no special-case code
// needed; (2) the sessions a single application can cover must all fall
// on ONE calendar day (matching "1 or 2 periods of that day", not a
// multi-day session spree); (3) approving a session-scope request never
// writes back into any mod_attendance table - it stays purely within
// this plugin's own hrdep_studentleaveapp/-appsession tables, preserving
// student_attendance_manager's existing read-only-attendance rule.
//
// New table hrdep_studentleaveappsession (leaveappid -> sessionid
// junction) + new hrdep_studentleaveapp.leavescope field ('day'|
// 'session'). apply.php is now a 3-step flow: choose scope -> (if
// session) pick course + date -> the actual form (day form unchanged;
// a new student_leave_apply_session_form shows that date's sessions for
// that course as checkboxes, sourced read-only from a new
// student_attendance_manager::get_sessions_for_course_on_date()).
// myrequests.php/view.php updated to render session-scope rows (session
// list instead of a day count).
// 2026-09-13, v2026091301/0.8.1: same-day post-deploy fix - the user
// reported (in English) they could not find the leave request option in
// a student account at all. Root cause was TWO separate, pre-existing
// gaps that the new session-scope leave feature above simply exposed by
// making self-service leave something worth actually reaching for the
// first time:
// 1. classes/hooks/navigation/primary_extend.php (the TOP nav bar entry
//    point, added 2026-09-12) was deliberately HR-staff/admin-only by a
//    same-day design decision - a plain student was assumed to reach
//    their own pages via the classic extend_navigation() side-drawer
//    entry (lib.php) instead, which DOES check the broader self-service
//    condition, but doesn't render on this site's custom theme (the
//    exact top-bar-vs-drawer split this hook exists to work around in
//    the first place) - so a plain student had no nav path to this
//    plugin AT ALL. Fixed by widening the hook to also add a
//    differently-labelled entry (pluginnameselfservice = "My HR") for
//    any self-service-only viewer.
// 2. Even after landing on index.php, the self-service branch
//    (classes/output/my_summary.php) is built entirely from an
//    hrdep_employee record - dashboard_helper::get_my_snapshot() returns
//    null and the page shows nothing at all for a plain student or an
//    approver-only teacher, neither of whom necessarily has one (that
//    view was designed for STAFF/LECTURER self-service, a fully separate
//    data model from student_leave_manager/student_attendance_manager).
//    Fixed with a new index.php branch, gated on "$canselfservice but no
//    employee record", showing a small quicklink landing (Attendance/
//    Leave tiles, each individually capability-gated) instead of the
//    employee-oriented snapshot.
// No DB schema/capability change; 2 new lang strings
// (pluginnameselfservice, selfservicelandingsubtitle).
$plugin->version   = 2026091301;
$plugin->requires  = 2024042200; // Moodle 4.4+.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.8.1';
$plugin->dependencies = [
    'mod_attendance' => ANY_VERSION,
];
