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
$string['hrdepartment:manageattendance'] = 'View student attendance reports for any course (sourced from the Attendance activity)';
$string['hrdepartment:viewownattendance'] = 'View own attendance history';
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
$string['attendance'] = 'Attendance';
$string['leave'] = 'Leave';
$string['payroll'] = 'Payroll';
$string['department'] = 'Department';
$string['employeecode'] = 'Employee code';
$string['designation'] = 'Designation';
$string['reportsto'] = 'Reports to';
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

// My HR (self-service) strings.
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

// Student leave management strings.
//
// Corrected to match the Attendance module's architecture (see
// hrdepartment-entity-scope memory): this is a read-only report on top
// of the site's existing mod_attendance activity data. A student is "on
// leave" when a lecturer marks them with the site's configured leave
// status while taking attendance in mod_attendance - this plugin never
// writes leave data of its own, and has no separate application/
// approval workflow or balance/allocation (mod_attendance has no such
// concept to source one from).
$string['leaveoverview'] = 'Leave overview';
$string['leavelookup'] = 'Student leave lookup';
$string['leaverequestdetail'] = 'Leave record detail';
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
