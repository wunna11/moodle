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
 * Submit a scholarship request/nomination for a specific student.
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

$studentid = required_param('studentid', PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managescholarships');

$student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0]);
if (!$student) {
    throw new moodle_exception('invaliduser', 'error', new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'));
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php', ['studentid' => $studentid]));
$PAGE->set_pagelayout('standard');
$title = get_string('submitrequest', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

$returnurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', ['studentid' => $studentid]);

$form = new scholarshiprequest_form($PAGE->url, ['studentid' => $studentid]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $newid = scholarshiprequest_manager::submit($data, $USER->id);
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

echo local_financedepartment_render_page_hero(
    $title,
    get_string('submitrequestdesc', 'local_financedepartment', fullname($student))
);

echo html_writer::start_div('findept-form-card');
$form->display();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
