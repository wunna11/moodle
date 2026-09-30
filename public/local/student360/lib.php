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
 * Library callbacks and shared page-rendering helpers for the Student
 * 360 Profile local plugin.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_student360\access_manager;

/**
 * Adds the Student 360 entry to the site navigation tree
 * ($PAGE->navigation), for users who can view any student's profile or
 * their own.
 *
 * NOTE: on this site's Moodle version (5.2), this alone is NOT enough
 * to appear in the TOP primary navigation bar - see db/hooks.php and
 * classes/hooks/navigation/primary_extend.php, the second entry point
 * local_financedepartment/local_hrdepartment both needed for the same
 * reason (see [[financedepartment-step711]] project memory).
 *
 * @param global_navigation $nav
 */
function local_student360_extend_navigation(global_navigation $nav) {
    if (!access_manager::can_view_navigation_entry()) {
        return;
    }

    $node = $nav->add(
        get_string('pluginname', 'local_student360'),
        new moodle_url('/local/student360/pages/index.php'),
        navigation_node::TYPE_CUSTOM,
        get_string('pluginname', 'local_student360'),
        'local_student360',
        new pix_icon('i/report', '')
    );
    $node->showinflatnavigation = true;
}

/**
 * Renders the page hero (title + subtitle), same visual pattern as
 * local_financedepartment_render_page_hero()/local_hrdepartment_render_page_hero().
 *
 * @param string $title
 * @param string $subtitle
 * @return string
 */
function local_student360_render_page_hero(string $title, string $subtitle = ''): string {
    $out = html_writer::start_div('student360-page-hero');
    $out .= html_writer::tag('h2', $title, ['class' => 'student360-page-hero-title']);
    if ($subtitle !== '') {
        $out .= html_writer::tag('p', $subtitle, ['class' => 'student360-page-hero-subtitle']);
    }
    $out .= html_writer::end_div();

    return $out;
}

/**
 * Renders a "&laquo; back" link, styled consistently across pages.
 *
 * @param moodle_url $url
 * @param string $label
 * @return string
 */
function local_student360_render_back_link(moodle_url $url, string $label): string {
    $icon = html_writer::tag('i', '', ['class' => 'icon fa fa-arrow-left', 'aria-hidden' => 'true']);
    return html_writer::link($url, $icon . ' ' . $label, ['class' => 'student360-back-link']);
}

/**
 * Wraps already-rendered table HTML in the rounded card container used
 * across this plugin's pages.
 *
 * @param string $tablehtml
 * @return string
 */
function local_student360_render_table_card(string $tablehtml): string {
    return html_writer::div($tablehtml, 'student360-table-card');
}

/**
 * Renders the empty-state placeholder used in place of a bare "no
 * records" notification.
 *
 * @param string $message already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-inbox'
 * @return string
 */
function local_student360_render_empty_state(string $message, string $icon = 'fa-inbox'): string {
    return html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']) . ' ' . $message,
        'student360-empty-state'
    );
}
