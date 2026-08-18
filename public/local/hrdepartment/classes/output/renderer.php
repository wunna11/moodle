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
 * Plugin renderer for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Class renderer
 */
class renderer extends plugin_renderer_base {

    /**
     * Renders the organisation-wide dashboard summary.
     *
     * @param dashboard_summary $page
     * @return string
     */
    public function render_dashboard_summary(dashboard_summary $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/dashboard', $data);
    }

    /**
     * Renders the personal "My HR" snapshot.
     *
     * @param my_summary $page
     * @return string
     */
    public function render_my_summary(my_summary $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/my_summary', $data);
    }

    /**
     * Renders a lecturer's profile page.
     *
     * @param lecturer_profile $page
     * @return string
     */
    public function render_lecturer_profile(lecturer_profile $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/lecturer_profile', $data);
    }

    /**
     * Renders a staff member's profile page.
     *
     * @param staff_profile $page
     * @return string
     */
    public function render_staff_profile(staff_profile $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/staff_profile', $data);
    }

    /**
     * Renders the Students directory (search/filter, card grid, paging).
     *
     * @param students_directory $page
     * @return string
     */
    public function render_students_directory(students_directory $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/students_directory', $data);
    }

    /**
     * Renders the Lecturers directory (search/filter, card grid, paging).
     *
     * @param lecturers_directory $page
     * @return string
     */
    public function render_lecturers_directory(lecturers_directory $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/lecturers_directory', $data);
    }

    /**
     * Renders the Staff directory (search/filter, card grid, paging).
     *
     * @param staff_directory $page
     * @return string
     */
    public function render_staff_directory(staff_directory $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/staff_directory', $data);
    }

    /**
     * Renders a single student's profile page (details + full enrolled
     * course list).
     *
     * @param student_profile $page
     * @return string
     */
    public function render_student_profile(student_profile $page): string {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_hrdepartment/student_profile', $data);
    }
}
