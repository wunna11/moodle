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
 * Shared constants used across the Finance Department plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

/**
 * Class constants
 *
 * Centralises the enum-like string values used across the financedep_*
 * tables so every module (fee structures, fee records, scholarships,
 * discounts, installments, payments, dashboard, ...) references the same
 * literals instead of re-declaring them.
 *
 * All money amounts handled by this plugin are MMK only - there is no
 * currency field anywhere in the schema, and none should be added without
 * revisiting this assumption everywhere it is relied on.
 */
class constants {

    // -----------------------------------------------------------------
    // NOTE: this plugin no longer has its own finance-employee status
    // constants. "Is finance staff" is now decided by an hrdep_employee
    // row in local_hrdepartment whose department is named "Finance" -
    // see classes/access_manager.php. That plugin's own
    // \local_hrdepartment\constants::EMPLOYMENT_STATUS_* /
    // EMPLOYEE_TYPE_STAFF are the relevant enums now, not anything here.
    // -----------------------------------------------------------------

    // -----------------------------------------------------------------
    // Fee structure status (financedep_feestructure.status).
    // -----------------------------------------------------------------

    /** @var string Fee structure status: active (selectable for new fee records). */
    const FEESTRUCTURE_STATUS_ACTIVE = 'active';

    /** @var string Fee structure status: inactive (deactivated, kept for history). */
    const FEESTRUCTURE_STATUS_INACTIVE = 'inactive';

    // -----------------------------------------------------------------
    // Scholarship definition status (financedep_scholarship.status).
    // -----------------------------------------------------------------

    /** @var string Scholarship status: active (selectable for new requests). */
    const SCHOLARSHIP_STATUS_ACTIVE = 'active';

    /** @var string Scholarship status: inactive (deactivated, kept for history). */
    const SCHOLARSHIP_STATUS_INACTIVE = 'inactive';

    // -----------------------------------------------------------------
    // Discount definition status (financedep_discount.status). Reuses
    // the same active/inactive shape (and the same generic
    // status_active/status_inactive lang strings) as
    // SCHOLARSHIP_STATUS_*/FEESTRUCTURE_STATUS_* above.
    // -----------------------------------------------------------------

    /** @var string Discount status: active (selectable for new requests). */
    const DISCOUNT_STATUS_ACTIVE = 'active';

    /** @var string Discount status: inactive (deactivated, kept for history). */
    const DISCOUNT_STATUS_INACTIVE = 'inactive';

    // -----------------------------------------------------------------
    // Fee record status (financedep_feerecord.status) - a student's
    // assigned fee and its payment progress.
    // -----------------------------------------------------------------

    /** @var string Fee record status: nothing paid yet. */
    const FEE_STATUS_UNPAID = 'unpaid';

    /** @var string Fee record status: some but not all of the balance paid. */
    const FEE_STATUS_PARTIALLY_PAID = 'partiallypaid';

    /** @var string Fee record status: balance fully settled. */
    const FEE_STATUS_FULLY_PAID = 'fullypaid';

    /** @var string Fee record status: unpaid/partially paid past an installment due date. */
    const FEE_STATUS_OVERDUE = 'overdue';

    /** @var string Fee record status: the assignment itself was cancelled (mistaken assignment). */
    const FEE_STATUS_CANCELLED = 'cancelled';

    // -----------------------------------------------------------------
    // Shared request/approval status - used by both
    // financedep_scholarshipreq.status and
    // financedep_discountreq.status.
    // -----------------------------------------------------------------

    /** @var string Request status: awaiting finance approval. */
    const REQUEST_STATUS_PENDING = 'pending';

    /** @var string Request status: approved by finance. */
    const REQUEST_STATUS_APPROVED = 'approved';

    /** @var string Request status: rejected by finance. */
    const REQUEST_STATUS_REJECTED = 'rejected';

    /**
     * @var string Request status: soft-deleted (added 2026-08-24 for
     * scholarship requests' delete feature). The row is kept for audit
     * history and no longer shown in the normal list, but is not
     * physically removed. Deliberately NOT included in
     * request_statuses() below - that method's docblock describes the
     * shared pending/approved/rejected lifecycle used by both
     * scholarship and discount requests. Discount requests (Step 7.5,
     * built 2026-09-06) deliberately do NOT get a delete feature yet -
     * only scholarship requests use this status today. If discount
     * requests get their own delete feature later, revisit whether this
     * constant should move into request_statuses() at that point - see
     * [[financedepartment-schema]] project memory.
     */
    const REQUEST_STATUS_DELETED = 'deleted';

    // -----------------------------------------------------------------
    // NOTE: this plugin no longer classifies scholarships by type
    // (merit/needbased/sibling/staffward/other - the original Step 7.4
    // scope). Per the user's 2026-08-23 request, that classification was
    // removed and replaced with a program-level restriction instead: a
    // scholarship now belongs to exactly one course category
    // (financedep_scholarship.categoryid), the same pattern
    // financedep_feestructure already uses. See classes/scholarship_manager.php
    // and [[financedepartment-schema]] project memory for the full story.
    // -----------------------------------------------------------------

    // -----------------------------------------------------------------
    // Discount type classification (financedep_discount.type). Step 7.5,
    // built 2026-09-06 - see classes/discount_manager.php's docblock.
    //
    // Unlike scholarships, a discount is NOT restricted to one course
    // category (financedep_discount has no categoryid column) - any
    // discount can be requested against any student's fee record.
    //
    // 2026-09-06 scope decision (user asked via AskUserQuestion after
    // discovering neither financedep_feerecord nor
    // financedep_feestructure has a due-date column to evaluate an
    // automatic "days before due date" rule against): this first pass
    // builds ONLY the manual/hardship request-and-approve workflow,
    // mirroring scholarship requests. Every discount created right now
    // has isautomatic = 0 regardless of its type - discount_form.php
    // does not expose the isautomatic/rulejson fields at all. A discount
    // of type earlypayment or promotional can still be created and
    // manually requested/approved today; only the FUTURE automatic
    // rule-evaluation engine (rulejson-driven auto-apply) is deferred,
    // pending a due-date field being added to feerecord/feestructure.
    // -----------------------------------------------------------------

    /** @var string Discount type: early-payment. */
    const DISCOUNT_TYPE_EARLYPAYMENT = 'earlypayment';

    /** @var string Discount type: promotional. */
    const DISCOUNT_TYPE_PROMOTIONAL = 'promotional';

    /** @var string Discount type: hardship (always manual/request-based, never automatic). */
    const DISCOUNT_TYPE_HARDSHIP = 'hardship';

    // -----------------------------------------------------------------
    // How a scholarship/discount value is expressed
    // (financedep_scholarship.amounttype / financedep_discount.amounttype).
    // -----------------------------------------------------------------

    /** @var string Amount type: a fixed MMK value. */
    const AMOUNT_TYPE_FIXED = 'fixed';

    /** @var string Amount type: a percentage of the fee record's total amount. */
    const AMOUNT_TYPE_PERCENTAGE = 'percentage';

    // -----------------------------------------------------------------
    // Installment plan status (financedep_installmentplan.status).
    // -----------------------------------------------------------------

    /** @var string Installment plan status: active. */
    const INSTALLMENTPLAN_STATUS_ACTIVE = 'active';

    /** @var string Installment plan status: cancelled. */
    const INSTALLMENTPLAN_STATUS_CANCELLED = 'cancelled';

    // -----------------------------------------------------------------
    // Installment schedule status (financedep_installmentsched.status)
    // - one row per due installment within a plan.
    // -----------------------------------------------------------------

    /** @var string Installment status: not yet due, or due but not yet paid. */
    const INSTALLMENT_STATUS_PENDING = 'pending';

    /** @var string Installment status: partially paid. */
    const INSTALLMENT_STATUS_PARTIALLY_PAID = 'partiallypaid';

    /** @var string Installment status: fully paid. */
    const INSTALLMENT_STATUS_PAID = 'paid';

    /** @var string Installment status: due date passed and not yet fully paid. */
    const INSTALLMENT_STATUS_OVERDUE = 'overdue';

    // -----------------------------------------------------------------
    // Payment record type/status (financedep_feepayment).
    // -----------------------------------------------------------------

    /** @var string Payment type: a normal payment towards a fee record. */
    const PAYMENT_TYPE_PAYMENT = 'payment';

    /** @var string Payment type: a refund/reversal entry. */
    const PAYMENT_TYPE_REFUND = 'refund';

    /** @var string Payment status: active (counts towards the balance). */
    const PAYMENT_STATUS_ACTIVE = 'active';

    /** @var string Payment status: voided (excluded from the balance, kept for audit). */
    const PAYMENT_STATUS_VOID = 'void';

    // -----------------------------------------------------------------
    // Audit log (financedep_auditlog.entitytype / .action).
    // -----------------------------------------------------------------

    /** @var string Audit entity type: a fee structure. */
    const AUDIT_ENTITY_FEESTRUCTURE = 'feestructure';

    /** @var string Audit entity type: a student's fee record. */
    const AUDIT_ENTITY_FEERECORD = 'feerecord';

    /** @var string Audit entity type: a scholarship definition (create/edit/deactivate history). */
    const AUDIT_ENTITY_SCHOLARSHIP = 'scholarship';

    /** @var string Audit entity type: a fee payment. */
    const AUDIT_ENTITY_FEEPAYMENT = 'feepayment';

    /** @var string Audit entity type: a scholarship request. */
    const AUDIT_ENTITY_SCHOLARSHIPREQUEST = 'scholarshiprequest';

    /** @var string Audit entity type: a discount definition (create/edit/deactivate history). */
    const AUDIT_ENTITY_DISCOUNT = 'discount';

    /** @var string Audit entity type: a discount request. */
    const AUDIT_ENTITY_DISCOUNTREQUEST = 'discountrequest';

    /** @var string Audit entity type: an installment plan. */
    const AUDIT_ENTITY_INSTALLMENTPLAN = 'installmentplan';

    /** @var string Audit action: record created. */
    const AUDIT_ACTION_CREATE = 'create';

    /** @var string Audit action: record edited. */
    const AUDIT_ACTION_EDIT = 'edit';

    /** @var string Audit action: record cancelled/deactivated. */
    const AUDIT_ACTION_CANCEL = 'cancel';

    /** @var string Audit action: a payment was voided. */
    const AUDIT_ACTION_VOID = 'void';

    /** @var string Audit action: a refund/reversal was recorded. */
    const AUDIT_ACTION_REFUND = 'refund';

    /** @var string Audit action: a request was approved. */
    const AUDIT_ACTION_APPROVE = 'approve';

    /** @var string Audit action: a request was rejected. */
    const AUDIT_ACTION_REJECT = 'reject';

    /** @var string Audit action: a request was soft-deleted. */
    const AUDIT_ACTION_DELETE = 'delete';

    /**
     * Returns the list of valid fee record statuses.
     *
     * @return string[]
     */
    public static function fee_record_statuses(): array {
        return [
            self::FEE_STATUS_UNPAID,
            self::FEE_STATUS_PARTIALLY_PAID,
            self::FEE_STATUS_FULLY_PAID,
            self::FEE_STATUS_OVERDUE,
            self::FEE_STATUS_CANCELLED,
        ];
    }

    /**
     * Returns the list of valid request statuses (scholarship or discount requests).
     *
     * @return string[]
     */
    public static function request_statuses(): array {
        return [
            self::REQUEST_STATUS_PENDING,
            self::REQUEST_STATUS_APPROVED,
            self::REQUEST_STATUS_REJECTED,
        ];
    }


    /**
     * Returns the list of valid discount types.
     *
     * @return string[]
     */
    public static function discount_types(): array {
        return [
            self::DISCOUNT_TYPE_EARLYPAYMENT,
            self::DISCOUNT_TYPE_PROMOTIONAL,
            self::DISCOUNT_TYPE_HARDSHIP,
        ];
    }

    /**
     * Returns the list of valid amount types (fixed vs percentage).
     *
     * @return string[]
     */
    public static function amount_types(): array {
        return [
            self::AMOUNT_TYPE_FIXED,
            self::AMOUNT_TYPE_PERCENTAGE,
        ];
    }

    /**
     * Returns the list of valid installment schedule statuses.
     *
     * @return string[]
     */
    public static function installment_statuses(): array {
        return [
            self::INSTALLMENT_STATUS_PENDING,
            self::INSTALLMENT_STATUS_PARTIALLY_PAID,
            self::INSTALLMENT_STATUS_PAID,
            self::INSTALLMENT_STATUS_OVERDUE,
        ];
    }

    /**
     * Returns the list of valid payment record statuses.
     *
     * @return string[]
     */
    public static function payment_statuses(): array {
        return [
            self::PAYMENT_STATUS_ACTIVE,
            self::PAYMENT_STATUS_VOID,
        ];
    }
}
