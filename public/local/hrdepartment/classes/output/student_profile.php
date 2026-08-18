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
 * Renderable for a single student's profile page: header details plus
 * their full enrolled-course list as a card grid.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class student_profile
 */
class student_profile implements renderable, templatable {

    /** @var \stdClass */
    protected $student;

    /**
     * Constructor.
     *
     * @param \stdClass $student as returned by student_manager::get_student()
     */
    public function __construct(\stdClass $student) {
        $this->student = $student;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $student = $this->student;

        $courses = [];
        foreach ($student->courses as $course) {
            $courses[] = [
                'id' => $course->id,
                'shortname' => $course->shortname,
                'fullname' => format_string($course->fullname),
                'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
            ];
        }

        $location = trim(implode(', ', array_filter([$student->city ?? '', $student->country ?? ''])));

        return [
            'id' => $student->id,
            'fullname' => $student->fullname,
            'email' => $student->email,
            'location' => $location,
            'haslocation' => $location !== '',
            'avatarhtml' => $output->user_picture($student, ['size' => 100, 'link' => false, 'class' => 'rounded-circle']),
            'profileurl' => (new moodle_url('/user/profile.php', ['id' => $student->id]))->out(false),
            'issuspended' => (bool) $student->suspended,
            'lastaccess' => $student->lastaccess
                ? userdate($student->lastaccess, get_string('strftimedatetimeshort', 'langconfig'))
                : get_string('never'),

            'coursecount' => count($courses),
            'hascourses' => !empty($courses),
            'courses' => $courses,

            'backurl' => (new moodle_url('/local/hrdepartment/students/index.php'))->out(false),
        ];
    }
}
