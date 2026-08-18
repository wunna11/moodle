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
 * Renderable for the Students directory: a searchable/filterable,
 * paginated card grid of every student with their enrolled courses.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\course_assignment_manager;
use local_hrdepartment\student_manager;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class students_directory
 */
class students_directory implements renderable, templatable {

    /** @var string */
    protected $search;

    /** @var int */
    protected $courseid;

    /** @var string */
    protected $status;

    /** @var int zero-based page number */
    protected $page;

    /** @var int */
    protected $perpage;

    /** @var moodle_url the page's own URL, used as the filter form action and the paging bar base */
    protected $baseurl;

    /**
     * Constructor.
     *
     * @param string $search name/email search, '' = any
     * @param int $courseid 0 = any course
     * @param string $status '' = any, 'active', or 'suspended'
     * @param int $page zero-based page number
     * @param moodle_url $baseurl
     * @param int $perpage
     */
    public function __construct(
        string $search,
        int $courseid,
        string $status,
        int $page,
        moodle_url $baseurl,
        int $perpage = student_manager::PAGE_SIZE
    ) {
        $this->search = $search;
        $this->courseid = $courseid;
        $this->status = $status;
        $this->page = $page;
        $this->baseurl = $baseurl;
        $this->perpage = $perpage;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $stats = student_manager::get_summary_stats();
        $total = student_manager::count_students($this->search, $this->courseid, $this->status);
        $students = student_manager::get_students($this->search, $this->courseid, $this->status, $this->page, $this->perpage);

        $studentdata = [];
        foreach ($students as $student) {
            $location = trim(implode(', ', array_filter([$student->city ?? '', $student->country ?? ''])));

            $studentdata[] = [
                'id' => $student->id,
                'fullname' => $student->fullname,
                'email' => $student->email,
                'location' => $location,
                'avatarhtml' => $output->user_picture($student, ['size' => 64, 'link' => false, 'class' => 'rounded-circle']),
                'profileurl' => (new moodle_url('/user/profile.php', ['id' => $student->id]))->out(false),
                // The card links out to the Student profile page for the
                // full course list rather than showing courses inline -
                // see student_manager::get_student()'s docblock. A plain
                // link needs no JS, unlike the Bootstrap modal this
                // replaced, which didn't reliably wire up on this theme.
                'coursesurl' => (new moodle_url('/local/hrdepartment/students/view.php', ['id' => $student->id]))->out(false),
                'issuspended' => (bool) $student->suspended,
                'hascourses' => $student->coursecount > 0,
                'coursecount' => $student->coursecount,
            ];
        }

        $courseoptions = [
            ['value' => 0, 'label' => get_string('allcourses', 'local_hrdepartment'), 'issel' => $this->courseid === 0],
        ];
        foreach (course_assignment_manager::get_course_options() as $id => $label) {
            $courseoptions[] = ['value' => $id, 'label' => $label, 'issel' => $this->courseid === $id];
        }

        $statusoptions = [
            ['value' => '', 'label' => get_string('allstatuses', 'local_hrdepartment'), 'issel' => $this->status === ''],
            ['value' => 'active', 'label' => get_string('status_active', 'local_hrdepartment'), 'issel' => $this->status === 'active'],
            ['value' => 'suspended', 'label' => get_string('status_suspended', 'local_hrdepartment'), 'issel' => $this->status === 'suspended'],
        ];

        $pagingbarhtml = '';
        if ($total > $this->perpage) {
            $pagingbarhtml = $output->paging_bar($total, $this->page, $this->perpage, $this->baseurl);
        }

        return [
            'totalstudents' => $stats->totalstudents,
            'totalenrolments' => $stats->totalenrolments,
            'courseswithstudents' => $stats->courseswithstudents,
            'activestudents' => $stats->activestudents,
            'suspendedstudents' => $stats->suspendedstudents,

            'search' => $this->search,
            'formaction' => $this->baseurl->out_omit_querystring(),
            'courseoptions' => $courseoptions,
            'statusoptions' => $statusoptions,

            'hasstudents' => !empty($studentdata),
            'students' => $studentdata,
            'resultcount' => $total,

            'haspaging' => $pagingbarhtml !== '',
            'pagingbarhtml' => $pagingbarhtml,
        ];
    }
}
