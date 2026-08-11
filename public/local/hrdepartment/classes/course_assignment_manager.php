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
 * Business logic for lecturer-to-course assignments (hrdep_courseassign),
 * kept in sync with real Moodle enrolment/role assignment.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class course_assignment_manager
 *
 * hrdep_courseassign is the HR-side record of "who was assigned to
 * teach what, and when". Creating/ending an assignment also drives the
 * real Moodle-side enrolment and role assignment via enrol_manual, so
 * the lecturer actually gains/loses course access, not just a
 * bookkeeping row.
 */
class course_assignment_manager {

    /**
     * Returns the roles assignable at course context level, e.g.
     * Teacher (editingteacher), Non-editing teacher.
     *
     * @return array roleid => localised name
     */
    public static function get_assignable_roles(): array {
        global $DB;

        $roleids = get_roles_for_contextlevels(CONTEXT_COURSE);
        if (empty($roleids)) {
            return [];
        }

        // role_fix_names() needs full role records (id, name, shortname, ...)
        // and a real context, not a bare {id} stub or a null context - it
        // reads role name overrides scoped to that context.
        $roles = $DB->get_records_list('role', 'id', array_values($roleids), 'sortorder ASC');
        if (empty($roles)) {
            return [];
        }

        // With $returnmenu = true this already returns [roleid => localname].
        return role_fix_names($roles, \context_system::instance(), ROLENAME_ALIAS, true);
    }

    /**
     * Returns course options for the assignment form, excluding the site course.
     *
     * @return array courseid => "Shortname: Fullname"
     */
    public static function get_course_options(): array {
        global $DB;

        $courses = $DB->get_records_select(
            'course',
            'id <> :siteid',
            ['siteid' => SITEID],
            'fullname ASC',
            'id, shortname, fullname'
        );

        $options = [];
        foreach ($courses as $course) {
            $options[$course->id] = $course->shortname . ': ' . format_string($course->fullname);
        }

        return $options;
    }

    /**
     * Returns the course assignment history for a lecturer, most recent
     * first, with same-course rows grouped together so duplicate/repeat
     * assignments to one course are easy to spot rather than scattered
     * through the list.
     *
     * @param int $employeeid
     * @return \stdClass[]
     */
    public static function get_assignments_for_employee(int $employeeid): array {
        global $DB;

        $sql = "SELECT ca.*, c.shortname, c.fullname
                  FROM {hrdep_courseassign} ca
                  JOIN {course} c ON c.id = ca.courseid
                 WHERE ca.employeeid = :employeeid
              ORDER BY ca.status ASC, c.fullname ASC, ca.timecreated DESC";

        return array_values($DB->get_records_sql($sql, ['employeeid' => $employeeid]));
    }

    /**
     * Returns the role(s) a user is *currently* assigned in a course,
     * read live from Moodle's role assignments rather than the
     * hrdep_courseassign.roleassigned snapshot. A course's Participants
     * page lets anyone with the right course-level permission change a
     * lecturer's role directly, bypassing this plugin entirely, so the
     * stored value can go stale - this is what the assignment table
     * should actually display for an active assignment.
     *
     * @param int $userid
     * @param int $courseid
     * @return string comma-separated localised role names, or '' if the user holds no role there right now
     */
    public static function get_live_role_names(int $userid, int $courseid): string {
        global $DB;

        $context = \context_course::instance($courseid);
        $roleassignments = get_user_roles($context, $userid, false);
        if (empty($roleassignments)) {
            return '';
        }

        $roleids = array_unique(array_map(static fn($ra) => $ra->roleid, $roleassignments));
        $roles = $DB->get_records_list('role', 'id', $roleids, 'sortorder ASC');
        if (empty($roles)) {
            return '';
        }

        $rolenames = role_fix_names($roles, $context, ROLENAME_ALIAS, true);

        return implode(', ', $rolenames);
    }

    /**
     * Returns the user's *live* Moodle enrolment status on the manual
     * enrolment instance for a course - ENROL_USER_ACTIVE,
     * ENROL_USER_SUSPENDED, or null if there's no manual enrolment at
     * all right now. Like get_live_role_names(), this can be changed
     * directly on the course's Participants page (e.g. suspending the
     * enrolment) without this plugin ever being involved, so an
     * hrdep_courseassign row can say "active" while Moodle itself says
     * "suspended" - this is what lets the display show that drift
     * instead of silently trusting the stale HR record.
     *
     * @param int $userid
     * @param int $courseid
     * @return int|null
     */
    public static function get_live_enrolment_status(int $userid, int $courseid): ?int {
        global $DB;

        $sql = "SELECT ue.status
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE e.courseid = :courseid AND e.enrol = 'manual' AND ue.userid = :userid";
        $status = $DB->get_field_sql($sql, ['courseid' => $courseid, 'userid' => $userid]);

        return $status === false ? null : (int) $status;
    }

    /**
     * Whether an employee already has an active (not ended) assignment
     * for a given course. Used to block duplicate active assignments to
     * the same course - re-assigning after a prior stint has properly
     * ended (status = ended) is still allowed.
     *
     * @param int $employeeid
     * @param int $courseid
     * @param int|null $excludeassignid assignment id to ignore (when re-checking during an update)
     * @return bool
     */
    public static function has_active_assignment(int $employeeid, int $courseid, ?int $excludeassignid = null): bool {
        global $DB;

        $params = [
            'employeeid' => $employeeid,
            'courseid' => $courseid,
            'status' => constants::COURSEASSIGN_STATUS_ACTIVE,
        ];

        $sql = 'employeeid = :employeeid AND courseid = :courseid AND status = :status';
        if ($excludeassignid) {
            $sql .= ' AND id <> :excludeid';
            $params['excludeid'] = $excludeassignid;
        }

        return $DB->record_exists_select('hrdep_courseassign', $sql, $params);
    }

    /**
     * Assigns a lecturer to a course: creates the hrdep_courseassign
     * record and enrols the underlying Moodle user with the chosen role
     * via the manual enrolment plugin.
     *
     * Refuses to create a second active assignment for a course the
     * employee is already actively assigned to - courseassign_form
     * checks this too so the user sees it as a normal form error, this
     * is the backstop for anything that calls assign() directly.
     *
     * @param int $employeeid
     * @param \stdClass $data form data: courseid, roleid, startdate, enddate
     * @param int $usermodified
     * @return array [int $assignid, bool $enrolsynced, string $warning]
     */
    public static function assign(int $employeeid, \stdClass $data, int $usermodified): array {
        global $DB;

        $courseid = (int) $data->courseid;
        if (self::has_active_assignment($employeeid, $courseid)) {
            return [0, false, get_string('errorduplicateassignment', 'local_hrdepartment')];
        }

        $employee = $DB->get_record('hrdep_employee', ['id' => $employeeid], '*', MUST_EXIST);
        $roles = self::get_assignable_roles();
        $roleid = (int) $data->roleid;
        $roleshortname = $DB->get_field('role', 'shortname', ['id' => $roleid]);

        $now = time();
        $record = new \stdClass();
        $record->employeeid = $employeeid;
        $record->courseid = $courseid;
        $record->roleassigned = $roleshortname ?: '';
        $record->startdate = $data->startdate ?? null;
        $record->enddate = $data->enddate ?? null;
        $record->status = constants::COURSEASSIGN_STATUS_ACTIVE;
        $record->timecreated = $now;
        $record->usermodified = $usermodified;
        $assignid = $DB->insert_record('hrdep_courseassign', $record);

        $warning = '';
        $enrolsynced = false;

        try {
            $course = get_course($record->courseid);
            $enrolplugin = enrol_get_plugin('manual');
            $instance = self::get_or_create_manual_instance($course, $enrolplugin);

            $enrolplugin->enrol_user(
                $instance,
                $employee->userid,
                $roleid,
                $record->startdate ?: 0,
                $record->enddate ?: 0
            );
            $enrolsynced = true;
        } catch (\Throwable $e) {
            $warning = $e->getMessage();
        }

        return [$assignid, $enrolsynced, $warning];
    }

    /**
     * Ends a course assignment: marks the hrdep_courseassign record as
     * ended, suspends the Moodle enrolment, and unassigns the role
     * (grades/submission history is preserved, only access is revoked).
     *
     * @param int $assignid
     * @param int $usermodified
     * @return array [bool $enrolsynced, string $warning]
     */
    public static function end_assignment(int $assignid, int $usermodified): array {
        global $DB;

        $assignment = $DB->get_record('hrdep_courseassign', ['id' => $assignid], '*', MUST_EXIST);
        $employee = $DB->get_record('hrdep_employee', ['id' => $assignment->employeeid], '*', MUST_EXIST);

        $assignment->status = constants::COURSEASSIGN_STATUS_ENDED;
        $assignment->enddate = $assignment->enddate ?: time();
        $assignment->usermodified = $usermodified;
        $DB->update_record('hrdep_courseassign', $assignment);

        $warning = '';
        $enrolsynced = false;

        try {
            $course = get_course($assignment->courseid);
            $context = \context_course::instance($course->id);
            $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);

            $roleid = $DB->get_field('role', 'id', ['shortname' => $assignment->roleassigned]);
            if ($roleid && $instance) {
                // Must match the component/itemid that enrol_plugin::enrol_user()
                // used when it originally assigned the role, otherwise this
                // unassign silently matches nothing.
                role_unassign($roleid, $employee->userid, $context->id, 'enrol_manual', $instance->id);
            }

            if ($instance) {
                $enrolplugin = enrol_get_plugin('manual');
                $enrolplugin->update_user_enrol($instance, $employee->userid, ENROL_USER_SUSPENDED);
            }
            $enrolsynced = true;
        } catch (\Throwable $e) {
            $warning = $e->getMessage();
        }

        return [$enrolsynced, $warning];
    }

    /**
     * Syncs an employee's course assignments with a change to their
     * employment status.
     *
     * - Moving to inactive/terminated SUSPENDS the Moodle enrolment for
     *   every assignment still marked COURSEASSIGN_STATUS_ACTIVE. The
     *   role assignment is deliberately left in place (unlike
     *   end_assignment(), which is a permanent close-out) so reactivation
     *   can cleanly restore access without re-picking a role.
     * - Moving back to active VALIDATES each such assignment (course
     *   still exists, assignment hasn't already passed its own end date)
     *   and, if still valid, restores the enrolment to active and
     *   re-asserts the role assignment. Assignments that fail validation
     *   are closed out (status set to ended) rather than silently
     *   resurrected.
     *
     * hrdep_courseassign rows already marked "ended" (via
     * end_assignment()) are untouched either way - that's a deliberate,
     * permanent close-out, not something a status change should revive.
     *
     * @param int $employeeid
     * @param string $newstatus one of constants::EMPLOYMENT_STATUS_*
     * @param int $usermodified
     * @return array list of ['assignid' => int, 'courseid' => int, 'ok' => bool, 'warning' => string]
     */
    public static function sync_assignments_for_employee_status(int $employeeid, string $newstatus, int $usermodified): array {
        global $DB;

        $suspending = in_array($newstatus, [
            constants::EMPLOYMENT_STATUS_INACTIVE,
            constants::EMPLOYMENT_STATUS_TERMINATED,
        ], true);
        $activating = $newstatus === constants::EMPLOYMENT_STATUS_ACTIVE;

        if (!$suspending && !$activating) {
            return [];
        }

        $employee = $DB->get_record('hrdep_employee', ['id' => $employeeid], '*', MUST_EXIST);
        $assignments = $DB->get_records('hrdep_courseassign', [
            'employeeid' => $employeeid,
            'status' => constants::COURSEASSIGN_STATUS_ACTIVE,
        ]);

        $results = [];
        foreach ($assignments as $assignment) {
            $results[] = $suspending
                ? self::suspend_for_status_change($assignment, $employee)
                : self::restore_for_status_change($assignment, $employee, $usermodified);
        }

        return $results;
    }

    /**
     * Suspends the Moodle enrolment for one assignment because the
     * employee is being deactivated. The role assignment is left intact.
     *
     * @param \stdClass $assignment hrdep_courseassign row
     * @param \stdClass $employee hrdep_employee row
     * @return array ['assignid' => int, 'courseid' => int, 'ok' => bool, 'warning' => string]
     */
    protected static function suspend_for_status_change(\stdClass $assignment, \stdClass $employee): array {
        global $DB;

        $result = ['assignid' => $assignment->id, 'courseid' => $assignment->courseid, 'ok' => false, 'warning' => ''];

        try {
            $instance = $DB->get_record('enrol', ['courseid' => $assignment->courseid, 'enrol' => 'manual']);
            if ($instance) {
                $enrolplugin = enrol_get_plugin('manual');
                $enrolplugin->update_user_enrol($instance, $employee->userid, ENROL_USER_SUSPENDED);
            }
            $result['ok'] = true;
        } catch (\Throwable $e) {
            $result['warning'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Validates and restores one assignment - used both when the owning
     * employee is reactivated, and when an individual ended assignment is
     * manually reactivated via reactivate_assignment(). Assignments whose
     * course no longer exists, or whose own end date has genuinely
     * elapsed, are closed out instead of being resurrected.
     *
     * @param \stdClass $assignment hrdep_courseassign row
     * @param \stdClass $employee hrdep_employee row
     * @param int $usermodified
     * @return array ['assignid' => int, 'courseid' => int, 'ok' => bool, 'warning' => string]
     */
    protected static function restore_for_status_change(\stdClass $assignment, \stdClass $employee, int $usermodified): array {
        global $DB;

        $result = ['assignid' => $assignment->id, 'courseid' => $assignment->courseid, 'ok' => false, 'warning' => ''];

        $course = $DB->get_record('course', ['id' => $assignment->courseid]);
        if (!$course) {
            self::close_out_assignment($assignment, $usermodified);
            $result['warning'] = get_string('errorcoursemissing', 'local_hrdepartment');
            return $result;
        }

        // enddate is stored as midnight of the chosen day, so an
        // assignment "ending today" must stay valid through the whole of
        // today - it has only genuinely expired once that day is over,
        // i.e. once we've reached midnight of the *following* day.
        // Comparing directly against time() treated "ends today" as
        // already expired from the moment today began.
        if (!empty($assignment->enddate) && ($assignment->enddate + DAYSECS) <= time()) {
            self::close_out_assignment($assignment, $usermodified);
            $result['warning'] = get_string('errorassignmentexpired', 'local_hrdepartment');
            return $result;
        }

        try {
            $enrolplugin = enrol_get_plugin('manual');
            $instance = self::get_or_create_manual_instance($course, $enrolplugin);
            $context = \context_course::instance($course->id);
            $roleid = $DB->get_field('role', 'id', ['shortname' => $assignment->roleassigned]) ?: null;

            if ($DB->record_exists('user_enrolments', ['enrolid' => $instance->id, 'userid' => $employee->userid])) {
                $enrolplugin->update_user_enrol($instance, $employee->userid, ENROL_USER_ACTIVE);
            } else {
                // The enrolment itself is gone (e.g. it was manually removed
                // outside this plugin) - re-create it from scratch.
                $enrolplugin->enrol_user($instance, $employee->userid, $roleid, $assignment->startdate ?: 0, $assignment->enddate ?: 0);
            }

            if ($roleid) {
                // role_assign() is idempotent, safe to call even if the
                // role was never actually removed.
                role_assign($roleid, $employee->userid, $context->id, 'enrol_manual', $instance->id);
            }

            $result['ok'] = true;
        } catch (\Throwable $e) {
            $result['warning'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Manually reactivates one ended assignment (e.g. an "End assignment"
     * click that should be undone, or one that was closed out by an
     * employee-status sync and should be restored now). Blocked if the
     * owning employee isn't currently active, so an assignment can never
     * be "active" while its employee is inactive/terminated - the two
     * stay in sync regardless of which one is changed first.
     *
     * Reuses restore_for_status_change() for the actual validation and
     * Moodle sync, so "is this assignment still valid" (course exists,
     * end date not genuinely elapsed) is answered in exactly one place.
     *
     * @param int $assignid
     * @param int $usermodified
     * @return array ['ok' => bool, 'warning' => string]
     */
    public static function reactivate_assignment(int $assignid, int $usermodified): array {
        global $DB;

        $assignment = $DB->get_record('hrdep_courseassign', ['id' => $assignid], '*', MUST_EXIST);
        $employee = $DB->get_record('hrdep_employee', ['id' => $assignment->employeeid], '*', MUST_EXIST);

        if ($employee->employmentstatus !== constants::EMPLOYMENT_STATUS_ACTIVE) {
            return ['ok' => false, 'warning' => get_string('errorreactivateinactiveemployee', 'local_hrdepartment')];
        }

        $assignment->status = constants::COURSEASSIGN_STATUS_ACTIVE;
        $assignment->usermodified = $usermodified;
        $DB->update_record('hrdep_courseassign', $assignment);

        // restore_for_status_change() re-validates (course still exists,
        // end date not genuinely elapsed) and will flip status straight
        // back to ended if either check fails - its verdict wins.
        $outcome = self::restore_for_status_change($assignment, $employee, $usermodified);

        return ['ok' => $outcome['ok'], 'warning' => $outcome['warning']];
    }

    /**
     * Marks an assignment as permanently ended because it failed
     * validation during a reactivation sync (missing course, or already
     * past its own end date).
     *
     * @param \stdClass $assignment hrdep_courseassign row
     * @param int $usermodified
     * @return void
     */
    protected static function close_out_assignment(\stdClass $assignment, int $usermodified): void {
        global $DB;

        $DB->update_record('hrdep_courseassign', (object) [
            'id' => $assignment->id,
            'status' => constants::COURSEASSIGN_STATUS_ENDED,
            'enddate' => $assignment->enddate ?: time(),
            'usermodified' => $usermodified,
        ]);
    }

    /**
     * Fetches the course's manual enrolment instance, creating one if
     * the course doesn't have one enabled yet.
     *
     * @param \stdClass $course
     * @param \enrol_plugin $enrolplugin
     * @return \stdClass the enrol instance record
     */
    protected static function get_or_create_manual_instance(\stdClass $course, \enrol_plugin $enrolplugin): \stdClass {
        global $DB;

        $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual']);
        if ($instance) {
            return $instance;
        }

        $instanceid = $enrolplugin->add_instance($course);
        return $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
    }
}
