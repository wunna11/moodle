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
 * Business logic for scholarship requests/nominations and their
 * approval workflow (Step 7.4).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class scholarshiprequest_manager
 *
 * A scholarship request/nomination is submitted against one specific fee
 * record (financedep_feerecord), not just "a student" - a student can
 * hold several fee records (Step 7.3), so the request has to say which
 * one the scholarship applies to. is_eligible() is the program-level
 * restriction enforcement point: a scholarship can only be requested
 * against a fee record whose fee structure's category matches the
 * scholarship's own categoryid (see scholarship_manager's docblock for
 * why this replaced the original "scholarship type" classification).
 *
 * Approval auto-deducts via feerecord_manager::add_scholarship_amount()
 * rather than writing to financedep_feerecord directly - see that
 * method's docblock for why balance/status recalculation is centralised
 * there.
 */
class scholarshiprequest_manager {

    /**
     * Whether $scholarshipid may be requested against $feerecordid: the
     * scholarship must be ACTIVE, and its categoryid must match the fee
     * record's own category (via its fee structure). This is the actual
     * "which program has a scholarship" enforcement point - see
     * scholarship_manager's class docblock.
     *
     * @param int $scholarshipid
     * @param int $feerecordid
     * @return bool
     */
    public static function is_eligible(int $scholarshipid, int $feerecordid): bool {
        global $DB;

        $sql = "SELECT 1
                  FROM {financedep_scholarship} s
                  JOIN {financedep_feerecord} r ON r.id = :feerecordid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                 WHERE s.id = :scholarshipid
                   AND s.status = :status
                   AND s.categoryid = f.categoryid";

        return $DB->record_exists_sql($sql, [
            'scholarshipid' => $scholarshipid,
            'feerecordid' => $feerecordid,
            'status' => constants::SCHOLARSHIP_STATUS_ACTIVE,
        ]);
    }

    /**
     * Whether a PENDING or already-APPROVED request exists for this
     * exact fee record + scholarship pair. Blocks two different
     * problems: a duplicate nomination piling up in the approval queue
     * (PENDING), and the same scholarship being applied to the same fee
     * record twice - once approved, `feerecord_manager::add_scholarship_amount()`
     * has already deducted it, so approving a second identical request
     * would double-deduct. A REJECTED request deliberately does NOT
     * block a new submission - rejection might just mean the first
     * justification was weak, and the student should be able to be
     * re-nominated with a better one.
     *
     * (Originally only checked PENDING - broadened 2026-08-24 after the
     * user reported the same fee record/scholarship pair could be
     * submitted more than once even after approval.)
     *
     * @param int $feerecordid
     * @param int $scholarshipid
     * @return bool
     */
    public static function has_pending_request(int $feerecordid, int $scholarshipid): bool {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal(
            [constants::REQUEST_STATUS_PENDING, constants::REQUEST_STATUS_APPROVED],
            SQL_PARAMS_NAMED
        );

        $params = array_merge([
            'feerecordid' => $feerecordid,
            'scholarshipid' => $scholarshipid,
        ], $inparams);

        return $DB->record_exists_select(
            'financedep_scholarshipreq',
            "feerecordid = :feerecordid AND scholarshipid = :scholarshipid AND status $insql",
            $params
        );
    }

    /**
     * Computes the suggested requestedamount for a scholarship against a
     * fee record: the scholarship's fixed MMK value, or a percentage of
     * the fee record's totalamount. This is only a starting suggestion -
     * approve() lets the reviewer override it.
     *
     * @param \stdClass $scholarship a financedep_scholarship row
     * @param \stdClass $feerecord a financedep_feerecord row
     * @return float
     */
    public static function compute_suggested_amount(\stdClass $scholarship, \stdClass $feerecord): float {
        if ($scholarship->amounttype === constants::AMOUNT_TYPE_PERCENTAGE) {
            return round(((float) $scholarship->amountvalue / 100) * (float) $feerecord->totalamount, 2);
        }

        return (float) $scholarship->amountvalue;
    }

    /**
     * Returns one scholarship request with student, fee record, and
     * scholarship details joined in, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT q.*, u.firstname, u.lastname, u.email,
                       s.name AS scholarshipname, s.amounttype, s.amountvalue,
                       fs.academicyear, fs.categoryid, cc.name AS categoryname
                  FROM {financedep_scholarshipreq} q
                  JOIN {user} u ON u.id = q.studentid
                  JOIN {financedep_scholarship} s ON s.id = q.scholarshipid
                  JOIN {financedep_feerecord} r ON r.id = q.feerecordid
                  JOIN {financedep_feestructure} fs ON fs.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = fs.categoryid
                 WHERE q.id = :id";

        $record = $DB->get_record_sql($sql, ['id' => $id]);
        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);

        return $record;
    }

    /**
     * Returns every scholarship request submitted for one student,
     * newest first.
     *
     * @param int $studentid
     * @return \stdClass[]
     */
    public static function get_for_student(int $studentid): array {
        global $DB;

        $sql = "SELECT q.*, s.name AS scholarshipname
                  FROM {financedep_scholarshipreq} q
                  JOIN {financedep_scholarship} s ON s.id = q.scholarshipid
                 WHERE q.studentid = :studentid
              ORDER BY q.timecreated DESC";

        return array_values($DB->get_records_sql($sql, ['studentid' => $studentid]));
    }

    /**
     * Returns the oldest PENDING requests, for the default review-queue
     * view - oldest first, so nothing waits forever unnoticed.
     *
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_pending(int $limit = 20): array {
        global $DB;

        $sql = "SELECT q.*, u.firstname, u.lastname, u.email, s.name AS scholarshipname
                  FROM {financedep_scholarshipreq} q
                  JOIN {user} u ON u.id = q.studentid
                  JOIN {financedep_scholarship} s ON s.id = q.scholarshipid
                 WHERE q.status = :status
              ORDER BY q.timecreated ASC";

        $records = $DB->get_records_sql($sql, ['status' => constants::REQUEST_STATUS_PENDING], 0, $limit);

        foreach ($records as $record) {
            $record->fullname = fullname($record);
        }

        return array_values($records);
    }

    /**
     * Submits a new scholarship request/nomination. Does NOT touch the
     * fee record's balance - only approve() does that.
     *
     * @param \stdClass $data form data: studentid, feerecordid, scholarshipid, justification
     * @param int $requestedby
     * @return int the new request id
     */
    public static function submit(\stdClass $data, int $requestedby): int {
        global $DB;

        $scholarship = $DB->get_record('financedep_scholarship', ['id' => (int) $data->scholarshipid], '*', MUST_EXIST);
        $feerecord = $DB->get_record('financedep_feerecord', ['id' => (int) $data->feerecordid], '*', MUST_EXIST);

        $now = time();

        $record = new \stdClass();
        $record->studentid = (int) $data->studentid;
        $record->feerecordid = (int) $data->feerecordid;
        $record->scholarshipid = (int) $data->scholarshipid;
        $record->requestedamount = self::compute_suggested_amount($scholarship, $feerecord);
        $record->approvedamount = null;
        $record->status = constants::REQUEST_STATUS_PENDING;
        $record->justification = $data->justification ?? '';
        $record->requestedby = $requestedby;
        $record->reviewedby = null;
        $record->reviewnote = null;
        $record->reviewedat = null;
        $record->timecreated = $now;
        $record->timemodified = $now;

        $id = $DB->insert_record('financedep_scholarshipreq', $record);

        audit_manager::log(
            constants::AUDIT_ENTITY_SCHOLARSHIPREQUEST,
            $id,
            constants::AUDIT_ACTION_CREATE,
            null,
            ['status' => $record->status, 'requestedamount' => $record->requestedamount],
            $requestedby
        );

        return $id;
    }

    /**
     * Approves a pending scholarship request: records the final
     * (possibly overridden) approved amount, and auto-deducts it from
     * the fee record via feerecord_manager::add_scholarship_amount() -
     * Step 7.4's "auto-deduct the approved scholarship amount from the
     * student's fee record" requirement.
     *
     * Refuses (silent no-op) if $reviewedby is the same user who
     * submitted the request - the self-approval fix requested by the
     * user 2026-09-06 (see [[financedepartment-schema]] project
     * memory's former KNOWN GAP note). This check is defense-in-depth:
     * the primary, user-facing gate is in pages/scholarshiprequests/review.php,
     * which checks this BEFORE rendering the review form so the user
     * gets a clear error message rather than a silent no-op here.
     *
     * @param int $id
     * @param float $approvedamount MMK, may differ from the original requestedamount
     * @param string $reviewnote
     * @param int $reviewedby
     * @return void
     */
    public static function approve(int $id, float $approvedamount, string $reviewnote, int $reviewedby): void {
        global $DB;

        $before = $DB->get_record('financedep_scholarshipreq', ['id' => $id], '*', MUST_EXIST);
        if ($before->status !== constants::REQUEST_STATUS_PENDING) {
            return;
        }
        if ((int) $before->requestedby === $reviewedby) {
            return;
        }

        $now = time();

        $DB->update_record('financedep_scholarshipreq', (object) [
            'id' => $id,
            'status' => constants::REQUEST_STATUS_APPROVED,
            'approvedamount' => $approvedamount,
            'reviewedby' => $reviewedby,
            'reviewnote' => $reviewnote !== '' ? $reviewnote : null,
            'reviewedat' => $now,
            'timemodified' => $now,
        ]);

        feerecord_manager::add_scholarship_amount($before->feerecordid, $approvedamount, $reviewedby);

        audit_manager::log(
            constants::AUDIT_ENTITY_SCHOLARSHIPREQUEST,
            $id,
            constants::AUDIT_ACTION_APPROVE,
            ['status' => constants::REQUEST_STATUS_PENDING],
            ['status' => constants::REQUEST_STATUS_APPROVED, 'approvedamount' => $approvedamount],
            $reviewedby,
            $reviewnote
        );
    }

    /**
     * Rejects a pending scholarship request. Never touches the fee
     * record - nothing was deducted, so there's nothing to reverse.
     *
     * Refuses (silent no-op) if $reviewedby is the same user who
     * submitted the request - see approve()'s docblock for the full
     * explanation; the primary user-facing gate is in
     * pages/scholarshiprequests/review.php.
     *
     * @param int $id
     * @param string $reviewnote
     * @param int $reviewedby
     * @return void
     */
    public static function reject(int $id, string $reviewnote, int $reviewedby): void {
        global $DB;

        $before = $DB->get_record('financedep_scholarshipreq', ['id' => $id], '*', MUST_EXIST);
        if ($before->status !== constants::REQUEST_STATUS_PENDING) {
            return;
        }
        if ((int) $before->requestedby === $reviewedby) {
            return;
        }

        $now = time();

        $DB->update_record('financedep_scholarshipreq', (object) [
            'id' => $id,
            'status' => constants::REQUEST_STATUS_REJECTED,
            'reviewedby' => $reviewedby,
            'reviewnote' => $reviewnote !== '' ? $reviewnote : null,
            'reviewedat' => $now,
            'timemodified' => $now,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_SCHOLARSHIPREQUEST,
            $id,
            constants::AUDIT_ACTION_REJECT,
            ['status' => constants::REQUEST_STATUS_PENDING],
            ['status' => constants::REQUEST_STATUS_REJECTED],
            $reviewedby,
            $reviewnote
        );
    }

    /**
     * Soft-deletes a scholarship request (added 2026-08-24 per user
     * request). The row is never physically removed - only its status
     * changes to DELETED - so it stays out of the normal list
     * (scholarshiprequest_table always excludes DELETED rows) while
     * remaining visible via a direct view.php?id= link and kept in
     * financedep_auditlog for a full history.
     *
     * If the request was APPROVED, its approvedamount has already been
     * deducted from the fee record's balance via approve() ->
     * feerecord_manager::add_scholarship_amount(). Deleting it reverses
     * that deduction by calling add_scholarship_amount() again with a
     * NEGATIVE amount (that method's docblock already documents this as
     * the supported way to reverse a scholarship amount), restoring the
     * fee record's balance to what it would have been had this request
     * never been approved. A PENDING or REJECTED request never touched
     * the fee record, so no reversal is needed for those.
     *
     * Idempotent - deleting an already-deleted request is a no-op.
     *
     * @param int $id
     * @param int $userid the finance-staff user performing the delete
     * @return void
     */
    public static function delete(int $id, int $userid): void {
        global $DB;

        $before = $DB->get_record('financedep_scholarshipreq', ['id' => $id], '*', MUST_EXIST);
        if ($before->status === constants::REQUEST_STATUS_DELETED) {
            return;
        }

        $now = time();
        $reversedamount = null;

        if ($before->status === constants::REQUEST_STATUS_APPROVED && $before->approvedamount !== null) {
            $reversedamount = (float) $before->approvedamount;
            feerecord_manager::add_scholarship_amount($before->feerecordid, -$reversedamount, $userid);
        }

        $DB->update_record('financedep_scholarshipreq', (object) [
            'id' => $id,
            'status' => constants::REQUEST_STATUS_DELETED,
            'timemodified' => $now,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_SCHOLARSHIPREQUEST,
            $id,
            constants::AUDIT_ACTION_DELETE,
            ['status' => $before->status],
            ['status' => constants::REQUEST_STATUS_DELETED, 'restoredamount' => $reversedamount],
            $userid
        );
    }
}
