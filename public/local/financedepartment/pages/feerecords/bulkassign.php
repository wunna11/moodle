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
 * Bulk-assign a fee structure to every student in a course category.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feerecord_manager;
use local_financedepartment\form\feerecord_bulkassign_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managefeerecords');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/feerecords/bulkassign.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('bulkassignfeerecord', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

$form = new feerecord_bulkassign_form($PAGE->url);

$indexurl = new moodle_url('/local/financedepartment/pages/feerecords/index.php');

if ($form->is_cancelled()) {
    redirect($indexurl);
}

if ($data = $form->get_data()) {
    $result = feerecord_manager::bulk_assign((int) $data->feestructureid, (int) $data->categoryid, $USER->id);

    $message = get_string('bulkassignresult', 'local_financedepartment', (object) [
        'assigned' => $result['assigned'],
        'skipped' => $result['skipped'],
        'total' => $result['total'],
    ]);

    redirect($indexurl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('feerecords');

echo html_writer::start_div('local-financedepartment-feerecords-bulkassign');

echo local_financedepartment_render_page_hero(
    get_string('bulkassignfeerecord', 'local_financedepartment'),
    get_string('bulkassignfeerecorddesc', 'local_financedepartment')
);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
