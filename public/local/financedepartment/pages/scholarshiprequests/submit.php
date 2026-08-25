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
 * Submit a scholarship request/nomination - a single self-contained
 * page (student, fee record, scholarship, description, optional
 * attachment all picked here). REWRITTEN 2026-08-24 per the user's
 * request: originally this page required a studentid to already be
 * chosen via a separate search page (pages/scholarshiprequests/pick.php,
 * now retired) before it would even load. studentid is now an optional
 * GET param only kept for backward compatibility with old links (it
 * pre-selects the student autocomplete, see scholarshiprequest_form's
 * presetstudentid customdata) - the normal entry point is this page
 * with no params at all, reached from pages/scholarshiprequests/index.php's
 * "New request" action.
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

$presetstudentid = optional_param('studentid', 0, PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managescholarships');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php', ['studentid' => $presetstudentid]));
$PAGE->set_pagelayout('standard');
$title = get_string('submitrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

$returnurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php');

$form = new scholarshiprequest_form($PAGE->url, ['presetstudentid' => $presetstudentid]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $newid = scholarshiprequest_manager::submit($data, $USER->id);

    // Move the optional supporting-document upload out of the user's
    // draft area and into this plugin's own permanent file area,
    // itemid = the new request's id - see lib.php's
    // local_financedepartment_pluginfile() for how it's served back.
    if (!empty($data->attachment)) {
        file_save_draft_area_files(
            $data->attachment,
            $context->id,
            'local_financedepartment',
            'scholarshiprequest',
            $newid,
            ['subdirs' => 0, 'maxfiles' => 1]
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
