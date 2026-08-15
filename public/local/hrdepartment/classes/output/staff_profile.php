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
 * Renderable for a single staff member's profile page.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\constants;
use local_hrdepartment\user_account_sync;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class staff_profile
 *
 * Deliberately simpler than lecturer_profile: no academic details card
 * and no course assignments card, since staff have neither.
 */
class staff_profile implements renderable, templatable {

    /** @var \stdClass */
    protected $staff;

    /**
     * Constructor.
     *
     * @param \stdClass $staff as returned by staff_manager::get_staff()
     */
    public function __construct(\stdClass $staff) {
        $this->staff = $staff;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $staff = $this->staff;
        $dateformat = get_string('strftimedatefullshort', 'langconfig');

        // The stored employmentstatus (active/inactive/terminated) can
        // drift from reality: a Moodle account can be suspended (or
        // unsuspended) directly via Site administration > Users,
        // bypassing this plugin entirely. Treat the live Moodle account
        // state as the single source of truth for the Active/Suspended
        // label and for which action button shows.
        $employeeactive = !user_account_sync::is_account_suspended($staff->userid);

        return [
            'id' => $staff->id,
            'fullname' => $staff->fullname,
            'email' => $staff->email,
            'employeecode' => $staff->employeecode,
            'departmentname' => $staff->departmentname ?: '-',
            'designation' => $staff->designation ?: '-',
            'reportstoname' => $this->get_manager_name($staff->reportsto),
            'employmentstatus' => $employeeactive
                ? get_string('status_active', 'local_hrdepartment')
                : get_string('status_suspended', 'local_hrdepartment'),
            'isactive' => $employeeactive,
            'phone' => $staff->phone ?: '-',
            'emergencycontact' => $staff->emergencycontact ?: '-',
            'address' => $staff->address ?: '-',
            'joindate' => $staff->joindate ? userdate($staff->joindate, $dateformat) : '-',
            'editurl' => (new moodle_url('/local/hrdepartment/staff/edit.php', ['id' => $staff->id]))->out(false),
            'deactivateurl' => (new moodle_url('/local/hrdepartment/staff/delete.php', ['id' => $staff->id]))->out(false),
            'reactivateurl' => (new moodle_url('/local/hrdepartment/staff/delete.php', [
                'id' => $staff->id, 'reactivate' => 1,
            ]))->out(false),
        ];
    }

    /**
     * Looks up a manager's display name for the "reports to" field.
     *
     * @param int|null $employeeid
     * @return string
     */
    protected function get_manager_name(?int $employeeid): string {
        global $DB;

        if (!$employeeid) {
            return '-';
        }

        $sql = "SELECT u.* FROM {hrdep_employee} e JOIN {user} u ON u.id = e.userid WHERE e.id = :id";
        $manager = $DB->get_record_sql($sql, ['id' => $employeeid]);

        return $manager ? fullname($manager) : '-';
    }
}
