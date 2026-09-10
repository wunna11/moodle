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
// Shown instead of 'pluginname' to a plain self-service student (nav
// label + page title/heading) - see access_manager::get_display_name().
// Finance staff/admins always see 'pluginname' above, unchanged.
$string['pluginnamestudent'] = 'Scholarship';

// Capability display names/descriptions (Site administration > Users > Permissions > Define roles).
$string['financedepartment:managefeestructures'] = 'Manage fee structures';
$string['financedepartment:managefeestructures_help'] = 'Create, edit and deactivate fee structures (course category + academic year + MMK amount).';
$string['financedepartment:managefeerecords'] = 'Manage fee record assignment';
$string['financedepartment:managefeerecords_help'] = 'Assign fee records to individual students or in bulk to a category, and edit or cancel mistaken assignments.';
$string['financedepartment:managescholarships'] = 'Manage scholarships';
$string['financedepartment:managescholarships_help'] = 'Create and edit scholarship types. Submitting a scholarship request is student self-service (see the "Submit scholarship requests" capability) - finance staff no longer submit requests on a student\'s behalf.';
$string['financedepartment:approvescholarships'] = 'Approve scholarship requests';
$string['financedepartment:approvescholarships_help'] = 'Approve or reject scholarship requests. Approval auto-deducts the approved amount from the student\'s fee record.';
$string['financedepartment:submitscholarshiprequest'] = 'Submit scholarship requests';
$string['financedepartment:submitscholarshiprequest_help'] = 'Submit a scholarship request for the student\'s own fee record (student self-service, not a finance management capability).';
$string['financedepartment:managediscounts'] = 'Manage discounts';
$string['financedepartment:managediscounts_help'] = 'Create and edit discount types/rules. Submitting a discount request is student self-service (see the "Submit discount requests" capability) - finance staff no longer submit requests on a student\'s behalf.';
$string['financedepartment:submitdiscountrequest'] = 'Submit discount requests';
$string['financedepartment:submitdiscountrequest_help'] = 'Submit a manual/hardship discount request for the student\'s own fee record (student self-service, not a finance management capability).';
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
$string['auditaction_delete'] = 'Deleted';

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
$string['errorfeerecordwrongstudent'] = 'This fee record does not belong to the selected student.';
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
$string['myfeerecord'] = 'My Fee Record';
$string['myfeerecorddesc'] = 'A read-only view of your own fee records - balance, payment history, and installment schedule.';
$string['myfeerecordsempty'] = 'You don\'t have any fee records yet. Please contact the Finance office if you believe this is a mistake.';
$string['backtomyfeerecords'] = 'Back to my fee records';
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

// -----------------------------------------------------------------
// Scholarship management (Step 7.4) - pages/scholarships/*.php.
//
// NOTE: this section deliberately has no "scholarship type"
// strings (merit/needbased/sibling/staffward/other - the original
// spec's wording). Per the user's 2026-08-23 request, a scholarship
// is restricted to one course category/program instead - see
// classes/scholarship_manager.php's docblock.
// -----------------------------------------------------------------
$string['scholarships'] = 'Scholarships';
$string['scholarshipsdesc'] = 'Each scholarship belongs to one course category/program and applies only to fee records in that category.';
$string['addscholarship'] = 'Add scholarship';
$string['addscholarshipdesc'] = 'Restrict this scholarship to one course category, as a fixed MMK amount or a percentage of the fee.';
$string['editscholarship'] = 'Edit scholarship';
$string['editscholarshipdesc'] = 'Changing these details is recorded in this scholarship\'s history.';
$string['scholarshipcreated'] = 'Scholarship created.';
$string['scholarshipdeactivated'] = 'Scholarship deactivated.';
$string['scholarshipreactivated'] = 'Scholarship reactivated.';
$string['confirmdeactivatescholarship'] = 'Deactivate the scholarship "{$a}"? It will no longer be offered for new requests, but existing approved requests are unaffected.';
$string['confirmreactivatescholarship'] = 'Reactivate the scholarship "{$a}"?';
$string['errorscholarshipnotfound'] = 'Scholarship not found.';
$string['scholarshipname'] = 'Scholarship name';
$string['scholarshipcategory_help'] = 'The one course category/program this scholarship is restricted to. A student can only request it against a fee record in this category.';
$string['amounttype'] = 'Amount type';
$string['amounttype_fixed'] = 'Fixed MMK amount';
$string['amounttype_percentage'] = 'Percentage of fee';
$string['amountvalue'] = 'Amount';
$string['amountvalue_help'] = 'A fixed MMK amount, or a percentage (0-100) of the fee record\'s total amount, depending on the amount type chosen above.';
$string['errorpercentagerange'] = 'A percentage amount must be between 0 and 100.';
$string['noscholarships'] = 'No scholarships match these filters yet.';
$string['backtoscholarships'] = 'Back to scholarships';
$string['scholarshipdetails'] = 'Scholarship details';
$string['scholarshiphistory'] = 'History';

// pages/scholarships/browse.php - read-only catalog for any logged-in
// user (added 2026-09-10, see that page's own docblock).
$string['scholarshipcatalog'] = 'Available scholarships';
$string['scholarshipcatalogdesc'] = 'Every active scholarship across all course categories/programs, whether or not you have a fee record yet.';
$string['nocatalogscholarships'] = 'No active scholarships are available right now.';
$string['requestthisscholarship'] = 'Request this';
$string['browsescholarships'] = 'Browse scholarships';

// -----------------------------------------------------------------
// Scholarship requests/approval workflow (Step 7.4) -
// pages/scholarshiprequests/*.php.
// -----------------------------------------------------------------
$string['scholarshiprequests'] = 'Scholarship requests';
// Reworded 2026-09-10 (v2026091004/0.8.0): approving no longer
// auto-deducts anything - see scholarshiprequest_manager::approve()'s
// docblock. requestfeerecord_help/errorfeerecordnotfound/
// errorfeerecordwrongstudent/errorfeerecordcancelled below are left in
// place (orphaned for scholarship requests) since discountrequest_form
// still uses them.
$string['scholarshiprequestsdesc'] = 'Review scholarship requests students have submitted for themselves. Approving records the decision only - it does not change any fee record; adjust the student\'s fee record manually (Fee Records) if the scholarship should reduce their balance.';
$string['feerecord'] = 'Fee record';
$string['requestfeerecord_help'] = 'Which of the student\'s fee records this discount should be applied to.';
$string['scholarship'] = 'Scholarship';
$string['requestscholarship_help'] = 'Only active scholarships are listed.';
$string['justification'] = 'Justification';
$string['justification_help'] = 'A short explanation of why this student qualifies for the scholarship, shown to the reviewer.';
$string['requestdescription_help'] = 'A short explanation of why this student qualifies for the scholarship, shown to the reviewer.';
$string['attachment'] = 'Supporting document(s)';
$string['attachment_help'] = 'Optional - attach up to 5 supporting documents (e.g. an income certificate or other evidence) as PDF, JPG, or PNG files. Not required to submit a request.';
$string['submitrequest'] = 'Submit request';
// Reworded 2026-09-10 (v2026091004/0.8.0): no fee record is chosen here
// any more - see scholarshiprequest_form's class docblock.
$string['submitrequestdesc'] = 'Choose a scholarship, add a description, and optionally attach supporting documents.';
$string['scholarshiprequestsubmitted'] = 'Scholarship request submitted.';
$string['requestedamount'] = 'Requested amount';
// Added 2026-09-10 (v2026091004/0.8.0): shown instead of a computed MMK
// figure when requestedamount is null - a percentage-type scholarship
// has no fee record any more to compute a base amount against, see
// scholarshiprequest_manager::compute_suggested_amount()'s docblock.
$string['requestedamountpercentagebased'] = '{$a}% (final amount set on approval)';
$string['requestedby'] = 'Requested by';
$string['requestsfor'] = 'Scholarship requests for {$a}';
$string['viewrequests'] = 'View requests';
$string['noscholarshiprequests'] = 'No scholarship requests yet.';
$string['pendingqueue'] = 'Pending requests';
$string['review'] = 'Review';
$string['backtorequests'] = 'Back to requests';
$string['backtorequest'] = 'Back to request';
$string['errorscholarshiprequestnotfound'] = 'Scholarship request not found.';
$string['errorfeerecordcancelled'] = 'This fee record has been cancelled and can no longer receive a scholarship.';
$string['errorscholarshipnoteligible'] = 'This scholarship\'s category does not match the selected fee record\'s category.';
$string['errorscholarshippending'] = 'A pending or already-approved request already exists for this fee record and scholarship.';
$string['errorscholarshipalreadypaid'] = 'You have already made a payment towards this program, so a scholarship can no longer be requested for it.';
$string['errorrequestalreadyreviewed'] = 'This request has already been reviewed.';
$string['errorcannotreviewownrequest'] = 'You cannot approve or reject a scholarship request you submitted yourself. Ask another finance staff member to review it.';
$string['errorscholarshipnotactiveforapproval'] = 'This scholarship has been deactivated since this request was submitted, so it can no longer be approved. You can still reject it.';
$string['scholarshiprequestdetails'] = 'Scholarship request details';
$string['scholarshiprequesthistory'] = 'History';
$string['approvedamount'] = 'Approved amount';
$string['approvedamount_help'] = 'The MMK amount to deduct from the student\'s fee record balance. Pre-filled with the requested amount - change it to approve a different amount.';
$string['reviewnote'] = 'Review note';
$string['reviewedby'] = 'Reviewed by';
$string['reviewedon'] = 'Reviewed on';
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['approverequest'] = 'Approve scholarship request';
$string['approverequestdesc'] = 'Approve the scholarship request for {$a}. This immediately deducts the approved amount from the student\'s fee record.';
$string['rejectrequest'] = 'Reject scholarship request';
$string['rejectrequestdesc'] = 'Reject the scholarship request for {$a}. A review note explaining why is required.';
$string['scholarshiprequestapproved'] = 'Scholarship request approved.';
$string['scholarshiprequestrejected'] = 'Scholarship request rejected.';
$string['requeststatus_pending'] = 'Pending';
$string['requeststatus_approved'] = 'Approved';
$string['requeststatus_rejected'] = 'Rejected';
$string['requeststatus_deleted'] = 'Deleted';
$string['historyrequestsubmitted'] = 'Request submitted for {$a}.';
// Added 2026-09-10 (v2026091004/0.8.0): shown instead of
// historyrequestsubmitted when requestedamount is null (a
// percentage-type scholarship with no fee record to compute a base
// amount against).
$string['historyrequestsubmittedpercentage'] = 'Request submitted ({$a}% of a to-be-determined base amount).';
// Reworded 2026-09-10 (v2026091004/0.8.0): approving no longer deducts
// anything for a request submitted through the current form - see
// scholarshiprequest_manager::approve()'s docblock. This string is
// shared by both legacy (deducted) and new-style (history-only)
// approved requests, so it deliberately no longer claims a deduction
// happened.
$string['historyrequestapproved'] = 'Approved: {$a}.';
$string['historyrequestrejected'] = 'Request rejected.';
$string['historyrequestdeleted'] = 'Request deleted.';
$string['historyrequestdeletedwithreversal'] = 'Request deleted; {$a} restored to the fee record balance.';
$string['newrequest'] = 'New request';
$string['findstudentforrequestdesc'] = 'Search for a student to submit a scholarship request for.';
$string['showingrequestsfor'] = 'Showing requests for {$a}.';
$string['clearfilter'] = 'Clear filter';
$string['deleterequest'] = 'Delete scholarship request';
$string['scholarshiprequestdeleted'] = 'Scholarship request deleted.';
$string['confirmdeleterequest'] = 'Are you sure you want to delete the scholarship request for {$a}? This cannot be undone from here, but the request stays in the audit history.';
$string['confirmdeleterequestapproved'] = 'The request for {$a->label} was already APPROVED, and {$a->amount} has already been deducted from the student\'s fee record. Deleting it will restore that amount to the fee record\'s balance. This cannot be undone from here, but the request stays in the audit history. Are you sure you want to continue?';
$string['errorrequestalreadydeleted'] = 'This request has already been deleted.';

// -----------------------------------------------------------------
// Discount management (Step 7.5, built 2026-09-06) - pages/discounts/*.php.
//
// Unlike scholarships, a discount is NOT restricted to one course
// category - see classes/discount_manager.php's docblock. Every
// discount created here is manual-only for now (isautomatic/rulejson
// are not exposed) - see discountautomaticnotice below and
// classes/constants.php's DISCOUNT_TYPE_* comment for the full
// 2026-09-06 scope decision.
// -----------------------------------------------------------------
$string['discounts'] = 'Discounts';
$string['discountsdesc'] = 'Configure discount types and review manual/hardship discount requests.';
$string['adddiscount'] = 'Add discount';
$string['adddiscountdesc'] = 'Create a discount as a fixed MMK amount or a percentage of the fee.';
$string['editdiscount'] = 'Edit discount';
$string['editdiscountdesc'] = 'Changing these details is recorded in this discount\'s history.';
$string['discountcreated'] = 'Discount created.';
$string['discountdeactivated'] = 'Discount deactivated.';
$string['discountreactivated'] = 'Discount reactivated.';
$string['confirmdeactivatediscount'] = 'Deactivate the discount "{$a}"? It will no longer be offered for new requests, but existing approved requests are unaffected.';
$string['confirmreactivatediscount'] = 'Reactivate the discount "{$a}"?';
$string['errordiscountnotfound'] = 'Discount not found.';
$string['discountname'] = 'Discount name';
$string['discounttype'] = 'Discount type';
$string['discounttype_help'] = 'Early-payment and promotional discounts can eventually auto-apply based on a rule (e.g. days before a due date); hardship discounts are always manual. For now, every discount - regardless of type - requires a manual request and approval, exactly like a scholarship. See the notice on this form for why.';
$string['discounttype_earlypayment'] = 'Early-payment';
$string['discounttype_promotional'] = 'Promotional';
$string['discounttype_hardship'] = 'Hardship';
$string['discountautomaticnotice'] = 'Automatic rule-based discounts are not available yet (there is no due-date field on fee records/structures for a rule to evaluate against). Every discount created here - including Early-payment and Promotional types - requires a manual request and approval, the same as a scholarship.';
$string['alltypes'] = 'All types';
$string['nodiscounts'] = 'No discounts match these filters yet.';
$string['backtodiscounts'] = 'Back to discounts';
$string['discountdetails'] = 'Discount details';
$string['discounthistory'] = 'History';

// -----------------------------------------------------------------
// Discount requests/approval workflow (Step 7.5, built 2026-09-06) -
// pages/discountrequests/*.php. Mirrors the scholarship request
// workflow closely - see pages/scholarshiprequests/*.php and
// discountrequest_manager's class docblock for what's different
// (no category restriction, no delete feature yet, and the
// requestedby !== reviewedby self-approval guard was built in from day
// one here instead of added later as a post-deploy fix).
// -----------------------------------------------------------------
$string['discountrequests'] = 'Discount requests';
$string['discountrequestsdesc'] = 'Review manual or hardship discount requests students have submitted for themselves. Approving auto-applies the approved amount to the student\'s fee record.';
$string['discount'] = 'Discount';
$string['requestdiscount_help'] = 'Only active discounts are listed. Any discount can be requested against any fee record - discounts are not restricted to one course category.';
$string['requestdiscountdescription_help'] = 'A short explanation of why this student qualifies for the discount, shown to the reviewer.';
$string['errordiscountnoteligible'] = 'This discount is not currently active.';
$string['errordiscountpending'] = 'A pending or already-approved request already exists for this fee record and discount.';
$string['errordiscountnotactiveforapproval'] = 'This discount has been deactivated since this request was submitted, so it can no longer be approved. You can still reject it.';
$string['submitdiscountrequestdesc'] = 'Choose one of your own fee records and a discount, add a description, and optionally attach a supporting document.';
$string['discountrequestsubmitted'] = 'Discount request submitted.';
$string['nodiscountrequests'] = 'No discount requests yet.';
$string['errordiscountrequestnotfound'] = 'Discount request not found.';
$string['discountrequestapproved'] = 'Discount request approved.';
$string['discountrequestrejected'] = 'Discount request rejected.';
$string['discountrequestdetails'] = 'Discount request details';
$string['discountrequesthistory'] = 'History';

// -----------------------------------------------------------------
// Installment plans (Step 7.6, built 2026-09-08). An installment plan
// is a payment SCHEDULE for one fee record, not a financial transaction
// - creating/editing/cancelling one never moves money or touches the
// fee record's balance (see classes/installmentplan_manager.php's class
// docblock). Each installment's amount and due date is entered
// manually by finance staff (no auto-split), and "overdue" is a live,
// computed display state rather than a persisted DB status - see that
// same docblock for both 2026-09-08 scope decisions.
// -----------------------------------------------------------------
$string['installmentplans'] = 'Installments';
$string['backtoinstallments'] = 'Back to installments';
$string['backtoinstallmentplan'] = 'Back to installment plan';
$string['installmentplansdesc'] = 'Lay out a payment schedule for a student\'s fee record, and reschedule or cancel an existing plan. Deliberately never moves money - only Step 7.7 (payments) will do that.';
$string['installmentplan'] = 'Installment plan';
$string['installmentplanfeerecord_help'] = 'Which fee record this installment schedule belongs to. A fee record can have at most one plan at a time - if it already has an active one, cancel it first.';
$string['installmentamount'] = 'Installment amount';
$string['duedate'] = 'Due date';
$string['addinstallment'] = 'Add another installment';
$string['createinstallmentplan'] = 'Create installment plan';
$string['createinstallmentplandesc'] = 'Choose a fee record, then enter each installment\'s amount and due date. The amounts must add up to exactly the fee record\'s current balance.';
$string['numinstallments'] = 'Number of installments';
$string['numinstallmentsdesc'] = 'Choose how many installments this plan should have. You can still add more (or leave extra rows blank) on the next page.';
$string['rescheduleinstallmentplan'] = 'Reschedule installment plan';
$string['rescheduleinstallmentplandesc'] = 'Edit the amount and due date of each installment. The amounts must still add up to exactly the fee record\'s current balance.';
$string['installmentplancreated'] = 'Installment plan created.';
$string['installmentplanupdated'] = 'Installment plan rescheduled.';
$string['installmentplancancelled'] = 'Installment plan cancelled.';
$string['confirmcancelinstallmentplan'] = 'Cancel this installment plan? The schedule is kept for history, but no longer shown as active. This does not change the fee record\'s balance.';
$string['errorinstallmentplannotfound'] = 'Installment plan not found.';
$string['errorinstallmentplanexists'] = 'This fee record already has an active installment plan. Cancel it first if you want to create a new one.';
$string['errorinstallmentplanhaspayments'] = 'This installment plan already has payments recorded against it, so its schedule can no longer be changed or replaced. Void or refund the existing payments first if the schedule really needs to change.';
$string['errorinstallmentplannotactive'] = 'This installment plan has been cancelled and can no longer be rescheduled.';
$string['errorinstallmentnonefilled'] = 'Enter an amount for at least one installment.';
$string['errorinstallmentsumamountmismatch'] = 'The installment amounts add up to {$a->sum} MMK, but the fee record\'s current balance is {$a->balance} MMK. These must match exactly.';
$string['errorinstallmentnotforrecord'] = 'That installment does not belong to this fee record.';
$string['installmentplanstatus_active'] = 'Active';
$string['installmentplanstatus_cancelled'] = 'Cancelled';
$string['installmentstatus_pending'] = 'Pending';
$string['installmentstatus_partiallypaid'] = 'Partially paid';
$string['installmentstatus_paid'] = 'Paid';
$string['installmentstatus_overdue'] = 'Overdue';
$string['installmentschedule'] = 'Installment schedule';
$string['installmentnumber'] = 'Installment #';
$string['noinstallmentplan'] = 'No plan yet';
$string['viewinstallmentplan'] = 'View plan';
$string['createplan'] = 'Create plan';
$string['viewinstallmentplans'] = 'View installment plans';
$string['recentinstallmentplans'] = 'Recently created installment plans';
$string['noinstallmentplansyet'] = 'No installment plans yet.';
$string['installmentplanhistory'] = 'History';
$string['historyinstallmentplancreated'] = 'Installment plan created with {$a} installment(s).';
$string['historyinstallmentplanupdated'] = 'Installment plan rescheduled to {$a} installment(s).';
$string['historyinstallmentplancancelled'] = 'Installment plan cancelled.';

// -----------------------------------------------------------------
// Payments (Step 7.7, built 2026-09-09) - recording payments/refunds
// against a fee record via classes/feepayment_manager.php,
// pages/payments/*.php.
// -----------------------------------------------------------------

$string['payments'] = 'Payments';
$string['paymentsdesc'] = 'Search for a student to record a payment or refund against one of their fee records, or browse the most recently recorded activity below.';
$string['recordpayment'] = 'Record payment';
$string['recordrefund'] = 'Record refund';
$string['payment'] = 'Payment';
$string['paymentnotlinked'] = 'Not linked to a specific installment';
$string['linkedinstallment'] = 'Linked installment';
$string['linkedinstallment_help'] = 'Optional - tie this payment or refund to one specific installment in the student\'s payment schedule. Only installments that are not already fully paid are listed. Leave as "not linked" to record it against the fee record as a whole.';
$string['installmentoptionlabel'] = 'Installment #{$a->number} - due {$a->duedate} - {$a->amount} (paid so far: {$a->paid})';
$string['installmentnumbershort'] = 'Installment #{$a}';
$string['paymentdate'] = 'Payment date';
$string['paymentmethod'] = 'Payment method';
$string['paymentmethod_cash'] = 'Cash';
$string['paymentmethod_banktransfer'] = 'Bank transfer';
$string['paymentmethod_mobilebanking'] = 'Mobile banking';
$string['paymentmethod_other'] = 'Other';
$string['notes'] = 'Notes';
$string['paymentattachment'] = 'Supporting document(s)';
$string['paymentattachment_help'] = 'Optional - attach up to 5 supporting documents (e.g. a payment receipt, bank transfer confirmation, or deposit slip) as PDF, JPG, or PNG files. Not required to record a payment or refund.';
$string['receiptnumber'] = 'Receipt number';
$string['recordedby'] = 'Recorded by';
$string['paymenttype'] = 'Type';
$string['paymenttype_payment'] = 'Payment';
$string['paymenttype_refund'] = 'Refund';
$string['paymentstatus_active'] = 'Active';
$string['paymentstatus_void'] = 'Voided';
$string['paymentrecorded'] = 'Payment recorded.';
$string['refundrecorded'] = 'Refund recorded.';
$string['recentpayments'] = 'Recently recorded payments';
$string['nopaymentsyet'] = 'No payments recorded yet.';
$string['viewfeerecord'] = 'View fee record';
$string['backtofeerecord'] = 'Back to fee record';
$string['backtopayment'] = 'Back to payment';
$string['paymenthistory'] = 'History';
$string['historypaymentrecorded'] = 'Payment of {$a} recorded.';
$string['historyrefundrecorded'] = 'Refund of {$a} recorded.';
$string['historypaymentvoided'] = 'Payment voided.';
$string['voidpayment'] = 'Void payment';
$string['voidpaymentdesc'] = 'Voiding a payment reverses its effect on the fee record (and its linked installment, if any) and keeps the original entry for audit history - it does not delete anything.';
$string['voidreason'] = 'Reason for voiding';
$string['voidreason_help'] = 'Required. Explain why this payment or refund is being voided - this is stored and shown in the payment\'s history.';
$string['voidedby'] = 'Voided by';
$string['voidedat'] = 'Voided at';
$string['paymentvoided'] = 'Payment voided.';
$string['errorpaymentnotfound'] = 'Payment not found.';
$string['errorpaymentnotactive'] = 'This payment has already been voided.';
$string['errorrefundexceedspaid'] = 'This refund amount is more than has actually been paid on this fee record so far.';
$string['errorrefundexceedsinstallmentpaid'] = 'This refund amount is more than has actually been paid on this specific installment so far.';

// Fee record status history + overdue detection (Step 7.8).
$string['statusautorecalcreason'] = 'Status automatically recalculated.';

// Student self-service scholarship/discount request submission
// (2026-09-09 fix - see [[financedepartment-schema]] project memory).
// myscholarshiprequestsdesc reworded 2026-09-10 (v2026091004/0.8.0): no
// longer tied to "your own fee records" - see scholarshiprequest_form's
// class docblock. nofeerecordsownscholarship is now orphaned/unused
// (scholarship submission no longer requires a fee record at all) but
// kept in place rather than deleted, matching this codebase's usual
// practice for strings a fix makes obsolete.
$string['myscholarshiprequestsdesc'] = 'Scholarship requests you have submitted.';
$string['mydiscountrequestsdesc'] = 'Discount requests you have submitted for your own fee records.';
$string['nofeerecordsownscholarship'] = 'You don\'t have any fee record yet, so there is nothing to request a scholarship against. Please contact the Finance office if you believe this is a mistake.';
$string['nofeerecordsowndiscount'] = 'You don\'t have any fee record yet, so there is nothing to request a discount against. Please contact the Finance office if you believe this is a mistake.';
