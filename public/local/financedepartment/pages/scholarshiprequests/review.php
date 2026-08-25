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
 * Approve or reject a pending scholarship request. Approving auto-
 * deducts the approved amount from the fee record via
 * scholarshiprequest_manager::approve() -> feerecord_manager::add_scholarship_amount().
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarshiprequest_manager;
use local_financedepartment\form\scholarshiprequestreview_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$decision = required_param('decision', PARAM_ALPHA);

if (!in_array($decision, ['approve', 'reject'], true)) {
    throw new moodle_exception('invalidparameter', 'debug');
}

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:approvescholarships');

$request = scholarshiprequest_manager::get($id);
if (!$request) {
    throw new moodle_exception(
        'errorscholarshiprequestnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php')
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $id]);

if ($request->status !== constants::REQUEST_STATUS_PENDING) {
    redirect($returnurl, get_string('errorrequestalreadyreviewed', 'local_financedepartment'), null, \core\output\notification::NOTIFY_WARNING);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/review.php', ['id' => $id, 'decision' => $decision]));
$PAGE->set_pagelayout('standard');
$title = $decision === 'approve'
    ? get_string('approverequest', 'local_financedepartment')
    : get_string('rejectrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

$form = new scholarshiprequestreview_form($PAGE->url, [
    'requestid' => $id,
    'decision' => $decision,
    'suggestedamount' => $request->requestedamount,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    if ($decision === 'approve') {
        scholarshiprequest_manager::approve($id, (float) $data->amountapproved, $data->reviewnote ?? '', $USER->id);
        $message = get_string('scholarshiprequestapproved', 'local_financedepartment');
    } else {
        scholarshiprequest_manager::reject($id, $data->reviewnote ?? '', $USER->id);
        $message = get_string('scholarshiprequestrejected', 'local_financedepartment');
    }

    redirect($returnurl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarshiprequests-review');

echo local_financedepartment_render_back_link($returnurl, get_string('backtorequest', 'local_financedepartment'));

$subtitle = get_string(
    $decision === 'approve' ? 'approverequestdesc' : 'rejectrequestdesc',
    'local_financedepartment',
    format_string($request->fullname) . ' - ' . format_string($request->scholarshipname)
);

echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
