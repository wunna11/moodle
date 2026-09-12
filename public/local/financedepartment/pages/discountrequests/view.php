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
 * View one discount request's details and its review history. Mirrors
 * pages/scholarshiprequests/view.php's shape, minus the delete action
 * (discount requests have no delete feature yet - see
 * discountrequest_manager's class docblock).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\audit_manager;
use local_financedepartment\constants;
use local_financedepartment\discountrequest_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$id = required_param('id', PARAM_INT);

$context = context_system::instance();

$canmanage = access_manager::can_manage('local/financedepartment:managediscounts');
$canapprove = access_manager::can_manage('local/financedepartment:approvediscounts');

$request = discountrequest_manager::get($id);
if (!$request) {
    throw new moodle_exception(
        'errordiscountrequestnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/discountrequests/index.php')
    );
}

// A student may always view a request THEY submitted (2026-09-09 fix -
// requests are now student self-service, see submit.php's docblock),
// read-only - computed once here and reused below for the
// self-approval guard too.
$isownrequest = (int) $request->requestedby === (int) $USER->id;

if (!$canmanage && !$canapprove && !$isownrequest) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

$heading = format_string($request->fullname) . ' - ' . format_string($request->discountname);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/discountrequests/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($heading);
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('discounts');

echo html_writer::start_div('local-financedepartment-discountrequests-view');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/discountrequests/index.php', ['studentid' => $request->studentid]),
    get_string('backtorequests', 'local_financedepartment')
);

$actions = [];
// Self-approval guard, built in from day one - see
// discountrequest_manager::approve()'s docblock and review.php's
// matching server-side check (the actual enforcement point; this is
// just so the buttons don't appear only to error out).
// $isownrequest was already computed above for the view-access guard.
$ispending = $request->status === constants::REQUEST_STATUS_PENDING;
// Deactivated-discount guard, added 2026-09-06 - see
// discountrequest_manager::approve()'s docblock and review.php's
// matching server-side check (the actual enforcement point; this is
// just so the Approve button doesn't appear only to error out). Reject
// stays available regardless, so a stale request can still be cleared.
$discountinactive = $request->discountstatus !== constants::DISCOUNT_STATUS_ACTIVE;
if ($canapprove && $ispending && !$isownrequest) {
    if (!$discountinactive) {
        $actions[] = [
            'url' => new moodle_url('/local/financedepartment/pages/discountrequests/review.php', ['id' => $id, 'decision' => 'approve']),
            'label' => get_string('approve', 'local_financedepartment'),
            'icon' => 'fa-check',
        ];
    }
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/discountrequests/review.php', ['id' => $id, 'decision' => 'reject']),
        'label' => get_string('reject', 'local_financedepartment'),
        'icon' => 'fa-times',
    ];
}

echo local_financedepartment_render_page_hero(
    $heading,
    get_string('discountrequestdetails', 'local_financedepartment'),
    $actions
);

if ($canapprove && $isownrequest && $ispending) {
    echo $OUTPUT->notification(
        get_string('errorcannotreviewownrequest', 'local_financedepartment'),
        \core\output\notification::NOTIFY_INFO
    );
}

if ($canapprove && !$isownrequest && $ispending && $discountinactive) {
    echo $OUTPUT->notification(
        get_string('errordiscountnotactiveforapproval', 'local_financedepartment'),
        \core\output\notification::NOTIFY_INFO
    );
}

// Details card.
echo html_writer::start_div('findept-detail-card');

$rows = [
    [get_string('student', 'local_financedepartment'), format_string($request->fullname) . ' (' . s($request->email) . ')'],
    [get_string('feerecord', 'local_financedepartment'), format_string($request->categoryname) . ' - ' . s($request->academicyear)],
    [get_string('discount', 'local_financedepartment'), format_string($request->discountname)],
    [get_string('discounttype', 'local_financedepartment'), get_string('discounttype_' . $request->discounttype, 'local_financedepartment')],
    [get_string('requestedamount', 'local_financedepartment'), local_financedepartment_format_money($request->requestedamount)],
];

if ($request->status !== constants::REQUEST_STATUS_PENDING) {
    $rows[] = [
        get_string('approvedamount', 'local_financedepartment'),
        $request->approvedamount !== null ? local_financedepartment_format_money($request->approvedamount) : '-',
    ];
}

$rows[] = [get_string('status', 'local_financedepartment'), local_financedepartment_discountrequest_status_badge($request->status)];
$rows[] = [get_string('description', 'local_financedepartment'), $request->justification !== ''
    ? format_text($request->justification, FORMAT_PLAIN) : '-'];

// Optional supporting-document attachment - own filearea
// ('discountrequest') so it never collides with a scholarship request's
// attachment even if the two share the same itemid space.
$fs = get_file_storage();
$attachments = $fs->get_area_files($context->id, 'local_financedepartment', 'discountrequest', $id, 'filename', false);
if (!empty($attachments)) {
    $attachmentlinks = [];
    foreach ($attachments as $file) {
        $fileurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            'local_financedepartment',
            'discountrequest',
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );
        $attachmentlinks[] = html_writer::link($fileurl, s($file->get_filename()));
    }
    $rows[] = [get_string('attachment', 'local_financedepartment'), implode(html_writer::empty_tag('br'), $attachmentlinks)];
} else {
    $rows[] = [get_string('attachment', 'local_financedepartment'), '-'];
}

$requestedbyuser = \core_user::get_user($request->requestedby);
$rows[] = [
    get_string('requestedby', 'local_financedepartment'),
    $requestedbyuser ? fullname($requestedbyuser) : get_string('unknownuser', 'local_financedepartment'),
];
$rows[] = [get_string('when', 'local_financedepartment'), userdate($request->timecreated)];

if ($request->status !== constants::REQUEST_STATUS_PENDING) {
    $reviewedbyuser = $request->reviewedby ? \core_user::get_user($request->reviewedby) : false;
    $rows[] = [
        get_string('reviewedby', 'local_financedepartment'),
        $reviewedbyuser ? fullname($reviewedbyuser) : get_string('unknownuser', 'local_financedepartment'),
    ];
    $rows[] = [get_string('reviewedon', 'local_financedepartment'), $request->reviewedat ? userdate($request->reviewedat) : '-'];
    $rows[] = [get_string('reviewnote', 'local_financedepartment'), $request->reviewnote ? format_text($request->reviewnote, FORMAT_PLAIN) : '-'];
}

echo html_writer::start_tag('dl', ['class' => 'findept-detail-list']);
foreach ($rows as [$label, $value]) {
    echo html_writer::tag('dt', $label);
    echo html_writer::tag('dd', $value);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div();

// History.
echo html_writer::tag('h3', get_string('discountrequesthistory', 'local_financedepartment'), ['class' => 'findept-section-title']);

$history = audit_manager::get_history(constants::AUDIT_ENTITY_DISCOUNTREQUEST, $id);

if (empty($history)) {
    echo local_financedepartment_render_empty_state(get_string('nohistoryyet', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('when', 'local_financedepartment'),
        get_string('who', 'local_financedepartment'),
        get_string('action', 'local_financedepartment'),
        get_string('change', 'local_financedepartment'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-history-table';

    foreach ($history as $entry) {
        $user = \core_user::get_user($entry->userid);
        $who = $user ? fullname($user) : get_string('unknownuser', 'local_financedepartment');

        $new = $entry->newdata !== null ? json_decode($entry->newdata, true) : [];

        $changelines = [];
        if ($entry->action === constants::AUDIT_ACTION_CREATE) {
            $changelines[] = get_string('historyrequestsubmitted', 'local_financedepartment', local_financedepartment_format_money($new['requestedamount'] ?? 0));
        } else if ($entry->action === constants::AUDIT_ACTION_APPROVE) {
            $changelines[] = get_string('historyrequestapproved', 'local_financedepartment', local_financedepartment_format_money($new['approvedamount'] ?? 0));
        } else if ($entry->action === constants::AUDIT_ACTION_REJECT) {
            $changelines[] = get_string('historyrequestrejected', 'local_financedepartment');
        }

        if (!empty($entry->reason)) {
            $changelines[] = html_writer::tag('em', get_string('reason', 'local_financedepartment') . ': ' . s($entry->reason));
        }

        $table->data[] = [
            userdate($entry->timecreated),
            $who,
            get_string('auditaction_' . $entry->action, 'local_financedepartment'),
            implode(html_writer::empty_tag('br'), $changelines),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
