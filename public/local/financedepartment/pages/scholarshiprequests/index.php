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
 * Scholarship request list. Finance staff (managescholarships or
 * approvescholarships) see every request across every student -
 * sortable, filterable by status/student, paginated - exactly as
 * before. REWRITTEN AGAIN 2026-09-09: a plain student who only has the
 * new local/financedepartment:submitscholarshiprequest capability now
 * sees a separate, much simpler "my requests" branch instead (their own
 * requests only, via scholarshiprequest_manager::get_for_student() - no
 * admin table, no filters) - see submit.php's docblock for the full
 * history of why submission moved from finance-staff-nominates to
 * student self-service. The "New request" hero action moved from the
 * finance-staff branch to this new student branch for the same reason -
 * finance staff can no longer submit on a student's behalf.
 *
 * REWRITTEN 2026-08-24: the original version of this page only showed
 * anything once you typed a student's name to search for them (or, for
 * an approver, a PENDING-only queue) - the user reported having to
 * search by name every time just to see the list at all. This is now a
 * real browsable list via scholarshiprequest_table, same pattern as
 * pages/scholarships/index.php and pages/fees/index.php.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarshiprequest_manager;
use local_financedepartment\table\scholarshiprequest_table;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$context = context_system::instance();

$canmanage = access_manager::can_manage('local/financedepartment:managescholarships');
$canapprove = access_manager::can_manage('local/financedepartment:approvescholarships');
// Plain per-user self-service capability, NOT routed through
// access_manager - see db/access.php's docblock for
// submitscholarshiprequest and pages/scholarshiprequests/submit.php.
$cansubmitown = has_capability('local/financedepartment:submitscholarshiprequest', $context);

if (!$canmanage && !$canapprove && !$cansubmitown) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

// Finance staff (manage or approve) get the full admin list below,
// unchanged. A plain student (submit-only) gets a much simpler "my
// requests" view instead - see the branch near the bottom of this file.
if (!$canmanage && !$canapprove) {
    $PAGE->set_context($context);
    $PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'));
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title(get_string('scholarshiprequests', 'local_financedepartment'));
    $PAGE->set_heading(access_manager::get_display_name());

    echo $OUTPUT->header();

    echo local_financedepartment_render_tab_bar('scholarships');

    echo html_writer::start_div('local-financedepartment-scholarshiprequests');

    echo local_financedepartment_render_page_hero(
        get_string('scholarshiprequests', 'local_financedepartment'),
        get_string('myscholarshiprequestsdesc', 'local_financedepartment'),
        [
            [
                'url' => new moodle_url('/local/financedepartment/pages/scholarships/browse.php'),
                'label' => get_string('browsescholarships', 'local_financedepartment'),
                'icon' => 'fa-search',
            ],
            [
                'url' => new moodle_url('/local/financedepartment/pages/scholarshiprequests/submit.php'),
                'label' => get_string('newrequest', 'local_financedepartment'),
                'icon' => 'fa-plus',
            ],
        ]
    );

    $myrequests = scholarshiprequest_manager::get_for_student((int) $USER->id);

    if (empty($myrequests)) {
        echo local_financedepartment_render_empty_state(get_string('noscholarshiprequests', 'local_financedepartment'));
    } else {
        $table = new html_table();
        $table->head = [
            get_string('scholarship', 'local_financedepartment'),
            get_string('requestedamount', 'local_financedepartment'),
            get_string('status', 'local_financedepartment'),
            get_string('when', 'local_financedepartment'),
            get_string('actions'),
        ];
        $table->attributes['class'] = 'generaltable local-financedepartment-scholarshiprequests-table';

        foreach ($myrequests as $myrequest) {
            // requestedamount is null for a percentage-type scholarship
            // with no fee record to compute a base amount against (see
            // scholarshiprequest_manager::compute_suggested_amount()'s
            // docblock, v2026091004/0.8.0) - get_for_student() joins in
            // amounttype/amountvalue specifically so this can still show
            // something meaningful instead of a misleading "0 MMK".
            $requestedamountdisplay = $myrequest->requestedamount !== null
                ? local_financedepartment_format_money($myrequest->requestedamount)
                : get_string(
                    'requestedamountpercentagebased',
                    'local_financedepartment',
                    rtrim(rtrim(number_format((float) $myrequest->amountvalue, 2), '0'), '.')
                );

            $table->data[] = [
                format_string($myrequest->scholarshipname),
                $requestedamountdisplay,
                local_financedepartment_scholarshiprequest_status_badge($myrequest->status),
                userdate($myrequest->timecreated, get_string('strftimedatetimeshort', 'core_langconfig')),
                html_writer::link(
                    new moodle_url('/local/financedepartment/pages/scholarshiprequests/view.php', ['id' => $myrequest->id]),
                    get_string('view')
                ),
            ];
        }

        echo local_financedepartment_render_table_card(html_writer::table($table));
    }

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
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
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarshiprequests');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/scholarships/index.php'),
    get_string('backtoscholarships', 'local_financedepartment')
);

// Finance staff can no longer submit a request on a student's behalf
// (2026-09-09 fix - see submit.php's docblock) - no "New request" hero
// action here any more, that only appears on the student self-service
// branch above.
echo local_financedepartment_render_page_hero(
    get_string('scholarshiprequests', 'local_financedepartment'),
    get_string('scholarshiprequestsdesc', 'local_financedepartment')
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
