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
 * Attendance Tracking landing page: a read-only report on top of the
 * site's mod_attendance activity data, organised Course -> Day/session ->
 * record list. This plugin never takes attendance itself - that still
 * happens the normal way, in each course's Attendance activity.
 *
 * Users who can view attendance for at least one course (HR/admins
 * holding local/hrdepartment:manageattendance, or lecturers with an
 * active hrdep_courseassign) see the course listing here. Everyone else
 * (a plain student) sees their own attendance summary and history
 * instead of being blocked outright.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\student_attendance_manager;
use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$canviewall = access_manager::can_manage('local/hrdepartment:manageattendance');
$manageablecourses = student_attendance_manager::get_manageable_courses((int) $USER->id, $canviewall);
$canviewany = $canviewall || !empty($manageablecourses);

if (!$canviewany) {
    // Falls through to the self-service branch below - viewownattendance
    // defaults to every logged-in user, so this is mostly a safety net
    // for sites that have deliberately revoked it.
    require_capability('local/hrdepartment:viewownattendance', $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/attendance/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('attendance', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('attendance');

echo html_writer::start_div('local-hrdepartment-attendance');

if ($canviewany) {

    echo local_hrdepartment_render_page_hero(
        get_string('attendance', 'local_hrdepartment'),
        get_string('attendanceoverviewsubtitle', 'local_hrdepartment')
    );

    $courseidsarray = $canviewall ? null : array_keys($manageablecourses);
    $courses = student_attendance_manager::get_courses_with_attendance($courseidsarray);

    if (empty($courses)) {
        echo local_hrdepartment_render_empty_state(
            get_string('nocoursesattendance', 'local_hrdepartment'),
            'fa-clipboard-check'
        );
    } else {
        $table = new html_table();
        $table->head = [
            get_string('course', 'local_hrdepartment'),
            get_string('sessioncount', 'local_hrdepartment'),
            get_string('lastsession', 'local_hrdepartment'),
            get_string('actions'),
        ];
        $table->attributes['class'] = 'generaltable local-hrdepartment-attendance-courses';

        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        foreach ($courses as $course) {
            $viewurl = new moodle_url('/local/hrdepartment/attendance/course.php', ['courseid' => $course->id]);
            $table->data[] = [
                html_writer::link($viewurl, $course->shortname . ': ' . format_string($course->fullname)),
                $course->sessioncount,
                $course->lastsessiondate ? userdate($course->lastsessiondate, $dateformat) : '-',
                html_writer::link($viewurl, get_string('viewsessions', 'local_hrdepartment')),
            ];
        }

        echo local_hrdepartment_render_table_card(html_writer::table($table));
    }

} else {

    // Self-service: the logged-in user's own attendance across every
    // course they have attendance records in.
    echo local_hrdepartment_render_page_hero(
        get_string('myattendance', 'local_hrdepartment'),
        get_string('myattendancesubtitle', 'local_hrdepartment')
    );

    $summary = student_attendance_manager::get_student_status_summary((int) $USER->id);

    if (empty($summary)) {
        echo local_hrdepartment_render_empty_state(
            get_string('noattendancerecords', 'local_hrdepartment'),
            'fa-clipboard-check'
        );
    } else {
        $colors = ['hrdept-stat-c1', 'hrdept-stat-c2', 'hrdept-stat-c3', 'hrdept-stat-c4', 'hrdept-stat-c5'];
        echo html_writer::start_div('hrdept-stats-strip');
        foreach (array_values($summary) as $i => $row) {
            echo local_hrdepartment_render_stat_card(
                (string) $row->total,
                s($row->description) . ' (' . s($row->acronym) . ')',
                $colors[$i % count($colors)],
                'fa-clipboard-check'
            );
        }
        echo html_writer::end_div();

        $records = student_attendance_manager::get_student_records((int) $USER->id);

        $table = new html_table();
        $table->head = [
            get_string('course', 'local_hrdepartment'),
            get_string('attendancedate', 'local_hrdepartment'),
            get_string('status', 'local_hrdepartment'),
            get_string('remarks', 'local_hrdepartment'),
        ];
        $table->attributes['class'] = 'generaltable local-hrdepartment-my-attendance';

        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        foreach ($records as $record) {
            $table->data[] = [
                $record->shortname . ': ' . format_string($record->fullname),
                userdate($record->sessdate, $dateformat),
                s($record->statusdescription) . ' (' . s($record->acronym) . ')',
                $record->remarks !== null && $record->remarks !== '' ? format_string($record->remarks) : '-',
            ];
        }

        echo local_hrdepartment_render_table_card(html_writer::table($table));
    }
}

echo html_writer::end_div();

echo $OUTPUT->footer();
