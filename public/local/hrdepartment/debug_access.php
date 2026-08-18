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
 * TEMPORARY diagnostic page for access_manager::can_access_hr_department().
 *
 * Shows exactly why a user does/doesn't pass the "who is HR" rule: their
 * hrdep_employee record's type/department and the department's actual
 * name (the rule itself, since v2026081907/0.6.7), plus - informational
 * only, no longer part of the rule - every Moodle role assignment they
 * hold at any context (kept from the original v2026081906 version, which
 * required a "staff" Moodle role; that check was dropped after this very
 * page proved no such role existed on site at all - see access_manager's
 * class docblock). Any logged-in user can see their OWN diagnostic; only
 * a site admin can check someone else's via ?userid=.
 *
 * Delete this file once the access rule is confirmed working across
 * every HR Department section - it is not linked from any menu/tab and
 * exists purely to debug the "who is HR" rule.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\constants;

require_once(__DIR__ . '/../../config.php');

require_login();

$userid = optional_param('userid', (int) $USER->id, PARAM_INT);

if ($userid !== (int) $USER->id && !is_siteadmin()) {
    throw new moodle_exception('nopermissions', 'error', '', 'view another user\'s HR access diagnostic');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/debug_access.php', ['userid' => $userid]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title('HR Department access diagnostic');
$PAGE->set_heading('HR Department access diagnostic');

global $DB;

echo $OUTPUT->header();

$targetuser = $DB->get_record('user', ['id' => $userid]);
echo html_writer::tag('p', 'Checking userid ' . $userid . ' (' . fullname($targetuser) . ', ' . s($targetuser->username) . ')');

// 1. Site admin?
$issiteadmin = is_siteadmin($userid);
echo html_writer::tag('p', html_writer::tag('strong', 'is_siteadmin(): ') . ($issiteadmin ? 'TRUE' : 'false'));

// 2. Moodle role assignments - informational only, NOT part of the rule
// since v2026081907 (see this file's docblock and access_manager's).
// "Role is staff" is decided by hrdep_employee.type below instead.
$sql = "SELECT ra.id, r.shortname, r.name, ctx.contextlevel, ctx.id AS contextid, ctx.instanceid
          FROM {role_assignments} ra
          JOIN {role} r ON r.id = ra.roleid
          JOIN {context} ctx ON ctx.id = ra.contextid
         WHERE ra.userid = :userid
      ORDER BY ctx.contextlevel";
$assignments = $DB->get_records_sql($sql, ['userid' => $userid]);

echo html_writer::tag('h3', 'All role assignments held by this user (any context) - informational only, not used by the rule:');
if (!$assignments) {
    echo html_writer::tag('p', '(none at all)');
} else {
    $table = new html_table();
    $table->head = ['Role shortname', 'Role name', 'Context level', 'Context id', 'Instance id'];
    foreach ($assignments as $a) {
        $levelnames = [
            10 => 'CONTEXT_SYSTEM',
            30 => 'CONTEXT_USER',
            40 => 'CONTEXT_COURSECAT',
            50 => 'CONTEXT_COURSE',
            70 => 'CONTEXT_MODULE',
            80 => 'CONTEXT_BLOCK',
        ];
        $levelname = $levelnames[$a->contextlevel] ?? ('level ' . $a->contextlevel);
        $table->data[] = [s($a->shortname), s($a->name), $levelname, $a->contextid, $a->instanceid];
    }
    echo html_writer::table($table);
}

// 3. hrdep_employee record - this IS the rule (type + department, see
// access_manager::is_staff_in_hr_department()).
$employee = $DB->get_record('hrdep_employee', ['userid' => $userid]);
echo html_writer::tag('h3', 'hrdep_employee record (this plugin\'s Staff/Lecturer record):');
if (!$employee) {
    echo html_writer::tag('p', '(none found for this userid - no Staff or Lecturer record exists at all)');
} else {
    echo html_writer::tag('p', html_writer::tag('strong', 'type: ') . s($employee->type)
        . ($employee->type === constants::EMPLOYEE_TYPE_STAFF ? ' (matches "staff" - OK)' : ' (NOT "staff" - this is why the rule fails, it only looks at Staff-type records)'));
    echo html_writer::tag('p', html_writer::tag('strong', 'departmentid: ') . ($employee->departmentid ?? '(null - no department set)'));

    if ($employee->departmentid) {
        $dept = $DB->get_record('hrdep_department', ['id' => $employee->departmentid]);
        if ($dept) {
            $matches = strcasecmp(trim($dept->name), 'HR') === 0;
            echo html_writer::tag('p', html_writer::tag('strong', 'department name: ') . '"' . s($dept->name) . '" ('
                . strlen($dept->name) . ' characters) - '
                . ($matches ? 'matches "HR" (case-insensitive) - OK' : 'does NOT match "HR" - check for typos, extra spaces, or a different department entirely'));
        } else {
            echo html_writer::tag('p', 'department row not found for departmentid ' . $employee->departmentid . ' (orphaned reference)');
        }
    }
}

// 4. Final verdict, straight from the real function.
echo html_writer::tag('h3', 'Final verdict:');
$result = access_manager::can_access_hr_department($userid);
echo html_writer::tag('p', html_writer::tag('strong', 'access_manager::can_access_hr_department(): ')
    . ($result
        ? 'TRUE - should have full management access: Dashboard, Lecturers, Staff, Students, Attendance management, Leave management, Payroll'
        : 'FALSE - sees only self-service views (My HR snapshot, own attendance/leave/payroll)'));

echo $OUTPUT->footer();
