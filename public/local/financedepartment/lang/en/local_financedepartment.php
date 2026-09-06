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

// -----------------------------------------------------------------
// Scholarship requests/approval workflow (Step 7.4) -
// pages/scholarshiprequests/*.php.
// -----------------------------------------------------------------
$string['scholarshiprequests'] = 'Scholarship requests';
$string['scholarshiprequestsdesc'] = 'Nominate a student for a scholarship, and review pending requests. Approving auto-deducts the approved amount from the student\'s fee record.';
$string['feerecord'] = 'Fee record';
$string['requestfeerecord_help'] = 'Which of the student\'s fee records this scholarship should be applied to. Only that fee record\'s category is eligible for the scholarship chosen below.';
$string['scholarship'] = 'Scholarship';
$string['requestscholarship_help'] = 'Only active scholarships are listed. The scholarship\'s category must match the fee record chosen above - this is checked when you submit.';
$string['justification'] = 'Justification';
$string['justification_help'] = 'A short explanation of why this student qualifies for the scholarship, shown to the reviewer.';
$string['requestdescription_help'] = 'A short explanation of why this student qualifies for the scholarship, shown to the reviewer.';
$string['attachment'] = 'Supporting document';
$string['attachment_help'] = 'Optional - attach a supporting document (e.g. an income certificate or other evidence) as a PDF, JPG, or PNG. Not required to submit a request.';
$string['submitrequest'] = 'Submit request';
$string['submitrequestdesc'] = 'Search for a student, choose their fee record and a matching scholarship, add a description, and optionally attach a supporting document.';
$string['scholarshiprequestsubmitted'] = 'Scholarship request submitted.';
$string['requestedamount'] = 'Requested amount';
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
$string['errorrequestalreadyreviewed'] = 'This request has already been reviewed.';
$string['errorcannotreviewownrequest'] = 'You cannot approve or reject a scholarship request you submitted yourself. Ask another finance staff member to review it.';
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
$string['historyrequestapproved'] = 'Approved: {$a} deducted from the fee record.';
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
$string['discountrequestsdesc'] = 'Submit a manual or hardship discount request, and review pending requests. Approving auto-applies the approved amount to the student\'s fee record.';
$string['discount'] = 'Discount';
$string['requestdiscount_help'] = 'Only active discounts are listed. Any discount can be requested against any fee record - discounts are not restricted to one course category.';
$string['requestdiscountdescription_help'] = 'A short explanation of why this student qualifies for the discount, shown to the reviewer.';
$string['errordiscountnoteligible'] = 'This discount is not currently active.';
$string['errordiscountpending'] = 'A pending or already-approved request already exists for this fee record and discount.';
$string['submitdiscountrequestdesc'] = 'Search for a student, choose their fee record and a discount, add a description, and optionally attach a supporting document.';
$string['discountrequestsubmitted'] = 'Discount request submitted.';
$string['nodiscountrequests'] = 'No discount requests yet.';
$string['errordiscountrequestnotfound'] = 'Discount request not found.';
$string['discountrequestapproved'] = 'Discount request approved.';
$string['discountrequestrejected'] = 'Discount request rejected.';
$string['discountrequestdetails'] = 'Discount request details';
$string['discountrequesthistory'] = 'History';
