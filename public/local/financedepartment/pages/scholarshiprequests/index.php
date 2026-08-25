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
 * Scholarship request list - every request (any status), sortable,
 * filterable by status/student, paginated. REWRITTEN 2026-08-24: the
 * original version of this page only showed anything once you typed a
 * student's name to search for them (or, for an approver, a PENDING-only
 * queue) - the user reported having to search by name every time just
 * to see the list at all. This is now a real browsable list via
 * scholarshiprequest_table, same pattern as pages/scholarships/index.php
 * and pages/fees/index.php. Starting a brand new request still needs a
 * target student chosen first, so that flow moved to pages/scholarshiprequests/pick.php
 * - REMOVED 2026-08-24, see submit.php's own docblock: the student is
 * now a real field directly on the submit form, so this page just links
 * straight to submit.php with no params.
 * rather than living on this page.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\table\scholarshiprequest_table;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();

$canmanage = access_manager::can_manage('local/financedepartment:managescholarships');
$canapprove = access_manager::can_manage('local/financedepartment:approvescholarships');

if (!$canmanage && !$canapprove) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

$status = optional_param('status', '', PARAM_ALPHA);
$studentid = optional_param('studentid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', [
    'status' => $status, 'studentid' => $studentid, 'search' => $search,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('scholarshiprequests', 'local_financedepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarshiprequests');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/scholarships/index.php'),
    get_string('backtoscholarships', 'local_financedepartment')
);

$heroactions = [];
if ($canmanage) {
    $heroactions[] = [
        'url' => new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php'),
        'label' => get_string('newrequest', 'local_financedepartment'),
        'icon' => 'fa-plus',
    ];
}

echo local_financedepartment_render_page_hero(
    get_string('scholarshiprequests', 'local_financedepartment'),
    get_string('scholarshiprequestsdesc', 'local_financedepartment'),
    $heroactions
);

// Filter bar: status + free-text student search. studentid (set only
// via a link from elsewhere, e.g. col_student()'s self-link or
// view.php's back link) is preserved as a hidden field so re-filtering
// by status doesn't lose it, but is cleared by the Reset link.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $PAGE->url->out_omit_querystring(),
    'class' => 'findept-filter-bar',
]);

$statusoptions = [
    '' => get_string('allstatuses', 'local_financedepartment'),
    constants::REQUEST_STATUS_PENDING => get_string('requeststatus_pending', 'local_financedepartment'),
    constants::REQUEST_STATUS_APPROVED => get_string('requeststatus_approved', 'local_financedepartment'),
    constants::REQUEST_STATUS_REJECTED => get_string('requeststatus_rejected', 'local_financedepartment'),
];
echo html_writer::select($statusoptions, 'status', $status, false, ['class' => 'findept-filter-select']);

echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudent', 'local_financedepartment'),
    'class' => 'findept-filter-text',
]);

if ($studentid) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'studentid', 'value' => $studentid]);
}

echo html_writer::tag('button', get_string('filter', 'local_financedepartment'), [
    'type' => 'submit',
    'class' => 'btn btn-primary findept-filter-submit',
]);
echo html_writer::link(
    $PAGE->url->out_omit_querystring(),
    get_string('reset', 'local_financedepartment'),
    ['class' => 'findept-filter-reset']
);

echo html_writer::end_tag('form');

if ($studentid) {
    $student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0]);
    if ($student) {
        echo html_writer::tag(
            'p',
            get_string('showingrequestsfor', 'local_financedepartment', fullname($student)) . ' '
                . html_writer::link($PAGE->url->out_omit_querystring(), get_string('clearfilter', 'local_financedepartment')),
            ['class' => 'findept-filter-note']
        );
    }
}

$table = new scholarshiprequest_table('financedep-scholarshiprequests', $status, $studentid, $search, $canapprove, $canmanage);
$table->define_baseurl($PAGE->url);

ob_start();
$table->out(20, false);
$tablehtml = ob_get_clean();

if ($table->totalrows === 0) {
    echo local_financedepartment_render_empty_state(get_string('noscholarshiprequests', 'local_financedepartment'));
} else {
    echo local_financedepartment_render_table_card($tablehtml);
}

echo html_writer::end_div();

echo $OUTPUT->footer();
