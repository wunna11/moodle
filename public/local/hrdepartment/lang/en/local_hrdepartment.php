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
 * English language strings for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Plugin name.
$string['pluginname'] = 'HR Department';

// Capabilities.
$string['hrdepartment:managedashboard'] = 'View HR dashboard summary metrics';
$string['hrdepartment:managelecturers'] = 'Manage lecturer profiles and course assignments';
$string['hrdepartment:managestaff'] = 'Manage staff profiles';
$string['hrdepartment:managestudents'] = 'View the students directory and their course enrolments';
$string['hrdepartment:manageattendance'] = 'View student attendance reports for any course (sourced from the Attendance activity)';
$string['hrdepartment:viewownattendance'] = 'View own attendance history';
$string['hrdepartment:managestudentleave'] = 'Manage student leave: create/edit leave requests, approve/reject them, configure leave types, and set leave balances (globally, or per delegated student profile)';
$string['hrdepartment:viewstudentleave'] = 'View student leave requests and balances (globally, or per delegated student profile)';
$string['hrdepartment:manageleavetypes'] = 'Configure leave types and allocations';
$string['hrdepartment:approveleave'] = 'Approve or reject leave applications';
$string['hrdepartment:applyownleave'] = 'Apply for own leave';
$string['hrdepartment:manageleave'] = 'View student leave reports for any course (sourced from the Attendance activity)';
$string['hrdepartment:viewownleave'] = 'View own leave history';
$string['hrdepartment:managepayroll'] = 'Process and edit payroll records';
$string['hrdepartment:viewownpayroll'] = 'View own payslips and payroll history';
$string['hrdepartment:viewallrecords'] = 'View all HR records regardless of reporting line';

// Settings.
$string['setting_currency'] = 'Currency code';
$string['setting_currency_desc'] = 'Currency code (e.g. USD, EUR, MMK) used when displaying payroll amounts.';
$string['setting_payrollapprovalrequired'] = 'Require payroll approval';
$string['setting_payrollapprovalrequired_desc'] = 'If enabled, payroll records must be approved before their status can move to Paid.';
$string['setting_notifyleavedecision'] = 'Notify employees of leave decisions';
$string['setting_notifyleavedecision_desc'] = 'If enabled, employees receive a notification when their leave application is approved or rejected.';
$string['setting_defaultannualleavedays'] = 'Default annual leave days';
$string['setting_defaultannualleavedays_desc'] = 'Default number of annual leave days allocated to a new employee, used to seed their leave balance.';

// Generic domain terms (used across dashboard, lecturer, staff, attendance, leave, payroll modules).
$string['dashboard'] = 'Dashboard';
$string['lecturers'] = 'Lecturers';
$string['staff'] = 'Staff';
$string['students'] = 'Students';
$string['attendance'] = 'Attendance';
$string['leave'] = 'Leave';
$string['payroll'] = 'Payroll';
$string['department'] = 'Department';
$string['employeecode'] = 'Employee code';
$string['designation'] = 'Designation';
$string['employmentstatus'] = 'Employment status';
$string['status_active'] = 'Active';
$string['status_suspended'] = 'Suspended';
$string['status_inactive'] = 'Inactive';
$string['status_terminated'] = 'Terminated';
$string['status_pending'] = 'Pending';
$string['status_approved'] = 'Approved';
$string['status_rejected'] = 'Rejected';
$string['status_cancelled'] = 'Cancelled';
$string['status_processed'] = 'Processed';
$string['status_paid'] = 'Paid';
$string['attendance_present'] = 'Present';
$string['attendance_absent'] = 'Absent';
$string['attendance_leave'] = 'Leave';
$string['attendance_halfday'] = 'Half-day';

// Dashboard strings.
$string['totalemployees'] = 'Total employees';
$string['inactiveemployeescount'] = 'Inactive/terminated';
$string['leavepending'] = 'Pending leave requests';
$string['onleavetoday'] = 'On leave today';
$string['payrollsummary'] = 'Payroll summary';
$string['payrollperiod'] = 'Period';
$string['viewpayroll'] = 'View payroll';
$string['payrollbase'] = 'Base salary';
$string['payrollallowances'] = 'Allowances';
$string['payrolldeductions'] = 'Deductions';
$string['payrollnet'] = 'Net salary';
$string['attendancetoday'] = "Today's attendance";
$string['viewattendance'] = 'View attendance';
$string['totalmarked'] = 'Total marked';
$string['leaveapprovedthismonth'] = 'Approved this month';
$string['pendingleaverequests'] = 'Pending leave requests';
$string['viewleave'] = 'View leave';
$string['employee'] = 'Employee';
$string['leavetype'] = 'Leave type';
$string['startdate'] = 'Start date';
$string['enddate'] = 'End date';
$string['totaldays'] = 'Days';
$string['nopendingleave'] = 'No pending leave requests.';
$string['hrdashboardhero'] = 'Welcome back, {$a}';
$string['hrdashboardsubtitle'] = "Here's what's happening across your organisation today.";
$string['quickaccess'] = 'Quick access';
$string['noaccessdashboard'] = 'You do not currently have access to any HR Department dashboard sections.';

// My HR (self-service) strings.
$string['myhrsubtitle'] = 'Here is your personal HR snapshot.';
$string['noemployeerecord'] = 'No HR employee record is linked to your account yet. Contact HR if you believe this is a mistake.';
$string['myattendancethismonth'] = 'My attendance this month';
$string['myleavebalance'] = 'My leave balance';
$string['pendingleave'] = 'Pending leave';
$string['allocated'] = 'Allocated';
$string['used'] = 'Used';
$string['remaining'] = 'Remaining';
$string['noleavebalance'] = 'No leave balance has been set up for you yet.';
$string['mylatestpayslip'] = 'My latest payslip';
$string['paymentstatus'] = 'Payment status';
$string['nopayrollrecord'] = 'No payroll record found yet.';

// Lecturer management strings.
$string['addlecturer'] = 'Add lecturer';
$string['editlecturer'] = 'Edit lecturer';
$string['lecturercreated'] = 'Lecturer profile created.';
$string['lecturerdeactivated'] = 'Lecturer deactivated.';
$string['lecturerreactivated'] = 'Lecturer reactivated.';
$string['confirmdeactivate'] = 'Deactivate {$a}? Their record is kept, but they will be marked as terminated.';
$string['confirmreactivate'] = 'Reactivate {$a}? Their employment status will be set back to active.';
$string['deactivate'] = 'Deactivate';
$string['reactivate'] = 'Reactivate';
$string['linkedaccount'] = 'Linked Moodle account';
$string['linkedmoodleuser'] = 'Moodle user';
$string['profiledetails'] = 'Profile details';
$string['academicdetails'] = 'Academic details';
$string['qualification'] = 'Qualification';
$string['specialization'] = 'Specialization';
$string['phone'] = 'Phone';
$string['emergencycontact'] = 'Emergency contact';
$string['address'] = 'Address';
$string['joindate'] = 'Join date';
$string['errorcodeinuse'] = 'This employee code is already in use.';
$string['erroruseralreadylinked'] = 'This Moodle user is already linked to another employee record.';
$string['errorlecturernotfound'] = 'Lecturer record not found.';
$string['errorassignmentnotfound'] = 'Course assignment not found.';

// Lecturers directory strings.
$string['lecturersdirectory'] = 'Lecturers directory';
$string['totallecturers'] = 'Total lecturers';
$string['departments'] = 'Departments';
$string['alldepartments'] = 'All departments';
$string['searchlecturerplaceholder'] = 'Search by name, email or employee code';
$string['nolecturersfound'] = 'No lecturers match your filters.';
$string['addlecturerdesc'] = 'Link a Moodle account and fill in their employment and academic details.';
$string['editlecturerdesc'] = "Update this lecturer's employment and academic details.";

// Course assignment strings.
$string['course'] = 'Course';
$string['role'] = 'Role';
$string['status'] = 'Status';
$string['courseassignments'] = 'Course assignments';
$string['courseassignended'] = 'Ended';
$string['nocourseassignments'] = 'No course assignments yet.';
$string['assigncourse'] = 'Assign course';
$string['assigncourseto'] = 'Assign a course to {$a}';
$string['courseassigned'] = 'Course assigned and enrolment synced.';
$string['courseassignedwithwarning'] = 'The HR assignment record was saved, but syncing Moodle enrolment failed: {$a}';
$string['endassignment'] = 'End assignment';
$string['confirmendassignment'] = 'End this course assignment? The lecturer\'s enrolment will be suspended and their role removed, but grade history is kept.';
$string['assignmentended'] = 'Assignment ended and enrolment suspended.';
$string['assignmentendedwithwarning'] = 'The assignment was marked as ended, but syncing Moodle enrolment failed: {$a}';
$string['errorenddatebeforestart'] = 'End date cannot be before the start date.';
$string['errorcoursemissing'] = 'The assigned course no longer exists; the assignment was closed out.';
$string['errorassignmentexpired'] = 'This assignment\'s end date has already passed; it was closed out instead of being restored.';
$string['assignmentsyncwarning'] = '{$a->ok} of {$a->total} course assignment(s) synced; {$a->failed} need attention.';
$string['reactivateassignment'] = 'Reactivate assignment';
$string['confirmreactivateassignment'] = 'Reactivate this course assignment? The lecturer\'s enrolment and role will be restored.';
$string['assignmentreactivated'] = 'Assignment reactivated and enrolment restored.';
$string['assignmentreactivatedwithwarning'] = 'The assignment could not be fully reactivated: {$a}';
$string['errorreactivateinactiveemployee'] = 'This lecturer is not active - reactivate their employment status first.';
$string['errorduplicateassignment'] = 'This lecturer already has an active assignment for this course.';
$string['status_suspendedinmoodle'] = 'Suspended in Moodle';
$string['status_suspendedinmoodle_desc'] = 'This assignment is marked active in HR, but the Moodle enrolment has been suspended (or removed) directly on the course\'s Participants page.';

// Staff management strings.
$string['addstaffmember'] = 'Add staff member';
$string['editstaffmember'] = 'Edit staff member';
$string['staffcreated'] = 'Staff profile created.';
$string['staffdeactivated'] = 'Staff member deactivated.';
$string['staffreactivated'] = 'Staff member reactivated.';
$string['errorstaffnotfound'] = 'Staff record not found.';

// Staff directory strings.
$string['staffdirectory'] = 'Staff directory';
$string['totalstaff'] = 'Total staff';
$string['searchstaffplaceholder'] = 'Search by name, email or employee code';
$string['nostaffmembersfound'] = 'No staff match your filters.';
$string['addstaffmemberdesc'] = 'Link a Moodle account and fill in their employment details.';
$string['editstaffmemberdesc'] = "Update this staff member's employment details.";

// Students directory strings.
//
// Read-only: every Moodle user holding the "student" role in at least
// one course, sourced from core enrolment/role assignment data, with
// their enrolled courses - see local_hrdepartment\student_manager.
$string['studentsdirectory'] = 'Students directory';
$string['totalstudents'] = 'Total students';
$string['totalenrolments'] = 'Course enrolments';
$string['courseswithstudents'] = 'Courses with students';
$string['enrolledcourses'] = 'Enrolled courses';
$string['nocoursesenrolled'] = 'Not enrolled in any course yet.';
$string['nostudentsfound'] = 'No students match your filters.';
$string['resetfilters'] = 'Reset';
$string['viewstudents'] = 'View students';
$string['backtostudents'] = 'Back to students';
$string['gotocourse'] = 'Go to course';
$string['errorstudentnotfound'] = 'Student record not found.';

// Attendance tracking strings.
//
// This module is a read-only report: student attendance is taken in the
// site's mod_attendance activity (Attendance) as normal, and this plugin
// just surfaces that data organised as Course -> Day/session -> record
// list, scoped by the same manageable-courses logic used elsewhere in
// the plugin (HR/admin see every course, a lecturer sees their own).
$string['attendancedate'] = 'Date';
$string['student'] = 'Student';
$string['remarks'] = 'Remarks';
$string['recordedby'] = 'Recorded by';
$string['recordedat'] = 'Recorded at';
$string['allcourses'] = 'All courses';
$string['allstatuses'] = 'All statuses';
$string['filter'] = 'Filter';
$string['noattendancerecords'] = 'No attendance records yet.';
$string['attendancehistoryfor'] = 'Attendance history: {$a}';
$string['myattendance'] = 'My attendance';
$string['attendanceactivity'] = 'Activity';
$string['sessions'] = 'Sessions';
$string['sessioncount'] = 'Sessions recorded';
$string['lastsession'] = 'Last session';
$string['viewsessions'] = 'View sessions';
$string['viewrecords'] = 'View records';
$string['backtocourses'] = 'Back to courses';
$string['backtosessions'] = 'Back to sessions';
$string['nocoursesattendance'] = 'None of your courses have any attendance sessions recorded yet.';
$string['nosessionsforcourse'] = 'No attendance sessions have been recorded for this course yet.';
$string['norecordsforsession'] = 'No students have been marked for this session yet.';
$string['errorsessionnotfound'] = 'Attendance session not found.';
$string['errorcoursenotfound'] = 'Course not found.';
$string['openinattendanceactivity'] = 'Open in Attendance activity';

// Attendance page hero/subheader subtitles (2026-08-17 Attendance/Leave
// redesign - see hrdepartment-entity-scope memory).
$string['attendanceoverviewsubtitle'] = 'A live, read-only view of every course\'s Attendance activity - taken the normal way, in each course.';
$string['myattendancesubtitle'] = 'Your attendance record across every course you\'re enrolled in.';
$string['attendancesessionssubtitle'] = 'Sessions recorded via this course\'s Attendance activity.';
$string['attendancerecordssubtitle'] = 'Who was marked for this session.';
$string['attendancehistorysubtitle'] = 'Status summary and full record history.';

// Student leave management strings.
//
// Restored 2026-08-15 as a self-contained request/approval workflow
// (hrdep_studentleaveapp, hrdep_studentleavetype, hrdep_studentleavebalance
// - see local_hrdepartment\student_leave_manager). HR/staff log a leave
// request on a student's behalf; an HR/Admin/Approver reviews it
// (approve/reject) - evaluated globally (local/hrdepartment:
// managestudentleave held at CONTEXT_SYSTEM) or per delegated student
// profile (the same capability held on that one student's CONTEXT_USER),
// never through course enrolment. See db/access.php.
//
// A few strings below (leavelookup, activeleavetoday, bycourseleave,
// leavestatuslabel*, errorrecordnotfound) are reserved/unused leftovers
// from the read-only mod_attendance-report iteration this supersedes -
// left defined, not wired to any page (see hrdepartment-entity-scope
// memory), same as the reserved capabilities in db/access.php.
$string['leaveoverview'] = 'Leave overview';
$string['leaveoverviewsubtitle'] = 'Review, approve, and track student leave across your institution.';
$string['leaverequestssubtitle'] = 'Search and filter every student leave application.';
$string['leavereportssubtitle'] = 'Filter applications and export them to CSV.';
$string['leavebalancessubtitle'] = 'Look up a student and adjust their allocated leave days per type.';
$string['leavetypessubtitle'] = 'Configure the leave types students can request.';
$string['leaverequests'] = 'Leave requests';
$string['leavelookup'] = 'Student leave lookup';
$string['leaverequestdetail'] = 'Leave request detail';
$string['leavereports'] = 'Reports & export';
$string['searchstudentplaceholder'] = 'Search by name or email';
$string['activeleavetoday'] = "On leave today";
$string['leavethismonth'] = 'On leave this month';
$string['totalleaverecords'] = 'Total leave records';
$string['bycourseleave'] = 'By course';
$string['recentleaverecords'] = 'Recent leave records';
$string['norecentleaverecords'] = 'No leave records yet.';
$string['noleaverecordsfound'] = 'No leave records match your search.';
$string['leavestatuslabel'] = 'Leave status';
$string['leavestatuslabel_desc'] = 'The mod_attendance status description that counts as "on leave" for this report (matched case-insensitively). Change this if your site uses a different label than "Leave" (e.g. "Excused").';
$string['errorrecordnotfound'] = 'Leave record not found.';
$string['datefrom'] = 'From';
$string['dateto'] = 'To';
$string['exportcsv'] = 'Export CSV';

// Student leave requests.
$string['logleaverequest'] = 'Log leave request';
$string['editleaverequest'] = 'Edit leave request';
$string['leaverequestcreated'] = 'Leave request logged.';
$string['leaverequestupdated'] = 'Leave request updated.';
$string['erroreditnotpending'] = 'This leave request has already been reviewed and can no longer be edited.';
$string['errorreviewnotpending'] = 'This leave request has already been reviewed.';
$string['errorapplicationnotfound'] = 'Leave request not found.';
$string['errornotastudent'] = 'The selected user does not hold the student role.';
$string['reason'] = 'Reason';
$string['submittedby'] = 'Submitted by';
$string['reviewedby'] = 'Reviewed by';
$string['reviewnote'] = 'Review note';
$string['approve'] = 'Approve';
$string['reject'] = 'Reject';
$string['leaveapproved'] = 'Leave request approved.';
$string['leaverejected'] = 'Leave request rejected.';
$string['leavecancelled'] = 'Leave request cancelled.';
$string['cancelleaverequest'] = 'Cancel leave';
$string['confirmcancelleave'] = 'Cancel this leave request for {$a}? If it was approved, the days will be returned to their balance.';
$string['allleavetypes'] = 'All leave types';

// Student self-service leave application (leave/apply.php,
// leave/myrequests.php) - added so a student prepares their own leave
// request instead of only HR/staff logging one on their behalf via
// leave/edit.php, and picks which of their own course teachers should
// review it. See student_leave_manager::can_review_application().
$string['applyforleave'] = 'Apply for leave';
$string['applyforleavesubtitle'] = 'Choose a leave type, pick the dates, and select one of your own course teachers to review it.';
$string['myleaverequests'] = 'My leave requests';
$string['myleaverequestssubtitle'] = 'Everything you\'ve submitted, and where it stands.';
$string['nomyleaverequests'] = "You haven't submitted any leave requests yet.";
$string['selectapprover'] = 'Approving teacher';
$string['approver'] = 'Approver';
$string['noapproverassigned'] = 'Not assigned';
$string['errorapprovernotteacher'] = 'Please choose one of your own course teachers as the approving teacher.';
$string['leaverequestsubmitted'] = 'Your leave request has been submitted.';
$string['notastudentnoaccess'] = 'This page is only available to users holding the student role.';
$string['myapprovals'] = 'Leave requests to review';
$string['myapprovalssubtitle'] = 'Applications where a student has chosen you as their approving teacher.';
$string['nomyapprovals'] = "No students have chosen you as their approving teacher yet.";
$string['logleaverequestsubtitle'] = 'Log a leave request on a student\'s behalf.';
$string['editleaverequestsubtitle'] = 'Update the details of this pending leave request.';
$string['setallocationsubtitle'] = 'Set how many days of this leave type the student is allocated for the year.';
$string['addleavetypesubtitle'] = 'Define a new type of student leave.';
$string['editleavetypesubtitle'] = 'Update this leave type\'s settings.';
$string['leaveattendanceheading'] = 'Leave and Attendance Checking';

// Leave types.
$string['leavetypes'] = 'Leave types';
$string['addleavetype'] = 'Add leave type';
$string['editleavetype'] = 'Edit leave type';
$string['leavetypename'] = 'Name';
$string['leavetypedescription'] = 'Description';
$string['maxdaysperyear'] = 'Max days per year';
$string['requiresapproval'] = 'Requires approval';
$string['active'] = 'Active';
$string['deleteleavetype'] = 'Delete';
$string['noleavetypes'] = 'No leave types have been configured yet.';
$string['leavetypesaved'] = 'Leave type saved.';
$string['leavetypedeleted'] = 'Leave type deleted.';
$string['errorleavetypenotfound'] = 'Leave type not found.';
$string['errorleavetypeinuse'] = 'This leave type is still used by an existing leave request or balance; deactivate it instead of deleting it.';
$string['errorleavetypenameinuse'] = 'A leave type with this name already exists.';
$string['confirmdeleteleavetype'] = 'Delete the leave type "{$a}"? This cannot be undone.';

// Leave balances.
$string['leavebalances'] = 'Leave balances';
$string['academicyear'] = 'Academic year';
$string['setallocation'] = 'Set allocation';
$string['allocateddays'] = 'Allocated days';
$string['balanceupdated'] = 'Leave balance updated.';
$string['nobalancesforstudent'] = 'No leave types are configured yet.';
$string['pickastudent'] = 'Search for a student above to view or adjust their leave balance.';
$string['nostudentsfound'] = 'No students found matching your search.';
