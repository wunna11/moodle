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
 * Finance Dashboard & Reports (Step 7.11): summary cards (total
 * collected, total outstanding, overdue count, active scholarships,
 * active discounts), two Moodle-native charts (collected vs outstanding;
 * outstanding balance by category), a category/academic-year/status
 * filter bar, and CSV/Excel/PDF export buttons for fee records and
 * payments (pages/reports/export.php).
 *
 * A separate page from index.php (the general navigation hub every role
 * lands on) rather than growing that page itself, gated on
 * viewfinancereports specifically - index.php's own docblock is updated
 * to point here. See [[financedepartment-step711]] project memory for
 * the AskUserQuestion scope decisions (page location, charts, export
 * formats, export scope) and the deliberate category/year-only filter
 * scope on the summary cards themselves (dashboard_manager.php's class
 * docblock has the full reasoning).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\dashboard_manager;
use local_financedepartment\feestructure_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:viewfinancereports');

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$academicyear = optional_param('academicyear', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/reports/index.php', [
    'categoryid' => $categoryid, 'academicyear' => $academicyear, 'status' => $status,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('financedashboard', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('reports');

echo html_writer::start_div('local-financedepartment-reports');

echo local_financedepartment_render_page_hero(
    get_string('financedashboard', 'local_financedepartment'),
    get_string('financedashboarddesc', 'local_financedepartment')
);

// Filter bar - category + academic year narrow every card/chart/export;
// status only narrows the exports (see dashboard_manager's docblock).
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
echo html_writer::end_div();

// Charts - core\chart_pie/chart_bar, Moodle's own Chart.js wrapper, so
// they stay theme-consistent and interactive with no extra library.
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

echo html_writer::end_div();

echo $OUTPUT->footer();
