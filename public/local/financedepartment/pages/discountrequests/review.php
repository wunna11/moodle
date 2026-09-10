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
 * Approve or reject a pending discount request. Approving auto-applies
 * the approved amount to the fee record via
 * discountrequest_manager::approve() -> feerecord_manager::add_discount_amount().
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\discountrequest_manager;
use local_financedepartment\form\discountrequestreview_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$decision = required_param('decision', PARAM_ALPHA);

if (!in_array($decision, ['approve', 'reject'], true)) {
    throw new moodle_exception('invalidparameter', 'debug');
}

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:approvediscounts');

$request = discountrequest_manager::get($id);
if (!$request) {
    throw new moodle_exception(
        'errordiscountrequestnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/discountrequests/index.php')
    );
}

$returnurl = new moodle_url('/local/financedepartment/pages/discountrequests/view.php', ['id' => $id]);

if ($request->status !== constants::REQUEST_STATUS_PENDING) {
    redirect($returnurl, get_string('errorrequestalreadyreviewed', 'local_financedepartment'), null, \core\output\notification::NOTIFY_WARNING);
}

// Self-approval guard, built in from day one (2026-09-06, per the
// user's explicit AskUserQuestion decision) - see
// discountrequest_manager::approve()'s docblock. This is the primary,
// user-facing gate; the manager methods also refuse silently as
// defense-in-depth.
if ((int) $request->requestedby === (int) $USER->id) {
    redirect($returnurl, get_string('errorcannotreviewownrequest', 'local_financedepartment'), null, \core\output\notification::NOTIFY_WARNING);
}

// Deactivated-discount guard, added 2026-09-06 per the user's explicit
// request: an approval is refused (redirected with a clear message)
// once the underlying discount has since been deactivated - discount
// deactivation never cascades to an existing PENDING request (see
// discount_manager::set_status()'s docblock), so without this check a
// stale-but-still-pending request could still be approved and deducted
// from the fee record after the discount itself was turned off.
// Rejecting is deliberately still allowed regardless - a reviewer needs
// a way to resolve/clear such a request out of the pending queue.
if ($decision === 'approve' && $request->discountstatus !== constants::DISCOUNT_STATUS_ACTIVE) {
    redirect($returnurl, get_string('errordiscountnotactiveforapproval', 'local_financedepartment'), null, \core\output\notification::NOTIFY_WARNING);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/discountrequests/review.php', ['id' => $id, 'decision' => $decision]));
$PAGE->set_pagelayout('standard');
$title = $decision === 'approve'
    ? get_string('approverequest', 'local_financedepartment')
    : get_string('rejectrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$form = new discountrequestreview_form($PAGE->url, [
    'requestid' => $id,
    'decision' => $decision,
    'suggestedamount' => $request->requestedamount,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    if ($decision === 'approve') {
        discountrequest_manager::approve($id, (float) $data->amountapproved, $data->reviewnote ?? '', $USER->id);
        $message = get_string('discountrequestapproved', 'local_financedepartment');
    } else {
        discountrequest_manager::reject($id, $data->reviewnote ?? '', $USER->id);
        $message = get_string('discountrequestrejected', 'local_financedepartment');
    }

    redirect($returnurl, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('discounts');

echo html_writer::start_div('local-financedepartment-discountrequests-review');

echo local_financedepartment_render_back_link($returnurl, get_string('backtorequest', 'local_financedepartment'));

$subtitle = get_string(
    $decision === 'approve' ? 'approverequestdesc' : 'rejectrequestdesc',
    'local_financedepartment',
    format_string($request->fullname) . ' - ' . format_string($request->discountname)
);

echo local_financedepartment_render_page_hero($title, $subtitle);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
