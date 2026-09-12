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
 * Submit a manual/hardship discount request - a single self-contained
 * page, mirroring pages/scholarshiprequests/submit.php's shape and its
 * 2026-09-09 rewrite: submitting is now student self-service, gated on
 * the new local/financedepartment:submitdiscountrequest capability
 * (plain per-user capability, not routed through access_manager -
 * see db/access.php's docblock), feerecordid scoped to the viewer's own
 * fee records only. See [[financedepartment-schema]] project memory.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feerecord_manager;
use local_financedepartment\discountrequest_manager;
use local_financedepartment\form\discountrequest_form;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$context = context_system::instance();
require_capability('local/financedepartment:submitdiscountrequest', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/discountrequests/submit.php'));
$PAGE->set_pagelayout('standard');
$title = get_string('submitrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

$returnurl = new moodle_url('/local/financedepartment/pages/discountrequests/index.php');

// A student with no non-cancelled fee record at all has nothing to
// request a discount against - see
// pages/scholarshiprequests/submit.php's matching block.
$feerecordoptions = feerecord_manager::get_active_options_for_student((int) $USER->id);
if (empty($feerecordoptions)) {
    echo $OUTPUT->header();
    echo local_financedepartment_render_tab_bar('discounts');
    echo html_writer::start_div('local-financedepartment-discountrequests-form');
    echo local_financedepartment_render_back_link($returnurl, get_string('backtorequests', 'local_financedepartment'));
    echo local_financedepartment_render_page_hero($title, get_string('submitdiscountrequestdesc', 'local_financedepartment'));
    echo local_financedepartment_render_empty_state(get_string('nofeerecordsowndiscount', 'local_financedepartment'));
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit;
}

$form = new discountrequest_form($PAGE->url, ['studentid' => (int) $USER->id]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $newid = discountrequest_manager::submit($data, $USER->id);

    // Move the optional supporting-document upload out of the user's
    // draft area and into this plugin's own permanent file area,
    // itemid = the new request's id - same pattern as
    // pages/scholarshiprequests/submit.php, own filearea
    // ('discountrequest') so the two don't collide. maxfiles raised
    // 1 -> discountrequest_form::ATTACHMENT_MAXFILES (5) 2026-09-10,
    // per the user's request for multiple-file upload - must match the
    // form's own filemanager maxfiles option exactly.
    if (!empty($data->attachment)) {
        file_save_draft_area_files(
            $data->attachment,
            $context->id,
            'local_financedepartment',
            'discountrequest',
            $newid,
            ['subdirs' => 0, 'maxfiles' => discountrequest_form::ATTACHMENT_MAXFILES]
        );
    }

    redirect(
        new moodle_url('/local/financedepartment/pages/discountrequests/view.php', ['id' => $newid]),
        get_string('discountrequestsubmitted', 'local_financedepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('discounts');

echo html_writer::start_div('local-financedepartment-discountrequests-form');

echo local_financedepartment_render_back_link($returnurl, get_string('backtorequests', 'local_financedepartment'));

echo local_financedepartment_render_page_hero($title, get_string('submitdiscountrequestdesc', 'local_financedepartment'));

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
