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
 * CHANGED 2026-09-10 (v2026091004/0.8.0): the user reported that a
 * plain self-service student should NOT have to pick which fee record
 * a scholarship request applies to - the fee record field is gone
 * entirely from the submission form (see scholarshiprequest_form.php).
 * Confirmed via AskUserQuestion: (1) `financedep_scholarshipreq.feerecordid`
 * is now NULLABLE (db/upgrade.php v2026091004) and always NULL for a
 * request submitted through this form going forward; (2) the
 * program/category restriction that used to be enforced via
 * is_eligible() (matching the fee record's category against the
 * scholarship's categoryid) is REMOVED entirely - any ACTIVE scholarship
 * may be requested by any student holding submitscholarshiprequest, and
 * finance staff use their own judgement when reviewing; (3) approve()
 * no longer calls feerecord_manager::add_scholarship_amount() for a
 * NEW-style request (feerecordid null) - approval is now a pure history/
 * decision record, no automated balance effect. delete() mirrors this:
 * it only reverses a balance deduction if one was actually made.
 *
 * BACKWARD COMPATIBILITY: a request submitted BEFORE this change still
 * has a real feerecordid and, if approved, really did deduct from that
 * fee record's balance via add_scholarship_amount(). approve()/delete()
 * both branch on `!empty($before->feerecordid)` so those legacy rows
 * keep behaving exactly as before (deduct on approve, restore on
 * delete) - only NEW requests (feerecordid null) skip the fee-record
 * side effect entirely. Do not remove this branching without checking
 * for legacy rows with a non-null feerecordid first.
 *
 * is_eligible() (the old category-matching check) has been REMOVED -
 * do not reintroduce it without a fresh AskUserQuestion decision, since
 * removing it was an explicit, deliberate user choice, not an oversight.
 */
class scholarshiprequest_manager {

    /**
     * Whether a PENDING or already-APPROVED request exists for this
     * student + scholarship pair. Blocks two different problems: a
     * duplicate submission piling up in the approval queue (PENDING),
     * and the same scholarship being requested by the same student
     * twice in a row. A REJECTED request deliberately does NOT block a
     * new submission - rejection might just mean the first
     * justification was weak, and the student should be able to
     * resubmit with a better one.
     *
     * CHANGED 2026-09-10 (v2026091004/0.8.0): originally keyed on
     * (feerecordid, scholarshipid) - now keyed on (studentid,
     * scholarshipid) since a request is no longer tied to a specific
     * fee record at all. (Before that, originally only checked PENDING -
     * broadened 2026-08-24 after the user reported the same pairing
     * could be submitted more than once even after approval - that
     * broadening is preserved here.)
     *
     * @param int $studentid
     * @param int $scholarshipid
     * @return bool
     */
    public static function has_pending_request(int $studentid, int $scholarshipid): bool {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal(
            [constants::REQUEST_STATUS_PENDING, constants::REQUEST_STATUS_APPROVED],
            SQL_PARAMS_NAMED
        );

        $params = array_merge([
            'studentid' => $studentid,
            'scholarshipid' => $scholarshipid,
        ], $inparams);

        return $DB->record_exists_select(
            'financedep_scholarshipreq',
            "studentid = :studentid AND scholarshipid = :scholarshipid AND status $insql",
            $params
        );
    }

    /**
     * Whether $studentid has already made a real payment (paidamount >
     * 0, on any non-CANCELLED fee record) against $categoryid - the
     * course category a scholarship belongs to (financedep_scholarship.
     * categoryid). Added 2026-09-10 per the user's explicit request: once
     * a student has started paying for a program, they should no longer
     * be able to request a scholarship against that same program - a
     * scholarship is meant to reduce what is owed BEFORE payment, not
     * refund what has already been paid (that is what a refund/Step 7.7
     * is for, a completely separate workflow with its own approval-free,
     * direct-entry shape - see feepayment_manager's docblock).
     *
     * Deliberately keyed on the CATEGORY, not a specific fee record - a
     * scholarship request no longer carries a feerecordid at all as of
     * v2026091004/0.8.0 (see this class's own docblock), so this checks
     * every fee record the student holds in the same category as the
     * scholarship, not just one. A CANCELLED fee record is excluded (a
     * cancelled assignment is not a real, current obligation - matches
     * feerecord_manager::has_active_assignment()'s same exclusion).
     *
     * @param int $studentid
     * @param int $categoryid a financedep_scholarship.categoryid value
     * @return bool
     */
    public static function has_paid_in_category(int $studentid, int $categoryid): bool {
        global $DB;

        $sql = "SELECT 1
                  FROM {financedep_feerecord} r
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                 WHERE r.studentid = :studentid
                   AND f.categoryid = :categoryid
                   AND r.status <> :cancelled
                   AND r.paidamount > 0";

        return $DB->record_exists_sql($sql, [
            'studentid' => $studentid,
            'categoryid' => $categoryid,
            'cancelled' => constants::FEE_STATUS_CANCELLED,
        ]);
    }

    /**
     * Computes the suggested requestedamount for a scholarship: the
     * scholarship's fixed MMK value as-is, or null for a percentage-type
     * scholarship - a percentage needs a base amount (a fee record's
     * totalamount) to compute against, and requests no longer carry a
     * fee record (see this class's own docblock, v2026091004/0.8.0).
     * A null requestedamount means "not yet known" everywhere it's
     * displayed (table/view/history rendering all check for this) - the
     * reviewer still types a real amountapproved value manually on
     * approval regardless.
     *
     * @param \stdClass $scholarship a financedep_scholarship row
     * @return float|null
     */
    public static function compute_suggested_amount(\stdClass $scholarship): ?float {
        if ($scholarship->amounttype === constants::AMOUNT_TYPE_PERCENTAGE) {
            return null;
        }

        return (float) $scholarship->amountvalue;
    }

    /**
     * Returns one scholarship request with student and scholarship
     * details joined in, or false if not found. The fee record/category
     * join is now a LEFT JOIN (v2026091004/0.8.0) since feerecordid is
     * nullable going forward - categoryname/academicyear come back null
     * for a request with no linked fee record; callers must handle that.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT q.*, u.firstname, u.lastname, u.email,
                       s.name AS scholarshipname, s.amounttype, s.amountvalue, s.status AS scholarshipstatus,
                       fs.academicyear, fs.categoryid, cc.name AS categoryname
                  FROM {financedep_scholarshipreq} q
                  JOIN {user} u ON u.id = q.studentid
                  JOIN {financedep_scholarship} s ON s.id = q.scholarshipid
             LEFT JOIN {financedep_feerecord} r ON r.id = q.feerecordid
             LEFT JOIN {financedep_feestructure} fs ON fs.id = r.feestructureid
             LEFT JOIN {course_categories} cc ON cc.id = fs.categoryid
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
     * newest first. Includes the scholarship's amounttype/amountvalue
     * (added 2026-09-10) so a null requestedamount (percentage-type, see
     * compute_suggested_amount()'s docblock) can still be rendered
     * meaningfully by callers.
     *
     * @param int $studentid
     * @return \stdClass[]
     */
    public static function get_for_student(int $studentid): array {
        global $DB;

        $sql = "SELECT q.*, s.name AS scholarshipname, s.amounttype, s.amountvalue
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
     * Submits a new scholarship request. Does NOT touch any fee record's
     * balance - only approve() does that, and only for a legacy request
     * that still has a real feerecordid (see this class's own docblock,
     * v2026091004/0.8.0). feerecordid is always stored NULL here going
     * forward - the field was removed from the submission form entirely.
     *
     * @param \stdClass $data form data: studentid, scholarshipid, justification
     * @param int $requestedby
     * @return int the new request id
     */
    public static function submit(\stdClass $data, int $requestedby): int {
        global $DB;

        $scholarship = $DB->get_record('financedep_scholarship', ['id' => (int) $data->scholarshipid], '*', MUST_EXIST);

        $now = time();

        $record = new \stdClass();
        $record->studentid = (int) $data->studentid;
        $record->feerecordid = null;
        $record->scholarshipid = (int) $data->scholarshipid;
        $record->requestedamount = self::compute_suggested_amount($scholarship);
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
            [
                'status' => $record->status,
                'requestedamount' => $record->requestedamount,
                'amounttype' => $scholarship->amounttype,
                'amountvalue' => $scholarship->amountvalue,
            ],
            $requestedby
        );

        return $id;
    }

    /**
     * Approves a pending scholarship request: records the final
     * (possibly overridden) approved amount.
     *
     * CHANGED 2026-09-10 (v2026091004/0.8.0): approval is now a pure
     * history/decision record for any request submitted through the
     * current form - it does NOT touch any fee record's balance,
     * per the user's explicit choice (see this class's own docblock).
     * feerecord_manager::add_scholarship_amount() is only still called
     * for a LEGACY request that has a real (non-null) feerecordid, so
     * pre-existing approved-and-deducted requests keep behaving exactly
     * as before. If finance staff want an approved scholarship to
     * actually reduce a student's balance, they now do that manually via
     * pages/feerecords/edit.php - this method no longer does it for them.
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

        // Deactivated-scholarship guard, added 2026-09-06 per the
        // user's explicit request (mirrors the same fix built into
        // discountrequest_manager::approve() the same day): refuse
        // (silent no-op) if the scholarship this request references has
        // since been deactivated. Deactivating a scholarship
        // (scholarship_manager::set_status()) never cascades to
        // existing financedep_scholarshipreq rows, so a PENDING request
        // against a now-inactive scholarship would otherwise still
        // approve normally. This is defense-in-depth only - the
        // primary, user-facing gate is in
        // pages/scholarshiprequests/review.php.
        $scholarship = $DB->get_record('financedep_scholarship', ['id' => $before->scholarshipid]);
        if (!$scholarship || $scholarship->status !== constants::SCHOLARSHIP_STATUS_ACTIVE) {
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

        // Legacy-only: a request submitted before v2026091004/0.8.0
        // still has a real feerecordid and expects the old auto-deduct
        // behaviour - see this method's own docblock above. A new-style
        // request (feerecordid null) skips this entirely.
        if (!empty($before->feerecordid)) {
            feerecord_manager::add_scholarship_amount((int) $before->feerecordid, $approvedamount, $reviewedby);
        }

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
     * If the request was APPROVED AND has a real (legacy) feerecordid,
     * its approvedamount was already deducted from that fee record's
     * balance via approve() -> feerecord_manager::add_scholarship_amount().
     * Deleting it reverses that deduction the same way (a NEGATIVE
     * amount - see that method's own docblock). CHANGED 2026-09-10
     * (v2026091004/0.8.0): a new-style approved request (feerecordid
     * null) never touched any fee record on approval in the first place
     * (see approve()'s docblock), so there is nothing to reverse -
     * deleting one just marks it DELETED with no balance side effect.
     * A PENDING or REJECTED request never touched the fee record either
     * way, so no reversal is needed for those regardless of feerecordid.
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

        if ($before->status === constants::REQUEST_STATUS_APPROVED && $before->approvedamount !== null
                && !empty($before->feerecordid)) {
            $reversedamount = (float) $before->approvedamount;
            feerecord_manager::add_scholarship_amount((int) $before->feerecordid, -$reversedamount, $userid);
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
