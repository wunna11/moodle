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
 * Capability definitions for the HR Department local plugin.
 *
 * All capabilities are defined at CONTEXT_SYSTEM because HR data is
 * organisation-wide and is not tied to a Moodle course context, EXCEPT
 * the two Student Leave capabilities below, which are defined at
 * CONTEXT_USER so that a role can also be assigned directly on one
 * student's own profile (delegated per-student approval), not just at
 * system level.
 *
 * Every manage* capability below (managedashboard, managelecturers,
 * managestaff, managestudents, manageattendance, managestudentleave,
 * managepayroll) is still checked the normal Moodle way via
 * has_capability()/require_capability() - but always through
 * local_hrdepartment\access_manager::can_manage()/require_manage(), not
 * called directly. That wrapper additionally grants full access to
 * anyone satisfying access_manager::can_access_hr_department() (a
 * Moodle "staff" role + an HR-department Staff record, or a site
 * administrator - added 2026-08-17), which can't be expressed as a
 * capability since it depends on a custom field's value. A role that
 * already grants one of these capabilities keeps working unchanged;
 * the new rule is an additional way in, not a replacement for role
 * assignment.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$capabilities = [

    // Dashboard. See the file-level docblock above - checked via
    // access_manager::can_access_hr_department(), not has_capability()
    // directly, since that rule can't be expressed as a plain
    // capability. Left defined for its Define roles display name/
    // description and so a role that already grants it isn't silently
    // broken.
    'local/hrdepartment:managedashboard' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Lecturer and staff profile management.
    'local/hrdepartment:managelecturers' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:managestaff' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Students directory (students/*.php): read-only view of every
    // Moodle user holding the student role in at least one course, with
    // their enrolled courses. Sourced entirely from core enrolment/role
    // assignment data (mdl_user, mdl_role_assignments, mdl_context,
    // mdl_course) - see local_hrdepartment\student_manager - this plugin
    // owns no table of its own here, same "there's already a real system
    // for this" pattern as Attendance/Leave (see hrdepartment-entity-scope
    // memory).
    'local/hrdepartment:managestudents' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Attendance.
    'local/hrdepartment:manageattendance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:viewownattendance' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],

    // -----------------------------------------------------------------
    // Student Leave (leave/*.php): a self-contained request/approval
    // workflow (hrdep_studentleaveapp, hrdep_studentleavetype,
    // hrdep_studentleavebalance) - HR/staff log a leave request on a
    // student's behalf and an HR/Admin/Approver reviews (approve/reject)
    // it. Restored 2026-08-15 (see hrdepartment-entity-scope memory for
    // the prior read-only-report iteration this supersedes).
    //
    // 'contextlevel' => CONTEXT_USER (not CONTEXT_COURSE, and not just
    // CONTEXT_SYSTEM) so that approval rights are evaluated globally OR
    // per student profile, never through course enrollment:
    //   - A role assigned at CONTEXT_SYSTEM (HR, Admin - "manager"
    //     archetype below) cascades down through every context below it,
    //     including every individual student's user context, so
    //     system-assigned roles get the capability for every student
    //     automatically, with no course involved.
    //   - A role can ALSO be assigned directly on one student's user
    //     context (that student's profile page -> "This user's role
    //     assignments"), which is how a single "Approver" is delegated
    //     approval rights for just that student without being made an
    //     approver for the whole institution. Create a custom "Approver"
    //     role in Site administration > Users > Permissions > Define
    //     roles, allow it these capabilities, and assign it either at
    //     System context (global) or on one student's user context
    //     (delegated).
    // -----------------------------------------------------------------

    'local/hrdepartment:managestudentleave' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:viewstudentleave' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Reserved/unused. These predate the project's Entity Scope
    // Isolation rule (see hrdepartment-entity-scope memory) and were
    // written for an employee-scoped leave feature that this plugin
    // does not implement (HR/Admin/Leave Management here is
    // student-scoped only - see local/hrdepartment:managestudentleave /
    // :viewstudentleave above). Left defined so a role that already
    // grants them isn't silently broken; not wired to any page.
    'local/hrdepartment:manageleavetypes' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:approveleave' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:applyownleave' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],

    // Reserved/unused as of 2026-08-15: the mod_attendance-sourced,
    // read-only Student Leave report that local/hrdepartment:
    // managestudentleave / :viewstudentleave (above) now supersedes.
    // Left defined for the same reason as the employee-scoped block
    // above - not wired to any page.
    'local/hrdepartment:manageleave' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:viewownleave' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],

    // Payroll.
    'local/hrdepartment:managepayroll' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    'local/hrdepartment:viewownpayroll' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],

    // HR administrator override, sees all records.
    'local/hrdepartment:viewallrecords' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
];
