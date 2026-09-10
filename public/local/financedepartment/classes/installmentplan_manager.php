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
 * Business logic for installment plans (Step 7.6).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class installmentplan_manager
 *
 * An installment plan is a payment SCHEDULE for one fee record, not a
 * financial transaction - creating, editing, or cancelling a plan never
 * touches financedep_feerecord's totalamount/scholarshipamount/
 * discountamount/paidamount/balance/status in any way (unlike
 * scholarshiprequest_manager::approve()/discountrequest_manager::approve(),
 * which do move real money via feerecord_manager::add_*_amount()). Actual
 * payments against an installment (financedep_feepayment.installmentschedid)
 * are Step 7.7's job - this step only lets finance staff lay out how many
 * installments a student's balance will be split into and when each one
 * is due.
 *
 * 2026-09-06/09-08 scope decisions confirmed with the user before
 * building (see [[financedepartment-schema]] project memory for the full
 * discussion): (1) each installment's amount and due date is entered
 * MANUALLY by finance staff (not auto-split evenly across N
 * installments) - create()/reschedule() both validate that the entered
 * amounts sum to the fee record's CURRENT balance at the time of the
 * call, so nothing is left unaccounted for. (2) "overdue" is a LIVE,
 * COMPUTED display state, never a persisted one - display_status() below
 * derives it from duedate + status at render time; no cron/scheduled
 * task exists in this plugin to sweep the database and flip a row's
 * status column to constants::INSTALLMENT_STATUS_OVERDUE. That constant
 * exists in classes/constants.php purely as a possible schedule-row
 * status value for a future cron-based implementation - this step's code
 * never writes it, and financedep_installmentsched.status should only
 * ever be found holding pending/partiallypaid/paid values today.
 *
 * financedep_installmentplan.feerecordid has a UNIQUE index (only one
 * plan row can EVER exist per fee record, not just one ACTIVE plan at a
 * time) - see db/install.xml. This means "create a new plan after
 * cancelling the old one" cannot be a second INSERT; create() below
 * detects an existing CANCELLED row for the same feerecordid and reuses
 * it (reactivates it with the new schedule) instead of inserting a
 * duplicate that would violate the unique index. A CREATE audit entry is
 * still logged in that case, since from the finance staff's perspective
 * a brand-new plan is being established even though the underlying row
 * id is reused for schema reasons.
 */
class installmentplan_manager {

    /**
     * The editable fields snapshotted into the audit log on create/edit,
     * and diffed to build the "what changed" newdata array on edit.
     *
     * @var string[]
     */
    const AUDITED_FIELDS = ['feerecordid', 'numberofinstallments', 'status'];

    /**
     * Returns one installment plan with its fee record/student/category
     * details joined in, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT p.*, r.studentid, r.totalamount, r.scholarshipamount, r.discountamount,
                       r.paidamount, r.balance AS feerecordbalance, r.status AS feerecordstatus,
                       u.firstname, u.lastname, u.email,
                       f.academicyear, cc.name AS categoryname
                  FROM {financedep_installmentplan} p
                  JOIN {financedep_feerecord} r ON r.id = p.feerecordid
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid
                 WHERE p.id = :id";

        $record = $DB->get_record_sql($sql, ['id' => $id]);
        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);

        return $record;
    }

    /**
     * Returns the installment plan for one fee record (there can be at
     * most one, ever - see this class's docblock), or false if none
     * exists yet.
     *
     * @param int $feerecordid
     * @return \stdClass|false
     */
    public static function get_for_feerecord(int $feerecordid) {
        global $DB;

        return $DB->get_record('financedep_installmentplan', ['feerecordid' => $feerecordid]);
    }

    /**
     * Returns the most recently created installment plans across every
     * student, with student/category details joined in - for
     * pages/installments/index.php's default (no student picked yet)
     * view, same "recent activity" panel pattern as
     * feerecord_manager::get_recent().
     *
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_recent(int $limit = 10): array {
        global $DB;

        $sql = "SELECT p.*, u.firstname, u.lastname, u.email,
                       f.academicyear, cc.name AS categoryname
                  FROM {financedep_installmentplan} p
                  JOIN {financedep_feerecord} r ON r.id = p.feerecordid
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid
              ORDER BY p.timecreated DESC";

        $records = $DB->get_records_sql($sql, [], 0, $limit);

        foreach ($records as $record) {
            $record->fullname = fullname($record);
        }

        return array_values($records);
    }

    /**
     * Whether a fee record already has an ACTIVE installment plan - the
     * duplicate guard used by installmentplan_form::validation(). A
     * CANCELLED plan does not block creating a new one (create() reuses
     * the cancelled row - see this class's docblock).
     *
     * @param int $feerecordid
     * @return bool
     */
    public static function has_active_plan(int $feerecordid): bool {
        global $DB;

        return $DB->record_exists('financedep_installmentplan', [
            'feerecordid' => $feerecordid,
            'status' => constants::INSTALLMENTPLAN_STATUS_ACTIVE,
        ]);
    }

    /**
     * Returns every schedule line for one plan, ordered by installment
     * number.
     *
     * @param int $planid
     * @return \stdClass[]
     */
    public static function get_schedule(int $planid): array {
        global $DB;

        return array_values($DB->get_records(
            'financedep_installmentsched',
            ['installmentplanid' => $planid],
            'installmentnumber ASC'
        ));
    }

    /**
     * Returns one schedule line by its own id, or false if not found -
     * added for Step 7.7 so feepayment_form/feepayment_manager can look
     * up a single linked installment without needing its plan id first.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get_schedule_row(int $id) {
        global $DB;

        return $DB->get_record('financedep_installmentsched', ['id' => $id]);
    }

    /**
     * Returns the schedule lines for one fee record's ACTIVE installment
     * plan (or an empty array if it has no plan, or only a CANCELLED
     * one) - added for Step 7.7 so feepayment_form can offer "link this
     * payment to installment #N" as an optional select without the
     * caller needing to know the plan id first.
     *
     * @param int $feerecordid
     * @return \stdClass[]
     */
    public static function get_schedule_for_feerecord(int $feerecordid): array {
        $plan = self::get_for_feerecord($feerecordid);
        if (!$plan || $plan->status !== constants::INSTALLMENTPLAN_STATUS_ACTIVE) {
            return [];
        }

        return self::get_schedule($plan->id);
    }

    /**
     * Whether any schedule line of a plan has a real payment applied to
     * it (paidamount > 0) - the guard shared by create()'s cancelled-row
     * reuse path and reschedule(), both of which otherwise delete and
     * reinsert every schedule row for a plan. Added for Step 7.7 exactly
     * as reschedule()'s own docblock (written back in Step 7.6) said it
     * must be once real payments existed - see that docblock, and note
     * that create()'s reuse path has the IDENTICAL risk for the same
     * reason (financedep_installmentplan.feerecordid's UNIQUE index
     * forces reuse-by-delete-and-reinsert rather than a fresh row).
     *
     * @param int $planid
     * @return bool
     */
    protected static function schedule_has_payments(int $planid): bool {
        global $DB;

        return $DB->record_exists_select(
            'financedep_installmentsched',
            'installmentplanid = :planid AND paidamount > 0',
            ['planid' => $planid]
        );
    }

    /**
     * Whether one schedule line is overdue right now: still
     * pending/partiallypaid AND its due date has passed. Computed live,
     * never persisted - see this class's docblock.
     *
     * @param \stdClass $schedrow a financedep_installmentsched row
     * @return bool
     */
    public static function is_overdue(\stdClass $schedrow): bool {
        $unsettled = in_array($schedrow->status, [
            constants::INSTALLMENT_STATUS_PENDING,
            constants::INSTALLMENT_STATUS_PARTIALLY_PAID,
        ], true);

        return $unsettled && (int) $schedrow->duedate < time();
    }

    /**
     * The status to actually DISPLAY for one schedule line: the same as
     * is_overdue()'s check, returning constants::INSTALLMENT_STATUS_OVERDUE
     * when it applies instead of the row's raw (never-overdue) persisted
     * status. Use this everywhere a schedule line's status is shown to a
     * user - never read $schedrow->status directly for display.
     *
     * @param \stdClass $schedrow a financedep_installmentsched row
     * @return string one of constants::INSTALLMENT_STATUS_*
     */
    public static function display_status(\stdClass $schedrow): string {
        return self::is_overdue($schedrow) ? constants::INSTALLMENT_STATUS_OVERDUE : $schedrow->status;
    }

    /**
     * Creates (or, if a CANCELLED plan already exists for this fee
     * record, reactivates) an installment plan and its schedule lines,
     * and logs a CREATE audit entry. Callers must have already validated
     * (installmentplan_form::validation() does this) that no ACTIVE plan
     * already exists for $feerecordid and that the amounts sum to the fee
     * record's current balance - this method re-checks the ACTIVE-plan
     * guard defensively (throws) but does NOT re-validate the amount sum,
     * matching this plugin's established pattern of eligibility checks
     * living in form validation() (see [[financedepartment-schema]]
     * project memory's note on this pattern).
     *
     * As of Step 7.7, the CANCELLED-row reuse path below also refuses
     * (throws) if any of that row's existing schedule lines already have
     * a real payment applied (paidamount > 0) - see
     * schedule_has_payments()'s docblock for why this is the same risk
     * reschedule() has always had. This can only happen if a plan was
     * cancelled after payments were recorded against it and someone then
     * tries to re-create a plan for the same fee record; in that case the
     * fee record's existing payment history must be dealt with (e.g. a
     * refund) before a fresh schedule can be laid down.
     *
     * @param int $feerecordid
     * @param array $installments list of ['amount' => float, 'duedate' => int], in order
     * @param int $usermodified
     * @return int the installment plan id (a reused row's existing id, or a freshly inserted one)
     */
    public static function create(int $feerecordid, array $installments, int $usermodified): int {
        global $DB;

        if (self::has_active_plan($feerecordid)) {
            throw new \moodle_exception('errorinstallmentplanexists', 'local_financedepartment');
        }

        $now = time();
        $existing = $DB->get_record('financedep_installmentplan', ['feerecordid' => $feerecordid]);

        if ($existing) {
            // A CANCELLED row already occupies this feerecordid's unique
            // slot - reuse it rather than inserting a duplicate. Refuse
            // if doing so would destroy schedule rows with real payments
            // already applied (see schedule_has_payments()).
            if (self::schedule_has_payments($existing->id)) {
                throw new \moodle_exception('errorinstallmentplanhaspayments', 'local_financedepartment');
            }

            $planid = $existing->id;
            $DB->update_record('financedep_installmentplan', (object) [
                'id' => $planid,
                'numberofinstallments' => count($installments),
                'status' => constants::INSTALLMENTPLAN_STATUS_ACTIVE,
                'timemodified' => $now,
                'usermodified' => $usermodified,
            ]);
            $DB->delete_records('financedep_installmentsched', ['installmentplanid' => $planid]);
        } else {
            $plan = new \stdClass();
            $plan->feerecordid = $feerecordid;
            $plan->numberofinstallments = count($installments);
            $plan->status = constants::INSTALLMENTPLAN_STATUS_ACTIVE;
            $plan->createdby = $usermodified;
            $plan->timecreated = $now;
            $plan->timemodified = $now;
            $plan->usermodified = $usermodified;

            $planid = $DB->insert_record('financedep_installmentplan', $plan);
        }

        self::insert_schedule($planid, $installments, $now);

        audit_manager::log(
            constants::AUDIT_ENTITY_INSTALLMENTPLAN,
            $planid,
            constants::AUDIT_ACTION_CREATE,
            null,
            [
                'feerecordid' => $feerecordid,
                'numberofinstallments' => count($installments),
                'status' => constants::INSTALLMENTPLAN_STATUS_ACTIVE,
            ],
            $usermodified
        );

        return $planid;
    }

    /**
     * Replaces a plan's schedule lines (reschedule). Callers must have
     * already validated the new amounts sum to the fee record's current
     * balance, same as create().
     *
     * UPDATED for Step 7.7: now that real payments exist
     * (financedep_feepayment.installmentschedid, feepayment_manager), this
     * method refuses (throws) rather than deleting/replacing any schedule
     * row if ANY of the plan's rows already has paidamount > 0 - a
     * reschedule must never be able to erase a real payment's target row.
     * This is an all-or-nothing guard, not a per-row skip: once a single
     * payment has been recorded against a plan, the whole plan can no
     * longer be rescheduled at all (cancelling and starting a fresh plan
     * is create()'s job, which has the equivalent guard on its own
     * cancelled-row reuse path - see schedule_has_payments()).
     *
     * @param int $planid
     * @param array $installments list of ['amount' => float, 'duedate' => int], in order
     * @param int $usermodified
     * @return void
     */
    public static function reschedule(int $planid, array $installments, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_installmentplan', ['id' => $planid], '*', MUST_EXIST);

        if (self::schedule_has_payments($planid)) {
            throw new \moodle_exception('errorinstallmentplanhaspayments', 'local_financedepartment');
        }

        $DB->delete_records('financedep_installmentsched', ['installmentplanid' => $planid]);
        self::insert_schedule($planid, $installments, time());

        $DB->update_record('financedep_installmentplan', (object) [
            'id' => $planid,
            'numberofinstallments' => count($installments),
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_INSTALLMENTPLAN,
            $planid,
            constants::AUDIT_ACTION_EDIT,
            ['numberofinstallments' => $before->numberofinstallments],
            ['numberofinstallments' => count($installments)],
            $usermodified
        );
    }

    /**
     * Cancels an installment plan. Idempotent (no-op if already
     * cancelled). Deliberately never touches the fee record's balance -
     * see this class's docblock. Schedule lines are kept as-is (not
     * deleted) for audit/history purposes.
     *
     * @param int $planid
     * @param int $usermodified
     * @return void
     */
    public static function cancel(int $planid, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_installmentplan', ['id' => $planid], '*', MUST_EXIST);

        if ($before->status === constants::INSTALLMENTPLAN_STATUS_CANCELLED) {
            return;
        }

        $DB->update_record('financedep_installmentplan', (object) [
            'id' => $planid,
            'status' => constants::INSTALLMENTPLAN_STATUS_CANCELLED,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_INSTALLMENTPLAN,
            $planid,
            constants::AUDIT_ACTION_CANCEL,
            ['status' => $before->status],
            ['status' => constants::INSTALLMENTPLAN_STATUS_CANCELLED],
            $usermodified
        );
    }

    /**
     * Inserts one plan's schedule rows, numbered 1-based in the order
     * given.
     *
     * @param int $planid
     * @param array $installments list of ['amount' => float, 'duedate' => int], in order
     * @param int $now
     * @return void
     */
    protected static function insert_schedule(int $planid, array $installments, int $now): void {
        global $DB;

        $number = 1;
        foreach ($installments as $installment) {
            $sched = new \stdClass();
            $sched->installmentplanid = $planid;
            $sched->installmentnumber = $number;
            $sched->amount = (float) $installment['amount'];
            $sched->duedate = (int) $installment['duedate'];
            $sched->paidamount = 0;
            $sched->status = constants::INSTALLMENT_STATUS_PENDING;
            $sched->timecreated = $now;
            $sched->timemodified = $now;

            $DB->insert_record('financedep_installmentsched', $sched);
            $number++;
        }
    }
}
