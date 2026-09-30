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
 * The Student 360 Profile aggregated view for one student - pulls
 * together fee/scholarship/discount data from local_financedepartment
 * and attendance/leave data from local_hrdepartment into a single
 * read-only page. See classes/profile_manager.php for the aggregation
 * itself; this page is rendering only.
 *
 * Visual design (2026-09-18 pass): a gradient hero + KPI stat-card row
 * + card-style panels, matching the "modern premium" look the user
 * pointed to. Deliberately reuses local_financedepartment's own
 * local_financedepartment_render_stat_card() (findept-stat-card /
 * --findept-brand-gradient) rather than inventing a second visual
 * language, so this new plugin reads as part of the same site rather
 * than a bolted-on theme - the only NEW CSS is the hero/avatar/chip/
 * panel treatment in this plugin's own styles.css.
 *
 * Reachable two ways: a view-any viewer (Finance/HR staff, or a site
 * admin) opens any studentid from the search page (pages/index.php); a
 * view-own viewer (a plain student) can only open their own userid -
 * access_manager::require_view_profile() enforces this.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_student360\access_manager;
use local_student360\profile_manager;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/financedepartment/lib.php');

require_login();
$PAGE->set_primary_active_tab('local_student360');

$studentid = required_param('studentid', PARAM_INT);

$context = context_system::instance();
access_manager::require_view_profile($studentid);
$canviewany = access_manager::can_view_any();

$student = \local_hrdepartment\student_manager::get_student($studentid);
if (!$student) {
    throw new moodle_exception('errorstudentnotfound', 'local_student360');
}

$profile = profile_manager::get_profile($studentid);

$heading = format_string($student->fullname);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/student360/pages/view.php', ['studentid' => $studentid]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_student360') . ': ' . $heading);
$PAGE->set_heading(get_string('pluginname', 'local_student360'));

echo $OUTPUT->header();
echo html_writer::start_div('local-student360-view');

if ($canviewany) {
    echo local_student360_render_back_link(
        new moodle_url('/local/student360/pages/index.php'),
        get_string('backtosearch', 'local_student360')
    );
}

// ---- Hero ----
$initials = strtoupper(
    mb_substr($student->firstname ?? '', 0, 1) . mb_substr($student->lastname ?? '', 0, 1)
);
$coursecount = count($student->courses ?? []);

echo html_writer::start_div('student360-hero');
echo html_writer::div(s($initials), 'student360-hero-avatar');
echo html_writer::start_div('student360-hero-text');
echo html_writer::tag('h2', $heading, ['class' => 'student360-hero-name']);
echo html_writer::tag('p', s($student->email), ['class' => 'student360-hero-email']);
echo html_writer::start_div('student360-hero-chips');
echo html_writer::tag(
    'span',
    $coursecount . ' ' . get_string('coursesenrolled', 'local_student360'),
    ['class' => 'student360-chip']
);
if (!empty($profile->summary->pendingrequests)) {
    echo html_writer::tag(
        'span',
        $profile->summary->pendingrequests . ' ' . get_string('pendingrequests', 'local_student360'),
        ['class' => 'student360-chip student360-chip-warning']
    );
}
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// ---- KPI stat row (reuses local_financedepartment's own stat-card renderer). ----
echo html_writer::start_div('student360-statgrid');
echo local_financedepartment_render_stat_card(
    get_string('statoutstanding', 'local_student360'),
    local_financedepartment_format_money($profile->summary->totalbalance),
    'fa-balance-scale',
    $profile->summary->totalbalance > 0 ? 'danger' : 'success'
);
echo local_financedepartment_render_stat_card(
    get_string('stattotalpaid', 'local_student360'),
    local_financedepartment_format_money($profile->summary->totalpaid),
    'fa-money',
    'success'
);
echo local_financedepartment_render_stat_card(
    get_string('statactivescholarships', 'local_student360'),
    (string) $profile->summary->activescholarships,
    'fa-graduation-cap',
    'info'
);
echo local_financedepartment_render_stat_card(
    get_string('statpendingrequests', 'local_student360'),
    (string) $profile->summary->pendingrequests,
    'fa-clock-o',
    'warning'
);
echo local_financedepartment_render_stat_card(
    get_string('stattotalsessions', 'local_student360'),
    (string) $profile->summary->totalsessions,
    'fa-check-square-o',
    'teal'
);
echo html_writer::end_div();

// ---- Finance panel ----
echo html_writer::start_div('student360-panel');
echo html_writer::start_div('student360-panel-header');
echo html_writer::tag('span', html_writer::tag('i', '', ['class' => 'fa fa-money', 'aria-hidden' => 'true']), ['class' => 'icon']);
echo html_writer::tag('h3', get_string('financesummary', 'local_student360'));
echo html_writer::end_div();

if (empty($profile->feerecords)) {
    echo local_student360_render_empty_state(get_string('nofeerecords', 'local_student360'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('feestructure', 'local_financedepartment'),
        get_string('totalamount', 'local_financedepartment'),
        get_string('paidamount', 'local_financedepartment'),
        get_string('balance', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
        '',
    ];
    $table->data = [];
    foreach ($profile->feerecords as $feerecord) {
        $link = $canviewany
            ? html_writer::link(
                new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $feerecord->id]),
                get_string('viewfullrecord', 'local_student360'),
                ['class' => 'btn btn-sm btn-outline-primary']
            )
            : '';
        $table->data[] = [
            format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear),
            local_financedepartment_format_money($feerecord->totalamount),
            local_financedepartment_format_money($feerecord->paidamount),
            local_financedepartment_format_money($feerecord->balance),
            local_financedepartment_feerecord_status_badge($feerecord),
            $link,
        ];
    }
    echo local_student360_render_table_card(html_writer::table($table));
}

echo html_writer::start_div('student360-panel-subsection');
echo html_writer::tag('h4', get_string('scholarshiprequests', 'local_student360'));
if (empty($profile->scholarshiprequests)) {
    echo local_student360_render_empty_state(get_string('noscholarshiprequests', 'local_student360'));
} else {
    $table = new html_table();
    $table->head = [get_string('amount', 'local_student360'), get_string('status', 'local_financedepartment'), get_string('date')];
    $table->data = [];
    foreach ($profile->scholarshiprequests as $req) {
        $amount = $req->approvedamount ?? $req->requestedamount ?? null;
        $table->data[] = [
            $amount !== null ? local_financedepartment_format_money($amount) : get_string('pendingapproval', 'local_student360'),
            local_financedepartment_scholarshiprequest_status_badge($req->status),
            userdate($req->timecreated),
        ];
    }
    echo local_student360_render_table_card(html_writer::table($table));
}
echo html_writer::end_div();

echo html_writer::start_div('student360-panel-subsection');
echo html_writer::tag('h4', get_string('discountrequests', 'local_student360'));
if (empty($profile->discountrequests)) {
    echo local_student360_render_empty_state(get_string('nodiscountrequests', 'local_student360'));
} else {
    $table = new html_table();
    $table->head = [get_string('amount', 'local_student360'), get_string('status', 'local_financedepartment'), get_string('date')];
    $table->data = [];
    foreach ($profile->discountrequests as $req) {
        $amount = $req->approvedamount ?? $req->requestedamount ?? null;
        $table->data[] = [
            $amount !== null ? local_financedepartment_format_money($amount) : get_string('pendingapproval', 'local_student360'),
            local_financedepartment_discountrequest_status_badge($req->status),
            userdate($req->timecreated),
        ];
    }
    echo local_student360_render_table_card(html_writer::table($table));
}
echo html_writer::end_div();
echo html_writer::end_div(); // .student360-panel (finance)

// ---- Attendance panel ----
echo html_writer::start_div('student360-panel');
echo html_writer::start_div('student360-panel-header');
echo html_writer::tag('span', html_writer::tag('i', '', ['class' => 'fa fa-check-square-o', 'aria-hidden' => 'true']), ['class' => 'icon']);
echo html_writer::tag('h3', get_string('attendancesummary', 'local_student360'));
echo html_writer::end_div();

if (empty($profile->attendancebycourse)) {
    echo local_student360_render_empty_state(get_string('noattendancedata', 'local_student360'));
} else {
    foreach ($profile->attendancebycourse as $course) {
        echo html_writer::start_div('student360-panel-subsection');
        echo html_writer::tag('h4', format_string($course->fullname));

        if (empty($course->statuses)) {
            echo local_student360_render_empty_state(get_string('nocoursesattendance', 'local_student360'));
        } else {
            $table = new html_table();
            $table->head = [get_string('status', 'local_student360'), get_string('totalsessions', 'local_student360')];
            $table->data = [];
            foreach ($course->statuses as $row) {
                $table->data[] = [format_string($row->description) . ' (' . s($row->acronym) . ')', $row->total];
            }
            echo local_student360_render_table_card(html_writer::table($table));
        }
        echo html_writer::end_div();
    }
}

echo html_writer::start_div('student360-panel-subsection');
echo html_writer::tag('h4', get_string('leaveapplications', 'local_student360'));
if (empty($profile->leaveapplications)) {
    echo local_student360_render_empty_state(get_string('noleaveapplications', 'local_student360'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('leavetype', 'local_student360'),
        get_string('period', 'local_student360'),
        get_string('leavestatus', 'local_student360'),
    ];
    $table->data = [];
    foreach ($profile->leaveapplications as $app) {
        $table->data[] = [
            format_string($app->leavetypename),
            userdate($app->startdate, get_string('strftimedateshort')) . ' - ' . userdate($app->enddate, get_string('strftimedateshort')),
            html_writer::span(ucfirst($app->status), 'badge badge-secondary'),
        ];
    }
    echo local_student360_render_table_card(html_writer::table($table));
}
echo html_writer::end_div();
echo html_writer::end_div(); // .student360-panel (attendance)

echo html_writer::end_div();
echo $OUTPUT->footer();
