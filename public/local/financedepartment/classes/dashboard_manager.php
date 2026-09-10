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
 * Aggregation queries for Step 7.11 (Finance Dashboard & Reports) -
 * pages/reports/index.php's summary cards/charts, and
 * pages/reports/export.php's raw record exports.
 *
 * Filter scope, a deliberate design choice (not asked about - a
 * judgement call, see [[financedepartment-step711]] project memory for
 * the full reasoning): category + academic year narrow every summary
 * card/chart AND the exports, but the STATUS filter only narrows the
 * exports - the five summary cards ARE themselves a breakdown by status
 * (collected/outstanding/overdue are inherently cross-status
 * aggregates), so applying a status filter to them would zero out most
 * cards for no useful reason. The status dropdown is still offered on
 * the dashboard page because it feeds the "export overdue students" /
 * "export fully-paid students" style use case directly.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class dashboard_manager
 */
class dashboard_manager {

    /**
     * Builds the shared WHERE fragment (category + academic year only,
     * never status - see class docblock) + params for every fee-record
     * based aggregate below. Always excludes CANCELLED records - a
     * cancelled assignment was a mistake, not real institutional
     * receivable/outstanding money.
     *
     * @param int $categoryid 0 = any category
     * @param string $academicyear '' = any academic year
     * @return array [string $where, array $params]
     */
    protected static function base_where(int $categoryid, string $academicyear): array {
        global $DB;

        $where = 'r.status != :cancelled';
        $params = ['cancelled' => constants::FEE_STATUS_CANCELLED];

        if ($categoryid) {
            $where .= ' AND f.categoryid = :categoryid';
            $params['categoryid'] = $categoryid;
        }

        if ($academicyear !== '') {
            $where .= ' AND ' . $DB->sql_like('f.academicyear', ':academicyear', false);
            $params['academicyear'] = '%' . $DB->sql_like_escape($academicyear) . '%';
        }

        return [$where, $params];
    }

    /**
     * The EXISTS subquery identifying a fee record with at least one
     * overdue installment schedule line on its ACTIVE plan - identical
     * logic to classes/table/feerecord_table.php's build_status_where()
     * and feerecord_manager::has_overdue_installment(), duplicated here
     * in SQL form rather than shared, since feerecord_table's version is
     * a protected instance method tied to that class's own $params
     * naming. Kept in sync manually if the overdue rule ever changes -
     * see that class's own docblock for the same caveat.
     *
     * @return array [string $sql, array $params]
     */
    protected static function overdue_exists_sql(): array {
        $sql = "EXISTS (
                SELECT 1
                  FROM {financedep_installmentplan} ip
                  JOIN {financedep_installmentsched} sc ON sc.installmentplanid = ip.id
                 WHERE ip.feerecordid = r.id
                   AND ip.status = :ipactive
                   AND sc.status IN (:schedpending, :schedpartial)
                   AND sc.duedate < :nowtime
            )";
        $params = [
            'ipactive' => constants::INSTALLMENTPLAN_STATUS_ACTIVE,
            'schedpending' => constants::INSTALLMENT_STATUS_PENDING,
            'schedpartial' => constants::INSTALLMENT_STATUS_PARTIALLY_PAID,
            'nowtime' => time(),
        ];
        return [$sql, $params];
    }

    /**
     * The five summary-card figures: total collected (net of
     * voided/refunded, since financedep_feerecord.paidamount is already
     * the running NET total - see feerecord_manager::add_payment_amount()),
     * total outstanding balance, how many fee records are currently
     * DISPLAYED as overdue (live-computed, same rule as
     * feerecord_manager::display_status()), and how many scholarship/
     * discount definitions are currently ACTIVE (unaffected by the
     * category/year filter - see class docblock).
     *
     * @param int $categoryid 0 = any category
     * @param string $academicyear '' = any academic year
     * @return \stdClass {totalcollected, totaloutstanding, overduecount, activescholarships, activediscounts}
     */
    public static function get_summary(int $categoryid = 0, string $academicyear = ''): \stdClass {
        global $DB;

        [$where, $params] = self::base_where($categoryid, $academicyear);
        $from = '{financedep_feerecord} r JOIN {financedep_feestructure} f ON f.id = r.feestructureid';

        $totals = $DB->get_record_sql(
            "SELECT COALESCE(SUM(r.paidamount), 0) AS collected, COALESCE(SUM(r.balance), 0) AS outstanding
               FROM $from
              WHERE $where",
            $params
        );

        [$overduesql, $overdueparams] = self::overdue_exists_sql();
        $unsettled = [constants::FEE_STATUS_UNPAID, constants::FEE_STATUS_PARTIALLY_PAID];
        [$insql, $inparams] = $DB->get_in_or_equal($unsettled, SQL_PARAMS_NAMED, 'ovst');

        $overduecount = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT r.id)
               FROM $from
              WHERE $where AND r.status $insql AND $overduesql",
            array_merge($params, $inparams, $overdueparams)
        );

        $summary = new \stdClass();
        $summary->totalcollected = (float) $totals->collected;
        $summary->totaloutstanding = (float) $totals->outstanding;
        $summary->overduecount = (int) $overduecount;
        $summary->activescholarships = (int) $DB->count_records(
            'financedep_scholarship',
            ['status' => constants::SCHOLARSHIP_STATUS_ACTIVE]
        );
        $summary->activediscounts = (int) $DB->count_records(
            'financedep_discount',
            ['status' => constants::DISCOUNT_STATUS_ACTIVE]
        );

        return $summary;
    }

    /** @var int Cap on how many category bars the dashboard's bar chart draws, so it stays readable on a site with many categories. */
    const MAX_CHART_CATEGORIES = 10;

    /**
     * Outstanding balance grouped by course category, for the dashboard's
     * bar chart - every category with at least one matching (non-
     * cancelled) fee record, ordered highest-outstanding first, capped
     * at MAX_CHART_CATEGORIES bars.
     *
     * @param string $academicyear '' = any academic year
     * @return array categoryname => outstanding (float)
     */
    public static function get_outstanding_by_category(string $academicyear = ''): array {
        global $DB;

        [$where, $params] = self::base_where(0, $academicyear);
        $from = '{financedep_feerecord} r
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid';

        $records = $DB->get_records_sql(
            "SELECT f.categoryid AS id, cc.name AS categoryname, SUM(r.balance) AS outstanding
               FROM $from
              WHERE $where
           GROUP BY f.categoryid, cc.name
             HAVING SUM(r.balance) > 0
           ORDER BY outstanding DESC",
            $params,
            0,
            self::MAX_CHART_CATEGORIES
        );

        $result = [];
        foreach ($records as $record) {
            $result[format_string($record->categoryname)] = (float) $record->outstanding;
        }

        return $result;
    }

    /**
     * Every fee record matching the filters (category/year/status all
     * apply here - see class docblock), flattened for
     * pages/reports/export.php. Not paginated - the whole matching set
     * is exported in one pass, same as classes/table/feerecord_table.php's
     * own query shape minus the LIMIT.
     *
     * @param int $categoryid 0 = any category
     * @param string $academicyear '' = any academic year
     * @param string $status '' = any status (as DISPLAYED - see feerecord_table's docblock for the overdue caveat)
     * @return \stdClass[]
     */
    public static function get_feerecords_for_export(int $categoryid = 0, string $academicyear = '', string $status = ''): array {
        global $DB;

        $fields = 'r.id, r.totalamount, r.scholarshipamount, r.discountamount, r.paidamount, r.balance,
                   r.status, r.timecreated, u.firstname, u.lastname, u.email, f.academicyear, cc.name AS categoryname';
        $from = '{financedep_feerecord} r
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid';

        $where = '1 = 1';
        $params = [];

        if ($categoryid) {
            $where .= ' AND f.categoryid = :categoryid';
            $params['categoryid'] = $categoryid;
        }
        if ($academicyear !== '') {
            $where .= ' AND ' . $DB->sql_like('f.academicyear', ':academicyear', false);
            $params['academicyear'] = '%' . $DB->sql_like_escape($academicyear) . '%';
        }
        if ($status !== '') {
            [$overduesql, $overdueparams] = self::overdue_exists_sql();
            if ($status === constants::FEE_STATUS_OVERDUE) {
                [$insql, $inparams] = $DB->get_in_or_equal(
                    [constants::FEE_STATUS_UNPAID, constants::FEE_STATUS_PARTIALLY_PAID],
                    SQL_PARAMS_NAMED,
                    'ovst'
                );
                $where .= " AND r.status $insql AND $overduesql";
                $params = array_merge($params, $inparams, $overdueparams);
            } else if ($status === constants::FEE_STATUS_UNPAID || $status === constants::FEE_STATUS_PARTIALLY_PAID) {
                $where .= " AND r.status = :fstatus AND NOT $overduesql";
                $params = array_merge($params, ['fstatus' => $status], $overdueparams);
            } else {
                $where .= ' AND r.status = :fstatus';
                $params['fstatus'] = $status;
            }
        }

        $sql = "SELECT $fields FROM $from WHERE $where ORDER BY u.lastname ASC, u.firstname ASC";

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Every payment/refund transaction matching the filters (category/
     * year apply via the joined fee structure; status here means the
     * PAYMENT's own status - active/void, not a fee record status, so it
     * is offered as a separate dropdown on the export form, not the fee
     * record status one).
     *
     * @param int $categoryid 0 = any category
     * @param string $academicyear '' = any academic year
     * @param string $paymentstatus '' = any status, otherwise constants::PAYMENT_STATUS_*
     * @return \stdClass[]
     */
    public static function get_payments_for_export(int $categoryid = 0, string $academicyear = '', string $paymentstatus = ''): array {
        global $DB;

        $fields = 'p.id, p.receiptnumber, p.paymenttype, p.amount, p.paymentdate, p.paymentmethod, p.status,
                   u.firstname, u.lastname, u.email, f.academicyear, cc.name AS categoryname';
        $from = '{financedep_feepayment} p
                  JOIN {financedep_feerecord} r ON r.id = p.feerecordid
                  JOIN {user} u ON u.id = r.studentid
                  JOIN {financedep_feestructure} f ON f.id = r.feestructureid
                  JOIN {course_categories} cc ON cc.id = f.categoryid';

        $where = '1 = 1';
        $params = [];

        if ($categoryid) {
            $where .= ' AND f.categoryid = :categoryid';
            $params['categoryid'] = $categoryid;
        }
        if ($academicyear !== '') {
            $where .= ' AND ' . $DB->sql_like('f.academicyear', ':academicyear', false);
            $params['academicyear'] = '%' . $DB->sql_like_escape($academicyear) . '%';
        }
        if ($paymentstatus !== '') {
            $where .= ' AND p.status = :pstatus';
            $params['pstatus'] = $paymentstatus;
        }

        $sql = "SELECT $fields FROM $from WHERE $where ORDER BY p.paymentdate DESC";

        return array_values($DB->get_records_sql($sql, $params));
    }
}
