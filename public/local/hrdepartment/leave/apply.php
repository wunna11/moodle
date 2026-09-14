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
 * Self-service: a student prepares and submits their own leave request,
 * choosing which of their own course teachers should review it, and
 * either a whole day (constants::LEAVE_SCOPE_DAY, the original flow) or
 * 1+ specific mod_attendance session(s) of one of their own courses, all
 * on a single day (constants::LEAVE_SCOPE_SESSION, added 2026-09-13 - see
 * version.php). Distinct from leave/edit.php, which is the HR/staff-facing
 * "log a request on a student's behalf" form (day-scope only).
 *
 * Flow: no ?scope -> choose whole day vs specific session(s); scope=day ->
 * the original single-step form; scope=session with no course/date yet ->
 * a small course+date picker (a plain GET step, same pattern
 * pages/installments/create.php uses for its "how many installments?"
 * pre-step); scope=session with a course+date -> the actual
 * student_leave_apply_session_form, listing only that course's sessions
 * on that exact day.
 *
 * See local_hrdepartment\student_leave_manager.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\constants;
use local_hrdepartment\form\student_leave_apply_form;
use local_hrdepartment\form\student_leave_apply_session_form;
use local_hrdepartment\student_attendance_manager;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

require_capability(student_leave_manager::CAP_APPLYOWN, $context);

if (!student_leave_manager::is_student((int) $USER->id)) {
    throw new moodle_exception(
        'notastudentnoaccess',
        'local_hrdepartment',
        new moodle_url('/local/hrdepartment/leave/index.php')
    );
}

$scope = optional_param('scope', '', PARAM_ALPHA);
$pickcourseid = optional_param('courseid', 0, PARAM_INT);
$sessiondateraw = optional_param('sessiondate', '', PARAM_TEXT);

$urlparams = array_filter([
    'scope' => $scope !== '' ? $scope : null,
    'courseid' => $pickcourseid ?: null,
    'sessiondate' => $sessiondateraw !== '' ? $sessiondateraw : null,
]);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/apply.php', $urlparams));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('applyforleave', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

$studentdisplay = fullname($USER) . ' (' . $USER->email . ')';

// -----------------------------------------------------------------
// Branch 1: whole-day form - the original, unchanged flow.
// -----------------------------------------------------------------
if ($scope === constants::LEAVE_SCOPE_DAY) {
    $form = new student_leave_apply_form($PAGE->url, [
        'studentid' => (int) $USER->id,
        'studentdisplay' => $studentdisplay,
    ]);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/local/hrdepartment/leave/myrequests.php'));
    }

    if ($data = $form->get_data()) {
        // Defence in depth beyond the form's own validation(): never trust a
        // submitted approverid without re-checking it server-side against
        // this student's actual teachers.
        if (!student_leave_manager::is_teacher_of_student((int) $data->approverid, (int) $USER->id)) {
            throw new coding_exception('The selected approver is not a teacher of this student.');
        }

        $data->studentid = (int) $USER->id;
        $data->leavescope = constants::LEAVE_SCOPE_DAY;
        $newid = student_leave_manager::create_application($data, (int) $USER->id);

        redirect(
            new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $newid]),
            get_string('leaverequestsubmitted', 'local_hrdepartment'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }

    echo $OUTPUT->header();
    echo local_hrdepartment_render_tab_bar('leave');
    echo html_writer::start_div('local-hrdepartment-leave-form');
    echo html_writer::link(
        new moodle_url('/local/hrdepartment/leave/apply.php'),
        get_string('changeleaverequesttype', 'local_hrdepartment'),
        ['class' => 'hrdept-back-link']
    );
    echo html_writer::start_div('hrdept-form-hero');
    echo html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa fa-calendar-plus', 'aria-hidden' => 'true']),
        'hrdept-form-hero-icon'
    );
    echo html_writer::div(
        html_writer::tag('h2', get_string('applyforleavewholeday', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-title']) .
        html_writer::tag('p', get_string('applyforleavesubtitle', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-subtitle'])
    );
    echo html_writer::end_div();
    echo html_writer::start_div('hrdept-form-card');
    $form->display();
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo $OUTPUT->footer();

// -----------------------------------------------------------------
// Branch 2: session-scope, but the course+date pre-step hasn't been
// completed yet - render a small GET picker (no sesskey needed, it only
// navigates and never mutates data - same pattern
// pages/installments/create.php's "how many installments?" pre-step
// uses).
// -----------------------------------------------------------------
} else if ($scope === constants::LEAVE_SCOPE_SESSION && (!$pickcourseid || $sessiondateraw === '')) {
    $courseoptions = student_leave_manager::get_course_options_for_student((int) $USER->id);

    echo $OUTPUT->header();
    echo local_hrdepartment_render_tab_bar('leave');
    echo html_writer::start_div('local-hrdepartment-leave-form');
    echo html_writer::link(
        new moodle_url('/local/hrdepartment/leave/apply.php'),
        get_string('changeleaverequesttype', 'local_hrdepartment'),
        ['class' => 'hrdept-back-link']
    );
    echo html_writer::start_div('hrdept-form-hero');
    echo html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa fa-calendar-day', 'aria-hidden' => 'true']),
        'hrdept-form-hero-icon'
    );
    echo html_writer::div(
        html_writer::tag('h2', get_string('applyforleavesession', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-title']) .
        html_writer::tag('p', get_string('selectcoursedatesubtitle', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-subtitle'])
    );
    echo html_writer::end_div();

    echo html_writer::start_div('hrdept-form-card');

    if (empty($courseoptions)) {
        echo local_hrdepartment_render_empty_state(
            get_string('nocoursesforsessionleave', 'local_hrdepartment'),
            'fa-book'
        );
    } else {
        echo html_writer::start_tag('form', [
            'method' => 'get',
            'action' => new moodle_url('/local/hrdepartment/leave/apply.php'),
            'class' => 'hrdept-inline-form',
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'scope', 'value' => constants::LEAVE_SCOPE_SESSION]);

        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('course', 'local_hrdepartment'), ['for' => 'id_courseid']);
        echo html_writer::select($courseoptions, 'courseid', $pickcourseid ?: '', ['' => get_string('choosedots')], ['id' => 'id_courseid', 'class' => 'form-control']);
        echo html_writer::end_div();

        echo html_writer::start_div('form-group');
        echo html_writer::tag('label', get_string('sessiondate', 'local_hrdepartment'), ['for' => 'id_sessiondate']);
        echo html_writer::empty_tag('input', [
            'type' => 'date', 'name' => 'sessiondate', 'id' => 'id_sessiondate', 'class' => 'form-control',
            'value' => $sessiondateraw, 'required' => 'required',
        ]);
        echo html_writer::end_div();

        echo html_writer::tag('button', get_string('findsessions', 'local_hrdepartment'), ['type' => 'submit', 'class' => 'btn btn-primary']);
        echo html_writer::end_tag('form');
    }

    echo html_writer::end_div();
    echo html_writer::end_div();
    echo $OUTPUT->footer();

// -----------------------------------------------------------------
// Branch 3: session-scope with a chosen course+date - show that day's
// sessions for that course (read-only, via student_attendance_manager)
// and the real apply form, or an empty-state if there are none.
// -----------------------------------------------------------------
} else if ($scope === constants::LEAVE_SCOPE_SESSION) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sessiondateraw)) {
        redirect(new moodle_url('/local/hrdepartment/leave/apply.php', ['scope' => constants::LEAVE_SCOPE_SESSION]));
    }

    $courseoptions = student_leave_manager::get_course_options_for_student((int) $USER->id);
    if (!array_key_exists($pickcourseid, $courseoptions)) {
        // Defence in depth: never trust a submitted courseid without
        // re-checking it's actually one of this student's own courses.
        throw new moodle_exception('errorapplicationnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/leave/apply.php'));
    }

    [$year, $month, $day] = array_map('intval', explode('-', $sessiondateraw));
    $daytimestamp = make_timestamp($year, $month, $day);

    $sessions = student_attendance_manager::get_sessions_for_course_on_date($pickcourseid, $daytimestamp);

    echo $OUTPUT->header();
    echo local_hrdepartment_render_tab_bar('leave');
    echo html_writer::start_div('local-hrdepartment-leave-form');
    echo html_writer::link(
        new moodle_url('/local/hrdepartment/leave/apply.php', ['scope' => constants::LEAVE_SCOPE_SESSION]),
        get_string('changecourseordate', 'local_hrdepartment'),
        ['class' => 'hrdept-back-link']
    );

    $dateformat = get_string('strftimedatefullshort', 'langconfig');
    $sessiondatedisplay = userdate($daytimestamp, $dateformat);

    echo html_writer::start_div('hrdept-form-hero');
    echo html_writer::div(
        html_writer::tag('i', '', ['class' => 'icon fa fa-calendar-day', 'aria-hidden' => 'true']),
        'hrdept-form-hero-icon'
    );
    echo html_writer::div(
        html_writer::tag('h2', get_string('applyforleavesession', 'local_hrdepartment'), ['class' => 'hrdept-form-hero-title']) .
        html_writer::tag('p', $courseoptions[$pickcourseid] . ' &middot; ' . $sessiondatedisplay, ['class' => 'hrdept-form-hero-subtitle'])
    );
    echo html_writer::end_div();

    echo html_writer::start_div('hrdept-form-card');

    if (empty($sessions)) {
        echo local_hrdepartment_render_empty_state(
            get_string('nosessionsonthatdate', 'local_hrdepartment'),
            'fa-calendar-times'
        );
    } else {
        $timeformat = get_string('strftimetime', 'langconfig');
        $sessionoptions = [];
        foreach ($sessions as $session) {
            $label = userdate($session->sessdate, $timeformat) . ' - ' . $session->attendancename;
            if (!empty($session->description)) {
                $label .= ' (' . format_string($session->description) . ')';
            }
            $sessionoptions[$session->sessionid] = $label;
        }

        $form = new student_leave_apply_session_form(null, [
            'studentid' => (int) $USER->id,
            'studentdisplay' => $studentdisplay,
            'courseid' => $pickcourseid,
            'coursedisplay' => $courseoptions[$pickcourseid],
            'sessiondate' => $daytimestamp,
            'sessiondatedisplay' => $sessiondatedisplay,
            'sessionoptions' => $sessionoptions,
        ]);

        if ($form->is_cancelled()) {
            redirect(new moodle_url('/local/hrdepartment/leave/myrequests.php'));
        }

        if ($data = $form->get_data()) {
            if (!student_leave_manager::is_teacher_of_student((int) $data->approverid, (int) $USER->id)) {
                throw new coding_exception('The selected approver is not a teacher of this student.');
            }

            $data->studentid = (int) $USER->id;
            $data->startdate = $daytimestamp;
            $data->enddate = $daytimestamp;
            $newid = student_leave_manager::create_application($data, (int) $USER->id);

            redirect(
                new moodle_url('/local/hrdepartment/leave/view.php', ['id' => $newid]),
                get_string('leaverequestsubmitted', 'local_hrdepartment'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        }

        $form->display();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();
    echo $OUTPUT->footer();

// -----------------------------------------------------------------
// Branch 4 (default): no scope chosen yet - let the student pick
// whole day vs specific session(s).
// -----------------------------------------------------------------
} else {
    echo $OUTPUT->header();
    echo local_hrdepartment_render_tab_bar('leave');
    echo html_writer::start_div('local-hrdepartment-leave');
    echo local_hrdepartment_render_page_hero(
        get_string('applyforleave', 'local_hrdepartment'),
        get_string('choosehowtoapplysubtitle', 'local_hrdepartment')
    );
    echo html_writer::start_div('hrdept-quicklink-grid');
    echo local_hrdepartment_render_quicklink(
        new moodle_url('/local/hrdepartment/leave/apply.php', ['scope' => constants::LEAVE_SCOPE_DAY]),
        get_string('applyforleavewholeday', 'local_hrdepartment'),
        'fa-calendar-plus'
    );
    echo local_hrdepartment_render_quicklink(
        new moodle_url('/local/hrdepartment/leave/apply.php', ['scope' => constants::LEAVE_SCOPE_SESSION]),
        get_string('applyforleavesession', 'local_hrdepartment'),
        'fa-calendar-day'
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo $OUTPUT->footer();
}
