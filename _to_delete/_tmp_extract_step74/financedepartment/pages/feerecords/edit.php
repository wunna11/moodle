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
 * Assign a fee structure to a student, or edit a mistaken assignment.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feerecord_manager;
use local_financedepartment\form\feerecord_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);
$studentid = optional_param('studentid', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managefeerecords');

$feerecord = null;
if ($id) {
    $feerecord = feerecord_manager::get($id);
    if (!$feerecord) {
        throw new moodle_exception(
            'errorfeerecordnotfound',
            'local_financedepartment',
            new moodle_url('/local/financedepartment/pages/feerecords/index.php')
        );
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/feerecords/edit.php', ['id' => $id, 'studentid' => $studentid]));
$PAGE->set_pagelayout('standard');
$title = $id
    ? get_string('editfeerecord', 'local_financedepartment')
    : get_string('assignfeerecord', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

$form = new feerecord_form($PAGE->url, ['feerecordid' => $id]);

$cancelurl = new moodle_url('/local/financedepartment/pages/feerecords/index.php', [
    'studentid' => $feerecord ? $feerecord->studentid : $studentid,
]);

if ($form->is_cancelled()) {
    redirect($cancelurl);
}

if ($data = $form->get_data()) {
    if ($id) {
        feerecord_manager::update($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $id]),
            get_string('changessaved'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        $newid = feerecord_manager::create($data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $newid]),
            get_string('feerecordcreated', 'local_financedepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($feerecord) {
    $form->set_data([
        'feerecordid' => $feerecord->id,
        'studentid' => $feerecord->studentid,
        'feestructureid' => $feerecord->feestructureid,
    ]);
} else if ($studentid) {
    $form->set_data(['studentid' => $studentid]);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('feerecords');

$subtitle = $id
    ? get_string('editfeerecorddesc', 'local_financedepartment')
    : get_string('assignfeerecorddesc', 'local_financedepartment');

echo html_writer::start_div('local-financedepartment-feerecords-form');

echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
