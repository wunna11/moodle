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
 * Finance Department landing page. A lightweight hub for now - just a
 * quicklink to Fee Structures - that grows into the full dashboard
 * (summary cards, reports) once Step 7.11 is built.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$canmanagefees = access_manager::can_manage('local/financedepartment:managefeestructures');
$canviewreports = has_capability('local/financedepartment:viewfinancereports', $context);
$canviewown = has_capability('local/financedepartment:viewownfeerecord', $context);

if (!$canmanagefees && !$canviewreports && !$canviewown) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_financedepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('');

echo html_writer::start_div('local-financedepartment-dashboard');

echo local_financedepartment_render_page_hero(
    get_string('pluginname', 'local_financedepartment'),
    get_string('dashboardsubtitle', 'local_financedepartment')
);

echo html_writer::start_div('findept-quicklink-grid');

if ($canmanagefees) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/fees/index.php'),
        get_string('feestructures', 'local_financedepartment'),
        'fa-list-alt'
    );
}

echo html_writer::end_div();

if (!$canmanagefees) {
    echo local_financedepartment_render_empty_state(get_string('nosectionsyet', 'local_financedepartment'));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
