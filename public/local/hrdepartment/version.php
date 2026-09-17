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
// 2026-09-14, v2026091400/0.8.2: Phase 1 of a proposed, multi-phase
// migration away from the "department name string-match + blanket
// hrdep_employee grant" access model (see project memory
// hrdepartment-access-migration-plan.md for the full plan and why) -
// the user asked whether the current model is a good long-term fit and
// a phased migration plan was written up and agreed on.
//
// This step is purely additive/dual-write and changes NO live access:
// a new classes/role_sync_manager.php maps a Staff-type hrdep_employee
// record's department name ("HR"/"Finance") + employmentstatus to a
// real Moodle system-context role assignment (shortnames
// hrdepartmentstaff/financedepartmentstaff - created manually via
// Define roles as Phase 0, not by this plugin), and is now called from
// staff_manager::create()/update()/set_employment_status() after every
// write. access_manager::can_manage() itself is UNCHANGED and still the
// only thing that actually grants access - these role assignments are
// inert "shadow" data until a future Phase 2 deploy switches
// can_manage() over to reading them instead of querying hrdep_employee
// directly. A standalone one-off CLI backfill script (delivered
// directly to the user, NOT part of this plugin's codebase - same
// pattern as the 0.7.0 fix_studentleave_tables.php repair script) syncs
// every EXISTING Staff-type record retroactively; new/updated records
// are covered automatically going forward by this version. No schema,
// capability, or lang-string change.
// 2026-09-14, v2026091401/0.8.3: Phase 2 of the same migration - the
// user confirmed the Phase 1 backfill ran successfully (2 active Staff
// records, HR/Finance, synced) and asked to proceed. access_manager::
// can_access_hr_department() was cut over from reading an
// hrdep_employee row directly (is_staff_in_hr_department(), now dead
// code kept for a clean one-file rollback) to has_capability() against
// a new MANAGEMENT_CAPABILITIES const (the same 9 capabilities Phase 0
// Allow-checked on the hrdepartmentstaff role). can_manage() is now a
// plain has_capability() call - a site admin is already covered by
// Moodle's own has_capability() shortcut for them, so the separate
// is_siteadmin()/can_access_hr_department() OR is gone. Every public
// method's signature is UNCHANGED, so no caller (index.php, lib.php,
// primary_extend.php, student_leave_manager.php, and every lecturer/
// staff/students/attendance page) needed to change - confirmed via grep
// before making this change. local_financedepartment\access_manager got
// the mirror-image same-day cutover (its own v2026091208/0.9.9).
// can_manage_departments()/require_manage_departments() were NOT
// touched - already a separate, plain has_capability() check, per that
// method's own docblock. No DB schema/capability/lang-string change.
// 2026-09-14, v2026091402/0.8.4: Phase 4 of the same migration (cleanup,
// no live-access change) - the user asked what Phase 4 was, then said to
// go ahead. This plugin's two navigation entry points (lib.php's
// local_hrdepartment_extend_navigation(), the site nav tree/side drawer;
// classes/hooks/navigation/primary_extend.php's callback(), the TOP nav
// bar) each maintained their OWN copy of the "should this user see an HR
// Department nav entry at all" OR chain - this exact duplication is what
// caused the v2026091301/0.8.1 bug (primary_extend.php's copy was never
// updated when self-service capabilities were added to lib.php's copy,
// so a plain student had no nav path to their own leave/attendance pages
// at all). Even after that fix, the two copies became functionally
// IDENTICAL but stayed as separate code - exactly the drift risk that
// bug demonstrated, and primary_extend.php's own class docblock still
// claimed a "deliberately narrower" difference that was no longer true.
// Fixed by adding access_manager::can_view_navigation_entry() (mirrors
// local_financedepartment\access_manager::can_view_navigation_entry(),
// added there 2026-09-10 for the identical reason) - both nav entry
// points now call this ONE method for the visibility gate instead of
// maintaining their own copy. Deliberately did NOT touch the LABEL logic
// (lib.php's side-drawer entry always shows "HR Department"; primary_
// extend.php's top-bar entry picks between "HR Department"/"My HR"
// based on can_access_hr_department()) - that inconsistency pre-dates
// this change and unifying it would be a user-facing behaviour change,
// not a pure consolidation, so it was left alone and flagged in project
// memory instead of decided unilaterally. index.php's own similar-but-
// not-identical $canselfservice check (missing student_leave_manager::
// can_view()/is_approver()) was also deliberately left untouched - a
// different concern (which PAGE to render, not nav visibility) and out
// of this phase's stated scope. Verified: the extracted condition is
// byte-for-byte the same boolean logic both call sites had before, just
// in one place now. No schema/capability/lang-string change; no DB
// upgrade or cache purge needed (no new hook registration, just changed
// method bodies in already-autoloaded classes).
// 2026-09-16, v2026091600/0.8.5: two small UI fixes reported by the user
// directly. (1) The stat-card/quicklink circular icon badges on the
// Dashboard (and every other page reusing local_hrdepartment_render_
// stat_card()/render_quicklink() - Students/Lecturers/Staff directories,
// Attendance, Leave) rendered their icon glyph visibly off-center inside
// its circle: Moodle core (Boost) gives every <i class="icon ..."> a
// default right-only margin meant for an icon that prefixes inline text,
// and the flex/text-align centering these badges use centers the icon's
// MARGIN box, not the glyph itself - fixed with one page-unscoped CSS
// rule in styles.css zeroing that margin inside .hrdept-quicklink-icon/
// .hrdept-stat-icon, rather than duplicating the fix into every one of
// the existing page-scoped stat/quicklink CSS blocks. (2) The top-nav-bar
// self-service entry (classes/hooks/navigation/primary_extend.php) and
// index.php's self-service landing heading both showed the generic label
// "My HR" for a plain student or a leave-approving teacher - renamed to
// "Attendance & Leave" (pluginnameselfservice), since those two sections
// are the ONLY thing this landing page ever shows that audience (see
// index.php's $canselfservice branch and student_leave_manager::
// is_leave_attendance_only_role(), which hides Dashboard/Payroll from
// them entirely) - "My HR" implied HR-staff-only content that was never
// actually there for them. No PHP logic changed for either fix - CSS +
// one lang string only.
// 2026-09-16, v2026091601/0.8.6: same-day follow-up, again reported by
// the user directly - index.php's page heading (and browser title) were
// still literally "HR Department" for a plain student/self-service
// viewer, despite the v2026091600 rename above, because they came from
// student_leave_manager::get_page_heading() - a SEPARATE heuristic
// (is_leave_attendance_only_role()) that, for this user's account,
// disagreed with the $canviewdashboard/$canselfservice branch index.php
// actually rendered below it. Rather than chase that heuristic's edge
// case, index.php's heading/title are now derived directly from
// $canviewdashboard - the exact same flag that picks which branch
// renders - so they can never again disagree with the page's own
// content. (get_page_heading() itself is untouched - leave/*.php and
// attendance/*.php still use it for their own headings, which the user
// did not report as wrong.) Also, while in there: the self-service
// landing branch's plain <h2>+<p> was replaced with the same gradient
// local_hrdepartment_render_page_hero() banner Dashboard/Attendance/
// Leave already use, for visual consistency - and, noticing it while
// editing the same CSS block, departments/index.php's hero (which was
// ALSO calling render_page_hero() but was never added to styles.css's
// scoped selector list, so it rendered as plain unstyled text) was
// fixed the same way. No DB/capability/lang-string change beyond what
// v2026091600 already made.
// 2026-09-16, v2026091602/0.8.7: the user asked for the student
// self-service "My attendance" page (attendance/index.php's else
// branch) to be filterable by course and by attendance status
// (Present/Absent/Late/...), and for its history table to be
// full-width with a more attractive design instead of the plain
// generaltable it had before.
//
// student_attendance_manager::get_student_records() gained an optional
// third $statusacronym param (exact attendance_statuses.acronym match,
// same pattern as its existing $courseid param) and a new sibling
// get_student_courses() returns the distinct courses a student has
// attendance records in, to populate the course filter dropdown.
// index.php now renders a GET-based hrdept-filter-bar (same pattern as
// leave/reports.php) with a course <select> and a status <select> built
// from that student's own get_student_status_summary() rows - so the
// status options always match whatever acronyms this site's Attendance
// activities actually use, never a hardcoded P/A/L list - plus a Reset
// link shown only when a filter is active. The stat-card strip above it
// still respects the course filter (so switching course updates the
// counts) but deliberately ignores the status filter, otherwise
// selecting e.g. "Absent" would zero out the other cards. Status is now
// rendered as a colour-coded pill (green/red/amber for present/absent/
// late, a neutral pill for anything else, e.g. excused) instead of
// plain text.
//
// styles.css: table.local-hrdepartment-my-attendance now gets width:
// 100% (it was shrink-wrapping to its content, leaving a large empty
// gap in the card on wide screens) plus striped rows, a tinted header,
// and a stronger hover highlight, all scoped under
// .local-hrdepartment-attendance so no other page's table is affected.
// The .hrdept-filter-bar CSS itself already existed pre-scoped for this
// exact container (added earlier alongside leave/reports.php's filter
// bar) but had never actually been used on this page.
//
// No DB schema/capability change. No new lang strings (allcourses,
// allstatuses, filter, resetfilters, course, status, remarks,
// attendancedate, noattendancerecords all already existed).
$plugin->version   = 2026091602;
$plugin->requires  = 2024042200; // Moodle 4.4+.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.8.7';
$plugin->dependencies = [
    'mod_attendance' => ANY_VERSION,
];
