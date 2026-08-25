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
 * Scholarship requests: a pending-review queue for approvers, plus a
 * per-student search to view a student's own requests and submit a new
 * one. Same two-capability split as db/access.php's managescholarships/
 * approvescholarships - a role can nominate without also being able to
 * approve its own nomination.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarshiprequest_manager;
use local_hrdepartment\student_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();

$canmanage = access_manager::can_manage('local/financedepartment:managescholarships');
$canapprove = access_manager::can_manage('local/financedepartment:approvescholarships');

if (!$canmanage && !$canapprove) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

$studentid = optional_param('studentid', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', [
    'studentid' => $studentid, 'search' => $search,
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

echo local_financedepartment_render_page_hero(
    get_string('scholarshiprequests', 'local_financedepartment'),
    get_string('scholarshiprequestsdesc', 'local_financedepartment')
);

// Student search bar.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $PAGE->url->out_omit_querystring(),
    'class' => 'findept-filter-bar',
]);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('searchstudent', 'local_financedepartment'),
    'class' => 'findept-filter-text',
]);
echo html_writer::tag('button', get_string('filter', 'local_financedepartment'), [
    'type' => 'submit',
    'class' => 'btn btn-primary findept-filter-submit',
]);
echo html_writer::link(
    new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
    get_string('reset', 'local_financedepartment'),
    ['class' => 'findept-filter-reset']
);
echo html_writer::end_tag('form');

if ($studentid) {
    $student = $DB->get_record('user', ['id' => $studentid, 'deleted' => 0]);
    if (!$student) {
        throw new moodle_exception('invaliduser', 'error', new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'));
    }

    $heading = get_string('requestsfor', 'local_financedepartment', fullname($student) . ' (' . $student->email . ')');
    if ($canmanage) {
        echo html_writer::start_div('findept-inline-heading-row');
        echo html_writer::tag('h3', $heading, ['class' => 'findept-section-title']);
        echo html_writer::link(
            new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php', ['studentid' => $studentid]),
            html_writer::tag('i', '', ['class' => 'icon fa fa-plus', 'aria-hidden' => 'true']) . ' '
                . get_string('submitrequest', 'local_financedepartment'),
            ['class' => 'btn btn-secondary']
        );
        echo html_writer::end_div();
    } else {
        echo html_writer::tag('h3', $heading, ['class' => 'findept-section-title']);
    }

    $requests = scholarshiprequest_manager::get_for_student($studentid);

    if (empty($requests)) {
        echo local_financedepartment_render_empty_state(get_string('noscholarshiprequests', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('scholarship', 'local_financedepartment'),
            get_string('requestedamount', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('when', 'local_financedepartment'),
            '',
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-scholarshiprequest-table';

        foreach ($requests as $request) {
            $viewurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $request->id]);
            $table->data[] = [
                format_string($request->scholarshipname),
                local_financedepartment_format_money($request->requestedamount),
                local_financedepartment_scholarshiprequest_status_badge($request->status),
                userdate($request->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
                html_writer::link($viewurl, get_string('view')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else if ($search !== '') {
    $matches = student_manager::get_students($search, 0, '', 0, 20);

    if (empty($matches)) {
        echo local_financedepartment_render_empty_state(get_string('nostudentsfound', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [get_string('fullname'), get_string('email'), ''];
        $table->attributes['class'] = 'generaltable local-financedepartment-scholarshiprequest-table';

        foreach ($matches as $match) {
            $selecturl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php', ['studentid' => $match->id]);
            $table->data[] = [
                format_string($match->fullname),
                s($match->email),
                html_writer::link($selecturl, get_string('viewrequests', 'local_financedepartment')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else if ($canapprove) {
    echo html_writer::tag('h3', get_string('pendingqueue', 'local_financedepartment'), ['class' => 'findept-section-title']);

    $pending = scholarshiprequest_manager::get_pending(20);

    if (empty($pending)) {
        echo local_financedepartment_render_empty_state(get_string('noscholarshiprequests', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('student', 'local_financedepartment'),
            get_string('scholarship', 'local_financedepartment'),
            get_string('requestedamount', 'local_financedepartment'),
            get_string('when', 'local_financedepartment'),
            '',
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-scholarshiprequest-table';

        foreach ($pending as $request) {
            $viewurl = new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $request->id]);
            $table->data[] = [
                format_string($request->fullname),
                format_string($request->scholarshipname),
                local_financedepartment_format_money($request->requestedamount),
                userdate($request->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
                html_writer::link($viewurl, get_string('review', 'local_financedepartment')),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }
} else {
    echo local_financedepartment_render_empty_state(get_string('selectstudentprompt', 'local_financedepartment'));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
