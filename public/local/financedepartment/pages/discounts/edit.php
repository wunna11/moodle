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
 * Create/edit a discount definition.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\discount_manager;
use local_financedepartment\form\discount_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managediscounts');

$discount = null;
if ($id) {
    $discount = discount_manager::get($id);
    if (!$discount) {
        throw new moodle_exception(
            'errordiscountnotfound',
            'local_financedepartment',
            new moodle_url('/local/financedepartment/pages/discounts/index.php')
        );
    }
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/discounts/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$title = $id
    ? get_string('editdiscount', 'local_financedepartment')
    : get_string('adddiscount', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$form = new discount_form($PAGE->url, ['discountid' => $id]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/financedepartment/pages/discounts/index.php'));
}

if ($data = $form->get_data()) {
    if ($id) {
        discount_manager::update($id, $data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/discounts/view.php', ['id' => $id]),
            get_string('changessaved'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        $newid = discount_manager::create($data, $USER->id);
        redirect(
            new moodle_url('/local/financedepartment/pages/discounts/view.php', ['id' => $newid]),
            get_string('discountcreated', 'local_financedepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

if ($discount) {
    $form->set_data([
        'discountid' => $discount->id,
        'name' => $discount->name,
        'type' => $discount->type,
        'amounttype' => $discount->amounttype,
        'amountvalue' => $discount->amountvalue,
        'description' => $discount->description,
        'status' => $discount->status,
    ]);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('discounts');

$subtitle = $id
    ? get_string('editdiscountdesc', 'local_financedepartment')
    : get_string('adddiscountdesc', 'local_financedepartment');

echo html_writer::start_div('local-financedepartment-discounts-form');

echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
