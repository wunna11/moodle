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
 * View one scholarship request's details and its review history.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\audit_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarshiprequest_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();

$canmanage = access_manager::can_manage('local/financedepartment:managescholarships');
$canapprove = access_manager::can_manage('local/financedepartment:approvescholarships');

if (!$canmanage && !$canapprove) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

$request = scholarshiprequest_manager::get($id);
if (!$request) {
    throw new moodle_exception(
        'errorscholarshiprequestnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php')
    );
}

$heading = format_string($request->fullname) . ' - ' . format_string($request->scholarshipname);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($heading);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarshiprequests-view');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', ['studentid' => $request->studentid]),
    get_string('backtorequests', 'local_financedepartment')
);

$actions = [];
if ($canapprove && $request->status === constants::REQUEST_STATUS_PENDING) {
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/scholarshiprequests/review.php', ['id' => $id, 'decision' => 'approve']),
        'label' => get_string('approve', 'local_financedepartment'),
        'icon' => 'fa-check',
    ];
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/scholarshiprequests/review.php', ['id' => $id, 'decision' => 'reject']),
        'label' => get_string('reject', 'local_financedepartment'),
        'icon' => 'fa-times',
    ];
}

// Delete is offered for any non-deleted status (added 2026-08-24) - see
// scholarshiprequest_manager::delete()'s docblock for what happens to
// an approved row's fee record balance on delete.
if ($canmanage && $request->status !== constants::REQUEST_STATUS_DELETED) {
    $actions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/scholarshiprequests/delete.php', ['id' => $id]),
        'label' => get_string('delete'),
        'icon' => 'fa-trash',
    ];
}

echo local_financedepartment_render_page_hero(
    $heading,
    get_string('scholarshiprequestdetails', 'local_financedepartment'),
    $actions
);

// Details card.
echo html_writer::start_div('findept-detail-card');

$rows = [
    [get_string('student', 'local_financedepartment'), format_string($request->fullname) . ' (' . s($request->email) . ')'],
    [get_string('category', 'local_financedepartment'), format_string($request->categoryname) . ' - ' . s($request->academicyear)],
    [get_string('scholarship', 'local_financedepartment'), format_string($request->scholarshipname)],
    [get_string('requestedamount', 'local_financedepartment'), local_financedepartment_format_money($request->requestedamount)],
];

if ($request->status !== constants::REQUEST_STATUS_PENDING) {
    $rows[] = [
        get_string('approvedamount', 'local_financedepartment'),
        $request->approvedamount !== null ? local_financedepartment_format_money($request->approvedamount) : '-',
    ];
}

$rows[] = [get_string('status', 'local_financedepartment'), local_financedepartment_scholarshiprequest_status_badge($request->status)];
$rows[] = [get_string('description', 'local_financedepartment'), $request->justification !== ''
    ? format_text($request->justification, FORMAT_PLAIN) : '-'];

// Optional supporting-document attachment - see lib.php's
// local_financedepartment_pluginfile() for how this is served, and
// pages/scholarshiprequests/submit.php for where it's saved.
$fs = get_file_storage();
$attachments = $fs->get_area_files($context->id, 'local_financedepartment', 'scholarshiprequest', $id, 'filename', false);
if (!empty($attachments)) {
    $attachmentlinks = [];
    foreach ($attachments as $file) {
        $fileurl = moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            'local_financedepartment',
            'scholarshiprequest',
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
echo html_writer::tag('h3', get_string('scholarshiprequesthistory', 'local_financedepartment'), ['class' => 'findept-section-title']);

$history = audit_manager::get_history(constants::AUDIT_ENTITY_SCHOLARSHIPREQUEST, $id);

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
        } else if ($entry->action === constants::AUDIT_ACTION_DELETE) {
            $restoredamount = $new['restoredamount'] ?? null;
            $changelines[] = $restoredamount !== null
                ? get_string('historyrequestdeletedwithreversal', 'local_financedepartment', local_financedepartment_format_money($restoredamount))
                : get_string('historyrequestdeleted', 'local_financedepartment');
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
