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
 * Renderable for a single lecturer's profile page.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\constants;
use local_hrdepartment\course_assignment_manager;
use local_hrdepartment\user_account_sync;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class lecturer_profile
 */
class lecturer_profile implements renderable, templatable {

    /** @var \stdClass */
    protected $lecturer;

    /**
     * Constructor.
     *
     * @param \stdClass $lecturer as returned by lecturer_manager::get_lecturer()
     */
    public function __construct(\stdClass $lecturer) {
        $this->lecturer = $lecturer;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $lecturer = $this->lecturer;
        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        // The stored employmentstatus (active/inactive/terminated) can
        // drift from reality: a Moodle account can be suspended (or
        // unsuspended) directly via Site administration > Users,
        // bypassing this plugin entirely. Rather than showing the stored
        // status text alongside a separate "drift" badge, treat the live
        // Moodle account state as the single source of truth for the
        // Active/Suspended label and for which action button shows.
        $employeeactive = !user_account_sync::is_account_suspended($lecturer->userid);

        $assignments = [];
        foreach (course_assignment_manager::get_assignments_for_employee($lecturer->id) as $assignment) {
            $isactive = $assignment->status === constants::COURSEASSIGN_STATUS_ACTIVE;

            // Prefer whatever role Moodle says the lecturer holds in this
            // course right now (it may have been changed directly on the
            // course's Participants page); fall back to the role we
            // recorded at assignment time for historical/ended rows where
            // no live role remains.
            $liverole = course_assignment_manager::get_live_role_names($lecturer->userid, $assignment->courseid);

            // An hrdep_courseassign row marked "active" only means *we*
            // haven't closed it - Moodle's own enrolment can be suspended
            // (or removed entirely) directly on the course's Participants
            // page without this plugin knowing. Surface that drift as its
            // own status rather than showing a misleadingly green "Active".
            $showactive = false;
            $showsuspended = false;
            if ($isactive) {
                $enrolstatus = course_assignment_manager::get_live_enrolment_status(
                    $lecturer->userid,
                    $assignment->courseid
                );
                if ($enrolstatus === ENROL_USER_ACTIVE) {
                    $showactive = true;
                } else {
                    $showsuspended = true;
                }
            }

            $assignments[] = [
                'coursename' => $assignment->shortname . ': ' . format_string($assignment->fullname),
                'courseurl' => (new moodle_url('/course/view.php', ['id' => $assignment->courseid]))->out(false),
                'roleassigned' => $liverole !== '' ? $liverole : $assignment->roleassigned,
                'startdate' => $assignment->startdate ? userdate($assignment->startdate, $dateformat) : '-',
                'enddate' => $assignment->enddate ? userdate($assignment->enddate, $dateformat) : '-',
                'isactive' => $isactive,
                'showactive' => $showactive,
                'showsuspended' => $showsuspended,
                // Reactivating only makes sense while the lecturer
                // themselves is active - keeps assignment state from
                // drifting out of sync with employment status.
                'canreactivate' => !$isactive && $employeeactive,
                'unassignurl' => (new moodle_url('/local/hrdepartment/lecturer/courseunassign.php', [
                    'id' => $assignment->id,
                ]))->out(false),
                'reactivateurl' => (new moodle_url('/local/hrdepartment/lecturer/coursereactivate.php', [
                    'id' => $assignment->id,
                ]))->out(false),
            ];
        }

        return [
            'id' => $lecturer->id,
            'fullname' => $lecturer->fullname,
            'email' => $lecturer->email,
            'employeecode' => $lecturer->employeecode,
            'departmentname' => $lecturer->departmentname ?: '-',
            'designation' => $lecturer->designation ?: '-',
            'employmentstatus' => $employeeactive
                ? get_string('status_active', 'local_hrdepartment')
                : get_string('status_suspended', 'local_hrdepartment'),
            'isactive' => $employeeactive,
            'phone' => $lecturer->phone ?: '-',
            'emergencycontact' => $lecturer->emergencycontact ?: '-',
            'address' => $lecturer->address ?: '-',
            'joindate' => $lecturer->joindate ? userdate($lecturer->joindate, $dateformat) : '-',
            'qualification' => $lecturer->qualification ?: '-',
            'specialization' => $lecturer->specialization ?: '-',
            'hasassignments' => !empty($assignments),
            'assignments' => $assignments,
            'editurl' => (new moodle_url('/local/hrdepartment/lecturer/edit.php', ['id' => $lecturer->id]))->out(false),
            'assignurl' => (new moodle_url('/local/hrdepartment/lecturer/courseassign.php', ['id' => $lecturer->id]))->out(false),
            'deactivateurl' => (new moodle_url('/local/hrdepartment/lecturer/delete.php', ['id' => $lecturer->id]))->out(false),
            'reactivateurl' => (new moodle_url('/local/hrdepartment/lecturer/delete.php', [
                'id' => $lecturer->id, 'reactivate' => 1,
            ]))->out(false),
        ];
    }
}
