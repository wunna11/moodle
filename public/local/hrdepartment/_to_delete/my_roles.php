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
 * Renderable for the "My roles" self-service section: lets any logged-in
 * user see every Moodle role they currently hold, and where, without
 * navigating through Preferences > Roles > This user's role assignments.
 *
 * Added so users can confirm role assignments that matter to this plugin
 * at a glance - in particular a delegated student-leave "Approver" role,
 * which is assigned on one student's own user context (see
 * local_hrdepartment\student_leave_manager and db/access.php) and would
 * otherwise be easy to miss in the standard Moodle admin UI.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\dashboard_helper;
use renderable;
use renderer_base;
use templatable;

/**
 * Class my_roles
 */
class my_roles implements renderable, templatable {

    /** @var int */
    protected $userid;

    /**
     * Constructor.
     *
     * @param int $userid
     */
    public function __construct(int $userid) {
        $this->userid = $userid;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $assignments = dashboard_helper::get_my_role_assignments($this->userid);

        $roles = [];
        foreach ($assignments as $assignment) {
            $roles[] = [
                'rolename' => $assignment->rolename,
                'contextname' => $assignment->contextname,
                'hasurl' => $assignment->contexturl !== null,
                'contexturl' => $assignment->contexturl !== null ? $assignment->contexturl->out(false) : null,
            ];
        }

        return [
            'hasroles' => !empty($roles),
            'roles' => $roles,
        ];
    }
}
