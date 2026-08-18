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
 * English language strings for the Finance Department local plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Finance Department';

// Capability display names/descriptions (Site administration > Users > Permissions > Define roles).
$string['financedepartment:managefeestructures'] = 'Manage fee structures';
$string['financedepartment:managefeestructures_help'] = 'Create, edit and deactivate fee structures (course category + academic year + MMK amount).';
$string['financedepartment:managefeerecords'] = 'Manage fee record assignment';
$string['financedepartment:managefeerecords_help'] = 'Assign fee records to individual students or in bulk to a category, and edit or cancel mistaken assignments.';
$string['financedepartment:managescholarships'] = 'Manage scholarships';
$string['financedepartment:managescholarships_help'] = 'Create and edit scholarship types, and submit scholarship requests/nominations for students.';
$string['financedepartment:approvescholarships'] = 'Approve scholarship requests';
$string['financedepartment:approvescholarships_help'] = 'Approve or reject scholarship requests. Approval auto-deducts the approved amount from the student\'s fee record.';
$string['financedepartment:managediscounts'] = 'Manage discounts';
$string['financedepartment:managediscounts_help'] = 'Create and edit discount types/rules, and submit manual or hardship discount requests.';
$string['financedepartment:approvediscounts'] = 'Approve discount requests';
$string['financedepartment:approvediscounts_help'] = 'Approve or reject manual/hardship discount requests. Approval auto-applies the approved amount to the student\'s fee record.';
$string['financedepartment:manageinstallments'] = 'Manage installment plans';
$string['financedepartment:manageinstallments_help'] = 'Create, edit and reschedule installment plans for a student\'s fee record.';
$string['financedepartment:recordpayments'] = 'Record payments';
$string['financedepartment:recordpayments_help'] = 'Record a manual MMK payment against a fee record, generate receipts, and edit or void a payment with a reason.';
$string['financedepartment:managerefunds'] = 'Manage refunds';
$string['financedepartment:managerefunds_help'] = 'Record a refund/reversal entry against a fee record. Audit-logged.';
$string['financedepartment:viewfinancereports'] = 'View finance reports';
$string['financedepartment:viewfinancereports_help'] = 'View the finance dashboard, summary cards, and the finance staff list view of all students\' fee status.';
$string['financedepartment:viewallrecords'] = 'View all fee records';
$string['financedepartment:viewallrecords_help'] = 'View any student\'s fee statement, regardless of who assigned or manages it.';
$string['financedepartment:viewownfeerecord'] = 'View own fee record';
$string['financedepartment:viewownfeerecord_help'] = 'View the student\'s own fee record, statement and payment history (read-only).';
