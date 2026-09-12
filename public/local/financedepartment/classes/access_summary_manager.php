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
 * Read-only aggregation for Step 7.12 (Access) - a summary view of this
 * plugin's own capabilities/role assignments plus the actual Finance
 * Department staff list, so a finance manager/admin can see "who has
 * access to what" without leaving the plugin.
 *
 * Deliberately READ-ONLY (per the user's own scope decision, Option 1 of
 * the two presented): this class only ever queries and formats existing
 * Moodle/local_hrdepartment data. It never writes a role assignment, a
 * capability override, or an hrdep_employee row - editing any of that
 * still goes through Moodle's stock "Define roles" UI (linked to from
 * the summary page) or local_hrdepartment's own Staff pages, exactly as
 * it did before this step existed. See [[financedepartment-step712]]
 * project memory for the full scope decision and why a read-only page
 * was chosen over a custom permission editor.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

class access_summary_manager {

    /**
     * One row per local/financedepartment:* capability this plugin
     * defines, read LIVE from the {capabilities} table (populated from
     * db/access.php at install/upgrade time) rather than hardcoded as a
     * list here - so this summary can never drift out of sync with
     * db/access.php the way access_manager::MANAGEMENT_CAPABILITIES (a
     * manually-maintained list, by that const's own docblock) can.
     *
     * Each returned object has: name (full capability name), shortname
     * (without the local/financedepartment: prefix), displayname (Moodle's
     * own get_capability_string(), reading the SAME
     * financedepartment:<shortname> lang string every capability already
     * has for the Define roles UI), description (that capability's
     * *_help lang string, when one exists - several do, added since
     * Step 7.1), captype ('read'|'write'), and roles (string[] of
     * localised role names currently granted CAP_ALLOW on it at system
     * context, via Moodle's own get_roles_with_capability()).
     *
     * @return \stdClass[]
     */
    public static function get_capability_summary(): array {
        global $DB;

        $caps = $DB->get_records('capabilities', ['component' => 'local_financedepartment'], 'name ASC');

        $context = \context_system::instance();
        $stringmanager = get_string_manager();

        $out = [];
        foreach ($caps as $cap) {
            $shortname = preg_replace('#^local/financedepartment:#', '', $cap->name);
            $helpstring = 'financedepartment:' . $shortname . '_help';

            $roleswithcap = get_roles_with_capability($cap->name, CAP_ALLOW, $context);
            $rolenames = [];
            foreach ($roleswithcap as $role) {
                $rolenames[] = role_get_name($role, $context, ROLENAME_ORIGINALANDSHORT);
            }
            sort($rolenames);

            $out[] = (object) [
                'name' => $cap->name,
                'shortname' => $shortname,
                'displayname' => get_capability_string($cap->name),
                'description' => $stringmanager->string_exists($helpstring, 'local_financedepartment')
                    ? get_string($helpstring, 'local_financedepartment')
                    : '',
                'captype' => $cap->captype,
                'roles' => $rolenames,
            ];
        }

        return $out;
    }

    /**
     * The actual Finance Department Staff list - hrdep_employee rows
     * (type = staff) whose department is named "Finance" - the PRIMARY
     * real-world access path this plugin's access_manager::can_manage()
     * grants full access through (see that class's docblock: "a
     * Finance-department hrdep_employee record ... OR a site
     * administrator"), independent of any Moodle role/capability
     * assignment shown by get_capability_summary() above. Deliberately
     * shown alongside the capability/role table so an admin isn't misled
     * into thinking "no roles have these capabilities" means "nobody has
     * access" - in practice, on this site, this list (plus site admins)
     * IS who has access, not role assignment.
     *
     * Identical query shape to
     * access_manager::is_staff_in_finance_department(), just selecting
     * full rows (with the linked user's name/email) instead of a single
     * EXISTS boolean.
     *
     * @return \stdClass[]
     */
    public static function get_finance_staff_list(): array {
        global $DB;

        $sql = "SELECT e.id, e.userid, e.employeecode, e.designation, e.employmentstatus,
                       u.firstname, u.lastname, u.email
                  FROM {hrdep_employee} e
                  JOIN {hrdep_department} d ON d.id = e.departmentid
                  JOIN {user} u ON u.id = e.userid
                 WHERE e.type = :type
                   AND " . $DB->sql_equal('d.name', ':deptname', false) . "
              ORDER BY u.lastname ASC, u.firstname ASC";

        return $DB->get_records_sql($sql, [
            'type' => \local_hrdepartment\constants::EMPLOYEE_TYPE_STAFF,
            'deptname' => access_manager::FINANCE_DEPARTMENT_NAME,
        ]);
    }
}
