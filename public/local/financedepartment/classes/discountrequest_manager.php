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
 * Business logic for manual/hardship discount requests and their
 * approval workflow (Step 7.5).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class discountrequest_manager
 *
 * A discount request is submitted against one specific fee record
 * (financedep_feerecord), same shape as scholarshiprequest_manager.
 * Unlike a scholarship request, is_eligible() does NOT check a category
 * match - a discount has no categoryid to match against (see
 * discount_manager's docblock) - it only confirms the discount is
 * ACTIVE and the fee record exists and hasn't been cancelled.
 *
 * Built 2026-09-06 with the requestedby !== reviewedby self-approval
 * guard included FROM THE START in approve()/reject() below, rather
 * than added later as a post-deploy fix - the user explicitly asked for
 * this (2026-09-06 AskUserQuestion) after the same gap had to be
 * patched into scholarshiprequest_manager the same day. See that
 * class's approve()/reject() docblocks and [[financedepartment-schema]]
 * project memory for the full history of why this check exists.
 *
 * Deliberately has NO delete() method yet, unlike
 * scholarshiprequest_manager::delete() - the user was not asked about a
 * discount-request delete feature this round; see
 * [[financedepartment-schema]] project memory's open question on
 * whether REQUEST_STATUS_DELETED should ever apply to discount
 * requests too.
 *
 * Approval auto-applies via feerecord_manager::add_discount_amount()
 * rather than writing to financedep_feerecord directly - see that
 * method's docblock for why balance/status recalculation is centralised
 * there.
 */
class discountrequest_manager {

    /**
     * Whether $discountid may be requested against $feerecordid: the
     * discount must be ACTIVE, and the fee record must exist and not be
     * cancelled. No category match is enforced (unlike
     * scholarshiprequest_manager::is_eligible()) - a discount applies
     * system-wide, see discount_manager's docblock.
     *
     * @param int $discountid
     * @param int $feerecordid
     * @return bool
     */
    public static function is_eligible(int $discountid, int $feerecordid): bool {
        global $DB;

        $discount = $DB->get_record('financedep_discount', ['id' => $discountid]);
        if (!$discount || $discount->status !== constants::DISCOUNT_STATUS_ACTIVE) {
            return false;
        }

        $feerecord = $DB->get_record('financedep_feerecord', ['id' => $feerecordid]);
        if (!$feerecord || $feerecord->status === constants::FEE_STATUS_CANCELLED) {
            return false;
        }

        return true;
    }

    /**
     * Whether a PENDING or already-APPROVED request exists for this
     * exact fee record + discount pair - same duplicate-prevention
     * reasoning as scholarshiprequest_manager::has_pending_request().
     *
     * @param int $feerecordid
     * @param int $discountid
     * @return bool
     */
    public static function has_pending_request(int $feerecordid, int $discountid): bool {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal(
            [constants::REQUEST_STATUS_PENDING, constants::REQUEST_STATUS_APPROVED],
            SQL_PARAMS_NAMED
        );

        $params = array_merge([
            'feerecordid' => $feerecordid,
            'discountid' => $discountid,
        ], $inparams);

        return $DB->record_exists_select(
            'financedep_discountreq',
            "feerecordid = :feerecordid AND discountid = :discountid AND status $insql",
            $params
        );
    }

    /**
     * Computes the suggested requestedamount for a discount against a
     * fee record: the discount's fixed MMK value, or a percentage of
     * the fee record's totalamount. Only a starting suggestion -
     * approve() lets the reviewer override it.
     *
     * @param \stdClass $discount a financedep_discount row
     * @param \stdClass $feerecord a financedep_feerecord row
     * @return float
     */
    public static function compute_suggested_amount(\stdClass $discount, \stdClass $feerecord): float {
        if ($discount->amounttype === constants::AMOUNT_TYPE_PERCENTAGE) {
            return round(((float) $discount->amountvalue / 100) * (float) $feerecord->totalamount, 2);
        }

        return (float) $discount->amountvalue;
    }

    /**
     * Returns one discount request with student, fee record, and
     * discount details joined in, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT q.*, u.firstname, u.lastname, u.email,
                       d.name AS discountname, d.type AS discounttype, d.amounttype, d.amountvalue,
                       fs.academicyear, cc.name AS categoryname
                  FROM {financedep_discountreq} q
                  JOIN {user} u ON u.id = q.studentid
                  JOIN {financedep_discount} d ON d.id = q.discountid
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
     * Returns every discount request submitted for one student, newest
     * first.
     *
     * @param int $studentid
     * @return \stdClass[]
     */
    public static function get_for_student(int $studentid): array {
        global $DB;

        $sql = "SELECT q.*, d.name AS discountname
                  FROM {financedep_discountreq} q
                  JOIN {financedep_discount} d ON d.id = q.discountid
                 WHERE q.studentid = :studentid
              ORDER BY q.timecreated DESC";

        return array_values($DB->get_records_sql($sql, ['studentid' => $studentid]));
    }

    /**
     * Returns the oldest PENDING requests, for the default review-queue
     * view - oldest first.
     *
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_pending(int $limit = 20): array {
        global $DB;

        $sql = "SELECT q.*, u.firstname, u.lastname, u.email, d.name AS discountname
                  FROM {financedep_discountreq} q
                  JOIN {user} u ON u.id = q.studentid
                  JOIN {financedep_discount} d ON d.id = q.discountid
                 WHERE q.status = :status
              ORDER BY q.timecreated ASC";

        $records = $DB->get_records_sql($sql, ['status' => constants::REQUEST_STATUS_PENDING], 0, $limit);

        foreach ($records as $record) {
            $record->fullname = fullname($record);
        }

        return array_values($records);
    }

    /**
     * Submits a new manual/hardship discount request. Does NOT touch
     * the fee record's balance - only approve() does that.
     *
     * @param \stdClass $data form data: studentid, feerecordid, discountid, justification
     * @param int $requestedby
     * @return int the new request id
     */
    public static function submit(\stdClass $data, int $requestedby): int {
        global $DB;

        $discount = $DB->get_record('financedep_discount', ['id' => (int) $data->discountid], '*', MUST_EXIST);
        $feerecord = $DB->get_record('financedep_feerecord', ['id' => (int) $data->feerecordid], '*', MUST_EXIST);

        $now = time();

        $record = new \stdClass();
        $record->studentid = (int) $data->studentid;
        $record->feerecordid = (int) $data->feerecordid;
        $record->discountid = (int) $data->discountid;
        $record->requestedamount = self::compute_suggested_amount($discount, $feerecord);
        $record->approvedamount = null;
        $record->status = constants::REQUEST_STATUS_PENDING;
        $record->justification = $data->justification ?? '';
        $record->requestedby = $requestedby;
        $record->reviewedby = null;
        $record->reviewnote = null;
        $record->reviewedat = null;
        $record->timecreated = $now;
        $record->timemodified = $now;

        $id = $DB->insert_record('financedep_discountreq', $record);

        audit_manager::log(
            constants::AUDIT_ENTITY_DISCOUNTREQUEST,
            $id,
            constants::AUDIT_ACTION_CREATE,
            null,
            ['status' => $record->status, 'requestedamount' => $record->requestedamount],
            $requestedby
        );

        return $id;
    }

    /**
     * Approves a pending discount request: records the final (possibly
     * overridden) approved amount, and auto-applies it to the fee
     * record via feerecord_manager::add_discount_amount().
     *
     * Refuses (silent no-op) if $reviewedby is the same user who
     * submitted the request - built in from day one (2026-09-06), see
     * this class's docblock. This check is defense-in-depth: the
     * primary, user-facing gate is in pages/discountrequests/review.php,
     * which checks this BEFORE rendering the review form so the user
     * gets a clear error message rather than an approve/reject that
     * silently did nothing.
     *
     * @param int $id
     * @param float $approvedamount MMK, may differ from the original requestedamount
     * @param string $reviewnote
     * @param int $reviewedby
     * @return void
     */
    public static function approve(int $id, float $approvedamount, string $reviewnote, int $reviewedby): void {
        global $DB;

        $before = $DB->get_record('financedep_discountreq', ['id' => $id], '*', MUST_EXIST);
        if ($before->status !== constants::REQUEST_STATUS_PENDING) {
            return;
        }
        if ((int) $before->requestedby === $reviewedby) {
            return;
        }

        $now = time();

        $DB->update_record('financedep_discountreq', (object) [
            'id' => $id,
            'status' => constants::REQUEST_STATUS_APPROVED,
            'approvedamount' => $approvedamount,
            'reviewedby' => $reviewedby,
            'reviewnote' => $reviewnote !== '' ? $reviewnote : null,
            'reviewedat' => $now,
            'timemodified' => $now,
        ]);

        feerecord_manager::add_discount_amount($before->feerecordid, $approvedamount, $reviewedby);

        audit_manager::log(
            constants::AUDIT_ENTITY_DISCOUNTREQUEST,
            $id,
            constants::AUDIT_ACTION_APPROVE,
            ['status' => constants::REQUEST_STATUS_PENDING],
            ['status' => constants::REQUEST_STATUS_APPROVED, 'approvedamount' => $approvedamount],
            $reviewedby,
            $reviewnote
        );
    }

    /**
     * Rejects a pending discount request. Never touches the fee record -
     * nothing was applied, so there's nothing to reverse.
     *
     * Refuses (silent no-op) if $reviewedby is the same user who
     * submitted the request - see approve()'s docblock for the full
     * explanation; the primary user-facing gate is in
     * pages/discountrequests/review.php.
     *
     * @param int $id
     * @param string $reviewnote
     * @param int $reviewedby
     * @return void
     */
    public static function reject(int $id, string $reviewnote, int $reviewedby): void {
        global $DB;

        $before = $DB->get_record('financedep_discountreq', ['id' => $id], '*', MUST_EXIST);
        if ($before->status !== constants::REQUEST_STATUS_PENDING) {
            return;
        }
        if ((int) $before->requestedby === $reviewedby) {
            return;
        }

        $now = time();

        $DB->update_record('financedep_discountreq', (object) [
            'id' => $id,
            'status' => constants::REQUEST_STATUS_REJECTED,
            'reviewedby' => $reviewedby,
            'reviewnote' => $reviewnote !== '' ? $reviewnote : null,
            'reviewedat' => $now,
            'timemodified' => $now,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_DISCOUNTREQUEST,
            $id,
            constants::AUDIT_ACTION_REJECT,
            ['status' => constants::REQUEST_STATUS_PENDING],
            ['status' => constants::REQUEST_STATUS_REJECTED],
            $reviewedby,
            $reviewnote
        );
    }
}
