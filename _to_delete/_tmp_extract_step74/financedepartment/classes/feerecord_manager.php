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
 * Business logic for fee record assignment (Step 7.3): manually
 * assigning a fee structure to a student, bulk-assigning to everyone in
 * a course category, and editing/cancelling a mistaken assignment.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class feerecord_manager
 *
 * A fee record is a fee structure assigned to one student. `totalamount`
 * is a SNAPSHOT of the fee structure's amount taken at assignment time
 * (or re-taken if the fee structure link itself is corrected via
 * update() - see that method's docblock), not a live lookup - so a later
 * change to the fee structure's own price (Step 7.2) never silently
 * changes what a student who was already assigned owes. Every
 * create/edit/cancel writes a matching audit_manager entry, same pattern
 * as feestructure_manager.
 *
 * scholarshipamount/discountamount/paidamount all start at 0 and stay
 * there until Steps 7.4/7.5/7.7 (scholarships, discounts, payments) are
 * built - this step only creates the assignment itself. balance is kept
 * as a stored, not computed, column (per db/install.xml's comment) so
 * later steps only ever need to update() the running totals rather than
 * recompute the whole record; update() here always keeps it consistent
 * with totalamount - scholarshipamount - discountamount - paidamount.
 */
class feerecord_manager {

    /**
     * The editable fields snapshotted into the audit log on create/edit,
     * and diffed to build the "what changed" newdata array on edit.
     *
     * @var string[]
     */
    const AUDITED_FIELDS = ['studentid', 'feestructureid', 'totalamount', 'status'];

    /**
     * Returns one fee record with its student and fee structure details
     * joined in, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT r.*, u.firstname, u.lastname, u.email,
                       f.academicyear, f.amount AS feestructureamount, f.status AS feestructurestatus,
                       cc.name AS categoryname
                  FROM {financedep_feerecord} r
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid
                 WHERE r.id = :id";

        $record = $DB->get_record_sql($sql, ['id' => $id]);
        if (!$record) {
            return false;
        }

        $record->fullname = fullname($record);

        return $record;
    }

    /**
     * Returns every fee record assigned to one student, newest first,
     * with fee structure details joined in.
     *
     * @param int $studentid
     * @return \stdClass[]
     */
    public static function get_for_student(int $studentid): array {
        global $DB;

        $sql = "SELECT r.*, f.academicyear, f.amount AS feestructureamount, cc.name AS categoryname
                  FROM {financedep_feerecord} r
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid
                 WHERE r.studentid = :studentid
              ORDER BY r.timecreated DESC";

        return array_values($DB->get_records_sql($sql, ['studentid' => $studentid]));
    }

    /**
     * Returns the most recently assigned fee records across every
     * student, for index.php's default (no student picked yet) view.
     *
     * NOT the Step 7.9 finance staff list view - this has no
     * search/filter, just a small "recent activity" panel so the page
     * isn't empty on first load. Step 7.9 builds the real
     * searchable/filterable all-students list.
     *
     * @param int $limit
     * @return \stdClass[]
     */
    public static function get_recent(int $limit = 10): array {
        global $DB;

        $sql = "SELECT r.*, u.firstname, u.lastname, u.email,
                       f.academicyear, f.amount AS feestructureamount, cc.name AS categoryname
                  FROM {financedep_feerecord} r
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid
              ORDER BY r.timecreated DESC";

        $records = $DB->get_records_sql($sql, [], 0, $limit);

        foreach ($records as $record) {
            $record->fullname = fullname($record);
        }

        return array_values($records);
    }

    /**
     * Whether the student already has a non-cancelled fee record for
     * this exact fee structure, excluding one id (used when editing).
     * Enforced at the application layer, same pattern as
     * feestructure_manager::has_active_duplicate() - prevents assigning
     * the same fee twice by mistake, while still allowing a student to
     * hold fee records for several different fee structures (different
     * categories/years) at once.
     *
     * @param int $studentid
     * @param int $feestructureid
     * @param int $excludeid
     * @return bool
     */
    public static function has_active_assignment(int $studentid, int $feestructureid, int $excludeid = 0): bool {
        global $DB;

        $params = [
            'studentid' => $studentid,
            'feestructureid' => $feestructureid,
            'cancelled' => constants::FEE_STATUS_CANCELLED,
        ];
        $sql = 'studentid = :studentid AND feestructureid = :feestructureid AND status <> :cancelled';

        if ($excludeid) {
            $sql .= ' AND id <> :excludeid';
            $params['excludeid'] = $excludeid;
        }

        return $DB->record_exists_select('financedep_feerecord', $sql, $params);
    }

    /**
     * Assigns a fee structure to one student and logs a CREATE audit
     * entry. totalamount is snapshotted from the fee structure's current
     * amount at the moment of assignment.
     *
     * @param \stdClass $data form data: studentid, feestructureid
     * @param int $assignedby
     * @return int the new fee record id
     */
    public static function create(\stdClass $data, int $assignedby): int {
        global $DB;

        $feestructure = $DB->get_record('financedep_feestructure', ['id' => (int) $data->feestructureid], '*', MUST_EXIST);
        $now = time();

        $record = new \stdClass();
        $record->studentid = (int) $data->studentid;
        $record->feestructureid = (int) $data->feestructureid;
        $record->totalamount = (float) $feestructure->amount;
        $record->scholarshipamount = 0;
        $record->discountamount = 0;
        $record->paidamount = 0;
        $record->balance = $record->totalamount;
        $record->status = constants::FEE_STATUS_UNPAID;
        $record->assignedby = $assignedby;
        $record->timecreated = $now;
        $record->timemodified = $now;
        $record->usermodified = $assignedby;

        $id = $DB->insert_record('financedep_feerecord', $record);

        audit_manager::log(
            constants::AUDIT_ENTITY_FEERECORD,
            $id,
            constants::AUDIT_ACTION_CREATE,
            null,
            self::snapshot($record),
            $assignedby
        );

        return $id;
    }

    /**
     * Corrects a mistakenly-assigned fee record: which student it
     * belongs to, and/or which fee structure it is. If the fee structure
     * link changes, totalamount is re-snapshotted from the new fee
     * structure's current amount (the old assignment was wrong, so its
     * old snapshot shouldn't carry over) and balance is recalculated
     * from the existing scholarship/discount/paid amounts. Logs an EDIT
     * audit entry containing only the fields that actually changed.
     *
     * @param int $id
     * @param \stdClass $data form data: studentid, feestructureid
     * @param int $usermodified
     * @return void
     */
    public static function update(int $id, \stdClass $data, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_feerecord', ['id' => $id], '*', MUST_EXIST);

        $record = new \stdClass();
        $record->id = $id;
        $record->studentid = (int) $data->studentid;
        $record->feestructureid = (int) $data->feestructureid;
        $record->timemodified = time();
        $record->usermodified = $usermodified;

        if ($record->feestructureid !== (int) $before->feestructureid) {
            $feestructure = $DB->get_record('financedep_feestructure', ['id' => $record->feestructureid], '*', MUST_EXIST);
            $record->totalamount = (float) $feestructure->amount;
            $record->balance = $record->totalamount - $before->scholarshipamount - $before->discountamount - $before->paidamount;
        }

        $DB->update_record('financedep_feerecord', $record);

        $after = $DB->get_record('financedep_feerecord', ['id' => $id], '*', MUST_EXIST);

        [$old, $new] = self::diff($before, $after);
        if (!empty($new)) {
            audit_manager::log(
                constants::AUDIT_ENTITY_FEERECORD,
                $id,
                constants::AUDIT_ACTION_EDIT,
                $old,
                $new,
                $usermodified
            );
        }
    }

    /**
     * Adds (or subtracts, for a negative $amount - e.g. reversing a
     * rejected/undone approval) a scholarship amount to a fee record's
     * running total, then recalculates balance and status via
     * save_and_recalculate(). This is the one place
     * financedep_feerecord.scholarshipamount changes - called by
     * scholarshiprequest_manager on approval. Steps 7.5 (discounts) and
     * 7.7 (payments) should add their own equivalent
     * add_discount_amount()/add_payment_amount() methods here rather
     * than writing to financedep_feerecord's running totals directly
     * from those managers - see [[financedepartment-schema]] project
     * memory's note on this.
     *
     * @param int $feerecordid
     * @param float $amount MMK to add to scholarshipamount (negative to reverse)
     * @param int $usermodified
     * @return void
     */
    public static function add_scholarship_amount(int $feerecordid, float $amount, int $usermodified): void {
        global $DB;

        $feerecord = $DB->get_record('financedep_feerecord', ['id' => $feerecordid], '*', MUST_EXIST);

        $feerecord->scholarshipamount = max(0, (float) $feerecord->scholarshipamount + $amount);

        self::save_and_recalculate($feerecord, $usermodified);
    }

    /**
     * Recomputes balance = totalamount - scholarshipamount -
     * discountamount - paidamount, and derives status from it: fully
     * paid once balance reaches zero (or the fee structure was free to
     * begin with), partially paid once ANY reduction has been applied
     * (a payment, a scholarship, or a discount - not payment alone,
     * since a student whose balance was cut in half by a scholarship
     * has legitimately made progress even before paying anything),
     * otherwise unpaid. Never touches a CANCELLED record's numbers, and
     * never sets/clears OVERDUE - that depends on installment due dates
     * (Step 7.6/7.8), out of scope for whatever caller reaches this.
     *
     * Shared by every manager that touches a fee record's running
     * totals, so balance/status logic lives in exactly one place instead
     * of being reimplemented per step - see add_scholarship_amount()'s
     * docblock.
     *
     * @param \stdClass $feerecord a fetched financedep_feerecord row,
     *                             with scholarshipamount/discountamount/
     *                             paidamount already updated to their new values
     * @param int $usermodified
     * @return void
     */
    protected static function save_and_recalculate(\stdClass $feerecord, int $usermodified): void {
        global $DB;

        if ($feerecord->status !== constants::FEE_STATUS_CANCELLED) {
            $balance = $feerecord->totalamount - $feerecord->scholarshipamount - $feerecord->discountamount - $feerecord->paidamount;
            $feerecord->balance = $balance;

            if ($balance <= 0) {
                $feerecord->status = constants::FEE_STATUS_FULLY_PAID;
            } else if ($balance < $feerecord->totalamount) {
                $feerecord->status = constants::FEE_STATUS_PARTIALLY_PAID;
            } else {
                $feerecord->status = constants::FEE_STATUS_UNPAID;
            }
        }

        $feerecord->timemodified = time();
        $feerecord->usermodified = $usermodified;

        $DB->update_record('financedep_feerecord', $feerecord);
    }

    /**
     * Cancels a mistakenly-assigned fee record. One-directional (unlike
     * feestructure_manager's deactivate/reactivate toggle) - Step 7.3
     * only asks to cancel a mistake, not to later un-cancel it. Never
     * hard-deleted, so the audit trail and any future payment history
     * against it stay intact.
     *
     * @param int $id
     * @param int $usermodified
     * @return void
     */
    public static function cancel(int $id, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_feerecord', ['id' => $id], '*', MUST_EXIST);

        if ($before->status === constants::FEE_STATUS_CANCELLED) {
            return;
        }

        $DB->update_record('financedep_feerecord', (object) [
            'id' => $id,
            'status' => constants::FEE_STATUS_CANCELLED,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_FEERECORD,
            $id,
            constants::AUDIT_ACTION_CANCEL,
            ['status' => $before->status],
            ['status' => constants::FEE_STATUS_CANCELLED],
            $usermodified
        );
    }

    /**
     * Every distinct student holding the "student" role in at least one
     * course belonging to $categoryid (direct courses only, not
     * sub-categories - matches how a fee structure's own categoryid is a
     * single category with no hierarchy handling elsewhere in this
     * plugin). Used by bulk_assign().
     *
     * @param int $categoryid
     * @return int[] userids
     */
    public static function get_studentids_in_category(int $categoryid): array {
        global $DB;

        $courseids = $DB->get_fieldset_select('course', 'id', 'category = :categoryid', ['categoryid' => $categoryid]);
        if (empty($courseids)) {
            return [];
        }

        [$insql, $inparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cid');
        $params = array_merge($inparams, [
            'studentrole' => 'student',
            'coursecontextlevel' => CONTEXT_COURSE,
        ]);

        $sql = "SELECT DISTINCT ra.userid
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :coursecontextlevel
                  JOIN {role} r ON r.id = ra.roleid AND r.shortname = :studentrole
                  JOIN {user} u ON u.id = ra.userid AND u.deleted = 0
                 WHERE ctx.instanceid $insql";

        return array_map('intval', array_keys($DB->get_records_sql($sql, $params)));
    }

    /**
     * Bulk-assigns one fee structure to every student in a course
     * category, skipping anyone who already has a non-cancelled fee
     * record for that exact fee structure (has_active_assignment()) -
     * safe to run more than once for the same category/fee structure
     * pair without creating duplicates.
     *
     * @param int $feestructureid
     * @param int $categoryid
     * @param int $assignedby
     * @return array ['total' => int, 'assigned' => int, 'skipped' => int]
     */
    public static function bulk_assign(int $feestructureid, int $categoryid, int $assignedby): array {
        $studentids = self::get_studentids_in_category($categoryid);

        $assigned = 0;
        $skipped = 0;

        foreach ($studentids as $studentid) {
            if (self::has_active_assignment($studentid, $feestructureid)) {
                $skipped++;
                continue;
            }

            self::create((object) [
                'studentid' => $studentid,
                'feestructureid' => $feestructureid,
            ], $assignedby);
            $assigned++;
        }

        return [
            'total' => count($studentids),
            'assigned' => $assigned,
            'skipped' => $skipped,
        ];
    }

    /**
     * Returns feestructureid => "Category - Academic year (amount MMK)"
     * options for a select, limited to ACTIVE fee structures (matching
     * feestructure_form's own create/edit restriction to active-only
     * assignment). Deliberately doesn't call
     * local_financedepartment_format_money() (a lib.php function) since
     * this can run during form construction, before lib.php is
     * guaranteed loaded - see local_hrdepartment\department_helper's
     * docblock for the same caveat on this codebase.
     *
     * @return array
     */
    public static function get_feestructure_options(): array {
        global $DB;

        $sql = "SELECT f.id, f.academicyear, f.amount, cc.name AS categoryname
                  FROM {financedep_feestructure} f
                  JOIN {course_categories} cc ON cc.id = f.categoryid
                 WHERE f.status = :status
              ORDER BY cc.name ASC, f.academicyear DESC";

        $records = $DB->get_records_sql($sql, ['status' => constants::FEESTRUCTURE_STATUS_ACTIVE]);

        $options = [];
        foreach ($records as $record) {
            $amount = (float) $record->amount;
            $decimals = (abs($amount - round($amount)) > 0.001) ? 2 : 0;
            $options[$record->id] = format_string($record->categoryname) . ' - ' . $record->academicyear
                . ' (' . number_format($amount, $decimals) . ' MMK)';
        }

        return $options;
    }

    /**
     * Builds the [old, new] audited-field diff between two fee record
     * records, only including fields that actually changed.
     *
     * @param \stdClass $before
     * @param \stdClass $after
     * @return array [array $old, array $new]
     */
    protected static function diff(\stdClass $before, \stdClass $after): array {
        $old = [];
        $new = [];

        foreach (self::AUDITED_FIELDS as $field) {
            if ((string) ($before->$field ?? '') !== (string) ($after->$field ?? '')) {
                $old[$field] = $before->$field ?? null;
                $new[$field] = $after->$field ?? null;
            }
        }

        return [$old, $new];
    }

    /**
     * Extracts the audited fields from a fee record for use as an
     * audit_manager newdata snapshot.
     *
     * @param \stdClass $record
     * @return array
     */
    protected static function snapshot(\stdClass $record): array {
        $snapshot = [];
        foreach (self::AUDITED_FIELDS as $field) {
            $snapshot[$field] = $record->$field ?? null;
        }
        return $snapshot;
    }
}
