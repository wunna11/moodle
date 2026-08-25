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
 * Create/edit a fee structure.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feestructure_manager;
use local_financedepartment\form\feestructure_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managefeestructures');

$feestructure = null;
if ($id) {
    $feestructure = feestructure_manager::get($id);
    if (!$feestructure) {
        throw new moodle_exception(
            'errorfeestructurenotfound',
            'local_financedepartment',
            new moodle_url('/local/financedepartment/pages/fees/index.php')
        );
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/fees/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id
    ? get_string('editfeestructure', 'local_financedepartment')
    : get_string('addfeestructure', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

$form = new feestructure_form($PAGE->url, ['feestructureid' => $id]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/financedepartment/pages/fees/index.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        feestructure_manager::update($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/fees/view.php', ['id' => $id]),
            get_string('changessaved'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        $newid = feestructure_manager::create($data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/fees/view.php', ['id' => $newid]),
            get_string('feestructurecreated', 'local_financedepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($feestructure) {
    $form->set_data([
        'feestructureid' => $feestructure->id,
        'categoryid' => $feestructure->categoryid,
        'academicyear' => $feestructure->academicyear,
        'amount' => $feestructure->amount,
        'description' => $feestructure->description,
        'status' => $feestructure->status,
    ]);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('fees');

$subtitle = $id
    ? get_string('editfeestructuredesc', 'local_financedepartment')
    : get_string('addfeestructuredesc', 'local_financedepartment');

echo html_writer::start_div('local-financedepartment-fees-form');

echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
