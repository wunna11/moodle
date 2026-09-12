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
 * Soft-delete a scholarship request (added 2026-08-24 per user request).
 * Never physically removed - see scholarshiprequest_manager::delete()'s
 * docblock. If the request being deleted was APPROVED, deleting it
 * reverses the deduction already applied to the fee record's balance,
 * so this confirmation page warns about that specifically rather than
 * using one generic confirm string for every status (mirrors
 * pages/scholarships/deactivate.php's confirm-page pattern).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarshiprequest_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managescholarships');

$request = scholarshiprequest_manager::get($id);
if (!$request) {
    throw new moodle_exception(
        'errorscholarshiprequestnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php')
    );
}

if ($request->status === constants::REQUEST_STATUS_DELETED) {
    redirect(
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
        get_string('errorrequestalreadydeleted', 'local_financedepartment'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php');
$actionurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/delete.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('deleterequest', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

if ($confirm && confirm_sesskey()) {
    scholarshiprequest_manager::delete($id, $USER->id);

    redirect(
        $returnurl,
        get_string('scholarshiprequestdeleted', 'local_financedepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

// local_financedepartment_format_money() is only safe to call here,
// after header() - never earlier in a page script, same CRITICAL RULE
// as manager/form classes (see [[financedepartment-schema]] project
// memory and scholarshiprequest_form::format_mmk()'s docblock).
$label = format_string($request->fullname) . ' - ' . format_string($request->scholarshipname);

$confirmstring = $request->status === constants::REQUEST_STATUS_APPROVED
    ? get_string('confirmdeleterequestapproved', 'local_financedepartment', (object) [
        'label' => $label,
        'amount' => local_financedepartment_format_money($request->approvedamount),
      ])
    : get_string('confirmdeleterequest', 'local_financedepartment', $label);

echo $OUTPUT->confirm(
    $confirmstring,
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $id])
);

echo $OUTPUT->footer();
