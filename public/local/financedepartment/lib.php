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
 * Adds the Finance Department entry to the site navigation tree
 * ($PAGE->navigation, shown in the navigation drawer/"Site pages") for
 * users who have finance management access, or their own self-service
 * capability to view their own fee record or submit a scholarship/
 * discount request.
 *
 * Fixed 2026-09-10 (first fix, same day): the two student self-service
 * capabilities added by the 2026-09-09 fix (submitscholarshiprequest/
 * submitdiscountrequest) were never added to this check.
 *
 * Fixed 2026-09-10 (second fix, same day): this callback alone is NOT
 * enough to make the plugin visible in this site's TOP navigation bar -
 * see \local_financedepartment\access_manager::can_view_navigation_entry()'s
 * docblock and classes/hooks/navigation/primary_extend.php for why a
 * second, hook-based entry point was needed on this Moodle version, and
 * why the capability check itself now lives in access_manager rather
 * than being duplicated here.
 *
 * Changed 2026-09-10 (third change, same day): the node's label is now
 * access_manager::get_display_name() instead of a hardcoded
 * get_string('pluginname', ...) - "Finance Department" for finance
 * staff/admins, "Scholarship" for a plain self-service student. See
 * that method's own docblock. The primary_extend hook callback (the
 * OTHER entry point added earlier the same day) uses the same method,
 * so both nav labels always agree.
 *
 * @param global_navigation $nav
 */
function local_financedepartment_extend_navigation(global_navigation $nav) {
    if (!access_manager::can_view_navigation_entry()) {
        return;
    }

    $url = new moodle_url('/local/financedepartment/index.php');
    $name = access_manager::get_display_name();

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

    if (access_manager::can_manage('local/financedepartment:managefeerecords')) {
        $tabs[] = local_financedepartment_make_tab(
            'feerecords',
            new moodle_url('/local/financedepartment/pages/feerecords/index.php'),
            get_string('feerecords', 'local_financedepartment'),
            'fa-id-card'
        );
    } else if (has_capability('local/financedepartment:viewownfeerecord', context_system::instance())) {
        // Student self-service (2026-09-10, per the user's request) - a
        // plain student can't reach the admin fee records list
        // (managefeerecords-gated), so their tab routes to their own
        // read-only pages/myfeerecord/index.php instead - same routing
        // split already established for the scholarships/discounts tabs
        // below.
        $tabs[] = local_financedepartment_make_tab(
            'myfeerecord',
            new moodle_url('/local/financedepartment/pages/myfeerecord/index.php'),
            get_string('myfeerecord', 'local_financedepartment'),
            'fa-id-card'
        );
    } else if (access_manager::can_manage('local/financedepartment:viewfinancereports')) {
        // Step 7.9 (2026-09-10) - a report-only viewer who holds
        // viewfinancereports but neither managefeerecords nor
        // viewownfeerecord (e.g. a custom role built purely for
        // reporting) previously had NO tab in this slot at all - the
        // page existed nowhere in navigation without a direct URL, same
        // class of gap already fixed once for viewownfeerecord itself.
        // Routes straight to the new all-students list,
        // pages/feerecords/all.php.
        $tabs[] = local_financedepartment_make_tab(
            'allfeerecords',
            new moodle_url('/local/financedepartment/pages/feerecords/all.php'),
            get_string('allfeerecords', 'local_financedepartment'),
            'fa-table'
        );
    }

    // Scholarships tab: finance staff (manage/approve) land on the
    // scholarship DEFINITIONS list (pages/scholarships/index.php,
    // managescholarships-gated); a plain student who only has the new
    // local/financedepartment:submitscholarshiprequest self-service
    // capability (2026-09-09 fix - see pages/scholarshiprequests/
    // submit.php's docblock) would be denied entry to that page, so
    // they're routed straight to their own request list instead. The
    // capability check is a plain has_capability(), NOT routed through
    // access_manager - see db/access.php's docblock for why self-service
    // capabilities are checked directly, same as viewownfeerecord.
    $syscontext = context_system::instance();
    $canmanagescholarships = access_manager::can_manage('local/financedepartment:managescholarships');
    $canapprovescholarships = access_manager::can_manage('local/financedepartment:approvescholarships');
    if ($canmanagescholarships || $canapprovescholarships) {
        $tabs[] = local_financedepartment_make_tab(
            'scholarships',
            new moodle_url('/local/financedepartment/pages/scholarships/index.php'),
            get_string('scholarships', 'local_financedepartment'),
            'fa-graduation-cap'
        );
    } else if (has_capability('local/financedepartment:submitscholarshiprequest', $syscontext)) {
        $tabs[] = local_financedepartment_make_tab(
            'scholarships',
            new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
            get_string('scholarships', 'local_financedepartment'),
            'fa-graduation-cap'
        );
    }

    // Discounts tab: same routing split as scholarships above.
    $canmanagediscounts = access_manager::can_manage('local/financedepartment:managediscounts');
    $canapprovediscounts = access_manager::can_manage('local/financedepartment:approvediscounts');
    if ($canmanagediscounts || $canapprovediscounts) {
        $tabs[] = local_financedepartment_make_tab(
            'discounts',
            new moodle_url('/local/financedepartment/pages/discounts/index.php'),
            get_string('discounts', 'local_financedepartment'),
            'fa-tags'
        );
    } else if (has_capability('local/financedepartment:submitdiscountrequest', $syscontext)) {
        $tabs[] = local_financedepartment_make_tab(
            'discounts',
            new moodle_url('/local/financedepartment/pages/discountrequests/index.php'),
            get_string('discounts', 'local_financedepartment'),
            'fa-tags'
        );
    }

    if (access_manager::can_manage('local/financedepartment:manageinstallments')) {
        $tabs[] = local_financedepartment_make_tab(
            'installments',
            new moodle_url('/local/financedepartment/pages/installments/index.php'),
            get_string('installmentplans', 'local_financedepartment'),
            'fa-calendar-check-o'
        );
    }

    if (access_manager::can_manage('local/financedepartment:recordpayments')
            || access_manager::can_manage('local/financedepartment:managerefunds')) {
        $tabs[] = local_financedepartment_make_tab(
            'payments',
            new moodle_url('/local/financedepartment/pages/payments/index.php'),
            get_string('payments', 'local_financedepartment'),
            'fa-money'
        );
    }

    // Reports tab (Step 7.11, 2026-09-10) - a SEPARATE top-level slot
    // from the fee-records one above (not an else-if branch there),
    // since the dashboard is a distinct concept from the fee-records
    // list/assign flow - a full finance manager sees BOTH "Fee records"
    // and "Reports" tabs at once. Gated purely on viewfinancereports,
    // independent of every other capability, matching that capability's
    // own db/access.php comment ("the finance dashboard, summary cards,
    // and the finance staff list view").
    if (access_manager::can_manage('local/financedepartment:viewfinancereports')) {
        $tabs[] = local_financedepartment_make_tab(
            'reports',
            new moodle_url('/local/financedepartment/pages/reports/index.php'),
            get_string('financedashboard', 'local_financedepartment'),
            'fa-bar-chart'
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
 * Renders a fee record's status as a coloured pill badge.
 *
 * OVERDUE is a live-computed display state (Step 7.8) - it is never
 * written to financedep_feerecord.status itself, only derived on the fly
 * by feerecord_manager::display_status() from the record's schedule (if
 * it has an active installment plan). Never bypass this helper and badge
 * $feerecord->status directly, or a genuinely overdue fee record will
 * render as if it were merely "unpaid"/"partially paid".
 *
 * @param \stdClass $feerecord a financedep_feerecord row
 * @return string
 */
function local_financedepartment_feerecord_status_badge(\stdClass $feerecord): string {
    $status = \local_financedepartment\feerecord_manager::display_status($feerecord);

    $variants = [
        \local_financedepartment\constants::FEE_STATUS_UNPAID => 'secondary',
        \local_financedepartment\constants::FEE_STATUS_PARTIALLY_PAID => 'warning',
        \local_financedepartment\constants::FEE_STATUS_FULLY_PAID => 'success',
        \local_financedepartment\constants::FEE_STATUS_OVERDUE => 'danger',
        \local_financedepartment\constants::FEE_STATUS_CANCELLED => 'dark',
    ];
    $variant = $variants[$status] ?? 'secondary';

    return html_writer::span(
        get_string('feestatus_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders a scholarship request's status as a coloured pill badge.
 *
 * @param string $status one of local_financedepartment\constants::REQUEST_STATUS_*
 * @return string
 */
function local_financedepartment_scholarshiprequest_status_badge(string $status): string {
    $variants = [
        \local_financedepartment\constants::REQUEST_STATUS_PENDING => 'warning',
        \local_financedepartment\constants::REQUEST_STATUS_APPROVED => 'success',
        \local_financedepartment\constants::REQUEST_STATUS_REJECTED => 'danger',
        \local_financedepartment\constants::REQUEST_STATUS_DELETED => 'dark',
    ];
    $variant = $variants[$status] ?? 'secondary';

    return html_writer::span(
        get_string('requeststatus_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders a discount request's status as a coloured pill badge. Reuses
 * the same 'requeststatus_*' lang strings as
 * local_financedepartment_scholarshiprequest_status_badge() (both
 * request types share constants::REQUEST_STATUS_* literals) - but has
 * no DELETED variant, since discount requests have no delete feature
 * yet (see discountrequest_manager's class docblock).
 *
 * @param string $status one of local_financedepartment\constants::REQUEST_STATUS_* (excluding DELETED)
 * @return string
 */
function local_financedepartment_discountrequest_status_badge(string $status): string {
    $variants = [
        \local_financedepartment\constants::REQUEST_STATUS_PENDING => 'warning',
        \local_financedepartment\constants::REQUEST_STATUS_APPROVED => 'success',
        \local_financedepartment\constants::REQUEST_STATUS_REJECTED => 'danger',
    ];
    $variant = $variants[$status] ?? 'secondary';

    return html_writer::span(
        get_string('requeststatus_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders one installment schedule line's status as a coloured pill
 * badge. IMPORTANT: takes the row itself, not a bare status string -
 * unlike every other status badge helper in this file, this one calls
 * \local_financedepartment\installmentplan_manager::display_status()
 * internally to compute whether the row is actually overdue right now
 * (pending/partiallypaid AND its due date has passed), since "overdue"
 * is a live, computed display state that is never written to
 * financedep_installmentsched.status - see that manager's class
 * docblock for the 2026-09-08 scope decision behind this. Never bypass
 * this helper and badge $schedrow->status directly, or a genuinely
 * overdue installment will render as if it were merely "pending".
 *
 * @param \stdClass $schedrow a financedep_installmentsched row
 * @return string
 */
function local_financedepartment_installment_status_badge(\stdClass $schedrow): string {
    $status = \local_financedepartment\installmentplan_manager::display_status($schedrow);

    $variants = [
        \local_financedepartment\constants::INSTALLMENT_STATUS_PENDING => 'secondary',
        \local_financedepartment\constants::INSTALLMENT_STATUS_PARTIALLY_PAID => 'warning',
        \local_financedepartment\constants::INSTALLMENT_STATUS_PAID => 'success',
        \local_financedepartment\constants::INSTALLMENT_STATUS_OVERDUE => 'danger',
    ];
    $variant = $variants[$status] ?? 'secondary';

    return html_writer::span(
        get_string('installmentstatus_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders an installment plan's own status (active/cancelled, not to be
 * confused with one schedule line's status above) as a coloured pill
 * badge.
 *
 * @param string $status one of local_financedepartment\constants::INSTALLMENTPLAN_STATUS_*
 * @return string
 */
function local_financedepartment_installmentplan_status_badge(string $status): string {
    $variant = ($status === \local_financedepartment\constants::INSTALLMENTPLAN_STATUS_ACTIVE) ? 'success' : 'dark';

    return html_writer::span(
        get_string('installmentplanstatus_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders a payment/refund row's status (active/void, NOT its
 * paymenttype - see local_financedepartment_payment_type_badge() for
 * that) as a coloured pill badge. Added for Step 7.7.
 *
 * @param string $status one of local_financedepartment\constants::PAYMENT_STATUS_*
 * @return string
 */
function local_financedepartment_payment_status_badge(string $status): string {
    $variant = ($status === \local_financedepartment\constants::PAYMENT_STATUS_ACTIVE) ? 'success' : 'dark';

    return html_writer::span(
        get_string('paymentstatus_' . $status, 'local_financedepartment'),
        'badge badge-' . $variant
    );
}

/**
 * Renders a payment row's type (payment vs refund) as a coloured pill
 * badge - separate from local_financedepartment_payment_status_badge()
 * above, since a payment row's type and its status are two independent
 * things (a REFUND can be ACTIVE or VOID, same as a PAYMENT can). Added
 * for Step 7.7.
 *
 * @param string $paymenttype one of local_financedepartment\constants::PAYMENT_TYPE_*
 * @return string
 */
function local_financedepartment_payment_type_badge(string $paymenttype): string {
    $variant = ($paymenttype === \local_financedepartment\constants::PAYMENT_TYPE_REFUND) ? 'warning' : 'info';

    return html_writer::span(
        get_string('paymenttype_' . $paymenttype, 'local_financedepartment'),
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

/**
 * Renders one summary stat card for pages/reports/index.php (Step
 * 7.11). $value is passed already formatted (money string or a plain
 * count) - this helper only handles layout/icon/colour, not formatting,
 * since some cards are MMK amounts and others are plain integers.
 *
 * @param string $label already a get_string() result
 * @param string $value already-formatted display value
 * @param string $icon a Font Awesome class, e.g. 'fa-money'
 * @param string $variant one of success|warning|danger|info|teal
 * @return string
 */
function local_financedepartment_render_stat_card(string $label, string $value, string $icon, string $variant = 'info'): string {
    $out = html_writer::start_div('findept-stat-card findept-variant-' . $variant);

    $out .= html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true']),
        'findept-stat-icon findept-variant-' . $variant
    );

    $out .= html_writer::start_div('findept-stat-body');
    $out .= html_writer::div($value, 'findept-stat-value');
    $out .= html_writer::div($label, 'findept-stat-label');
    $out .= html_writer::end_div();

    $out .= html_writer::end_div();

    return $out;
}

/**
 * Serves a scholarship or discount request's optional supporting-
 * document attachment. Scholarship requests: added 2026-08-24,
 * classes/form/scholarshiprequest_form.php's 'attachment' filemanager
 * element, saved via file_save_draft_area_files() in
 * pages/scholarshiprequests/submit.php into this component's own
 * 'scholarshiprequest' file area, itemid = the
 * financedep_scholarshipreq.id it belongs to. Discount requests: added
 * 2026-09-06, same shape via classes/form/discountrequest_form.php and
 * pages/discountrequests/submit.php, into its OWN 'discountrequest'
 * file area (itemid = financedep_discountreq.id) so the two never
 * collide even when both share the same itemid.
 *
 * These are finance/HR-sensitive documents (income certificates and
 * similar), so access is gated the same way the request itself is -
 * managescholarships/managediscounts (still allowed to view, even
 * though they can no longer SUBMIT one - see submit.php's 2026-09-09
 * docblock) or approvescholarships/approvediscounts (reviewing it) -
 * PLUS, as of that same 2026-09-09 fix, the request's own submitter
 * (requestedby), since a student can now upload their own supporting
 * document and must be able to see it again afterward - never a plain
 * "logged in" check.
 *
 * @param stdClass $course
 * @param stdClass|null $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool false to let Moodle send a 404, never actually returned on the success path (send_stored_file() exits)
 */
function local_financedepartment_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $DB, $USER;

    require_login();

    if ($context->contextlevel !== CONTEXT_SYSTEM) {
        return false;
    }

    if ($filearea !== 'scholarshiprequest' && $filearea !== 'discountrequest' && $filearea !== 'payment') {
        return false;
    }

    // itemid (the request's own id) is needed both for the ownership
    // check below and for the file lookup further down, so it's shifted
    // off $args once, up front.
    $itemid = (int) array_shift($args);

    if ($filearea === 'scholarshiprequest') {
        $isowner = (int) $DB->get_field('financedep_scholarshipreq', 'requestedby', ['id' => $itemid]) === (int) $USER->id;
        if (!$isowner
                && !\local_financedepartment\access_manager::can_manage('local/financedepartment:managescholarships')
                && !\local_financedepartment\access_manager::can_manage('local/financedepartment:approvescholarships')) {
            return false;
        }
    } else if ($filearea === 'discountrequest') {
        // discountrequest - same finance/HR-sensitive-document gating as
        // scholarshiprequest above, added 2026-09-06 for Step 7.5's
        // discount request attachments (classes/form/
        // discountrequest_form.php's own 'attachment' filemanager
        // element, saved via pages/discountrequests/submit.php into this
        // OWN filearea so it never collides with a scholarship request's
        // attachment even when both share the same itemid).
        $isowner = (int) $DB->get_field('financedep_discountreq', 'requestedby', ['id' => $itemid]) === (int) $USER->id;
        if (!$isowner
                && !\local_financedepartment\access_manager::can_manage('local/financedepartment:managediscounts')
                && !\local_financedepartment\access_manager::can_manage('local/financedepartment:approvediscounts')) {
            return false;
        }
    } else {
        // payment - added 2026-09-10 for Step 7.7's payment/refund
        // attachments (classes/form/feepayment_form.php's own
        // 'attachment' filemanager element, saved via
        // pages/payments/create.php into this OWN filearea, itemid = the
        // financedep_feepayment row's own id). Unlike scholarshiprequest/
        // discountrequest, a payment row has no "requestedby" (finance
        // staff always records it, never the student themselves - see
        // feepayment_manager's docblock, there is no student self-service
        // payment flow at all) - so gating is exactly the same as
        // pages/payments/view.php's own gate, with no separate owner
        // check.
        if (!\local_financedepartment\access_manager::can_manage('local/financedepartment:recordpayments')
                && !\local_financedepartment\access_manager::can_manage('local/financedepartment:managerefunds')) {
            return false;
        }
    }

    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_financedepartment', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}
