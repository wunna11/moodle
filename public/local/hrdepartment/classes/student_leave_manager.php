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
 * Student leave request/approval workflow: either HR/staff log a leave
 * request on a student's behalf (leave/edit.php), or - added 2026-08-17 -
 * a student prepares and submits their own (leave/apply.php,
 * leave/myrequests.php), choosing which of their own course teachers
 * should review it. Either way it lands in the same hrdep_studentleaveapp
 * table and an HR/Admin/Approver/chosen-teacher reviews it (approve/
 * reject); leave types are configurable (hrdep_studentleavetype), and
 * approved days are tracked against a per-academic-year balance
 * (hrdep_studentleavebalance).
 *
 * Restored 2026-08-15 as a self-contained workflow (see
 * hrdepartment-entity-scope memory for the read-only mod_attendance-report
 * iteration this supersedes). Permission checks (can_manage()/can_view())
 * are evaluated at CONTEXT_SYSTEM (global HR/Admin) or, for a specific
 * student, that student's own CONTEXT_USER (a delegated "Approver") -
 * never against a course context. See db/access.php for the capability
 * definitions.
 *
 * The 2026-08-17 self-service addition works alongside that, not instead
 * of it: can_review_application() also allows the specific teacher stored
 * in an application's approverid (set only by leave/apply.php) to review
 * just that one application, without needing a delegated Approver role
 * assignment set up first. See get_teacher_options_for_student() and
 * is_teacher_of_student().
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class student_leave_manager
 */
class student_leave_manager {

    /** @var string Capability: full control over student leave (create/edit/review/cancel applications, types, balances). */
    const CAP_MANAGE = 'local/hrdepartment:managestudentleave';

    /** @var string Capability: read-only access to student leave. */
    const CAP_VIEW = 'local/hrdepartment:viewstudentleave';

    /** @var string Capability: a student preparing and submitting their own leave request (leave/apply.php). */
    const CAP_APPLYOWN = 'local/hrdepartment:applyownleave';

    // -----------------------------------------------------------------
    // Permission checks.
    // -----------------------------------------------------------------

    /**
     * Whether $userid can manage (create/edit/review/cancel) student leave
     * - either globally (a role holding local/hrdepartment:managestudentleave
     * assigned at CONTEXT_SYSTEM cascades to every student's context), or,
     * when $studentid is given, for that one student specifically (a role
     * holding the same capability assigned directly on that student's own
     * CONTEXT_USER - a delegated Approver).
     *
     * Pass $studentid = 0 for a "can manage at all" check (browsing every
     * student's applications, leave types, dashboard) - delegated
     * per-student approvers only pass this for pages scoped to their one
     * student (view/edit/review/cancel/balance with a specific studentid).
     *
     * @param int $studentid 0 for a global-only check, or a specific student's userid.
     * @param int $userid the user being checked; defaults to $USER.
     * @return bool
     */
    public static function can_manage(int $studentid = 0, int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (access_manager::can_access_hr_department($userid)) {
            return true;
        }

        if (has_capability(self::CAP_MANAGE, \context_system::instance(), $userid)) {
            return true;
        }

        if ($studentid > 0) {
            $usercontext = \context_user::instance($studentid, IGNORE_MISSING);
            if ($usercontext && has_capability(self::CAP_MANAGE, $usercontext, $userid)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether $userid can at least view student leave - can_manage()
     * implies this; otherwise checks local/hrdepartment:viewstudentleave
     * with the same global-or-per-student-context logic.
     *
     * @param int $studentid 0 for a global-only check, or a specific student's userid.
     * @param int $userid the user being checked; defaults to $USER.
     * @return bool
     */
    public static function can_view(int $studentid = 0, int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (self::can_manage($studentid, $userid)) {
            return true;
        }

        if (has_capability(self::CAP_VIEW, \context_system::instance(), $userid)) {
            return true;
        }

        if ($studentid > 0) {
            $usercontext = \context_user::instance($studentid, IGNORE_MISSING);
            if ($usercontext && has_capability(self::CAP_VIEW, $usercontext, $userid)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether $userid can review (approve/reject) one specific
     * application: either they can_manage() it via the existing
     * global/delegated-context path above, OR they are the teacher the
     * student themselves chose as the approver when submitting it (see
     * leave/apply.php and get_teacher_options_for_student()) - a much
     * lighter-weight path than setting up a delegated "Approver" role
     * assignment, scoped to just this one application.
     *
     * @param \stdClass $application must include studentid and approverid
     * @param int $userid the user being checked; defaults to $USER.
     * @return bool
     */
    public static function can_review_application(\stdClass $application, int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        if (self::can_manage((int) $application->studentid, $userid)) {
            return true;
        }

        return !empty($application->approverid) && (int) $application->approverid === $userid;
    }

    /**
     * Whether $userid has been chosen as the approving teacher on at
     * least one self-service leave application (leave/apply.php) -
     * i.e. whether they're a "reviewing teacher" at all, regardless of
     * whether anything is currently pending. Used to decide whether to
     * show the "Leave requests to review" link in navigation/the account
     * menu for a teacher who holds none of the HR capabilities
     * (CAP_MANAGE/CAP_VIEW/CAP_APPLYOWN) - see leave/myapprovals.php,
     * lib.php, and classes/hook_callbacks.php.
     *
     * @param int $userid
     * @return bool
     */
    public static function is_approver(int $userid): bool {
        global $DB;

        return $DB->record_exists('hrdep_studentleaveapp', ['approverid' => $userid]);
    }

    /**
     * Whether $userid holds an editing/non-editing teacher role in at
     * least one course - a generic "is this person a teacher at all"
     * check, unlike is_teacher_of_student() which checks a specific
     * teacher/student relationship. Used by is_leave_attendance_only_role()
     * below.
     *
     * @param int $userid
     * @return bool
     */
    public static function is_teacher(int $userid): bool {
        global $DB;

        [$archsql, $archparams] = $DB->get_in_or_equal(['editingteacher', 'teacher'], SQL_PARAMS_NAMED, 'arch');

        $sql = "SELECT 1
                  FROM {role_assignments} ra
                  JOIN {role} r ON r.id = ra.roleid AND r.archetype $archsql
                 WHERE ra.userid = :userid";

        return $DB->record_exists_sql($sql, $archparams + ['userid' => $userid]);
    }

    /**
     * Whether $userid should see the simplified "Leave and Attendance
     * Checking" view - only the Attendance and Leave tabs, with the
     * section heading swapped from "HR Department" - instead of the
     * full HR Department navigation. True for a plain student or
     * teacher who holds none of this plugin's management-level
     * capabilities (dashboard/lecturers/staff/students/payroll, or a
     * global leave manage/view right). HR/Admin/manager accounts, and
     * delegated per-student Approvers, always keep the full view even
     * if they also happen to hold a student/teacher role somewhere
     * (e.g. a manager testing as a course teacher).
     *
     * Added 2026-08-17: previously every non-manager account (student
     * or teacher alike) saw all four tabs - Dashboard, Attendance,
     * Leave, Payroll - even though Payroll has no pages built yet and
     * Dashboard is redundant with the two sections they actually use.
     *
     * @param int $userid defaults to $USER.
     * @return bool
     */
    public static function is_leave_attendance_only_role(int $userid = 0): bool {
        global $USER;
        $userid = $userid ?: (int) $USER->id;

        $context = \context_system::instance();

        $ismanager = access_manager::can_access_hr_department($userid)
            || has_capability('local/hrdepartment:managelecturers', $context, $userid)
            || has_capability('local/hrdepartment:managestaff', $context, $userid)
            || has_capability('local/hrdepartment:managestudents', $context, $userid)
            || has_capability('local/hrdepartment:managepayroll', $context, $userid)
            || self::can_manage(0, $userid)
            || self::can_view(0, $userid);

        if ($ismanager) {
            return false;
        }

        return self::is_student($userid) || self::is_teacher($userid);
    }

    /**
     * Section heading for $PAGE->set_heading() on every HR Department
     * page - "HR Department" normally, or "Leave and Attendance
     * Checking" for a plain student/teacher restricted to those two
     * sections (see is_leave_attendance_only_role()).
     *
     * Deliberately a class method rather than a lib.php function: this
     * is called before $OUTPUT->header(), when lib.php isn't reliably
     * loaded yet, whereas namespaced classes autoload regardless of
     * call order - see classes/department_helper.php's docblock for
     * the same reasoning.
     *
     * @return string
     */
    public static function get_page_heading(): string {
        return self::is_leave_attendance_only_role()
            ? get_string('leaveattendanceheading', 'local_hrdepartment')
            : get_string('pluginname', 'local_hrdepartment');
    }

    // -----------------------------------------------------------------
    // Students.
    // -----------------------------------------------------------------

    /**
     * Whether $userid holds the student role in at least one course.
     *
     * @param int $userid
     * @return bool
     */
    public static function is_student(int $userid): bool {
        global $DB;

        $sql = "SELECT 1
                  FROM {role_assignments} ra
                  JOIN {role} r ON r.id = ra.roleid
                 WHERE ra.userid = :userid AND r.archetype = :archetype";

        return $DB->record_exists_sql($sql, ['userid' => $userid, 'archetype' => 'student']);
    }

    /**
     * Searches users holding the student role by name/email.
     *
     * @param string $search
     * @param int $limit
     * @return \stdClass[] id, firstname, lastname, email
     */
    public static function search_students(string $search, int $limit = 20): array {
        global $DB;

        $like = '%' . $DB->sql_like_escape($search) . '%';

        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                  FROM {user} u
                  JOIN {role_assignments} ra ON ra.userid = u.id
                  JOIN {role} r ON r.id = ra.roleid AND r.archetype = :archetype
                 WHERE u.deleted = 0 AND u.suspended = 0
                   AND (" . $DB->sql_like('u.firstname', ':search1', false) . "
                     OR " . $DB->sql_like('u.lastname', ':search2', false) . "
                     OR " . $DB->sql_like('u.email', ':search3', false) . ")
              ORDER BY u.lastname ASC, u.firstname ASC";

        return array_values($DB->get_records_sql($sql, [
            'archetype' => 'student', 'search1' => $like, 'search2' => $like, 'search3' => $like,
        ], 0, $limit));
    }

    /**
     * Returns student options for an autocomplete element, optionally
     * ensuring a currently-selected student is included even if the
     * default candidate list wouldn't otherwise surface them.
     *
     * @param int|null $selectedid
     * @return array id => "Name (email)"
     */
    public static function get_student_options(?int $selectedid = null): array {
        global $DB;

        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                  FROM {user} u
                  JOIN {role_assignments} ra ON ra.userid = u.id
                  JOIN {role} r ON r.id = ra.roleid AND r.archetype = :archetype
                 WHERE u.deleted = 0
              ORDER BY u.lastname ASC, u.firstname ASC";

        $students = $DB->get_records_sql($sql, ['archetype' => 'student'], 0, 200);

        $options = [];
        foreach ($students as $student) {
            $options[$student->id] = fullname($student) . ' (' . $student->email . ')';
        }

        if ($selectedid && !isset($options[$selectedid])) {
            $student = \core_user::get_user($selectedid);
            if ($student && !$student->deleted) {
                $options[$selectedid] = fullname($student) . ' (' . $student->email . ')';
            }
        }

        return $options;
    }

    /**
     * Whether $teacherid holds an editing/non-editing teacher role in at
     * least one course that $studentid also holds the student role in -
     * i.e. $teacherid actually teaches $studentid. Used to populate the
     * self-service "Apply for leave" form's approver picker
     * (get_teacher_options_for_student()) and, critically, to re-validate
     * a submitted approverid server-side (leave/apply.php and the form's
     * own validation()) rather than trusting the picker's options.
     *
     * @param int $teacherid
     * @param int $studentid
     * @return bool
     */
    public static function is_teacher_of_student(int $teacherid, int $studentid): bool {
        global $DB;

        [$archsql, $archparams] = $DB->get_in_or_equal(['editingteacher', 'teacher'], SQL_PARAMS_NAMED, 'arch');

        $sql = "SELECT 1
                  FROM {role_assignments} tra
                  JOIN {context} ctx ON ctx.id = tra.contextid AND ctx.contextlevel = :coursecontextlevel
                  JOIN {role} tr ON tr.id = tra.roleid AND tr.archetype $archsql
                 WHERE tra.userid = :teacherid
                   AND ctx.instanceid IN (
                        SELECT ctx2.instanceid
                          FROM {role_assignments} sra
                          JOIN {context} ctx2 ON ctx2.id = sra.contextid AND ctx2.contextlevel = :coursecontextlevel2
                          JOIN {role} sr ON sr.id = sra.roleid AND sr.archetype = :studentarchetype
                         WHERE sra.userid = :studentid
                       )";

        $params = $archparams + [
            'coursecontextlevel' => CONTEXT_COURSE,
            'coursecontextlevel2' => CONTEXT_COURSE,
            'teacherid' => $teacherid,
            'studentarchetype' => 'student',
            'studentid' => $studentid,
        ];

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Returns teacher options for the self-service "Apply for leave"
     * form's approver picker: every (editing or non-editing) teacher of
     * any course $studentid holds the student role in, deduplicated -
     * i.e. only $studentid's own teachers, not every teacher site-wide.
     *
     * @param int $studentid
     * @return array id => "Name (email)"
     */
    public static function get_teacher_options_for_student(int $studentid): array {
        global $DB;

        [$archsql, $archparams] = $DB->get_in_or_equal(['editingteacher', 'teacher'], SQL_PARAMS_NAMED, 'arch');

        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                  FROM {role_assignments} tra
                  JOIN {context} ctx ON ctx.id = tra.contextid AND ctx.contextlevel = :coursecontextlevel
                  JOIN {role} tr ON tr.id = tra.roleid AND tr.archetype $archsql
                  JOIN {user} u ON u.id = tra.userid AND u.deleted = 0
                 WHERE ctx.instanceid IN (
                        SELECT ctx2.instanceid
                          FROM {role_assignments} sra
                          JOIN {context} ctx2 ON ctx2.id = sra.contextid AND ctx2.contextlevel = :coursecontextlevel2
                          JOIN {role} sr ON sr.id = sra.roleid AND sr.archetype = :studentarchetype
                         WHERE sra.userid = :studentid
                       )
              ORDER BY u.lastname ASC, u.firstname ASC";

        $params = $archparams + [
            'coursecontextlevel' => CONTEXT_COURSE,
            'coursecontextlevel2' => CONTEXT_COURSE,
            'studentarchetype' => 'student',
            'studentid' => $studentid,
        ];

        $teachers = $DB->get_records_sql($sql, $params);

        $options = [];
        foreach ($teachers as $teacher) {
            $options[$teacher->id] = fullname($teacher) . ' (' . $teacher->email . ')';
        }

        return $options;
    }

    /**
     * Returns course options for the self-service "Apply for leave"
     * form: only courses $studentid is themselves enrolled in as a
     * student (unlike the HR-facing student_leave_form, whose course
     * field draws from course_assignment_manager::get_course_options()
     * - every course site-wide - because HR/staff can log a request
     * against any course on the student's behalf).
     *
     * @param int $studentid
     * @return array id => "shortname: fullname"
     */
    public static function get_course_options_for_student(int $studentid): array {
        $courses = student_manager::get_courses_for_users([$studentid])[$studentid] ?? [];

        $options = [];
        foreach ($courses as $course) {
            $options[$course->id] = $course->shortname . ': ' . format_string($course->fullname);
        }

        return $options;
    }

    // -----------------------------------------------------------------
    // Leave types.
    // -----------------------------------------------------------------

    /**
     * Returns leave types.
     *
     * @param bool $activeonly
     * @return \stdClass[]
     */
    public static function get_leave_types(bool $activeonly = true): array {
        global $DB;

        $conditions = $activeonly ? ['active' => 1] : [];

        return array_values($DB->get_records('hrdep_studentleavetype', $conditions, 'name ASC'));
    }

    /**
     * Returns one leave type, or false if it doesn't exist.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get_leave_type(int $id) {
        global $DB;

        return $DB->get_record('hrdep_studentleavetype', ['id' => $id]);
    }

    /**
     * Returns active leave type options for a select/autocomplete element.
     *
     * @return array id => name
     */
    public static function get_leave_type_options(): array {
        $options = [];
        foreach (self::get_leave_types(true) as $type) {
            $options[$type->id] = format_string($type->name);
        }
        return $options;
    }

    /**
     * Whether $name is already used by another leave type.
     *
     * @param string $name
     * @param int $excludeid
     * @return bool
     */
    public static function leave_type_name_in_use(string $name, int $excludeid = 0): bool {
        global $DB;

        $conditions = ['name' => $name];
        $select = 'LOWER(name) = LOWER(:name)';
        $params = ['name' => $name];
        if ($excludeid) {
            $select .= ' AND id <> :excludeid';
            $params['excludeid'] = $excludeid;
        }

        return $DB->record_exists_select('hrdep_studentleavetype', $select, $params);
    }

    /**
     * Creates or updates a leave type.
     *
     * @param \stdClass $data form data: id (0 = new), name, description, maxdaysperyear, requiresapproval, active
     * @return int the leave type id
     */
    public static function save_leave_type(\stdClass $data): int {
        global $DB;

        $now = time();
        $record = (object) [
            'name' => trim($data->name),
            'description' => $data->description ?? null,
            'maxdaysperyear' => (float) $data->maxdaysperyear,
            'requiresapproval' => !empty($data->requiresapproval) ? 1 : 0,
            'active' => !empty($data->active) ? 1 : 0,
            'timemodified' => $now,
        ];

        if (!empty($data->id)) {
            $record->id = (int) $data->id;
            $DB->update_record('hrdep_studentleavetype', $record);
            return $record->id;
        }

        $record->timecreated = $now;
        return (int) $DB->insert_record('hrdep_studentleavetype', $record);
    }

    /**
     * Whether a leave type can be deleted (no application or balance
     * references it - deactivate it instead if so).
     *
     * @param int $id
     * @return bool
     */
    public static function can_delete_leave_type(int $id): bool {
        global $DB;

        return !$DB->record_exists('hrdep_studentleaveapp', ['leavetypeid' => $id])
            && !$DB->record_exists('hrdep_studentleavebalance', ['leavetypeid' => $id]);
    }

    /**
     * Deletes a leave type. Returns false without deleting if it's still
     * referenced by an application or balance record.
     *
     * @param int $id
     * @return bool
     */
    public static function delete_leave_type(int $id): bool {
        global $DB;

        if (!self::can_delete_leave_type($id)) {
            return false;
        }

        $DB->delete_records('hrdep_studentleavetype', ['id' => $id]);
        return true;
    }

    // -----------------------------------------------------------------
    // Leave applications.
    // -----------------------------------------------------------------

    /**
     * Returns one application's detail, or false if it doesn't exist.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get_application(int $id) {
        global $DB;

        $sql = "SELECT a.*,
                       u.firstname AS studentfirstname, u.lastname AS studentlastname, u.email AS studentemail,
                       lt.name AS leavetypename,
                       c.shortname AS courseshortname, c.fullname AS coursefullname,
                       su.firstname AS submittedbyfirstname, su.lastname AS submittedbylastname,
                       ru.firstname AS reviewedbyfirstname, ru.lastname AS reviewedbylastname,
                       au.firstname AS approverfirstname, au.lastname AS approverlastname, au.email AS approveremail
                  FROM {hrdep_studentleaveapp} a
                  JOIN {user} u ON u.id = a.studentid
                  JOIN {hrdep_studentleavetype} lt ON lt.id = a.leavetypeid
             LEFT JOIN {course} c ON c.id = a.courseid
                  JOIN {user} su ON su.id = a.submittedby
             LEFT JOIN {user} ru ON ru.id = a.reviewedby
             LEFT JOIN {user} au ON au.id = a.approverid
                 WHERE a.id = :id";

        $record = $DB->get_record_sql($sql, ['id' => $id]);
        if (!$record) {
            return false;
        }

        $record->studentfullname = fullname((object) [
            'firstname' => $record->studentfirstname, 'lastname' => $record->studentlastname,
        ]);
        $record->submittedbyfullname = fullname((object) [
            'firstname' => $record->submittedbyfirstname, 'lastname' => $record->submittedbylastname,
        ]);
        $record->reviewedbyfullname = $record->reviewedbyfirstname !== null
            ? fullname((object) ['firstname' => $record->reviewedbyfirstname, 'lastname' => $record->reviewedbylastname])
            : null;
        $record->approverfullname = $record->approverfirstname !== null
            ? fullname((object) ['firstname' => $record->approverfirstname, 'lastname' => $record->approverlastname])
            : null;

        return $record;
    }

    /**
     * Runs a filtered query over leave applications, shared by the Leave
     * requests table, the Leave Overview recent list, and the Reports &
     * Export page so they all stay consistent.
     *
     * @param array $filters keys: search, studentid, approverid, leavetypeid, status, datefrom, dateto (all optional)
     * @param int $limit 0 = no limit
     * @return \stdClass[]
     */
    public static function get_application_rows(array $filters, int $limit = 0): array {
        global $DB;

        $where = ['1 = 1'];
        $params = [];

        if (!empty($filters['studentid'])) {
            $where[] = 'a.studentid = :studentid';
            $params['studentid'] = (int) $filters['studentid'];
        }

        if (!empty($filters['approverid'])) {
            $where[] = 'a.approverid = :approverid';
            $params['approverid'] = (int) $filters['approverid'];
        }

        if (!empty($filters['leavetypeid'])) {
            $where[] = 'a.leavetypeid = :leavetypeid';
            $params['leavetypeid'] = (int) $filters['leavetypeid'];
        }

        if (!empty($filters['status'])) {
            $where[] = 'a.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $like = '%' . $DB->sql_like_escape($filters['search']) . '%';
            $where[] = '(' . $DB->sql_like('u.firstname', ':search1', false) . '
                     OR ' . $DB->sql_like('u.lastname', ':search2', false) . '
                     OR ' . $DB->sql_like('u.email', ':search3', false) . ')';
            $params['search1'] = $like;
            $params['search2'] = $like;
            $params['search3'] = $like;
        }

        if (!empty($filters['datefrom'])) {
            $where[] = 'a.startdate >= :datefrom';
            $params['datefrom'] = (int) $filters['datefrom'];
        }

        if (!empty($filters['dateto'])) {
            $where[] = 'a.enddate <= :dateto';
            $params['dateto'] = (int) $filters['dateto'];
        }

        $wheresql = implode(' AND ', $where);

        $sql = "SELECT a.id, a.studentid, u.firstname, u.lastname, u.email,
                       a.leavetypeid, lt.name AS leavetypename,
                       a.startdate, a.enddate, a.totaldays, a.reason, a.status,
                       a.submittedby, a.reviewedby, a.approverid,
                       au.firstname AS approverfirstname, au.lastname AS approverlastname,
                       a.timecreated
                  FROM {hrdep_studentleaveapp} a
                  JOIN {user} u ON u.id = a.studentid
                  JOIN {hrdep_studentleavetype} lt ON lt.id = a.leavetypeid
             LEFT JOIN {user} au ON au.id = a.approverid
                 WHERE $wheresql
              ORDER BY a.timecreated DESC";

        $records = $DB->get_records_sql($sql, $params, 0, $limit ?: 0);

        $rows = [];
        foreach ($records as $record) {
            $record->fullname = fullname($record);
            $record->approverfullname = $record->approverfirstname !== null
                ? fullname((object) ['firstname' => $record->approverfirstname, 'lastname' => $record->approverlastname])
                : null;
            $rows[] = $record;
        }

        return $rows;
    }

    /**
     * Creates a new leave application (always starts pending).
     *
     * @param \stdClass $data form data: studentid, leavetypeid, courseid, startdate, enddate, reason,
     *                        approverid (optional - only set by the self-service leave/apply.php form,
     *                        the teacher the student themselves chose to review this one application;
     *                        see can_review_application())
     * @param int $submittedby
     * @return int the new application id
     */
    public static function create_application(\stdClass $data, int $submittedby): int {
        global $DB;

        $now = time();
        $record = (object) [
            'studentid' => (int) $data->studentid,
            'courseid' => !empty($data->courseid) ? (int) $data->courseid : null,
            'leavetypeid' => (int) $data->leavetypeid,
            'startdate' => (int) $data->startdate,
            'enddate' => (int) $data->enddate,
            'totaldays' => self::calculate_total_days((int) $data->startdate, (int) $data->enddate),
            'reason' => $data->reason ?? null,
            'status' => constants::LEAVE_STATUS_PENDING,
            'submittedby' => $submittedby,
            // Null-coalesce, not a plain property access: student_leave_form
            // (the HR/staff-facing form used by leave/edit.php) doesn't
            // define an approverid element at all, so $data from that form
            // has no such property - accessing it directly would emit a
            // PHP "undefined property" warning on every HR-logged request.
            'approverid' => !empty($data->approverid ?? null) ? (int) $data->approverid : null,
            'reviewedby' => null,
            'reviewnote' => null,
            'timecreated' => $now,
            'timemodified' => $now,
        ];

        return (int) $DB->insert_record('hrdep_studentleaveapp', $record);
    }

    /**
     * Updates a pending leave application. Callers must have already
     * checked the application is still pending (see leave/edit.php).
     *
     * @param int $id
     * @param \stdClass $data form data: leavetypeid, courseid, startdate, enddate, reason
     * @param int $modifiedby
     */
    public static function update_application(int $id, \stdClass $data, int $modifiedby): void {
        global $DB;

        $DB->update_record('hrdep_studentleaveapp', (object) [
            'id' => $id,
            'leavetypeid' => (int) $data->leavetypeid,
            'courseid' => !empty($data->courseid) ? (int) $data->courseid : null,
            'startdate' => (int) $data->startdate,
            'enddate' => (int) $data->enddate,
            'totaldays' => self::calculate_total_days((int) $data->startdate, (int) $data->enddate),
            'reason' => $data->reason ?? null,
            'timemodified' => time(),
        ]);
    }

    /**
     * Approves or rejects a pending leave application. Approving deducts
     * the application's days from the student's leave balance for the
     * leave type/academic year of the application's start date.
     *
     * @param int $id
     * @param string $decision constants::LEAVE_STATUS_APPROVED or constants::LEAVE_STATUS_REJECTED
     * @param int $reviewedby
     * @param string|null $reviewnote
     */
    public static function review_application(int $id, string $decision, int $reviewedby, ?string $reviewnote = null): void {
        global $DB;

        $application = $DB->get_record('hrdep_studentleaveapp', ['id' => $id], '*', MUST_EXIST);

        $DB->update_record('hrdep_studentleaveapp', (object) [
            'id' => $id,
            'status' => $decision,
            'reviewedby' => $reviewedby,
            'reviewnote' => $reviewnote,
            'timemodified' => time(),
        ]);

        if ($decision === constants::LEAVE_STATUS_APPROVED) {
            $academicyear = self::academic_year_for_timestamp((int) $application->startdate);
            self::adjust_balance_used($application->studentid, $application->leavetypeid, $academicyear, (float) $application->totaldays);
        }

        self::notify_decision($application, $decision);
    }

    /**
     * Cancels a leave request (pending or already-approved). If it had
     * been approved, the days are returned to the student's balance.
     *
     * @param int $id
     * @param int $cancelledby
     */
    public static function cancel_application(int $id, int $cancelledby): void {
        global $DB;

        $application = $DB->get_record('hrdep_studentleaveapp', ['id' => $id], '*', MUST_EXIST);

        if ($application->status === constants::LEAVE_STATUS_APPROVED) {
            $academicyear = self::academic_year_for_timestamp((int) $application->startdate);
            self::adjust_balance_used($application->studentid, $application->leavetypeid, $academicyear, -(float) $application->totaldays);
        }

        $DB->update_record('hrdep_studentleaveapp', (object) [
            'id' => $id,
            'status' => constants::LEAVE_STATUS_CANCELLED,
            'timemodified' => time(),
        ]);
    }

    /**
     * Inclusive whole-day count between two date_selector timestamps.
     *
     * @param int $startdate
     * @param int $enddate
     * @return float
     */
    protected static function calculate_total_days(int $startdate, int $enddate): float {
        if ($enddate < $startdate) {
            return 0;
        }
        return floor(($enddate - $startdate) / DAYSECS) + 1;
    }

    /**
     * Sends the employee/student a notification of an approve/reject
     * decision, if local_hrdepartment/notifyleavedecision is enabled.
     *
     * @param \stdClass $application
     * @param string $decision
     */
    protected static function notify_decision(\stdClass $application, string $decision): void {
        if (empty(get_config('local_hrdepartment', 'notifyleavedecision'))) {
            return;
        }

        $student = \core_user::get_user($application->studentid);
        if (!$student || $student->deleted) {
            return;
        }

        $stringkey = $decision === constants::LEAVE_STATUS_APPROVED ? 'leaveapproved' : 'leaverejected';

        $message = new \core\message\message();
        $message->component = 'local_hrdepartment';
        $message->name = 'leavedecision';
        $message->userfrom = \core_user::get_noreply_user();
        $message->userto = $student;
        $message->subject = get_string($stringkey, 'local_hrdepartment');
        $message->fullmessage = get_string($stringkey, 'local_hrdepartment');
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = '';
        $message->smallmessage = get_string($stringkey, 'local_hrdepartment');
        $message->notification = 1;
        $message->contexturl = (new \moodle_url('/local/hrdepartment/leave/view.php', ['id' => $application->id]))->out(false);
        $message->contexturlname = get_string('leaverequestdetail', 'local_hrdepartment');

        message_send($message);
    }

    // -----------------------------------------------------------------
    // Leave balances.
    // -----------------------------------------------------------------

    /**
     * Returns the current academic year label (e.g. "2026-2027"),
     * assuming the academic year starts in August.
     *
     * @return string
     */
    public static function current_academic_year(): string {
        return self::academic_year_for_timestamp(time());
    }

    /**
     * Returns the academic year label (e.g. "2026-2027") a timestamp
     * falls in, assuming the academic year starts in August.
     *
     * @param int $timestamp
     * @return string
     */
    protected static function academic_year_for_timestamp(int $timestamp): string {
        $year = (int) userdate($timestamp, '%Y');
        $month = (int) userdate($timestamp, '%m');

        $startyear = $month >= 8 ? $year : $year - 1;
        return $startyear . '-' . ($startyear + 1);
    }

    /**
     * Returns a small set of academic year options centred on the
     * current one, for a filter/select element.
     *
     * @return array year => year
     */
    public static function get_academic_year_options(): array {
        $current = self::current_academic_year();
        $currentstart = (int) substr($current, 0, 4);

        $options = [];
        for ($offset = -1; $offset <= 1; $offset++) {
            $startyear = $currentstart + $offset;
            $label = $startyear . '-' . ($startyear + 1);
            $options[$label] = $label;
        }

        return $options;
    }

    /**
     * Returns one row per active leave type for a student/academic year,
     * joined to their existing balance (allocated/used/remaining default
     * to 0 if no balance row has been created for that type/year yet).
     *
     * @param int $studentid
     * @param string $academicyear
     * @return \stdClass[] leavetypeid, leavetypename, allocated, used, remaining
     */
    public static function get_balances_for_student(int $studentid, string $academicyear): array {
        global $DB;

        $sql = "SELECT lt.id AS leavetypeid, lt.name AS leavetypename,
                       COALESCE(b.allocated, 0) AS allocated,
                       COALESCE(b.used, 0) AS used,
                       COALESCE(b.remaining, 0) AS remaining
                  FROM {hrdep_studentleavetype} lt
             LEFT JOIN {hrdep_studentleavebalance} b
                    ON b.leavetypeid = lt.id AND b.studentid = :studentid AND b.academicyear = :academicyear
                 WHERE lt.active = 1
              ORDER BY lt.name ASC";

        return array_values($DB->get_records_sql($sql, ['studentid' => $studentid, 'academicyear' => $academicyear]));
    }

    /**
     * Returns a student's balance for one leave type/academic year,
     * defaulting to zeroes (not persisted) if no row exists yet.
     *
     * @param int $studentid
     * @param int $leavetypeid
     * @param string $academicyear
     * @return \stdClass allocated, used, remaining
     */
    public static function get_or_create_balance(int $studentid, int $leavetypeid, string $academicyear): \stdClass {
        global $DB;

        $balance = $DB->get_record('hrdep_studentleavebalance', [
            'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
        ]);

        if ($balance) {
            return $balance;
        }

        return (object) [
            'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
            'allocated' => 0.0, 'used' => 0.0, 'remaining' => 0.0,
        ];
    }

    /**
     * Sets (creates or updates) a student's allocated days for one leave
     * type/academic year; remaining is recalculated from allocated - used.
     *
     * @param int $studentid
     * @param int $leavetypeid
     * @param string $academicyear
     * @param float $allocated
     */
    public static function save_balance_allocation(int $studentid, int $leavetypeid, string $academicyear, float $allocated): void {
        global $DB;

        $existing = $DB->get_record('hrdep_studentleavebalance', [
            'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
        ]);

        $used = $existing ? (float) $existing->used : 0.0;
        $remaining = max(0, $allocated - $used);

        if ($existing) {
            $DB->update_record('hrdep_studentleavebalance', (object) [
                'id' => $existing->id, 'allocated' => $allocated, 'remaining' => $remaining, 'timemodified' => time(),
            ]);
            return;
        }

        $DB->insert_record('hrdep_studentleavebalance', (object) [
            'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
            'allocated' => $allocated, 'used' => $used, 'remaining' => $remaining, 'timemodified' => time(),
        ]);
    }

    /**
     * Adjusts (increments, or decrements via a negative $days) a
     * student's "used" balance for one leave type/academic year, creating
     * the balance row (with 0 allocated) if it doesn't exist yet.
     * "remaining" is recalculated as allocated - used, floored at 0.
     *
     * @param int $studentid
     * @param int $leavetypeid
     * @param string $academicyear
     * @param float $days positive to consume days, negative to return them
     */
    protected static function adjust_balance_used(int $studentid, int $leavetypeid, string $academicyear, float $days): void {
        global $DB;

        $existing = $DB->get_record('hrdep_studentleavebalance', [
            'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
        ]);

        $allocated = $existing ? (float) $existing->allocated : 0.0;
        $used = max(0, ($existing ? (float) $existing->used : 0.0) + $days);
        $remaining = max(0, $allocated - $used);

        if ($existing) {
            $DB->update_record('hrdep_studentleavebalance', (object) [
                'id' => $existing->id, 'used' => $used, 'remaining' => $remaining, 'timemodified' => time(),
            ]);
            return;
        }

        $DB->insert_record('hrdep_studentleavebalance', (object) [
            'studentid' => $studentid, 'leavetypeid' => $leavetypeid, 'academicyear' => $academicyear,
            'allocated' => $allocated, 'used' => $used, 'remaining' => $remaining, 'timemodified' => time(),
        ]);
    }

    // -----------------------------------------------------------------
    // Dashboard.
    // -----------------------------------------------------------------

    /**
     * Returns dashboard summary stats for the Leave Overview page.
     *
     * @return \stdClass pending, onleavetoday, approvedthismonth, total
     */
    public static function get_dashboard_summary(): \stdClass {
        global $DB;

        $summary = new \stdClass();

        $summary->pending = (int) $DB->count_records('hrdep_studentleaveapp', ['status' => constants::LEAVE_STATUS_PENDING]);

        $today = strtotime('midnight');
        $summary->onleavetoday = (int) $DB->count_records_select(
            'hrdep_studentleaveapp',
            'status = :status AND startdate <= :today AND enddate >= :today2',
            ['status' => constants::LEAVE_STATUS_APPROVED, 'today' => $today, 'today2' => $today]
        );

        $monthstart = strtotime('first day of this month midnight');
        $summary->approvedthismonth = (int) $DB->count_records_select(
            'hrdep_studentleaveapp',
            'status = :status AND timemodified >= :monthstart',
            ['status' => constants::LEAVE_STATUS_APPROVED, 'monthstart' => $monthstart]
        );

        $summary->total = (int) $DB->count_records('hrdep_studentleaveapp', []);

        return $summary;
    }

    /**
     * Returns the most recent leave applications, for the Leave Overview
     * quick-glance list.
     *
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_recent_applications(int $limit = 8): array {
        return self::get_application_rows([], $limit);
    }
}
