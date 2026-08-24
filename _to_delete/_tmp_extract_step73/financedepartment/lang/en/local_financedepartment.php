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

// -----------------------------------------------------------------
// Dashboard (index.php).
// -----------------------------------------------------------------
$string['dashboardsubtitle'] = 'Fees, payments, scholarships and discounts for the institution, in MMK.';
$string['nosectionsyet'] = 'You don\'t have access to any Finance Department section yet.';

// -----------------------------------------------------------------
// Fee structures (Step 7.2).
// -----------------------------------------------------------------
$string['feestructures'] = 'Fee structures';
$string['feestructuresdesc'] = 'Fees are priced per course category and academic year, in MMK - never per individual course.';
$string['addfeestructure'] = 'Add fee structure';
$string['addfeestructuredesc'] = 'Set an MMK amount for one course category and academic year.';
$string['editfeestructure'] = 'Edit fee structure';
$string['editfeestructuredesc'] = 'Changing the amount here is recorded in this fee structure\'s history.';
$string['feestructurecreated'] = 'Fee structure created.';
$string['feestructuredeactivated'] = 'Fee structure deactivated.';
$string['feestructurereactivated'] = 'Fee structure reactivated.';
$string['confirmdeactivatefeestructure'] = 'Deactivate the fee structure for {$a}? It will no longer be offered for new fee record assignments, but existing fee records that use it are unaffected.';
$string['confirmreactivatefeestructure'] = 'Reactivate the fee structure for {$a}?';
$string['errorfeestructurenotfound'] = 'Fee structure not found.';
$string['errorduplicatefeestructure'] = 'An active fee structure already exists for this category and academic year. Edit the existing one instead of creating a duplicate.';
$string['erroramountnegative'] = 'Amount must be a number of 0 or more.';
$string['erroracademicyeartoolong'] = 'Academic year must be 20 characters or fewer.';
$string['category'] = 'Course category';
$string['academicyear'] = 'Academic year';
$string['academicyear_help'] = 'A free-text label for the academic year this fee applies to, e.g. "2026-2027". There is no separate academic year list to choose from - type it exactly as you want it shown.';
$string['amount'] = 'Amount (MMK)';
$string['amount_help'] = 'The fee amount in Myanmar Kyat (MMK). This plugin handles MMK only.';
$string['description'] = 'Description';
$string['status'] = 'Status';
$string['status_active'] = 'Active';
$string['status_inactive'] = 'Inactive';
$string['lastupdated'] = 'Last updated';
$string['deactivate'] = 'Deactivate';
$string['reactivate'] = 'Reactivate';
$string['allcategories'] = 'All categories';
$string['allstatuses'] = 'All statuses';
$string['filter'] = 'Filter';
$string['reset'] = 'Reset';
$string['nofeestructures'] = 'No fee structures match these filters yet.';
$string['feestructuredetails'] = 'Fee structure details';
$string['backtofeestructures'] = 'Back to fee structures';

// History / audit trail (shared across Steps 7.2, 7.8, 7.12).
$string['feestructurehistory'] = 'History';
$string['nohistoryyet'] = 'No changes recorded yet.';
$string['when'] = 'When';
$string['who'] = 'Who';
$string['action'] = 'Action';
$string['change'] = 'Change';
$string['reason'] = 'Reason';
$string['historycreated'] = 'Fee structure created.';
$string['unknownuser'] = 'Unknown user';
$string['auditaction_create'] = 'Created';
$string['auditaction_edit'] = 'Edited';
$string['auditaction_cancel'] = 'Cancelled';
$string['auditaction_void'] = 'Voided';
$string['auditaction_refund'] = 'Refunded';
$string['auditaction_approve'] = 'Approved';
$string['auditaction_reject'] = 'Rejected';

// -----------------------------------------------------------------
// Fee record assignment (Step 7.3) - pages/feerecords/*.php.
// -----------------------------------------------------------------
$string['feerecords'] = 'Fee records';
$string['feerecordsdesc'] = 'Assign a fee structure to a student, individually or in bulk by category, and manage mistaken assignments.';
$string['assignfeerecord'] = 'Assign fee record';
$string['assignfeerecorddesc'] = 'Pick a student and a fee structure to assign.';
$string['editfeerecord'] = 'Edit fee record';
$string['editfeerecorddesc'] = 'Correct the student or fee structure on a mistaken assignment.';
$string['feerecordcreated'] = 'Fee record assigned.';
$string['feerecordcancelled'] = 'Fee record cancelled.';
$string['confirmcancelfeerecord'] = 'Cancel the fee record for {$a}? This cannot be undone from here - the record is kept, marked cancelled, for audit purposes.';
$string['errorfeerecordnotfound'] = 'Fee record not found.';
$string['errorduplicatefeerecord'] = 'This student already has an active fee record for this fee structure.';
$string['errorfeestructurecategorymismatch'] = 'Choose a fee structure that belongs to the selected category.';
$string['student'] = 'Student';
$string['student_help'] = 'Search by name or email. Only active, currently-enrolled students are listed, up to 500 at a time.';
$string['feestructure'] = 'Fee structure';
$string['totalamount'] = 'Total amount';
$string['scholarshipamount'] = 'Scholarship';
$string['discountamount'] = 'Discount';
$string['paidamount'] = 'Paid';
$string['balance'] = 'Balance';
$string['assignedby'] = 'Assigned by';
$string['assigneddate'] = 'Assigned on';
$string['feestatus_unpaid'] = 'Unpaid';
$string['feestatus_partiallypaid'] = 'Partially paid';
$string['feestatus_fullypaid'] = 'Fully paid';
$string['feestatus_overdue'] = 'Overdue';
$string['feestatus_cancelled'] = 'Cancelled';
$string['nofeerecords'] = 'This student has no fee records yet.';
$string['searchstudent'] = 'Search student by name or email';
$string['selectstudentprompt'] = 'Search for a student above, or assign a fee record, to get started.';
$string['viewfeerecordsfor'] = 'Fee records for {$a}';
$string['viewfeerecords'] = 'View fee records';
$string['backtofeerecords'] = 'Back to fee records';
$string['feerecorddetails'] = 'Fee record details';
$string['feerecordhistory'] = 'History';
$string['historyassigned'] = 'Fee record assigned.';
$string['nostudentsfound'] = 'No matching students found.';
$string['recentfeerecords'] = 'Recently assigned';
$string['bulkassign'] = 'Bulk assign';
$string['bulkassignfeerecord'] = 'Bulk assign fee record';
$string['bulkassignfeerecorddesc'] = 'Assign one fee structure to every student currently enrolled in a course category. Students who already have this fee structure are skipped automatically.';
$string['bulkassignfeestructure_help'] = 'Must belong to the category chosen above.';
$string['bulkassignresult'] = '{$a->assigned} of {$a->total} students assigned; {$a->skipped} already had this fee structure and were skipped.';
