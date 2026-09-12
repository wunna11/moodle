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
 * Version file for the Finance Department local plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->component = 'local_financedepartment';
$plugin->version   = 2026091206;
$plugin->requires  = 2024042200; // Moodle 4.4+.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.9.7';

// 2026-09-10: Step 7.11 (Finance Dashboard & Reports) built, per the
// user's own request ("let's go step 7.11 and create modern beautiful
// design"). Confirmed four scope decisions via AskUserQuestion before
// building (all Recommended):
//
// (1) NEW page, pages/reports/index.php, gated on
// local/financedepartment:viewfinancereports specifically - rather than
// growing index.php itself into the dashboard, which is what that
// page's OWN docblock had said would happen "once Step 7.11 is built".
// Reason: index.php is the shared navigation hub every role lands on,
// including a plain self-service student (viewownfeerecord only) - an
// institution-wide "total collected"/"total outstanding" figure has no
// business rendering unconditionally on that shared page even if
// capability-gated inline, and the same separate-page shape Step 7.9
// already established for pages/feerecords/all.php (own tab, own hero
// link from the general hub) fit naturally here too.
//
// (2) Charts: YES, using Moodle's OWN core\chart_pie/core\chart_bar
// (the Chart.js wrapper already shipped with Moodle core, rendered via
// $OUTPUT->render($chart) - confirmed available in this checkout before
// asking) - explicitly requested for the "modern beautiful design" the
// user asked for, rather than stat cards/tables alone. Two charts: a
// doughnut (collected vs outstanding) and a bar chart (outstanding
// balance by course category, capped at
// dashboard_manager::MAX_CHART_CATEGORIES = 10 bars).
//
// (3) Export formats: CSV + Excel + PDF, all three - confirmed this
// site already has all three core dataformat subplugins installed
// (dataformat_csv/dataformat_excel/dataformat_pdf, checked via ls before
// asking) via one shared call to \core\dataformat::download_data(), so
// supporting all three cost no more than supporting one.
//
// (4) Export scope: TWO separate exports - "Export fee records" (one
// row per fee record: student, category, year, total/scholarship/
// discount/paid/balance, status, assigned date) and "Export payments"
// (one row per payment/refund transaction: receipt number, student,
// category, year, type, amount, date, method, status) - six download
// links total (3 formats x 2 types) in pages/reports/index.php's export
// row.
//
// New: classes/dashboard_manager.php (all aggregation queries) +
// pages/reports/index.php (the dashboard) + pages/reports/export.php
// (the six-way export dispatcher). A deliberate, NOT-asked-about design
// choice made while planning (see dashboard_manager.php's own class
// docblock for the full reasoning): the category + academic year
// filters narrow every summary card/chart/export, but the STATUS filter
// on the dashboard page only narrows the two exports, not the five
// summary cards - those cards ARE themselves a status breakdown, so
// applying a status filter to them would zero out most cards for no
// useful reason (the filter bar shows a small inline hint,
// reportsstatusfilterhint, explaining this).
//
// The overdue-count aggregate and the fee-records export's status
// filter both reuse the EXACT SAME "status as DISPLAYED, not the raw DB
// column" EXISTS-subquery logic classes/table/feerecord_table.php's
// build_status_where() introduced in Step 7.9 - duplicated in SQL form
// (dashboard_manager::overdue_exists_sql()) rather than shared, since
// feerecord_table's version is a protected instance method tied to that
// class's own $params naming; kept in sync manually if the overdue rule
// ever changes, same caveat that class's own docblock already carries.
//
// lib.php's local_financedepartment_get_tabs() gained a Reports tab in
// its OWN top-level slot (not an else-if branch anywhere else - a full
// finance manager sees BOTH "Fee records" and "Reports" tabs
// simultaneously), gated purely on viewfinancereports. index.php gained
// a matching quicklink tile. Also fixed, while touching index.php: its
// $canviewreports variable was plain has_capability() instead of
// access_manager::can_manage() like every other capability check on
// that page - a pre-existing inconsistency (an actual Finance-department
// hrdep_employee with no capability directly assigned would never have
// seen ANY viewfinancereports-gated UI on that page) that would have
// silently broken the new Reports tile for that exact case if left
// unfixed; and index.php's bottom "no sections yet" empty-state
// condition, which never included $canviewreports at all (harmless
// before today since no tile existed for that capability, but would
// have shown a contradictory "no sections" message directly under the
// new Reports tile if left as-is).
//
// New CSS (styles.css) for the stat cards/chart cards/export buttons -
// this is the one place in the plugin's stylesheet with an explicitly
// heavier visual treatment than the rest (gradient icon badges, hover
// lift, per Step 7.11's own "modern beautiful design" ask) - see that
// file's own updated docblock.
//
// No DB schema/capability change (viewfinancereports already existed
// since Step 7.1, and its own db/access.php comment already named
// "Finance dashboard/reports (Step 7.11)" as one of its two intended
// uses). 14 new lang strings. Verified with php -l on every new/changed
// file (staged to a cloud container for the check, no local PHP CLI on
// this device) and the usual lang-string cross-check (used-vs-defined
// python scan, zero missing/duplicate).

// 2026-09-10: Step 7.9 (student fee statement & views) - the last two
// pieces of this step that were missing, per the user's own request to
// "start Step 7.9": pages/myfeerecord/* (student self-view) and
// pages/feerecords/view.php (a finance-staff itemised statement) were
// ALREADY built by earlier same-day work - only the "finance staff list
// view: all students' fee status, searchable/filterable by category,
// year, status" piece was missing, confirmed by grep before starting
// (pages/feerecords/index.php's own docblock explicitly named this as
// "Step 7.9's job", and db/access.php's viewfinancereports/
// viewallrecords capabilities were defined since Step 7.1 with comments
// naming Step 7.9 but never actually checked by any page).
//
// Asked the user three scope questions via AskUserQuestion before
// building (all Recommended): (1) pages/feerecords/view.php (the
// existing itemised statement) is widened from managefeerecords-only to
// ALSO accept local/financedepartment:viewallrecords, via a new
// access_manager::can_manage_any()/require_manage_any() helper (OR of
// several capabilities, still OR'd with the usual finance-staff blanket
// grant) - a report-only viewer can now drill into a student's
// statement from the new list, but sees no Edit/Cancel action and no
// managefeerecords-gated back-link (routes to the new list instead);
// (2) the new list's filters are category + academic year + status
// (the spec's three) PLUS a student name/email search box, matching
// every other list page in this plugin; (3) built as a new page,
// pages/feerecords/all.php, rather than folded into
// pages/feerecords/index.php's existing per-student search-then-assign
// flow - index.php (managefeerecords-gated) gained a hero-button link
// to it, and a viewfinancereports-only holder (no managefeerecords) gets
// their own new tab-bar entry routing straight to it, the same
// routing-split pattern already used for My Fee Record/Scholarships/
// Discounts.
//
// New: classes/table/feerecord_table.php (a \core_table\sql_table,
// same shape as feestructure_table/discount_table). Its status filter
// deliberately matches what local_financedepartment_feerecord_status_badge()
// DISPLAYS, not the raw financedep_feerecord.status column - "Overdue"
// is Step 7.8's live-computed state, never persisted, so filtering by it
// needed an EXISTS subquery against financedep_installmentplan/
// financedep_installmentsched (active plan, pending/partiallypaid
// schedule line, due date passed) mirroring
// feerecord_manager::has_overdue_installment()'s logic in SQL, and
// filtering by "Unpaid"/"Partially paid" explicitly EXCLUDES an overdue
// record for the same reason - otherwise a record could show as
// "Overdue" in the Status column while matching the "Unpaid" filter, or
// fail to appear under "Overdue" at all.
//
// No DB schema/capability change (viewfinancereports/viewallrecords both
// already existed since Step 7.1). 4 new lang strings (allfeerecords,
// allfeerecordsdesc, nofeerecordsfound, backtoallfeerecords). Verified
// with php -l on every new/changed file and the usual lang-string
// cross-check (comm -23, zero missing/duplicate).

// 2026-09-10 (post-deploy feature, same day, follows the My Fee Record
// self-service feature below) - the user asked for a validation rule: a
// student who has already made a payment towards a program should not
// be able to request a scholarship for that same program.
//
// scholarshiprequest_manager gained has_paid_in_category(int $studentid,
// int $categoryid): bool - checks whether the student has any
// non-CANCELLED financedep_feerecord with paidamount > 0 whose fee
// structure belongs to $categoryid. Deliberately keyed on the CATEGORY
// (financedep_scholarship.categoryid), not a specific fee record -
// scholarship requests no longer carry a feerecordid at all as of
// v2026091004/0.8.0 (the fee-record field was removed from the
// submission form that same day), so this checks every fee record the
// student holds in the scholarship's own category, not just one.
//
// Wired into classes/form/scholarshiprequest_form.php's validation()
// only (same place has_pending_request()/the ACTIVE-status check
// already live) - submit()/scholarshiprequest_manager itself does not
// re-check this, matching this class's existing pattern where form
// validation is the only gate for has_pending_request() too. New lang
// string: errorscholarshipalreadypaid.
//
// Deliberately did NOT restore the old is_eligible()/category-matching
// restriction that was removed in v0.8.0 - that removal was a separate,
// explicit user decision (any ACTIVE scholarship may be requested for
// any program) and is unrelated to this new already-paid guard, which
// only blocks a student who has started paying for the SAME program the
// scholarship is for.
//
// No schema/capability change. 1 new lang string. Verified with php -l
// on classes/scholarshiprequest_manager.php,
// classes/form/scholarshiprequest_form.php, and the lang file.

// 2026-09-10 (post-deploy feature, same day, follows the multiple-file
// upload feature below) - the user asked (in Burmese) for a student to
// be able to see their own paid/payment information, including
// installment plans, in their own account - "only the information
// related to them".
//
// Investigated first: local/financedepartment:viewownfeerecord already
// existed (defined since Step 7.1) and was already checked in index.php
// ($canviewown) and access_manager::can_view_navigation_entry() - but it
// was never actually WIRED to any page. A student holding it saw no
// broken link, just nothing at all: no tile, no tab, no page.
//
// Built pages/myfeerecord/index.php (list of the logged-in student's own
// fee records via feerecord_manager::get_for_student($USER->id), reused
// as-is) and pages/myfeerecord/view.php (one record's read-only detail:
// balance breakdown, full payment history via
// feepayment_manager::get_for_feerecord(), and installment schedule via
// installmentplan_manager::get_for_feerecord()/get_schedule() if a plan
// exists) - both gated on viewownfeerecord via a direct
// require_capability() call, same pattern already established for
// submitscholarshiprequest/submitdiscountrequest (see db/access.php's
// docblock). view.php also double-checks $feerecord->studentid ===
// $USER->id (a managefeerecords-holding finance-staff viewer is let
// through too, harmless since they already see everything on the admin
// pages) so one student can never open another's fee record via this
// page even by guessing/changing the id in the URL.
//
// Deliberately a SEPARATE section from pages/feerecords/*.php (the
// finance-staff admin pages) rather than reusing those with extra
// permission branching bolted on - matches this plugin's established
// pattern of always giving student self-service its own pages
// (scholarshiprequests, discountrequests) instead of retrofitting the
// admin ones; no edit/cancel/record-payment/void actions exist anywhere
// on the new pages.
//
// Wired in two more places: lib.php's local_financedepartment_get_tabs()
// gained an else-if branch (managefeerecords -> the existing admin
// "Fee records" tab; else viewownfeerecord -> a new "My Fee Record" tab
// routing to myfeerecord/index.php, same routing-split shape as the
// scholarships/discounts tabs). index.php's quicklink tile section
// gained the identical else-if split ($canviewown was computed there
// already but never actually used before this fix - see index.php's
// existing nopermissions guard). local_financedepartment_extend_navigation()
// and access_manager::can_view_navigation_entry() needed NO change -
// viewownfeerecord was already included in the top-nav visibility check
// from earlier work, it just had nowhere to go until now.
//
// Deliberately NOT built: payment attachment download access for the
// student's own payments (pages/payments/* attachments stay
// finance-staff-only, local_financedepartment_pluginfile()'s 'payment'
// filearea branch unchanged) - not asked for, can be added later as a
// small extension to that branch's gate if the user wants it.
//
// No schema/capability change (viewownfeerecord already existed). 4 new
// lang strings (myfeerecord, myfeerecorddesc, myfeerecordsempty,
// backtomyfeerecords). Verified with php -l on all 6 changed/new files.

// 2026-09-10 (post-deploy feature, same day, follows the void.php
// format_money()/payment_type_badge() fix below) - the user asked for a
// multiple-file upload field on the "Record payment"/"Record refund"
// form (classes/form/feepayment_form.php), to attach supporting
// documents such as a receipt or bank transfer confirmation.
//
// Same shape as scholarshiprequest_form/discountrequest_form's own
// attachment field: a filemanager element (feepayment_form::
// ATTACHMENT_TYPES = pdf/jpg/jpeg/png, ATTACHMENT_MAXFILES = 5),
// deliberately optional (no addRule('required')), added to both modes
// of the shared form (payment and refund alike - not asked to be
// restricted to one mode). pages/payments/create.php saves the files
// via file_save_draft_area_files() into a NEW 'payment' filearea
// (itemid = the new financedep_feepayment row's own id) right after
// feepayment_manager::record_payment()/record_refund() returns the new
// id, same "save right after insert, before redirect" placement as
// pages/scholarshiprequests/submit.php.
//
// local_financedepartment_pluginfile() (lib.php) gained a third
// filearea branch for 'payment'. Unlike scholarshiprequest/
// discountrequest, a payment row has no "requestedby"/self-service
// submitter (finance staff always records payments - there is no
// student-facing payment flow at all, see feepayment_manager's
// docblock), so this branch has no separate owner check - it's gated
// identically to pages/payments/view.php itself: recordpayments OR
// managerefunds. pages/payments/view.php renders the attached files as
// download links (same pattern as pages/scholarshiprequests/view.php),
// shown only when at least one file exists.
//
// No schema/capability change (Moodle's File API needs no new column,
// same as every other attachment field in this plugin). New lang
// strings: paymentattachment, paymentattachment_help. See
// [[financedepartment-schema]] project memory for the full write-up.

// 2026-09-10 (post-deploy fix, same day, follows the feepayment_form.php
// format_money() fix below) - after that fix, the user next hit "Call to
// undefined function local_financedepartment_payment_type_badge()"
// opening pages/payments/void.php - a third occurrence of the same
// underlying mistake, this time in a plain page script rather than a
// form class, and one that couldn't simply be fixed by moving the call
// after $OUTPUT->header() (the offending $summary string is needed as
// customdata for feepaymentvoid_form's constructor, which must run
// BEFORE header()). Fixed by reimplementing the badge/formatting logic
// inline in void.php itself, using only core Moodle functions and the
// already-autoloaded constants class. See [[financedepartment-schema]]
// project memory for the full write-up.

// 2026-09-10 (post-deploy feature, same day, follows the display-name
// feature below) - the user reported (in Burmese) that a student
// shouldn't have to pick a fee record when submitting a scholarship
// request, and asked for multiple-file upload on the supporting-
// document attachment.
//
// Confirmed via three rounds of AskUserQuestion: (1) the fee record
// field is removed from scholarshiprequest_form entirely - not
// auto-selected behind the scenes, not deferred to an edge-case picker -
// financedep_scholarshipreq.feerecordid is now nullable (db/upgrade.php
// v2026091004) and always null for a new submission; (2) approving a
// scholarship request is now a pure history/decision record - it no
// longer auto-deducts anything from any fee record
// (scholarshiprequest_manager::approve() only still calls
// feerecord_manager::add_scholarship_amount() for a LEGACY request that
// still has a real feerecordid from before this change, so pre-existing
// approved-and-deducted requests are unaffected; delete() mirrors this
// for reversal); (3) the old category-eligibility restriction
// (is_eligible(), matching a fee record's category against the
// scholarship's own categoryid) is removed entirely, not replaced with
// a course-enrollment-based check - any ACTIVE scholarship may now be
// requested by any student holding submitscholarshiprequest, and
// finance staff use their own judgement when reviewing.
//
// A percentage-type scholarship's requestedamount is now null at
// submission (compute_suggested_amount() has no fee record left to
// compute a base amount against) - every display site (the admin table,
// the student's own request list, view.php, and audit history
// rendering) was updated to show a "{$a}% (final amount set on
// approval)" message instead of a misleading "0 MMK" in that case; the
// reviewer still types the real amountapproved value manually either
// way.
//
// Multiple-file upload: scholarshiprequest_form and discountrequest_form
// (the user asked for this to apply to both) both raised their
// filemanager's maxfiles from 1 to 5 (ATTACHMENT_MAXFILES constant on
// each form) - the matching file_save_draft_area_files() maxfiles option
// on each submit.php was updated to match exactly.
//
// financedep_scholarshipreq.feerecordid, scholarshiprequest_manager::get()/
// get_for_student(), and classes/table/scholarshiprequest_table.php's
// fee-record/category JOINs all changed from INNER to LEFT JOIN so a
// null-feerecordid request still displays correctly (category shows "-"
// instead of disappearing from the query results entirely).
//
// No new capabilities. 1 schema change (feerecordid: NOT NULL -> NULL,
// via db/upgrade.php). A few new/reworded lang strings
// (requestedamountpercentagebased, historyrequestsubmittedpercentage,
// historyrequestapproved/scholarshiprequestsdesc/submitrequestdesc/
// myscholarshiprequestsdesc reworded since they described behaviour that
// no longer applies) - nofeerecordsownscholarship left orphaned rather
// than deleted, per this codebase's usual practice. See
// [[financedepartment-schema]] project memory for the full write-up.

// 2026-09-10 (post-deploy feature) - the user asked for a
// role-based display name: "Scholarship" instead of "Finance Department"
// for a plain student's nav label and page title/heading, while an
// admin/finance-staff account keeps seeing "Finance Department"
// unchanged everywhere.
//
// classes/access_manager.php gained MANAGEMENT_CAPABILITIES (every
// local/financedepartment:* capability that marks someone as finance
// STAFF - management/approval/reporting - as opposed to the three plain
// self-service capabilities), is_finance_staff_viewer() (true for a
// Finance-department hrdep_employee/site admin OR anyone holding at
// least one management capability directly), and get_display_name()
// (returns the 'pluginname' string for a finance-staff viewer, the new
// 'pluginnamestudent' = "Scholarship" string otherwise). Deliberately
// added to access_manager.php rather than as a lib.php helper, even
// though every call site could reach a lib.php function just as easily -
// classes/hooks/navigation/primary_extend.php (added in the fix above)
// calls this from a hook callback dispatched during navigation setup,
// and this plugin's own CRITICAL RULE already warns lib.php's inclusion
// timing is not guaranteed outside of page-body code running after
// $OUTPUT->header(). access_manager.php is autoloaded with no such
// timing risk.
//
// Wired into BOTH navigation entry points (lib.php's
// local_financedepartment_extend_navigation() and the primary_extend
// hook callback, so the site-tree/drawer label and the top-bar label
// always agree) and every page's $PAGE->set_title()/set_heading() call
// plus index.php's hero title (~40 call sites, replaced mechanically -
// verified via `php -l` on every file and the usual lang-string
// cross-check afterwards). Deliberately did NOT touch the
// get_string('pluginname', ...) calls used inside a 403/
// moodle_exception('nopermissions', ...) message (4 call sites) - those
// identify the plugin/component in an error context, not user-facing
// branding.
//
// Since a plain student can only ever reach the self-service pages
// (blocked by capability from every finance-staff-only page), this
// swap is safe to apply uniformly across every page without a
// per-section allowlist - a student will only ever actually see
// "Scholarship" on the handful of pages they can open (landing page,
// scholarship catalog, their own scholarship/discount requests); a
// finance-staff viewer sees "Finance Department" everywhere, unchanged.
//
// No DB schema/capability change. 1 new lang string (pluginnamestudent).

// 2026-09-10 (post-deploy fix, same day, follows the nav-visibility fix
// below): the user reported the plugin STILL wasn't visible in the top
// nav bar even after that fix. Investigated by tracing this site's
// actual Moodle 5.2 navigation code rather than assuming the theme was
// simply hiding it as previously guessed: local_financedepartment_
// extend_navigation() (lib.php) only ever populates $PAGE->navigation
// (the site navigation tree/drawer) - core\navigation\output\primary::
// get_primary_nav() (which builds the TOP bar: Home/Dashboard/My
// courses/...) reads $PAGE->primarynav instead, a completely separate
// tree that extend_navigation() never touches on this Moodle version.
// core\navigation\views\primary::initialise() (Moodle core) builds that
// tree itself, then dispatches core\hook\navigation\primary_extend so
// plugins/themes can add their own nodes to it (theme_stream, also
// present on this site, already does exactly this - used as the
// reference implementation). lib/navigationlib.php also confirms the
// OLD global_navigation-based flat_navigation/showinflatnavigation
// mechanism this plugin's extend_navigation() relied on for "flat"
// visibility is deprecated on this Moodle version, so that flag no
// longer does anything either.
//
// Fixed by adding a SECOND navigation entry point: db/hooks.php
// registers \local_financedepartment\hooks\navigation\primary_extend
// for core\hook\navigation\primary_extend, adding the same "Finance
// Department" node directly to the top primary nav bar. Both entry
// points now share ONE capability check -
// access_manager::can_view_navigation_entry() (new method) - rather
// than each duplicating it, since duplicated-then-drifted checks are
// exactly how the earlier same-day nav-visibility bug happened.
// lib.php's extend_navigation() was refactored to call this shared
// method instead of inlining the OR chain itself.
//
// No schema/capability/lang-string change - db/hooks.php + one new
// class + an access_manager refactor only. Requires a Moodle cache
// purge (hook callbacks are cached) to take effect after deploy - see
// [[financedepartment-schema]] project memory for the full write-up.

// 2026-09-10 (post-deploy fix, same day as the scholarship catalog
// feature below): the user reported not being able to find the
// scholarship request pages anywhere in navigation as a student.
// Investigated: local_financedepartment_extend_navigation() (lib.php)
// only checked viewfinancereports/viewownfeerecord to decide whether to
// add the "Finance Department" nav node - it never checked the two new
// submitscholarshiprequest/submitdiscountrequest self-service
// capabilities added by the 2026-09-09 fix. In practice most students
// also have viewownfeerecord (both are archetype user/CAP_ALLOW, so they
// usually travel together), which likely masked this - but a site whose
// student role has viewownfeerecord overridden/removed, or a custom role
// that grants only the submit capabilities, would never get this nav
// node at all, leaving the plugin's pages reachable only by direct URL.
// Fixed by adding both submit capabilities to the OR check. This does
// NOT change db/access.php or any capability definition - only the
// navigation-node visibility check in lib.php.
//
// Also relevant background given to the user: this particular Moodle
// site's top navigation bar (Home/Dashboard/My courses/"Kopere
// Dashboard") is a custom theme menu, not the standard Boost primary
// navigation - a plugin-added extend_navigation() node may render in a
// different place (a navigation drawer, "Site pages", the user menu)
// depending on the theme, so even after this fix the item may not
// appear in that exact top bar. The plugin's pages remain directly
// reachable by URL regardless (e.g. /local/financedepartment/index.php,
// /local/financedepartment/pages/scholarships/browse.php).

// 2026-09-10 (post-deploy feature, follows the 2026-09-09 student
// self-service fix below): the user asked for a page where a student can
// see which programs/course categories currently have a scholarship,
// before deciding whether to submit a request. Confirmed scope via
// AskUserQuestion (all Recommended): (1) show every ACTIVE scholarship
// across every category system-wide (a catalog), not just categories
// matching the viewer's own fee records; (2) any logged-in user can view
// it, not gated on submitscholarshiprequest or any manage/approve
// capability - a plain require_login() only; (3) each row gets a
// "Request this" link (shown only to a viewer who actually holds
// submitscholarshiprequest) that pre-selects that scholarship on the
// submit form.
//
// New: pages/scholarships/browse.php (read-only, reuses the new
// scholarship_manager::get_active_catalog()). classes/form/
// scholarshiprequest_form.php gained an optional `presetscholarshipid`
// customdata key (submit.php now accepts a `scholarshipid` GET param)
// to pre-select the scholarship element when arriving from a catalog
// row's "Request this" link. pages/scholarshiprequests/index.php's
// student "my requests" branch gained a "Browse scholarships" hero
// button alongside "New request" for discoverability. No schema/
// capability change; 5 new lang strings, zero missing/duplicate.

// 2026-09-09 (post-deploy fix, same day as Step 7.8) - scholarship AND
// discount request submission was backwards: only finance staff
// (managescholarships/managediscounts) could reach the submit form,
// picking the target student from a system-wide autocomplete - so a
// student could never nominate/request for themselves, only finance
// staff could submit on their behalf. The user reported this directly
// ("student ka thin ya mesha, finance staff ka mahotphu" - roughly "the
// student should submit it, not finance staff").
//
// Fixed by making submission student self-service on BOTH request
// types: two new capabilities, local/financedepartment:
// submitscholarshiprequest and submitdiscountrequest (archetype 'user',
// CAP_ALLOW - every logged-in user by default), checked directly via
// has_capability()/require_capability() rather than through
// access_manager::can_manage() - same pattern already established by
// viewownfeerecord, since this is a per-user self-service capability,
// not a finance-management one. Confirmed via AskUserQuestion (both
// Recommended): (1) student self-service only - finance staff's
// nominate-on-behalf-of flow is removed entirely, not kept alongside;
// (2) apply the same fix to discount requests too, not scholarships
// only.
//
// classes/form/scholarshiprequest_form.php / discountrequest_form.php:
// the studentid autocomplete is gone - the student is always the
// logged-in viewer (a hidden field + read-only static display), and
// feerecordid is now scoped to that student's own fee records only via
// the new feerecord_manager::get_active_options_for_student() (the old
// system-wide get_active_options() is unchanged and still used by
// installmentplan_form, which remains finance-staff CRUD).
// pages/{scholarship,discount}requests/submit.php now gate on the new
// submit capability and show a friendly empty state (no form) if the
// student has no fee record to request against at all.
// pages/{scholarship,discount}requests/view.php now let the request's
// own submitter view it (read-only - approve/reject/delete stay
// finance-staff-only, unaffected). pages/{scholarship,discount}requests/
// index.php gained a separate "my requests" branch for a plain student
// (their own requests only, with the "New request" action that used to
// sit in the finance-staff admin view) - the admin browsable list is
// unchanged for finance staff. lib.php's tab bar and the plugin landing
// page route a student's Scholarships/Discounts tab/tile to the
// requests list instead of the (managescholarships/managediscounts-
// gated) definitions list, which a student can't access.
// local_financedepartment_pluginfile() now also lets a request's own
// submitter download their own supporting-document attachment, which
// was impossible before this fix (only finance staff could).
//
// No schema/upgrade change (db/access.php only). 8 new lang strings, 4
// existing hero-subtitle strings reworded since they described the old
// "search for a student" flow. See [[financedepartment-schema]] project
// memory for the full write-up.

// 2026-09-09: Step 7.8 (fee record status history + overdue detection)
// built - two independent gaps closed, both traced to comments already
// left in the codebase (audit_manager's own class docblock names "Step
// 7.8"; feerecord_manager::save_and_recalculate()'s docblock flagged
// OVERDUE as never implemented). Asked the user what functions this step
// would include before building, then confirmed two scope questions via
// AskUserQuestion (both Recommended): (1) OVERDUE detection is
// LIVE-COMPUTED, same "never persisted" pattern as Step 7.6's installment
// overdue state - feerecord_manager gained has_overdue_installment()
// (checks the fee record's active installment schedule, if any, via
// installmentplan_manager::get_schedule_for_feerecord()/is_overdue()) and
// display_status() (returns FEE_STATUS_OVERDUE instead of the stored
// status when unpaid/partially-paid AND overdue) - financedep_feerecord.
// status itself is NEVER written as 'overdue'. No cron job was built, no
// existing scheduled-task infrastructure existed in either this plugin or
// local_hrdepartment to reuse (confirmed before asking). (2) automatic
// fee-record status changes (e.g. unpaid -> partially paid when a payment
// lands) now get a real audit_manager::log() entry - previously
// save_and_recalculate() (shared by add_scholarship_amount()/
// add_discount_amount()/add_payment_amount()) silently recalculated
// paidamount/scholarshipamount/discountamount/status with zero logging.
// Deliberately logs ONLY the status field (before/after) with a fixed
// generic reason (statusautorecalcreason), not which transaction caused
// it - reuses pages/feerecords/view.php's existing generic history
// diff-rendering with zero page changes, since it already special-cases
// status via feestatus_* strings.
//
// local_financedepartment_feerecord_status_badge() (lib.php) signature
// changed from (string $status) to (\stdClass $feerecord), calling
// feerecord_manager::display_status() internally - same redesign Step 7.6
// already did for the installment badge helper. All 4 call sites updated
// (pages/feerecords/index.php x2, pages/feerecords/view.php,
// pages/payments/index.php). No DB/capability change; 1 new lang string
// (statusautorecalcreason). See [[financedepartment-schema]] project
// memory for the full write-up.

// 2026-09-09: Step 7.7 (payment processing) built - classes/feepayment_manager.php,
// classes/form/feepayment_form.php, classes/form/feepaymentvoid_form.php,
// pages/payments/*.php. financedep_feepayment (schema already existed
// since Step 7.1) now finally gets real rows: every row is either a
// PAYMENT (money in, increases financedep_feerecord.paidamount via the
// new feerecord_manager::add_payment_amount()) or a REFUND (money out,
// decreases it) - the sign comes from paymenttype, never a signed
// amount column. Voiding a payment/refund reverses whichever effect it
// had, then marks it VOID - never deletes it, same "financial history is
// never destroyed" pattern as everywhere else in this plugin.
//
// Three scope decisions confirmed with the user via AskUserQuestion
// before building (all Recommended options): (1) linking a payment to a
// specific installment (financedep_feepayment.installmentschedid) is
// OPTIONAL in both directions - a payment can always be recorded against
// the fee record as a whole; (2) recording a refund is a DIRECT, instant
// entry (like a normal payment), not a request-and-approve workflow -
// see local/financedepartment:managerefunds; (3) voiding an existing
// payment requires that SAME higher-trust managerefunds capability, not
// the lower-trust recordpayments capability a normal payment needs.
//
// Proactively discovered while planning this step (not asked as a
// separate question, since it follows directly from the user's own Q1
// answer): installmentplan_manager::reschedule() AND create()'s
// cancelled-row-reuse path both delete/reinsert every schedule row for a
// plan, which would silently destroy a row with a real payment applied.
// Both now refuse (throw errorinstallmentplanhaspayments) if any
// schedule row already has paidamount > 0 - see
// installmentplan_manager::schedule_has_payments()'s docblock.
//
// Receipt numbers (financedep_feepayment.receiptnumber, UNIQUE) are
// SYSTEM-GENERATED (feepayment_manager::generate_receipt_number(), format
// RCPT-YYYYMMDD-XXXXXX) rather than typed in by finance staff - a
// discretionary implementation choice, not something the user was asked
// about; see feepayment_manager's class docblock if this needs revisiting.
//
// No new capabilities/tables (financedep_feepayment,
// local/financedepartment:recordpayments/managerefunds all already
// existed since Step 7.1); ~50 new lang strings. See
// [[financedepartment-schema]] project memory for the full write-up.

// 2026-09-08 (post-deploy fix, same day as Step 7.6, follows the hero-button
// fix below): the user reported the create form "felt stuck at 3" - it
// always started with exactly installmentplan_form::DEFAULT_REPEATS (3)
// blank installment rows, only growable one at a time via the
// repeat_elements() "Add another installment" button. Added an explicit
// "how many installments?" first step to pages/installments/create.php (a
// plain GET selector, 1-36) - the chosen count is passed into
// installmentplan_form as `repeatcount`, which (as of this fix) drives
// create mode too, not just reschedule mode - see that form's docblock and
// classes/form/installmentplan_form.php's MIN_REPEATS/MAX_REPEATS/
// DEFAULT_REPEATS constants. The "Add another installment" button still
// works afterwards for growing beyond the chosen count. No DB/capability
// change; 2 new lang strings (numinstallments, numinstallmentsdesc).

// 2026-09-08 (post-deploy fix, same day as Step 7.6): pages/installments/index.php
// had NO way to reach create.php except drilling into a specific student's fee
// record row (search -> pick student -> fee record with no plan yet). Every
// other section (scholarships, discounts) has an "Add X" button in the page
// hero - installments was missing its equivalent, so the create form was
// effectively undiscoverable. Fixed by adding a "Create installment plan"
// hero action button (reusing the existing createinstallmentplan string,
// same $heroactions pattern as pages/scholarships/index.php) that links to
// create.php with no feerecordid preset - the form's own autocomplete (any
// non-cancelled fee record without an active plan) handles the rest.

// 2026-09-08: Step 7.6 (installment plans) built - classes/installmentplan_manager.php,
// classes/form/installmentplan_form.php, pages/installments/*.php. An
// installment plan is a payment SCHEDULE for one fee record only - it
// never moves money or touches the fee record's balance (unlike
// scholarship/discount approval); only Step 7.7 (payments) will do that.
// Two scope decisions confirmed with the user via AskUserQuestion before
// building: (1) each installment's amount/due date is entered MANUALLY
// (Moodle's repeat_elements(), no auto-split), validated to sum to the
// fee record's current balance; (2) "overdue" is a LIVE, COMPUTED
// display state (installmentplan_manager::display_status()) - never
// persisted to financedep_installmentsched.status, no cron job exists
// for this. financedep_installmentplan.feerecordid has a UNIQUE index
// (one plan per fee record, ever) - create() reuses a CANCELLED row
// rather than inserting a duplicate. See [[financedepartment-schema]]
// project memory for the full 2026-09-08 write-up.

// 2026-09-10 (post-deploy fix #9): a further round of the same dashboard
// visual bug report - the user shared a NEW screenshot after fix #8, this
// time showing the "Outstanding balance by category" BAR chart rendering
// as a huge, near-square solid-colour block (and the doughnut chart's
// canvas was oversized too). Root cause this time was NOT this plugin's
// own CSS at all: Moodle core's own theme_boost/scss/moodle/core.scss
// hardcodes ".chart-area .chart-image { height: 48vh; width: 46vw; }" on
// large screens for EVERY Moodle chart site-wide. That sizing is
// reasonable for a chart with many data points, but this dashboard's bar
// chart currently has only a single category ("First Year (MBA)") - with
// responsive:true/maintainAspectRatio:false (Moodle's own Chart.js
// default, confirmed in lib/amd/src/chart_output_chartjs.js), Chart.js
// always fills its container exactly, so one bar reaching the y-axis max
// filled almost that entire 48vh x 46vw box, reading as a giant coloured
// square rather than a normal-looking bar. core\chart_bar/chart_series
// expose no maxBarThickness/barPercentage setter to constrain this from
// the PHP side (checked lib/classes/chart_bar.php, chart_series.php,
// chart_base.php), so fixed with a scoped CSS override instead:
// ".findept-chart-card .chart-area .chart-image { height: 280px; width:
// 100%; }" in styles.css - higher specificity than core's rule, and
// scoped only to this plugin's own dashboard chart cards, so no other
// page's charts anywhere else on the site are affected. CSS-only, no
// PHP/lang/capability change. See [[financedepartment-step711]] project
// memory.

// 2026-09-12: the user reported (in Burmese) that Add Scholarship's
// Amount field gives no indication of what unit to type once "Amount
// type" is switched to Percentage - it still just says "Amount" whether
// a fixed MMK figure or a 0-100 percentage is expected. Fixed in BOTH
// classes/form/scholarship_form.php and classes/form/discount_form.php
// (identical amounttype/amountvalue pattern in both) with a dynamic
// static-text hint shown right under the field, toggled instantly via
// Moodle's own $mform->hideIf('elementname', 'amounttype', 'eq'|'neq',
// constants::AMOUNT_TYPE_PERCENTAGE) - NOT by mutating the field's own
// label text via custom JS, which would risk clobbering mform's own
// required-field asterisk markup and would have been this plugin's
// first custom JS ever. Two new lang strings (amountvaluehint_fixed,
// amountvaluehint_percentage). No schema/capability change.

// 2026-09-10, Step 7.12 (Access) built - classes/access_summary_manager.php
// (new) + pages/access/index.php (new). A READ-ONLY permission summary
// page: (1) the actual Finance Department staff list (hrdep_employee
// rows, department "Finance") - the PRIMARY real-world access grant this
// plugin's access_manager::can_manage() uses, independent of any Moodle
// role; (2) every local/financedepartment:* capability this plugin
// defines (read LIVE from the {capabilities} table, not hardcoded, so
// it can never drift out of sync with db/access.php) with which Moodle
// roles currently hold each one at system context, via core's own
// get_roles_with_capability()/role_get_name(). The user was offered a
// choice between this read-only summary (chosen) and a fuller
// permission-EDITING UI - editing still goes through Moodle's stock
// Define roles page (linked from this page for a site admin viewer) or
// local_hrdepartment's Staff pages, deliberately not reimplemented here.
// New "Access" tab in local_financedepartment_get_tabs() (lib.php) and a
// matching index.php quicklink tile, both gated on the SAME
// viewfinancereports capability as the Reports tab/tile - no new
// capability was introduced for this step. 17 new lang strings, zero
// missing/duplicate (comm-style cross-check: 312 used / 399 defined).
// No DB schema change. See [[financedepartment-step712]] project memory
// for the full AskUserQuestion scope decision (gating, page content,
// navigation placement).

// 2026-09-10 (post-deploy fix #8): Step 7.11's finance dashboard
// (pages/reports/index.php) had a visual bug reported by the user - the
// two money-value stat cards ("Total collected", "Total outstanding")
// had their amount text clipped at the card edge (e.g. "20,000,000 MMK"
// cut off), while short numeric cards (counts) rendered fine. Root
// cause: .findept-stat-value used white-space: nowrap while the grid
// track width (grid-template-columns: repeat(auto-fit, minmax(220px,
// 1fr))) could be forced narrower than the nowrap text's natural width.
// Fixed in styles.css: removed white-space: nowrap in favour of
// white-space: normal + overflow-wrap: anywhere + a responsive
// clamp() font-size; widened the grid minmax to 240px; gave
// .findept-stat-card min-width: 0 and .findept-stat-body flex: 1 1 auto
// so flex/grid children can shrink instead of overflowing. Took the
// opportunity to also address the user's "design is missing" feedback
// with more visual polish: each stat card now has a per-variant
// gradient accent bar along its top edge (.findept-stat-card::before),
// matching the existing icon-badge gradient colours, plus a deeper
// icon shadow. This required lib.php's
// local_financedepartment_render_stat_card() to also add the
// findept-variant-{$variant} class to the OUTER card div (previously
// only the inner icon div carried a variant class), since the new
// ::before accent-bar rule is scoped to .findept-stat-card.findept-variant-*.
// CSS-only + one helper-function change - no lang string or capability
// changes. See [[financedepartment-step711]] project memory.

// 2026-09-06 (post-deploy fix #7): a PENDING scholarship/discount request
// could still be approved (and its amount deducted from the fee record)
// after the underlying scholarship/discount was deactivated, since
// set_status() never re-validates existing requests. Fixed with a
// deactivated-item guard mirroring the same day's self-approval guard
// pattern: scholarshiprequest_manager::approve() /
// discountrequest_manager::approve() now refuse (silent no-op) if the
// referenced item is no longer ACTIVE; review.php redirects with a clear
// warning as the primary user-facing gate; the Approve action is hidden
// in both the row-actions table and the view.php page (Reject stays
// available either way, so a stale request can still be cleared). See
// [[financedepartment-schema]] project memory.

// 2026-09-06: Step 7.5 (discount management) built - classes/discount_manager.php,
// classes/discountrequest_manager.php, pages/discounts/*.php,
// pages/discountrequests/*.php. Manual/hardship request-and-approve
// workflow only for this first pass (automatic rule-based discounts
// deferred - no due-date field exists yet on feerecord/feestructure to
// evaluate a rule against). See [[financedepartment-schema]] project
// memory for the full 2026-09-06 scope decision.

// 2026-08-22: this plugin now reuses local_hrdepartment's staff/department
// model instead of maintaining its own financedep_employee table - see
// classes/access_manager.php's docblock and [[financedepartment-schema]]
// project memory. Requires HR's departmentid support on hrdep_employee.


// 2026-09-12: plugin-wide visual redesign of styles.css, per the user's
// own request (in Burmese) to make the whole Finance department UI
// "clean and beautiful" - the design was "too plain" up to this point.
// Confirmed scope via AskUserQuestion before touching anything (all
// three Recommended options chosen): (1) redesign applies to EVERY page
// in the plugin, not only the Step 7.11 dashboard (previously the one
// deliberately more-designed corner of the stylesheet); (2) reuse the
// SAME colour/gradient family that dashboard already established
// (brand blue/purple gradient + the five stat-card variant colours -
// success/warning/danger/info/teal) rather than inventing a new
// palette, so the plugin now reads as one consistent design system
// instead of one polished section next to a plain rest; (3) Add/Edit
// forms get the same visual upgrade too, not just list/dashboard pages.
//
// Implemented as a CSS-ONLY change - zero PHP files touched, zero
// functional/logic risk. This was possible because of two patterns
// already consistently present across every page in this plugin
// (verified by grep before writing a single rule): every page's outer
// wrapper div carries a "local-financedepartment-<page>[-suffix]" class
// (its ONLY class, confirmed across every pages/*.php + index.php), and
// every Add/Edit form already wraps $form->display() in
// .findept-form-card. This let the "forms get upgraded too" requirement
// be met purely via scoped CSS selectors like
// [class^="local-financedepartment-"] .mform / .btn-primary / .btn-secondary,
// reaching every form/button/table-link in the plugin without editing
// a single form class or page script.
//
// New: a :root block of CSS custom properties (--findept-brand-1/-2 and
// -gradient, --findept-success/warning/danger/info/teal-1/-2,
// --findept-text/-text-muted/-border/-bg-soft, --findept-radius-sm/-md/-lg,
// --findept-shadow-sm/-md/-brand) centralising every colour/radius/shadow
// value used across the stylesheet - the exact same hex values Step
// 7.11's dashboard already used, just named and reused instead of
// hardcoded in multiple places. Restyled: .findept-tab-bar (pill-style
// tabs, active tab gets the brand gradient), .findept-page-hero (added a
// soft decorative radial-gradient highlight), .findept-filter-bar
// (shadow + focus-ring on inputs, gradient submit button),
// .findept-table-card (header row background wash, row hover
// highlight), .findept-empty-state (larger padding, styled icon),
// .findept-form-card/.findept-detail-card (refined shadow/typography),
// .findept-quicklink (icons became circular gradient badges, hover lift
// effect). The entire Step 7.11 dashboard section (stat cards, chart
// cards, export buttons) was preserved with IDENTICAL pixel output -
// only its hardcoded hex colours were refactored to reference the new
// custom properties, so post-deploy fixes #8 and #9's visual fixes are
// untouched.
//
// No DB schema, capability, or lang-string change - styles.css only.
// No PHP file was modified. See [[financedepartment-uipolish]] project
// memory for the full write-up.
// 2026-09-12: the user asked directly for index.php (the plugin's
// shared landing page) to become the finance dashboard itself, instead
// of the dashboard living on its own separate page. Confirmed scope via
// AskUserQuestion before touching anything (both Recommended options
// chosen): (1) the dashboard content (filter bar + 5 summary stat cards
// + 2 charts + CSV/Excel/PDF export links) now renders directly on
// index.php, ABOVE the quicklink tile grid, but ONLY for a viewer who
// holds viewfinancereports - every other role (a plain self-service
// student, finance staff without that specific capability, etc.) still
// sees exactly the plain quicklink hub index.php always was, completely
// unchanged; (2) the separate pages/reports/index.php page and its
// "Reports" tab (lib.php)/quicklink tile (index.php) were removed, so
// there is now a single dashboard entry point instead of two.
//
// index.php gained the exact same category/academic-year/status filter
// bar, dashboard_manager::get_summary()/get_outstanding_by_category()
// calls, core\chart_pie/chart_bar rendering, and export-link row that
// pages/reports/index.php used to have (moved, not reimplemented) -
// wrapped in "if ($canviewreports)" and placed between the page hero and
// the quicklink grid. $PAGE->set_url() now conditionally carries the
// three filter params, but ONLY when $canviewreports, so a viewer who
// never sees the dashboard section never gets filter params cluttering
// their landing-page URL either.
//
// lib.php's local_financedepartment_get_tabs() lost its "Reports" tab
// entry entirely (the dashboard is no longer a distinct page to link a
// tab to). pages/reports/index.php was deliberately NOT deleted outright
// (a discretionary safety choice, not asked about) - it is now a thin
// redirect stub forwarding straight to index.php with the same filter
// params, so an old bookmark or saved link keeps working instead of
// 404ing. pages/reports/export.php (the actual CSV/Excel/PDF download
// dispatcher) is completely unchanged - only the summary/chart page
// moved, not the exports themselves.
//
// No DB schema, capability, or lang-string change - index.php, lib.php,
// and pages/reports/index.php only. Verified with php -l on all three
// files and the usual lang-string cross-check (314 used / 373 defined,
// zero missing/duplicate). See [[financedepartment-uipolish]] project
// memory for the full write-up.

// 2026-09-12 (same day, follow-up to making index.php the finance
// dashboard): the user asked what else the dashboard should show. Given
// a short menu of options via AskUserQuestion, the user picked "pending
// scholarship/discount request counts + a recent payments feed" over
// collection-rate/overdue-list, payment-method breakdown, and monthly
// trend chart options.
//
// dashboard_manager::get_summary() gained two more unfiltered count
// fields, pendingscholarshiprequests/pendingdiscountrequests
// ($DB->count_records() against financedep_scholarshipreq/
// financedep_discountreq where status = PENDING) - same "unfiltered by
// category/year" treatment as the existing activescholarships/
// activediscounts fields, for the same reason (a request has nothing to
// filter a category/year against - scholarship requests haven't had a
// feerecordid at all since v2026091004/0.8.0).
//
// lib.php's local_financedepartment_render_stat_card() gained an
// optional $url parameter (backward compatible, every existing call
// site untouched) - when given, the whole card renders as a clickable
// link instead of a plain div. index.php's dashboard section uses this
// for two new stat cards, "Pending scholarship requests"/"Pending
// discount requests", each linking straight to that request type's
// PENDING-filtered review queue
// (pages/scholarshiprequests|discountrequests/index.php?status=pending,
// both pages already supported that filter param) - shown only to a
// viewer who holds the matching manage/approve capability, so a
// viewfinancereports-only reporting role never sees a card linking to a
// queue they'd 403 on. styles.css gained a small `a.findept-stat-card`
// rule so the link variant doesn't pick up the theme's default anchor
// underline/colour.
//
// index.php also gained a "Recent payments" widget (gated on
// recordpayments/managerefunds, matching pages/payments/view.php's own
// gate) - a 5-row table via feepayment_manager::get_recent(5), the
// EXACT SAME call pages/payments/index.php's own default view already
// makes, so no new manager/query code was needed - plus a "View all
// payments" link to that full page. Deliberately did NOT reproduce that
// page's per-row "click student name to filter" link - it turns out to
// reference $payment->studentid, a column financedep_feepayment does not
// have (only feerecordid) and get_recent()'s SQL never aliases one
// either, so that existing link on pages/payments/index.php silently
// resolves to an empty/missing param. Left AS-IS on that page (out of
// scope, pre-existing, not asked about) but flagged to the user
// separately - the new dashboard widget just shows the student's name as
// plain text instead of repeating the same latent bug.
//
// No DB schema or capability change. 3 new lang strings
// (pendingscholarshiprequests, pendingdiscountrequests,
// viewallpayments). Verified with php -l on every changed PHP file, a
// CSS brace-balance check (84/84), and the usual lang-string cross-check
// (317 used / 376 defined, zero missing/duplicate). See
// [[financedepartment-uipolish]] project memory for the full write-up.

// 2026-09-12, v2026091204/0.9.5: two post-deploy fixes, both reported
// directly by the user.
//
// (1) FIXED the pages/payments/index.php studentid bug flagged (but
// deliberately left unfixed) in the v0.9.4 changelog above: that page's
// default "Recent payments" view links each row's student name via
// $payment->studentid, but feepayment_manager::get_recent()'s SQL never
// selected/aliased such a column, so the link silently resolved to an
// empty param. Fixed at the source - get_recent() now also selects
// r.studentid (the financedep_feerecord row's real student), mirroring
// the exact same r.studentid selection feepayment_manager::get()
// already does. This one query-level fix automatically corrects BOTH
// call sites: pages/payments/index.php's own link now works, and
// index.php's "Recent payments" dashboard widget (added in v0.9.4) was
// updated to also link the student name the same way, replacing the
// plain-text rendering that widget deliberately used while the bug was
// still open.
//
// (2) FIXED the plugin's top primary-nav bar item ("Finance
// Department"/"Scholarship", added in v0.7.8's primary_extend hook)
// never showing as the ACTIVE/highlighted tab - "Home" stayed
// highlighted instead, on every Finance Department page. Root cause:
// Moodle's core\navigation\views\primary::search_and_set_active_node()
// only marks a primary-nav node active either (a) via an EXACT URL
// match against $PAGE->url (core\navigation\navigation_node::
// check_if_active(), default strength URL_MATCH_EXACT), or (b) if the
// page explicitly calls $PAGE->set_primary_active_tab($key) with a key
// matching the node's own key. This plugin's node is added with key
// 'local_financedepartment' (both lib.php's extend_navigation() and the
// primary_extend hook callback already pass that as the 5th ->add()
// arg), but NO page ever called set_primary_active_tab() - so route (b)
// was always unused, and route (a)'s exact-URL match only ever succeeds
// on a completely bare visit to index.php with zero query params (the
// node's action URL is exactly '/local/financedepartment/index.php');
// every other page in the plugin - all 40 of them, plus index.php
// itself whenever a categoryid/academicyear/status filter is active -
// has a different or longer URL and can never match, so Moodle's
// fallback logic defaults the highlight back to 'Home'. FIXED by adding
// $PAGE->set_primary_active_tab('local_financedepartment'); right after
// require_login() on every one of this plugin's 41 real pages
// (mechanically via a script matching each file's own require_login();
// call and preserving its indentation, then spot-checked) - the two
// pages with a student-self-service/staff branch that each call
// require_login() once but set_url() twice
// (scholarshiprequests/index.php, discountrequests/index.php) only
// needed the one insertion, since it does not depend on which branch's
// URL ends up set. pages/reports/index.php (now a pure redirect stub,
// see v0.9.3 above) and pages/reports/export.php (a raw file download,
// no page/header ever rendered) were deliberately left untouched - a
// redirect never reaches header() and a download has no nav bar to
// highlight.
//
// No DB schema/capability/lang-string change either fix. Verified with
// php -l on all 42 changed PHP files and the usual lang-string
// cross-check (zero missing/duplicate). See [[financedepartment-uipolish]]
// project memory for the full write-up.


// 2026-09-12, v2026091205/0.9.6: the user asked (in Burmese) to turn Add
// Fee Structure's plain "Academic year" text field into a picker, but
// raised a real concern themselves in the same message - an academic
// year isn't always exactly one calendar year, it can sometimes be
// described by specific months instead. Asked via AskUserQuestion how
// to handle that before building (4 options: year-range dropdown +
// custom fallback / single-year dropdown only / full month+year range
// picker / leave it free text) - the user picked "Year range dropdown +
// Custom option (Recommended)".
//
// classes/form/feestructure_form.php: the single 'academicyear' text
// field was replaced with two year <select> elements
// (academicyearfrom/academicyearto, populated currentyear-5 ..
// currentyear+10) plus an 'academicyearcustom' advcheckbox that
// hideIf()-reveals a free-text 'academicyeartext' field instead (the
// exact same hideIf() toggle pattern this form's Amount/Percentage hint
// fix already used in v0.9.1). The single stored value
// (financedep_feestructure.academicyear, still a plain char(20) - NO
// schema change) is unchanged in shape: picking 2026/2027 stores
// "2026-2027", picking the SAME year for both stores just "2026", and
// the custom checkbox stores whatever free text was typed - so every
// other manager/table/filter/export that already reads academicyear as
// a plain string (dashboard_manager, feerecord_table, the reports
// export, etc.) needed ZERO changes. A new protected static
// compose_academicyear() helper builds that final string from whichever
// path was used, shared by validation() (duplicate-checks the value
// that will actually be saved) and a new get_data() override (so
// $data->academicyear keeps existing for every caller exactly as
// before). A new set_data() override does the reverse for the EDIT
// form - it parses the existing stored string back into the picker
// (regex match on "YYYY-YYYY" or "YYYY") or into the custom fallback
// (anything else, e.g. a month-based label saved before this change or
// via the custom checkbox), so editing an old fee structure doesn't
// reset to today's default years.
//
// 2 new lang strings for labels/checkbox/custom field
// (academicyearfrom, academicyearto, academicyearcustom,
// academicyeartext, erroracademicyearrange - 5 total) plus an updated
// academicyear_help string explaining the picker and the custom
// fallback. No DB schema change. Verified with php -l and the lang
// cross-check (294 used / 381 defined, zero missing/duplicate). See
// [[financedepartment-uipolish]] project memory for the full write-up.


// 2026-09-12, v2026091206/0.9.7: the user asked (in Burmese) to remove
// the "Required" box that appears below the Save/Cancel buttons on
// every Add/Edit form in the plugin. This is Moodle CORE's standard
// mform behaviour, not anything this plugin built - every \moodleform
// with at least one required field automatically gets a required-fields
// legend appended via lib/formslib.php's setRequiredNote() (called in
// every form's constructor), rendered as
// `<div class="fdescription required" aria-hidden="true">[icon]
// Required</div>` right after the form's closing buttons - the same box
// this plugin's forms have always shown, on every site running this
// Moodle version, not something introduced by any earlier change here.
//
// FIXED with one small CSS rule, `[class^="local-financedepartment-"]
// .mform .fdescription.required { display: none; }`, added to the
// existing "Plugin-wide form/table/button polish" section of styles.css
// (v0.9.2) - scoped the same way every other rule in that section is, so
// it only ever hides this legend on THIS plugin's own pages, never
// touching Moodle core's own admin forms or any other plugin's forms
// site-wide. Deliberately did NOT touch the PER-FIELD red asterisk
// marker next to each required field's label (`.fitem .required`, the
// rule directly above this new one in styles.css) - only the one
// whole-form legend box is hidden, so a field still visibly shows it is
// required, just without the explanatory legend repeating that at the
// bottom. The legend div was already `aria-hidden="true"` in Moodle's
// own markup (not something added by this fix), so hiding it visually
// has no additional accessibility impact beyond what Moodle core itself
// already intended.
//
// No DB schema/capability/lang-string/PHP change - styles.css only.
// Verified with a CSS brace-balance check (85/85). See
// [[financedepartment-uipolish]] project memory for the full write-up.


$plugin->dependencies = [
    'local_hrdepartment' => 2026081908,
];
