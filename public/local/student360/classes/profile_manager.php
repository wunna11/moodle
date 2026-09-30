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
 * Aggregates a single student's data from local_financedepartment and
 * local_hrdepartment into one read-only object. This plugin owns no
 * database tables of its own - every value here is fetched live from
 * the other two plugins' manager classes (hard dependency, see
 * version.php), never cached or duplicated.
 *
 * Each external call is individually try/catch-guarded so that one
 * missing/broken data source degrades that ONE section to empty rather
 * than fataling the whole page - the same lesson
 * [[hrdepartment-studentleave-schema-fix]] project memory documents
 * from local_hrdepartment's own production incident.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_student360;

defined('MOODLE_INTERNAL') || die();

class profile_manager {

    /**
     * Builds the full aggregated profile for one student.
     *
     * @param int $studentid
     * @return \stdClass properties: student, feerecords, scholarshiprequests,
     *                    discountrequests, attendancebycourse, leaveapplications, summary
     */
    public static function get_profile(int $studentid): \stdClass {
        $profile = new \stdClass();

        $profile->student = self::safe(function () use ($studentid) {
            return \local_hrdepartment\student_manager::get_student($studentid);
        }, null);

        $profile->feerecords = self::safe(function () use ($studentid) {
            return \local_financedepartment\feerecord_manager::get_for_student($studentid);
        }, []);

        $profile->scholarshiprequests = self::safe(function () use ($studentid) {
            return \local_financedepartment\scholarshiprequest_manager::get_for_student($studentid);
        }, []);

        $profile->discountrequests = self::safe(function () use ($studentid) {
            return \local_financedepartment\discountrequest_manager::get_for_student($studentid);
        }, []);

        $profile->attendancebycourse = self::safe(function () use ($studentid) {
            return self::build_attendance_by_course($studentid);
        }, []);

        $profile->leaveapplications = self::safe(function () use ($studentid) {
            return \local_hrdepartment\student_leave_manager::get_application_rows(['studentid' => $studentid], 10);
        }, []);

        $profile->summary = self::build_summary($profile);

        return $profile;
    }

    /**
     * Breaks the attendance status summary down PER COURSE - the user
     * explicitly asked (2026-09-18, in Burmese) which course each
     * attendance figure belongs to, since the plain organisation-wide
     * total was "too generic". Uses
     * student_attendance_manager::get_student_courses() to find every
     * course the student has attendance records in, then re-runs
     * get_student_status_summary() scoped to each one via its optional
     * $courseid parameter (rather than fetching every raw
     * attendance_log row and grouping here - the manager class already
     * does the GROUP BY, this just calls it once per course).
     *
     * @param int $studentid
     * @return \stdClass[] each with ->id/->shortname/->fullname (the course)
     *                     plus ->statuses ([{acronym, description, total}])
     */
    protected static function build_attendance_by_course(int $studentid): array {
        $courses = \local_hrdepartment\student_attendance_manager::get_student_courses($studentid);

        $result = [];
        foreach ($courses as $course) {
            $course->statuses = \local_hrdepartment\student_attendance_manager::get_student_status_summary(
                $studentid,
                (int) $course->id
            );
            $result[] = $course;
        }

        return $result;
    }

    /**
     * Rolls the aggregated arrays above into the handful of headline
     * numbers the view's KPI stat-card row shows. Deliberately does NOT
     * attempt an "attendance %" figure here - mod_attendance's
     * present/absent/late acronyms are per-site configurable (no
     * "maxgrade" info comes back from
     * student_attendance_manager::get_student_status_summary()), so a
     * guessed P=present mapping could silently mislead a viewer. Total
     * sessions recorded is shown instead; the real per-course
     * acronym/description breakdown is rendered in full further down
     * the page.
     *
     * Request status strings are the literal DB values documented in
     * local_financedepartment's own db/install.xml column comments
     * (pending|approved|rejected|deleted), matching
     * \local_financedepartment\constants::REQUEST_STATUS_*.
     *
     * @param \stdClass $profile the profile object being built (already
     *                           has feerecords/scholarshiprequests/
     *                           discountrequests/attendancebycourse set)
     * @return \stdClass totalbalance, totalpaid, activescholarships, pendingrequests, totalsessions
     */
    protected static function build_summary(\stdClass $profile): \stdClass {
        $summary = new \stdClass();

        $totalbalance = 0.0;
        $totalpaid = 0.0;
        foreach ($profile->feerecords as $feerecord) {
            $totalbalance += (float) $feerecord->balance;
            $totalpaid += (float) $feerecord->paidamount;
        }
        $summary->totalbalance = $totalbalance;
        $summary->totalpaid = $totalpaid;

        $activescholarships = 0;
        $pendingrequests = 0;
        foreach ($profile->scholarshiprequests as $req) {
            if ($req->status === 'approved') {
                $activescholarships++;
            }
            if ($req->status === 'pending') {
                $pendingrequests++;
            }
        }
        foreach ($profile->discountrequests as $req) {
            if ($req->status === 'pending') {
                $pendingrequests++;
            }
        }
        $summary->activescholarships = $activescholarships;
        $summary->pendingrequests = $pendingrequests;

        $totalsessions = 0;
        foreach ($profile->attendancebycourse as $course) {
            foreach ($course->statuses as $row) {
                $totalsessions += (int) $row->total;
            }
        }
        $summary->totalsessions = $totalsessions;

        return $summary;
    }

    /**
     * Runs $callback, returning $default and logging a debugging notice
     * if it throws - see this class's own docblock for why.
     *
     * @param callable $callback
     * @param mixed $default
     * @return mixed
     */
    protected static function safe(callable $callback, $default) {
        try {
            return $callback();
        } catch (\Throwable $e) {
            debugging('local_student360\\profile_manager: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return $default;
        }
    }
}
