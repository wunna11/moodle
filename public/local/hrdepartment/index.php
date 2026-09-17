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
 * HR Department landing page.
 *
 * Users who satisfy access_manager::can_access_hr_department() - Moodle
 * "staff" role + an HR-department Staff record, or a site administrator,
 * see the organisation-wide summary (lecturer/staff counts, payroll
 * totals, leave stats); users who only hold self-service capabilities
 * see a personal "My HR" snapshot instead; a user with neither sees a
 * simple "no access" notice.
 *
 * ("My roles" - a self-service list of every Moodle role assignment held
 * by the current user - was removed 2026-08-17. It was not specific to
 * this plugin's own data, so it was dropped in favour of Preferences >
 * Roles > This user's role assignments, the standard Moodle location for
 * that information.)
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\dashboard_helper;
use local_hrdepartment\output\dashboard_summary;
use local_hrdepartment\output\my_summary;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();

// 2026-09-16: the heading used to come from student_leave_manager::
// get_page_heading(), a heuristic (is_leave_attendance_only_role())
// meant to swap "HR Department" for a self-service-appropriate label -
// but the user reported it was still showing the literal "HR Department"
// page heading above the self-service tile grid below, which only ever
// offers Attendance/Leave, never anything HR-department-wide (a plain
// student has no path to Dashboard/Lecturers/Staff/etc). Rather than
// chase that heuristic's edge case, the heading (and title) are now
// decided by the EXACT SAME $canviewdashboard flag that picks which
// branch renders below, so they can never again disagree with what the
// page actually shows.
$canviewdashboard = access_manager::can_access_hr_department((int) $USER->id);
$pageheading = $canviewdashboard
    ? get_string('pluginname', 'local_hrdepartment')
    : get_string('pluginnameselfservice', 'local_hrdepartment');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($pageheading);
$PAGE->set_heading($pageheading);

$renderer = $PAGE->get_renderer('local_hrdepartment');

$canselfservice = has_capability('local/hrdepartment:viewownattendance', $context)
    || has_capability('local/hrdepartment:applyownleave', $context)
    || has_capability('local/hrdepartment:viewownpayroll', $context);
// my_summary's snapshot (dashboard_helper::get_my_snapshot()) is built
// entirely from an hrdep_employee record (own attendance/leave/payroll
// AS AN EMPLOYEE) - it has nothing to show, and nothing to link to, for
// a plain enrolled STUDENT or a leave-approver teacher, neither of whom
// necessarily has one. Added 2026-09-13 after a student reported they
// could find no way to reach leave/apply.php at all: this was the other
// half of that gap (the top nav bar entry was the first half - see
// classes/hooks/navigation/primary_extend.php) - my_summary silently
// rendered a "no profile" dead end even once a student DID land here.
$hasemployeerecord = (bool) dashboard_helper::get_employee_for_user((int) $USER->id);

echo $OUTPUT->header();

if ($canviewdashboard) {
    $page = new dashboard_summary();
    echo $renderer->render_dashboard_summary($page);
} else if ($canselfservice && $hasemployeerecord) {
    $page = new my_summary((int) $USER->id);
    echo $renderer->render_my_summary($page);
} else if ($canselfservice) {
    // 2026-09-16: was a plain <h2>+<p>, the only spot in this plugin
    // still using bare text instead of the gradient hero banner every
    // other landing page (Dashboard, Attendance, Leave) already uses -
    // switched to local_hrdepartment_render_page_hero() for visual
    // consistency (see styles.css, where .local-hrdepartment-dashboard
    // was added to that helper's existing .hrdept-page-hero selectors).
    echo html_writer::start_div('local-hrdepartment-dashboard');
    echo local_hrdepartment_render_page_hero(
        $pageheading,
        get_string('selfservicelandingsubtitle', 'local_hrdepartment')
    );

    echo html_writer::start_div('hrdept-quicklink-grid');
    if (has_capability('local/hrdepartment:viewownattendance', $context)) {
        echo local_hrdepartment_render_quicklink(
            new moodle_url('/local/hrdepartment/attendance/index.php'),
            get_string('attendance', 'local_hrdepartment'),
            'fa-calendar-check'
        );
    }
    if (has_capability('local/hrdepartment:applyownleave', $context)
            || student_leave_manager::can_view()
            || student_leave_manager::is_approver((int) $USER->id)) {
        echo local_hrdepartment_render_quicklink(
            new moodle_url('/local/hrdepartment/leave/index.php'),
            get_string('leave', 'local_hrdepartment'),
            'fa-calendar-plus'
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();
} else {
    echo $OUTPUT->notification(get_string('noaccessdashboard', 'local_hrdepartment'), 'info');
}

echo $OUTPUT->footer();
