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
 * Library callbacks and shared page-rendering helpers for the Finance
 * Department local plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use local_financedepartment\access_manager;

/**
 * Adds the Finance Department entry to the main Moodle navigation for
 * users who have finance management access, or (once built) their own
 * self-service capability to view their own fee record.
 *
 * @param global_navigation $nav
 */
function local_financedepartment_extend_navigation(global_navigation $nav) {
    global $USER;

    if (!isloggedin() || isguestuser()) {
        return;
    }

    $context = context_system::instance();

    $cancontent = access_manager::can_access_finance_department((int) $USER->id)
        || has_capability('local/financedepartment:viewfinancereports', $context)
        || has_capability('local/financedepartment:viewownfeerecord', $context);

    if (!$cancontent) {
        return;
    }

    $url = new moodle_url('/local/financedepartment/index.php');
    $name = get_string('pluginname', 'local_financedepartment');

    $node = $nav->add(
        $name,
        $url,
        navigation_node::TYPE_CUSTOM,
        $name,
        'local_financedepartment',
        new pix_icon('i/report', '')
    );
    $node->showinflatnavigation = true;
}

/**
 * Builds the shared section tab bar shown at the top of every Finance
 * Department page, filtered to the sections the current user has access
 * to. Only sections with pages actually built are ever added here - see
 * [[financedepartment-schema]] project memory on why (a tab pointing at
 * an unbuilt section is a dead-end 404, a mistake local_hrdepartment's
 * Payroll tab made early on).
 *
 * @param string $selected the tab identifier to mark as active
 * @return \tabobject[]
 */
function local_financedepartment_get_tabs(string $selected): array {
    $tabs = [];

    if (access_manager::can_manage('local/financedepartment:managefeestructures')) {
        $tabs[] = local_financedepartment_make_tab(
            'fees',
            new moodle_url('/local/financedepartment/pages/fees/index.php'),
            get_string('feestructures', 'local_financedepartment'),
            'fa-list-alt'
        );
    }

    if (access_manager::can_manage('local/financedepartment:managestaff')) {
        $tabs[] = local_financedepartment_make_tab(
            'staff',
            new moodle_url('/local/financedepartment/pages/staff/index.php'),
            get_string('financestaff', 'local_financedepartment'),
            'fa-users'
        );
    }

    return $tabs;
}

/**
 * Builds one tabobject for local_financedepartment_get_tabs(), with a
 * small icon prefixed onto the visible label. tabobject's $text is
 * rendered unescaped (Mustache triple-brace) by core's tabtree template,
 * so it can carry the icon markup - but its $title (the tooltip/title
 * attribute) is rendered escaped and defaults to $text if not given
 * explicitly, so a plain-text $title is always passed here to avoid a
 * broken tooltip full of raw HTML.
 *
 * @param string $id unique tab id, matches local_financedepartment_get_tabs()'s $selected values
 * @param moodle_url $url
 * @param string $label already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-list-alt'
 * @return \tabobject
 */
function local_financedepartment_make_tab(string $id, moodle_url $url, string $label, string $icon): tabobject {
    $text = html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']) .
        html_writer::span($label);

    return new tabobject($id, $url->out(false), $text, $label);
}

/**
 * Builds and renders the shared section tab bar wrapped in the styled
 * `findept-tab-bar` container (see styles.css). Every Finance Department
 * page calls this once, right after $OUTPUT->header().
 *
 * @param string $selected the tab identifier to mark as active
 * @return string
 */
function local_financedepartment_render_tab_bar(string $selected): string {
    global $OUTPUT;

    $tabs = local_financedepartment_get_tabs($selected);
    if (empty($tabs)) {
        return '';
    }

    return html_writer::div($OUTPUT->tabtree($tabs, $selected), 'findept-tab-bar');
}

// -----------------------------------------------------------------
// Shared presentation helpers, used across every Finance Department
// page. Only ever called from page bodies after $OUTPUT->header() has
// run.
// -----------------------------------------------------------------

/**
 * Formats an MMK amount for display: thousands-separated, no decimal
 * places for a whole number, otherwise exactly two. This plugin handles
 * MMK only - see classes/constants.php's class docblock - so the unit is
 * always appended literally rather than looked up from any currency
 * setting.
 *
 * @param float|int|string $amount
 * @return string e.g. "150,000 MMK" or "150,000.50 MMK"
 */
function local_financedepartment_format_money($amount): string {
    $amount = (float) $amount;
    $decimals = (abs($amount - round($amount)) > 0.001) ? 2 : 0;

    return number_format($amount, $decimals) . ' MMK';
}

/**
 * Renders a fee structure's status as a coloured pill badge.
 *
 * @param string $status one of local_financedepartment\constants::FEESTRUCTURE_STATUS_*
 * @return string
 */
function local_financedepartment_feestructure_status_badge(string $status): string {
    $variant = ($status === \local_financedepartment\constants::FEESTRUCTURE_STATUS_ACTIVE) ? 'success' : 'secondary';

    return html_writer::span(
        get_string('status_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders the gradient hero banner used at the top of section landing
 * pages (index.php, pages/fees/index.php).
 *
 * @param string $title
 * @param string $subtitle already-safe HTML/text, may be empty
 * @param array $actions each: ['url' => moodle_url, 'label' => string, 'icon' => string fa- class]
 * @return string
 */
function local_financedepartment_render_page_hero(string $title, string $subtitle = '', array $actions = []): string {
    $out = html_writer::start_div('findept-page-hero');

    $out .= html_writer::start_div('findept-page-hero-text');
    $out .= html_writer::tag('h2', $title, ['class' => 'findept-page-hero-title']);
    if ($subtitle !== '') {
        $out .= html_writer::tag('p', $subtitle, ['class' => 'findept-page-hero-subtitle']);
    }
    $out .= html_writer::end_div();

    if (!empty($actions)) {
        $out .= html_writer::start_div('findept-page-hero-actions');
        foreach ($actions as $action) {
            $icon = !empty($action['icon'])
                ? html_writer::tag('i', '', ['class' => 'icon fa ' . $action['icon'], 'aria-hidden' => 'true'])
                : '';
            $out .= html_writer::link(
                $action['url'],
                $icon . html_writer::span($action['label']),
                ['class' => 'findept-page-hero-btn']
            );
        }
        $out .= html_writer::end_div();
    }

    $out .= html_writer::end_div();

    return $out;
}

/**
 * Renders a "&laquo; back" link, styled consistently across sub-pages.
 *
 * @param moodle_url $url
 * @param string $label
 * @return string
 */
function local_financedepartment_render_back_link(moodle_url $url, string $label): string {
    $icon = html_writer::tag('i', '', ['class' => 'icon fa fa-arrow-left', 'aria-hidden' => 'true']);
    return html_writer::link($url, $icon . ' ' . $label, ['class' => 'findept-back-link']);
}

/**
 * Wraps already-rendered table HTML (a table_sql's out()) in the rounded
 * card container used across every listing page.
 *
 * @param string $tablehtml
 * @return string
 */
function local_financedepartment_render_table_card(string $tablehtml): string {
    return html_writer::div($tablehtml, 'findept-table-card');
}

/**
 * Renders the empty-state placeholder used in place of
 * $OUTPUT->notification() where a quieter, on-brand look is wanted.
 *
 * @param string $message already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-inbox'
 * @return string
 */
function local_financedepartment_render_empty_state(string $message, string $icon = 'fa-inbox'): string {
    return html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']) . $message,
        'findept-empty-state'
    );
}

/**
 * Renders one tile in a quick-action tile row (index.php's shortcuts to
 * each section).
 *
 * @param moodle_url $url
 * @param string $label already a get_string() result
 * @param string $icon a Font Awesome class, e.g. 'fa-list-alt'
 * @return string
 */
function local_financedepartment_render_quicklink(moodle_url $url, string $label, string $icon): string {
    return html_writer::link(
        $url,
        html_writer::span(
            html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']),
            'findept-quicklink-icon'
        ) .
        html_writer::span($label, 'findept-quicklink-label'),
        ['class' => 'findept-quicklink']
    );
}
