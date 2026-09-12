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
 * Finance Department landing page - the shared entry point for every
 * role this plugin has any page for.
 *
 * 2026-09-10 (Step 7.11): the "full dashboard" (summary cards, charts,
 * exports) was built as its OWN separate page, pages/reports/index.php,
 * specifically to keep institution-wide financial totals off this page
 * (every role lands here, including a plain self-service student).
 *
 * 2026-09-12: the user asked directly for this landing page itself to
 * become the finance dashboard. Confirmed scope via AskUserQuestion
 * (both Recommended): (1) the dashboard content (filter bar + summary
 * cards + charts + export links) is now rendered directly on THIS page,
 * ABOVE the quicklink tile grid, but ONLY for a viewer who actually
 * holds viewfinancereports - every other role (a plain student,
 * finance staff without that specific capability, etc.) still sees
 * exactly the quicklink hub this page always was, unchanged; (2) the
 * separate pages/reports/index.php page and its "Reports"
 * tab/quicklink-tile were removed, so there is now a single dashboard
 * entry point rather than two. pages/reports/index.php was NOT deleted
 * outright though (a discretionary safety choice, not asked about) - it
 * now just redirects here (preserving any category/academicyear/status
 * query params) so an old bookmark or saved link keeps working instead
 * of 404ing. See [[financedepartment-uipolish]] project memory for the
 * full write-up.
 *
 * 2026-09-12 (same day, follow-up): the user asked what else the
 * dashboard should show, was given a short menu of options, and picked
 * "pending scholarship/discount request counts + a recent payments
 * feed". Both reuse data/manager methods that already existed
 * elsewhere in this plugin (dashboard_manager::get_summary() gained two
 * more unfiltered count fields; feepayment_manager::get_recent() is the
 * exact same call pages/payments/index.php's own default view already
 * made) - no new aggregation logic needed. The two new stat cards are
 * clickable (local_financedepartment_render_stat_card() gained an
 * optional $url param for this) straight to that request type's
 * PENDING-filtered review queue, and are only shown to a viewer who
 * actually holds the matching manage/approve capability - a
 * viewfinancereports-only reporting role sees the dashboard but would
 * hit a straight 403 on either review queue, so showing a "click here"
 * card they can't actually use would be a dead end, same reasoning
 * lib.php's get_tabs() already documents for why a tab is only ever
 * added for a section the viewer can actually open.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\dashboard_manager;
use local_financedepartment\feepayment_manager;
use local_financedepartment\feestructure_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$context = context_system::instance();

$canmanagefees = access_manager::can_manage('local/financedepartment:managefeestructures');
$canmanagefeerecords = access_manager::can_manage('local/financedepartment:managefeerecords');
$canmanagescholarships = access_manager::can_manage('local/financedepartment:managescholarships');
$canapprovescholarships = access_manager::can_manage('local/financedepartment:approvescholarships');
$canmanagediscounts = access_manager::can_manage('local/financedepartment:managediscounts');
$canapprovediscounts = access_manager::can_manage('local/financedepartment:approvediscounts');
$canmanageinstallments = access_manager::can_manage('local/financedepartment:manageinstallments');
$canrecordpayments = access_manager::can_manage('local/financedepartment:recordpayments');
$canmanagerefunds = access_manager::can_manage('local/financedepartment:managerefunds');
$canviewreports = access_manager::can_manage('local/financedepartment:viewfinancereports');
$canviewown = has_capability('local/financedepartment:viewownfeerecord', $context);
// Plain per-user self-service capabilities (2026-09-09 fix), NOT routed
// through access_manager - see db/access.php's docblock and
// pages/scholarshiprequests/submit.php's docblock for the full history.
$cansubmitscholarship = has_capability('local/financedepartment:submitscholarshiprequest', $context);
$cansubmitdiscount = has_capability('local/financedepartment:submitdiscountrequest', $context);

if (!$canmanagefees && !$canmanagefeerecords && !$canmanagescholarships && !$canapprovescholarships
        && !$canmanagediscounts && !$canapprovediscounts && !$canmanageinstallments
        && !$canrecordpayments && !$canmanagerefunds && !$canviewreports && !$canviewown
        && !$cansubmitscholarship && !$cansubmitdiscount) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

// Dashboard filters (2026-09-12) - only ever read/used when $canviewreports,
// same category/academic-year/status filter bar pages/reports/index.php
// used to have. Category and academic year narrow every summary card/
// chart/export; status only narrows the exports - see
// dashboard_manager's own class docblock for why.
$categoryid = 0;
$academicyear = '';
$status = '';
if ($canviewreports) {
    $categoryid = optional_param('categoryid', 0, PARAM_INT);
    $academicyear = optional_param('academicyear', '', PARAM_TEXT);
    $status = optional_param('status', '', PARAM_ALPHA);
}

$pageurl = new moodle_url('/local/financedepartment/index.php');
if ($canviewreports) {
    $pageurl->params(['categoryid' => $categoryid, 'academicyear' => $academicyear, 'status' => $status]);
}

$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(access_manager::get_display_name());
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('');

echo html_writer::start_div('local-financedepartment-dashboard');

echo local_financedepartment_render_page_hero(
    access_manager::get_display_name(),
    get_string('dashboardsubtitle', 'local_financedepartment')
);

if ($canviewreports) {
    // Finance dashboard (Step 7.11, moved here 2026-09-12) - summary
    // cards, charts, filter bar and exports, shown only to a viewer who
    // holds viewfinancereports, directly above the quicklink tiles.
    echo html_writer::tag('h3', get_string('financedashboard', 'local_financedepartment'), ['class' => 'findept-section-title']);
    echo html_writer::tag('p', get_string('financedashboarddesc', 'local_financedepartment'), ['class' => 'findept-form-intro']);

    echo html_writer::start_tag('form', [
        'method' => 'get',
        'action' => $PAGE->url->out_omit_querystring(),
        'class' => 'findept-filter-bar',
    ]);

    $categoryoptions = ['0' => get_string('allcategories', 'local_financedepartment')] + feestructure_manager::get_category_options();
    echo html_writer::select($categoryoptions, 'categoryid', $categoryid, false, ['class' => 'findept-filter-select']);

    echo html_writer::empty_tag('input', [
        'type' => 'text',
        'name' => 'academicyear',
        'value' => $academicyear,
        'placeholder' => get_string('academicyear', 'local_financedepartment'),
        'class' => 'findept-filter-text',
    ]);

    $statusoptions = [
        '' => get_string('allstatuses', 'local_financedepartment'),
        constants::FEE_STATUS_UNPAID => get_string('feestatus_unpaid', 'local_financedepartment'),
        constants::FEE_STATUS_PARTIALLY_PAID => get_string('feestatus_partiallypaid', 'local_financedepartment'),
        constants::FEE_STATUS_FULLY_PAID => get_string('feestatus_fullypaid', 'local_financedepartment'),
        constants::FEE_STATUS_OVERDUE => get_string('feestatus_overdue', 'local_financedepartment'),
        constants::FEE_STATUS_CANCELLED => get_string('feestatus_cancelled', 'local_financedepartment'),
    ];
    echo html_writer::select($statusoptions, 'status', $status, false, ['class' => 'findept-filter-select']);
    echo html_writer::tag('span', get_string('reportsstatusfilterhint', 'local_financedepartment'), ['class' => 'findept-form-intro', 'style' => 'margin:0 0.5rem;']);

    echo html_writer::tag('button', get_string('filter', 'local_financedepartment'), [
        'type' => 'submit',
        'class' => 'btn btn-primary findept-filter-submit',
    ]);
    echo html_writer::link(
        $PAGE->url->out_omit_querystring(),
        get_string('reset', 'local_financedepartment'),
        ['class' => 'findept-filter-reset']
    );

    echo html_writer::end_tag('form');

    // Summary cards.
    $summary = dashboard_manager::get_summary($categoryid, $academicyear);

    echo html_writer::start_div('findept-stat-grid');
    echo local_financedepartment_render_stat_card(
        get_string('totalcollected', 'local_financedepartment'),
        local_financedepartment_format_money($summary->totalcollected),
        'fa-check-circle',
        'success'
    );
    echo local_financedepartment_render_stat_card(
        get_string('totaloutstanding', 'local_financedepartment'),
        local_financedepartment_format_money($summary->totaloutstanding),
        'fa-hourglass-half',
        'warning'
    );
    echo local_financedepartment_render_stat_card(
        get_string('overduecount', 'local_financedepartment'),
        (string) $summary->overduecount,
        'fa-exclamation-triangle',
        'danger'
    );
    echo local_financedepartment_render_stat_card(
        get_string('activescholarshipscount', 'local_financedepartment'),
        (string) $summary->activescholarships,
        'fa-graduation-cap',
        'info'
    );
    echo local_financedepartment_render_stat_card(
        get_string('activediscountscount', 'local_financedepartment'),
        (string) $summary->activediscounts,
        'fa-tags',
        'teal'
    );
    // Pending-request cards (2026-09-12) - only for a viewer who can
    // actually act on that queue (managescholarships/approvescholarships,
    // managediscounts/approvediscounts) - see this file's own docblock
    // for why a viewfinancereports-only viewer never sees these.
    if ($canmanagescholarships || $canapprovescholarships) {
        echo local_financedepartment_render_stat_card(
            get_string('pendingscholarshiprequests', 'local_financedepartment'),
            (string) $summary->pendingscholarshiprequests,
            'fa-clock-o',
            'warning',
            new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', ['status' => constants::REQUEST_STATUS_PENDING])
        );
    }
    if ($canmanagediscounts || $canapprovediscounts) {
        echo local_financedepartment_render_stat_card(
            get_string('pendingdiscountrequests', 'local_financedepartment'),
            (string) $summary->pendingdiscountrequests,
            'fa-clock-o',
            'warning',
            new moodle_url('/local/financedepartment/pages/discountrequests/index.php', ['status' => constants::REQUEST_STATUS_PENDING])
        );
    }
    echo html_writer::end_div();

    // Charts - core\chart_pie/chart_bar, Moodle's own Chart.js wrapper,
    // so they stay theme-consistent and interactive with no extra
    // library.
    echo html_writer::start_div('findept-chart-grid');

    echo html_writer::start_div('findept-chart-card');
    echo html_writer::tag('h3', get_string('chartcollectedvsoutstanding', 'local_financedepartment'), ['class' => 'findept-chart-card-title']);
    if ($summary->totalcollected > 0 || $summary->totaloutstanding > 0) {
        $piechart = new core\chart_pie();
        $piechart->set_doughnut(true);
        $pieseries = new core\chart_series(
            get_string('financedashboard', 'local_financedepartment'),
            [$summary->totalcollected, $summary->totaloutstanding]
        );
        $pieseries->set_colors(['#2fb380', '#f5a623']);
        $piechart->add_series($pieseries);
        $piechart->set_labels([
            get_string('totalcollected', 'local_financedepartment'),
            get_string('totaloutstanding', 'local_financedepartment'),
        ]);
        echo $OUTPUT->render($piechart);
    } else {
        echo local_financedepartment_render_empty_state(get_string('nochartdata', 'local_financedepartment'));
    }
    echo html_writer::end_div();

    echo html_writer::start_div('findept-chart-card');
    echo html_writer::tag('h3', get_string('chartoutstandingbycategory', 'local_financedepartment'), ['class' => 'findept-chart-card-title']);
    $bycategory = dashboard_manager::get_outstanding_by_category($academicyear);
    if (!empty($bycategory)) {
        $palette = ['#2f6fed', '#6f42c1', '#17a2b8', '#f5a623', '#ef5b5b', '#2fb380', '#fd7e14', '#20c997', '#6610f2', '#e83e8c'];
        $barchart = new core\chart_bar();
        $barseries = new core\chart_series(get_string('chartoutstandingbycategory', 'local_financedepartment'), array_values($bycategory));
        $barseries->set_colors(array_slice($palette, 0, count($bycategory)));
        $barchart->add_series($barseries);
        $barchart->set_labels(array_keys($bycategory));
        echo $OUTPUT->render($barchart);
    } else {
        echo local_financedepartment_render_empty_state(get_string('nochartdata', 'local_financedepartment'));
    }
    echo html_writer::end_div();

    echo html_writer::end_div();

    // Export.
    echo html_writer::tag('h3', get_string('exportrecords', 'local_financedepartment'), ['class' => 'findept-section-title']);
    echo html_writer::start_div('findept-export-row');

    $exportparams = ['categoryid' => $categoryid, 'academicyear' => $academicyear, 'status' => $status];
    foreach (['csv' => 'fa-file-text-o', 'excel' => 'fa-file-excel-o', 'pdf' => 'fa-file-pdf-o'] as $format => $icon) {
        echo html_writer::link(
            new moodle_url('/local/financedepartment/pages/reports/export.php', $exportparams + ['type' => 'feerecords', 'format' => $format]),
            html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true'])
                . get_string('exportfeerecords', 'local_financedepartment', strtoupper($format)),
            ['class' => 'findept-export-btn']
        );
    }
    foreach (['csv' => 'fa-file-text-o', 'excel' => 'fa-file-excel-o', 'pdf' => 'fa-file-pdf-o'] as $format => $icon) {
        echo html_writer::link(
            new moodle_url('/local/financedepartment/pages/reports/export.php', $exportparams + ['type' => 'payments', 'format' => $format]),
            html_writer::tag('i', '', ['class' => 'icon fa ' . $icon, 'aria-hidden' => 'true'])
                . get_string('exportpayments', 'local_financedepartment', strtoupper($format)),
            ['class' => 'findept-export-btn']
        );
    }

    echo html_writer::end_div();

    // Recent payments (2026-09-12) - a small "recent activity" glance,
    // reusing feepayment_manager::get_recent() as-is (the exact same
    // call pages/payments/index.php's own default view already makes -
    // no new manager code needed). Gated on recordpayments/managerefunds
    // specifically, NOT viewfinancereports alone, since
    // pages/payments/view.php's own "View" link requires one of those
    // two capabilities - a pure reporting viewer would hit a 403 on it.
    if ($canrecordpayments || $canmanagerefunds) {
        echo html_writer::tag('h3', get_string('recentpayments', 'local_financedepartment'), ['class' => 'findept-section-title']);

        $recentpayments = feepayment_manager::get_recent(5);

        if (empty($recentpayments)) {
            echo local_financedepartment_render_empty_state(get_string('nopaymentsyet', 'local_financedepartment'));
        } else {
            $table = new html_table();
            $table->head = [
                get_string('student', 'local_financedepartment'),
                get_string('category', 'local_financedepartment'),
                get_string('paymenttype', 'local_financedepartment'),
                get_string('amount', 'local_financedepartment'),
                get_string('status', 'local_financedepartment'),
                get_string('when', 'local_financedepartment'),
                '',
            ];
            $table->attributes['class'] = 'generaltable local-financedepartment-dashboard-recentpayments-table';

            foreach ($recentpayments as $payment) {
                // 2026-09-12: feepayment_manager::get_recent() now selects
                // r.studentid (see that method's docblock/history), so this
                // can link to "this student's payments" exactly like
                // pages/payments/index.php's own default view does.
                $studenturl = new moodle_url('/local/financedepartment/pages/payments/index.php', ['studentid' => $payment->studentid]);
                $viewurl = new moodle_url('/local/financedepartment/pages/payments/view.php', ['id' => $payment->id]);

                $table->data[] = [
                    html_writer::link($studenturl, format_string($payment->fullname)),
                    format_string($payment->categoryname) . ' - ' . s($payment->academicyear),
                    local_financedepartment_payment_type_badge($payment->paymenttype),
                    local_financedepartment_format_money($payment->amount),
                    local_financedepartment_payment_status_badge($payment->status),
                    userdate($payment->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
                    html_writer::link($viewurl, get_string('view')),
                ];
            }

            echo local_financedepartment_render_table_card(html_writer::table($table));
        }

        echo html_writer::div(
            html_writer::link(
                new moodle_url('/local/financedepartment/pages/payments/index.php'),
                get_string('viewallpayments', 'local_financedepartment')
            ),
            '',
            ['style' => 'margin: 0.5rem 0 1.5rem;']
        );
    }
}

echo html_writer::start_div('findept-quicklink-grid');

if ($canmanagefees) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/fees/index.php'),
        get_string('feestructures', 'local_financedepartment'),
        'fa-list-alt'
    );
}

if ($canmanagefeerecords) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/feerecords/index.php'),
        get_string('feerecords', 'local_financedepartment'),
        'fa-id-card'
    );
} else if ($canviewown) {
    // Student self-service (2026-09-10, per the user's request) - same
    // routing split as the scholarships/discounts tiles below.
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/myfeerecord/index.php'),
        get_string('myfeerecord', 'local_financedepartment'),
        'fa-id-card'
    );
}

if ($canmanagescholarships || $canapprovescholarships) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/scholarships/index.php'),
        get_string('scholarships', 'local_financedepartment'),
        'fa-graduation-cap'
    );
} else if ($cansubmitscholarship) {
    // Student self-service (2026-09-09 fix) - a plain student can't
    // reach the scholarship DEFINITIONS page (managescholarships-gated),
    // so their tile routes straight to their own request list instead.
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
        get_string('scholarships', 'local_financedepartment'),
        'fa-graduation-cap'
    );
}

if ($canmanagediscounts || $canapprovediscounts) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/discounts/index.php'),
        get_string('discounts', 'local_financedepartment'),
        'fa-tags'
    );
} else if ($cansubmitdiscount) {
    // Same routing split as scholarships above.
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/discountrequests/index.php'),
        get_string('discounts', 'local_financedepartment'),
        'fa-tags'
    );
}

if ($canmanageinstallments) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/installments/index.php'),
        get_string('installmentplans', 'local_financedepartment'),
        'fa-calendar-check-o'
    );
}

if ($canrecordpayments || $canmanagerefunds) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/payments/index.php'),
        get_string('payments', 'local_financedepartment'),
        'fa-money'
    );
}

// Note (2026-09-12): the "Finance dashboard" quicklink tile that used to
// sit here (Step 7.11) was removed - the dashboard it linked to is now
// rendered directly on THIS page, above this tile grid, for the exact
// same $canviewreports viewer, so a self-referencing tile would have
// been pointless.

if ($canviewreports) {
    // Access tile (Step 7.12, 2026-09-10) - same $canviewreports gate as
    // the dashboard section above and the Access tab in lib.php's
    // local_financedepartment_get_tabs() (see that function's comment).
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/access/index.php'),
        get_string('accesssummary', 'local_financedepartment'),
        'fa-key'
    );
}

echo html_writer::end_div();

if (!$canmanagefees && !$canmanagefeerecords && !$canmanagescholarships && !$canapprovescholarships
        && !$canmanagediscounts && !$canapprovediscounts && !$canmanageinstallments
        && !$canrecordpayments && !$canmanagerefunds && !$cansubmitscholarship && !$cansubmitdiscount
        && !$canviewown && !$canviewreports) {
    // $canviewreports added 2026-09-10 (Step 7.11) - previously missing
    // from this list entirely (harmless before, since no tile existed
    // for a viewfinancereports-only holder either way) - kept here since
    // this viewer now sees the dashboard section itself, not merely a
    // tile, so this "no sections" message must still stay suppressed
    // for them.
    echo local_financedepartment_render_empty_state(get_string('nosectionsyet', 'local_financedepartment'));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
