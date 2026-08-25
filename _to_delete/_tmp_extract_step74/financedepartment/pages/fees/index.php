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
 * Fee structure list, filterable by category, academic year and status.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\feestructure_manager;
use local_financedepartment\table\feestructure_table;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managefeestructures');

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$academicyear = optional_param('academicyear', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/fees/index.php', [
    'categoryid' => $categoryid, 'academicyear' => $academicyear, 'status' => $status,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('feestructures', 'local_financedepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('fees');

echo html_writer::start_div('local-financedepartment-fees');

echo local_financedepartment_render_page_hero(
    get_string('feestructures', 'local_financedepartment'),
    get_string('feestructuresdesc', 'local_financedepartment'),
    [[
        'url' => new moodle_url('/local/financedepartment/pages/fees/edit.php'),
        'label' => get_string('addfeestructure', 'local_financedepartment'),
        'icon' => 'fa-plus',
    ]]
);

// Filter bar.
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
    constants::FEESTRUCTURE_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
    constants::FEESTRUCTURE_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
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

$table = new feestructure_table('financedep-feestructures', $categoryid, $academicyear, $status);
$table->define_baseurl($PAGE->url);

ob_start();
$table->out(20, false);
$tablehtml = ob_get_clean();

if ($table->totalrows === 0) {
    echo local_financedepartment_render_empty_state(get_string('nofeestructures', 'local_financedepartment'));
} else {
    echo local_financedepartment_render_table_card($tablehtml);
}

echo html_writer::end_div();

echo $OUTPUT->footer();
