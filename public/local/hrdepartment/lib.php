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

use local_hrdepartment\access_manager;
use local_hrdepartment\student_leave_manager;

/**
 * Adds the HR Department entry to the main Moodle navigation for users
 * who have at least dashboard access, or their own self-service
 * capabilities (attendance, leave, payroll).
 *
 * @param global_navigation $nav
 */
function local_hrdepartment_extend_navigation(global_navigation $nav) {
    global $PAGE, $USER;

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $context = context_system::instance();

    $cancontent = access_manager::can_access_hr_department((int) $USER->id)
        || has_capability('local/hrdepartment:viewownattendance', $context)
        || student_leave_manager::can_view()
        || has_capability('local/hrdepartment:applyownleave', $context)
        || student_leave_manager::is_approver((int) $USER->id)
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
 * Students | Attendance | Leave | Payroll) shown at the top of every HR
 * Department page, filtered to the sections the current user has access
 * to. A plain student or teacher (see
 * student_leave_manager::is_leave_attendance_only_role()) only ever
 * gets Attendance and Leave.
 *
 * @param string $selected the tab identifier to mark as active
 * @return \tabobject[]
 */
function local_hrdepartment_get_tabs(string $selected): array {
    global $USER;

    $context = context_system::instance();
    $tabs = [];

    // A plain student or teacher who holds none of this plugin's
    // management capabilities only ever uses Attendance and Leave -
    // Dashboard and Payroll (which has no pages built yet) are hidden
    // for them. See student_leave_manager::is_leave_attendance_only_role().
    $restricted = student_leave_manager::is_leave_attendance_only_role((int) $USER->id);

    if (!$restricted) {
        $tabs[] = local_hrdepartment_make_tab(
            'dashboard',
            new moodle_url('/local/hrdepartment/index.php'),
            get_string('dashboard', 'local_hrdepartment'),
            'fa-tachometer-alt'
        );
    }

    if (access_manager::can_manage('local/hrdepartment:managelecturers')) {
        $tabs[] = local_hrdepartment_make_tab(
            'lecturers',
            new moodle_url('/local/hrdepartment/lecturer/index.php'),
            get_string('lecturers', 'local_hrdepartment'),
            'fa-chalkboard-teacher'
        );
    }

    if (access_manager::can_manage('local/hrdepartment:managestaff')) {
        $tabs[] = local_hrdepartment_make_tab(
            'staff',
            new moodle_url('/local/hrdepartment/staff/index.php'),
            get_string('staff', 'local_hrdepartment'),
            'fa-user-tie'
        );
    }

    if (access_manager::can_manage('local/hrdepartment:managestudents')) {
        $tabs[] = local_hrdepartment_make_tab(
            'students',
            new moodle_url('/local/hrdepartment/students/index.php'),
            get_string('students', 'local_hrdepartment'),
            'fa-user-graduate'
        );
    }

    if (access_manager::can_manage('local/hrdepartment:manageattendance')
        || has_capability('local/hrdepartment:viewownattendance', $context)) {
        $tabs[] = local_hrdepartment_make_tab(
            'attendance',
            new moodle_url('/local/hrdepartment/attendance/index.php'),
            get_string('attendance', 'local_hrdepartment'),
            'fa-clipboard-check'
        );
    }

    // Student Leave: local/hrdepartment:managestudentleave / :viewstudentleave
    // are defined at CONTEXT_USER (see db/access.php), so
    // student_leave_manager::can_manage()/can_view() (studentid = 0 here)
    // check whether the capability is held globally at CONTEXT_SYSTEM -
    // showing this tab does not by itself expose any one student's data,
    // it just decides whether the section is visible at all. Also shown
    // to a plain student holding local/hrdepartment:applyownleave, who
    // lands on the self-service "My leave requests" page instead of the
    // HR-facing Leave Overview - see leave/index.php. And to a teacher
    // who has been chosen as the approver on at least one self-service
    // application (student_leave_manager::is_approver()), who lands on
    // "Leave requests to review" (leave/myapprovals.php) instead - they
    // hold none of the capabilities above, only a per-application right.
    if (student_leave_manager::can_manage()
            || student_leave_manager::can_view()
            || has_capability('local/hrdepartment:applyownleave', $context)
            || student_leave_manager::is_approver((int) $USER->id)) {
        $tabs[] = local_hrdepartment_make_tab(
            'leave',
            new moodle_url('/local/hrdepartment/leave/index.php'),
            get_string('leave', 'local_hrdepartment'),
            'fa-calendar-alt'
        );
    }

    if (!$restricted
        && (access_manager::can_manage('local/hrdepartment:managepayroll')
            || has_capability('local/hrdepartment:viewownpayroll', $context))) {
        $tabs[] = local_hrdepartment_make_tab(
            'payroll',
            new moodle_url('/local/hrdepartment/payroll/index.php'),
            get_string('payroll', 'local_hrdepartment'),
            'fa-money-bill-wave'
        );
    }

    return $tabs;
}

/**
 * Builds one tabobject for local_hrdepartment_get_tabs(), with a small
 * icon prefixed onto the visible label. tabobject's $text is rendered
 * unescaped (Mustache triple-brace) by core's tabtree template, so it can
 * carry the icon markup - but its $title (the tooltip/title attribute) is
 * rendered escaped and defaults to $text if not given explicitly, so a
 * plain-text $title is always passed here to avoid a broken tooltip full
 * of raw HTML.
 *
 * @param string $id unique tab id, matches local_hrdepartment_get_tabs()'s $selected values
 * @param moodle_url $url
 * @param string $label already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-calendar-alt'
 * @return \tabobject
 */
function local_hrdepartment_make_tab(string $id, moodle_url $url, string $label, string $icon): tabobject {
    $text = html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']) .
        html_writer::span($label);

    return new tabobject($id, $url->out(false), $text, $label);
}

/**
 * Builds and renders the shared section tab bar (see
 * local_hrdepartment_get_tabs()) wrapped in the styled `hrdept-tab-bar`
 * container (see styles.css) so it reads as a pill-style nav instead of
 * Moodle's plain default `nav-tabs` underline style. Every HR Department
 * page calls this once, right after $OUTPUT->header(), in place of the
 * old two-line `local_hrdepartment_get_tabs()` + `$OUTPUT->tabtree()` pair.
 *
 * @param string $selected the tab identifier to mark as active
 * @return string
 */
function local_hrdepartment_render_tab_bar(string $selected): string {
    global $OUTPUT;

    $tabs = local_hrdepartment_get_tabs($selected);

    return html_writer::div($OUTPUT->tabtree($tabs, $selected), 'hrdept-tab-bar');
}

// -----------------------------------------------------------------
// Shared presentation helpers for the Attendance Tracking and Leave
// Management pages (attendance/*.php, leave/*.php). Both sections wrap
// their page body in a `local-hrdepartment-attendance` or
// `local-hrdepartment-leave` container (see styles.css) and use these
// helpers to build the gradient hero, stat cards, summary cards, table
// wrapper, and status badges consistently across every page, reusing
// the same visual language already established by the Dashboard and
// the Students/Lecturers/Staff directories.
//
// Like local_hrdepartment_get_tabs() above, these are only ever called
// from page bodies after $OUTPUT->header() has run, so lib.php being
// loaded late is not an issue here (unlike department_helper's methods,
// which forms need before header() - see that class's docblock).
// -----------------------------------------------------------------

/**
 * Renders the gradient hero banner used at the top of the Attendance and
 * Leave landing pages (attendance/index.php, leave/index.php).
 *
 * @param string $title
 * @param string $subtitle already-safe HTML/text, may be empty
 * @param array $actions each: ['url' => moodle_url, 'label' => string, 'icon' => string fa- class]
 * @return string
 */
function local_hrdepartment_render_page_hero(string $title, string $subtitle = '', array $actions = []): string {
    $out = html_writer::start_div('hrdept-page-hero');

    $out .= html_writer::start_div('hrdept-page-hero-text');
    $out .= html_writer::tag('h2', $title, ['class' => 'hrdept-page-hero-title']);
    if ($subtitle !== '') {
        $out .= html_writer::tag('p', $subtitle, ['class' => 'hrdept-page-hero-subtitle']);
    }
    $out .= html_writer::end_div();

    if (!empty($actions)) {
        $out .= html_writer::start_div('hrdept-page-hero-actions');
        foreach ($actions as $action) {
            $icon = !empty($action['icon'])
                ? html_writer::tag('i', '', ['class' => 'icon fa ' . $action['icon'], 'aria-hidden' => 'true'])
                : '';
            $out .= html_writer::link(
                $action['url'],
                $icon . html_writer::span($action['label']),
                ['class' => 'hrdept-page-hero-btn']
            );
        }
        $out .= html_writer::end_div();
    }

    $out .= html_writer::end_div();

    return $out;
}

/**
 * Renders a compact white "subheader" card used at the top of Attendance
 * and Leave sub-pages that aren't the main landing page (course/session
 * listings, student history, leave request detail, filters, etc.).
 *
 * @param string $title
 * @param string $subtitle already-safe HTML/text, may be empty
 * @param moodle_url|null $backurl
 * @param string $backlabel
 * @return string
 */
function local_hrdepartment_render_subheader(string $title, string $subtitle = '', ?moodle_url $backurl = null, string $backlabel = ''): string {
    $out = html_writer::start_div('hrdept-subheader');

    if ($backurl !== null) {
        $out .= local_hrdepartment_render_back_link($backurl, $backlabel);
    }

    $out .= html_writer::tag('h2', $title, ['class' => 'hrdept-subheader-title']);
    if ($subtitle !== '') {
        $out .= html_writer::tag('p', $subtitle, ['class' => 'hrdept-subheader-subtitle']);
    }

    $out .= html_writer::end_div();

    return $out;
}

/**
 * Renders a "&laquo; back" link, styled consistently across Attendance
 * and Leave sub-pages.
 *
 * @param moodle_url $url
 * @param string $label
 * @return string
 */
function local_hrdepartment_render_back_link(moodle_url $url, string $label): string {
    $icon = html_writer::tag('i', '', ['class' => 'icon fa fa-arrow-left', 'aria-hidden' => 'true']);
    return html_writer::link($url, $icon . ' ' . $label, ['class' => 'hrdept-back-link']);
}

/**
 * Renders a leave application status as a coloured pill badge, reusing
 * Bootstrap's badge-{warning,success,danger,secondary} variants so
 * classes\table\student_leave_table's col_status() and every page that
 * prints a status inline stay visually identical - see the
 * `.local-hrdepartment-leave .badge` styling in styles.css.
 *
 * @param string $status one of pending/approved/rejected/cancelled
 * @return string
 */
function local_hrdepartment_leave_status_badge(string $status): string {
    $variant = [
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'cancelled' => 'secondary',
    ][$status] ?? 'secondary';

    return html_writer::span(
        get_string('status_' . $status, 'local_hrdepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Wraps already-rendered table HTML (html_writer::table() output, or a
 * table_sql's out()) in the rounded card container used across
 * Attendance and Leave listings.
 *
 * @param string $tablehtml
 * @return string
 */
function local_hrdepartment_render_table_card(string $tablehtml): string {
    return html_writer::div($tablehtml, 'hrdept-table-card');
}

/**
 * Renders the empty-state placeholder used in Attendance/Leave summary
 * cards and table cards, in place of $OUTPUT->notification() where a
 * quieter, on-brand look is wanted.
 *
 * @param string $message already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-inbox'
 * @return string
 */
function local_hrdepartment_render_empty_state(string $message, string $icon = 'fa-inbox'): string {
    return html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']) . $message,
        'hrdept-empty-state'
    );
}

/**
 * Renders one gradient stat card for a stat strip (attendance summary
 * counts, leave overview counts, etc).
 *
 * @param string $value
 * @param string $label already a get_string() result
 * @param string $colorclass one of the hrdept-stat-* colour modifiers defined in styles.css
 * @param string $icon a Font Awesome class, e.g. 'fa-hourglass-half'
 * @return string
 */
function local_hrdepartment_render_stat_card(string $value, string $label, string $colorclass, string $icon = ''): string {
    $iconhtml = $icon !== ''
        ? html_writer::div(html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']), 'hrdept-stat-icon')
        : '';

    return html_writer::div(
        $iconhtml .
        html_writer::div(
            html_writer::div($value, 'hrdept-stat-value') .
            html_writer::div($label, 'hrdept-stat-label')
        ),
        'hrdept-stat-card ' . $colorclass
    );
}

/**
 * Renders one tile in a quick-action tile row (leave/index.php's
 * shortcuts to lookup/log/types/balances/reports, etc).
 *
 * @param moodle_url $url
 * @param string $label already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-calendar-plus'
 * @return string
 */
function local_hrdepartment_render_quicklink(moodle_url $url, string $label, string $icon): string {
    return html_writer::link(
        $url,
        html_writer::span(
            html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']),
            'hrdept-quicklink-icon'
        ) .
        html_writer::span($label, 'hrdept-quicklink-label'),
        ['class' => 'hrdept-quicklink']
    );
}
