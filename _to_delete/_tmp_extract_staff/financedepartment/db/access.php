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
 * Capability definitions for the Finance Department local plugin.
 *
 * All capabilities are defined at CONTEXT_SYSTEM because finance data is
 * organisation-wide (fees are priced per course category + academic
 * year, not per individual course) and is not tied to a Moodle course
 * context.
 *
 * Every capability below is still checked the normal Moodle way via
 * has_capability()/require_capability() - but always through
 * local_financedepartment\access_manager::can_manage()/require_manage(),
 * never called directly. That wrapper additionally grants full access to
 * anyone satisfying access_manager::can_access_finance_department() (an
 * active financedep_employee record, or a site administrator), which
 * can't be expressed as a capability since it depends on this plugin's
 * own data, not a role assignment. A role that already grants one of
 * these capabilities keeps working unchanged; the employee-based rule is
 * an additional way in, not a replacement for role assignment. See
 * classes/access_manager.php for the full rule and rationale.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$capabilities = [

    // Finance staff (financedep_employee): not one of the doc's numbered
    // Task 7 steps - added as a small prerequisite so finance staff
    // records (this plugin's own "who is finance staff" rule, see
    // classes/access_manager.php) can be created through the UI at all,
    // rather than only via direct DB access or a site administrator.
    'local/financedepartment:managestaff' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Fee structures (Step 7.2): create/edit/deactivate, per category + academic year.
    'local/financedepartment:managefeestructures' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Fee record assignment (Step 7.3): assign/edit/cancel a student's fee record.
    'local/financedepartment:managefeerecords' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Scholarship types/requests (Step 7.4): create scholarship types, submit requests.
    'local/financedepartment:managescholarships' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Approve/reject a scholarship request. Kept separate from
    // managescholarships so a role can submit/nominate without also
    // being able to approve its own nomination.
    'local/financedepartment:approvescholarships' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Discount types/requests (Step 7.5): create discount types/rules, submit manual requests.
    'local/financedepartment:managediscounts' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Approve/reject a manual/hardship discount request. Kept separate
    // from managediscounts for the same reason as approvescholarships.
    'local/financedepartment:approvediscounts' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Installment plans (Step 7.6): create/edit/reschedule a student's installment plan.
    'local/financedepartment:manageinstallments' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Payment recording (Step 7.7): record a manual payment, generate receipts, edit/void a payment.
    'local/financedepartment:recordpayments' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Refunds/reversals: separate, higher-trust capability per Step 7.7
    // ("capability-gated and audit-logged").
    'local/financedepartment:managerefunds' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Finance dashboard/reports (Step 7.11) and the finance staff list view (Step 7.9).
    'local/financedepartment:viewfinancereports' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Finance administrator override, view any student's fee statement (Step 7.9).
    'local/financedepartment:viewallrecords' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Student self-service, read-only view of the student's own fee
    // record/statement and payment history (Step 7.9).
    'local/financedepartment:viewownfeerecord' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],
];
