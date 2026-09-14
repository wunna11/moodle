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

namespace local_hrdepartment\hooks\navigation;

use local_hrdepartment\access_manager;

/**
 * Adds the HR Department entry to the site's TOP primary navigation bar
 * (Home/Dashboard/My courses/...) - added 2026-09-12 per a user report
 * that this plugin was invisible in the top nav bar while
 * local_financedepartment's "Scholarship"/"Finance Department" entry
 * (added 2026-09-10, see that plugin's db/hooks.php) WAS showing there.
 *
 * Root cause: this plugin only ever had the classic extend_navigation()
 * callback (lib.php), which - on this Moodle version (5.2) - populates
 * $PAGE->navigation (the site navigation tree/side drawer) only. The TOP
 * bar is built separately by core\navigation\views\primary::initialise(),
 * which dispatches THIS hook for plugins to add a node to that bar
 * specifically - a mechanism this plugin never implemented until now,
 * exactly the gap local_financedepartment already hit and fixed first.
 * See local_financedepartment/db/hooks.php's docblock for the original
 * investigation this mirrors.
 *
 * DELIBERATELY narrower than lib.php's extend_navigation() cancontent
 * check: that side-drawer entry shows for ANY self-service capability
 * (applyownleave/viewownattendance/viewownpayroll/is_approver()/can_view()
 * leave - i.e. also a plain student/teacher), but this TOP-bar entry
 * shows ONLY for actual HR staff or a site admin
 * (access_manager::can_access_hr_department()) - a 2026-09-12 user
 * request: a plain student must keep seeing local_financedepartment's
 * "Scholarship" entry in the top bar and nothing else, an HR staff
 * member must see "HR Department" there instead of "Scholarship" (see
 * the matching exclusion added to
 * local_financedepartment\access_manager::can_view_navigation_entry()
 * the same day), and a site admin must see BOTH bars' entries. This is a
 * deliberate difference from local_financedepartment's own pattern
 * (which reuses ONE check for both its nav entry points to avoid drift)
 * - here the two entry points intentionally serve different audiences,
 * so sharing one check would be wrong, not right.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_extend {

    /**
     * Hook callback - see db/hooks.php.
     *
     * 2026-09-13 addendum: this was HR-staff/admin-only when first added
     * (see the class docblock above), by deliberate 2026-09-12 design -
     * a plain student was meant to see ONLY local_financedepartment's
     * "Scholarship" entry in this top bar. That design assumed a
     * student's OWN self-service pages (leave/apply.php, myrequests.php)
     * were reachable some other way - but they never were: the classic
     * extend_navigation() side-drawer entry (lib.php) does check the
     * broader self-service condition, yet doesn't render on this site's
     * custom theme (the same top-bar-vs-drawer split this hook exists to
     * work around in the first place), so a plain student had NO path to
     * their own leave/attendance pages at all. Fixed by widening this
     * hook to also add a (differently-labelled) entry for a self-service
     * -only viewer, mirroring local_financedepartment's own
     * pluginnamestudent role-based-label precedent - see version.php.
     *
     * @param \core\hook\navigation\primary_extend $hook
     * @return void
     */
    public static function callback(\core\hook\navigation\primary_extend $hook): void {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return;
        }

        $context = \context_system::instance();
        $ismanagement = access_manager::can_access_hr_department((int) $USER->id);
        $isselfservice = $ismanagement
            || has_capability('local/hrdepartment:viewownattendance', $context)
            || \local_hrdepartment\student_leave_manager::can_view()
            || has_capability('local/hrdepartment:applyownleave', $context)
            || \local_hrdepartment\student_leave_manager::is_approver((int) $USER->id)
            || has_capability('local/hrdepartment:viewownpayroll', $context);

        if (!$isselfservice) {
            return;
        }

        $name = $ismanagement
            ? get_string('pluginname', 'local_hrdepartment')
            : get_string('pluginnameselfservice', 'local_hrdepartment');

        $primarynav = $hook->get_primaryview();
        $primarynav->add(
            $name,
            new \moodle_url('/local/hrdepartment/index.php'),
            \navigation_node::TYPE_CUSTOM,
            $name,
            'local_hrdepartment',
            new \pix_icon('i/report', '')
        );
    }
}
