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
 * Staff listing page.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\table\staff_table;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/hrdepartment:managestaff', $context);

$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/staff/index.php', ['search' => $search]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('staff', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('staff');
echo $OUTPUT->tabtree($tabs, 'staff');

echo $OUTPUT->heading(get_string('staff', 'local_hrdepartment'));

echo html_writer::start_div('d-flex justify-content-between align-items-center mb-3');

echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url, 'class' => 'form-inline']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('search'),
    'class' => 'form-control mr-2',
]);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('search'), 'class' => 'btn btn-secondary']);
echo html_writer::end_tag('form');

echo html_writer::link(
    new moodle_url('/local/hrdepartment/staff/edit.php'),
    get_string('addstaffmember', 'local_hrdepartment'),
    ['class' => 'btn btn-primary']
);

echo html_writer::end_div();

$table = new staff_table('local-hrdepartment-staff', $search);
$table->define_baseurl($PAGE->url);
$table->out(20, true);

echo $OUTPUT->footer();
