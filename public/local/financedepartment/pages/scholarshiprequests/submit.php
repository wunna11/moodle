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
 * Submit a scholarship request - a single self-contained page
 * (scholarship, description, optional attachments all picked here).
 * CHANGED 2026-09-10 (v2026091004/0.8.0): no fee record is picked here
 * any more at all - the user reported this shouldn't be something a
 * student has to fill in, and the whole fee-record linkage was removed
 * from this workflow (see scholarshiprequest_form.php and
 * scholarshiprequest_manager's own docblocks for the full rationale).
 * The old "no active fee record yet" empty-state gate that used to
 * guard this page is gone too - there's nothing left that requires one.
 *
 * REWRITTEN AGAIN 2026-09-09: the student picked here used to be chosen
 * by finance staff from a system-wide autocomplete (managescholarships-
 * gated) - meaning a student could never nominate themselves, only
 * finance staff could submit on their behalf. This was backwards - a
 * scholarship request is now student self-service: the viewer is always
 * the student, gated on the new
 * local/financedepartment:submitscholarshiprequest capability (a plain
 * per-user capability, NOT routed through access_manager::can_manage() -
 * see db/access.php's docblock for that capability). See
 * [[financedepartment-schema]] project memory for the full history.
 *
 * Earlier revision (2026-08-24) kept for context: originally a studentid
 * had to be chosen via a separate search page
 * (pages/scholarshiprequests/pick.php, long since retired) before this
 * page would even load.
 *
 * Added 2026-09-10: an optional `scholarshipid` GET param (from
 * pages/scholarships/browse.php's "Request this" link) pre-selects the
 * scholarship on the form - see scholarshiprequest_form.php's
 * `presetscholarshipid` customdata handling.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\scholarshiprequest_manager;
use local_financedepartment\form\scholarshiprequest_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$context = context_system::instance();
require_capability('local/financedepartment:submitscholarshiprequest', $context);

$presetscholarshipid = optional_param('scholarshipid', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php', [
    'scholarshipid' => $presetscholarshipid,
]));
$PAGE->set_pagelayout('standard');
$title = get_string('submitrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$returnurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php');

$form = new scholarshiprequest_form($PAGE->url, [
    'studentid' => (int) $USER->id,
    'presetscholarshipid' => $presetscholarshipid,
]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $newid = scholarshiprequest_manager::submit($data, $USER->id);

    // Move the optional supporting-document upload out of the user's
    // draft area and into this plugin's own permanent file area,
    // itemid = the new request's id - see lib.php's
    // local_financedepartment_pluginfile() for how it's served back.
    // maxfiles raised 1 -> scholarshiprequest_form::ATTACHMENT_MAXFILES
    // (5) 2026-09-10, per the user's request for multiple-file upload -
    // must match the form's own filemanager maxfiles option exactly.
    if (!empty($data->attachment)) {
        file_save_draft_area_files(
            $data->attachment,
            $context->id,
            'local_financedepartment',
            'scholarshiprequest',
            $newid,
            ['subdirs' => 0, 'maxfiles' => scholarshiprequest_form::ATTACHMENT_MAXFILES]
        );
    }

    redirect(
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $newid]),
        get_string('scholarshiprequestsubmitted', 'local_financedepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarshiprequests-form');

echo local_financedepartment_render_back_link($returnurl, get_string('backtorequests', 'local_financedepartment'));

echo local_financedepartment_render_page_hero($title, get_string('submitrequestdesc', 'local_financedepartment'));

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
