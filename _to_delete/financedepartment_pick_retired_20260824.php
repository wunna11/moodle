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
 * Search for a student to start a new scholarship request for. Split
 * out from pages/scholarshiprequests/index.php (2026-08-24) so that
 * page could become a real browsable list instead of only showing
 * anything once you searched for a student - this page keeps that
 * search-then-act flow for the one place it's still needed: picking who
 * a brand new request is for. submit.php itself only needs a studentid,
 * so this page's only job is finding one.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_hrdepartment\student_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managescholarships');

$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/pick.php', ['search' => $search]));
$PAGE->set_pagelayout('standard');
$title = get_string('newrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarshiprequests-pick');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
    get_string('backtorequests', 'local_financedepartment')
);

echo local_financedepartment_render_page_hero($title, get_string('findstudentforrequestdesc', 'local_financedepartment'));

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
    'autofocus' => 'autofocus',
]);
echo html_writer::tag('button', get_string('filter', 'local_financedepartment'), [
    'type' => 'submit',
    'class' => 'btn btn-primary findept-filter-submit',
]);
echo html_writer::end_tag('form');

if ($search !== '') {
    $matches = student_manager::get_students($search, 0, '', 0, 20);

    if (empty($matches)) {
        echo local_financedepartment_render_empty_state(get_string('nostudentsfound', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [get_string('fullname'), get_string('email'), ''];
        $table->attributes['class'] = 'generaltable local-financedepartment-scholarshiprequest-table';

        foreach ($matches as $match) {
            $submiturl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php', ['studentid' => $match->id]);
            $table->data[] = [
                format_string($match->fullname),
                s($match->email),
                html_writer::link($submiturl, get_string('submitrequest', 'local_financedepartment')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else {
    echo local_financedepartment_render_empty_state(get_string('searchstudent', 'local_financedepartment'));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
