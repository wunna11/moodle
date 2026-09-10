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
 * All students' fee status, one searchable/filterable table - Step 7.9's
 * "finance staff list view". A separate page from
 * pages/feerecords/index.php (the per-student assign/bulk-assign flow,
 * managefeerecords-gated) rather than folded into it, so a report-only
 * viewer who holds local/financedepartment:viewfinancereports but not
 * managefeerecords can reach this list without also being able to
 * assign/edit/cancel fee records - see access_manager::can_manage_any().
 *
 * managefeerecords holders reach this page via a hero-button link on
 * index.php; a viewfinancereports-only holder gets their own tab-bar
 * entry pointing straight here (see lib.php's
 * local_financedepartment_get_tabs()).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\feestructure_manager;
use local_financedepartment\table\feerecord_table;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage_any([
    'local/financedepartment:viewfinancereports',
    'local/financedepartment:managefeerecords',
]);

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$academicyear = optional_param('academicyear', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/feerecords/all.php', [
    'categoryid' => $categoryid, 'academicyear' => $academicyear, 'status' => $status, 'search' => $search,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('allfeerecords', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar(
    access_manager::can_manage('local/financedepartment:managefeerecords') ? 'feerecords' : 'allfeerecords'
);

echo html_writer::start_div('local-financedepartment-feerecords');

$heroactions = [];
if (access_manager::can_manage('local/financedepartment:managefeerecords')) {
    $heroactions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/feerecords/index.php'),
        'label' => get_string('feerecords', 'local_financedepartment'),
        'icon' => 'fa-id-card',
    ];
}

echo local_financedepartment_render_page_hero(
    get_string('allfeerecords', 'local_financedepartment'),
    get_string('allfeerecordsdesc', 'local_financedepartment'),
    $heroactions
);

// Filter bar.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $PAGE->url->out_omit_querystring(),
    'class' => 'findept-filter-bar',
]);

echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudent', 'local_financedepartment'),
    'class' => 'findept-filter-text',
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

$table = new feerecord_table('financedep-allfeerecords', $categoryid, $academicyear, $status, $search);
$table->define_baseurl($PAGE->url);

ob_start();
$table->out(20, false);
$tablehtml = ob_get_clean();

if ($table->totalrows === 0) {
    echo local_financedepartment_render_empty_state(get_string('nofeerecordsfound', 'local_financedepartment'));
} else {
    echo local_financedepartment_render_table_card($tablehtml);
}

echo html_writer::end_div();

echo $OUTPUT->footer();
