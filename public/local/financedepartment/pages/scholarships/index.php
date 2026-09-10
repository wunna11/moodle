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
 * Scholarship definition list, filterable by category and status.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarship_manager;
use local_financedepartment\table\scholarship_table;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managescholarships');

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarships/index.php', [
    'categoryid' => $categoryid, 'status' => $status,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('scholarships', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarships');

$heroactions = [[
    'url' => new moodle_url('/local/financedepartment/pages/scholarships/edit.php'),
    'label' => get_string('addscholarship', 'local_financedepartment'),
    'icon' => 'fa-plus',
]];
if (access_manager::can_manage('local/financedepartment:approvescholarships')) {
    $heroactions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
        'label' => get_string('scholarshiprequests', 'local_financedepartment'),
        'icon' => 'fa-clipboard-list',
    ];
}

echo local_financedepartment_render_page_hero(
    get_string('scholarships', 'local_financedepartment'),
    get_string('scholarshipsdesc', 'local_financedepartment'),
    $heroactions
);

// Filter bar.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $PAGE->url->out_omit_querystring(),
    'class' => 'findept-filter-bar',
]);

$categoryoptions = ['0' => get_string('allcategories', 'local_financedepartment')] + scholarship_manager::get_category_options();
echo html_writer::select($categoryoptions, 'categoryid', $categoryid, false, ['class' => 'findept-filter-select']);

$statusoptions = [
    '' => get_string('allstatuses', 'local_financedepartment'),
    constants::SCHOLARSHIP_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
    constants::SCHOLARSHIP_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
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

$table = new scholarship_table('financedep-scholarships', $categoryid, $status);
$table->define_baseurl($PAGE->url);

ob_start();
$table->out(20, false);
$tablehtml = ob_get_clean();

if ($table->totalrows === 0) {
    echo local_financedepartment_render_empty_state(get_string('noscholarships', 'local_financedepartment'));
} else {
    echo local_financedepartment_render_table_card($tablehtml);
}

echo html_writer::end_div();

echo $OUTPUT->footer();
