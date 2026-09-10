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
 * Streams a fee-records or payments export in CSV/Excel/PDF, via
 * Moodle's own core\dataformat API (the csv/excel/pdf dataformat
 * subplugins already ship with core - no extra library needed). Two
 * separate export types (Step 7.11 AskUserQuestion decision - see
 * [[financedepartment-step711]] project memory): 'feerecords' (category/
 * academic year/status all apply - status here is the fee record's own
 * DISPLAYED status, same rule as classes/table/feerecord_table.php) and
 * 'payments' (category/academic year apply via the joined fee
 * structure; status here is the PAYMENT's own active/void status, a
 * completely different value space, so the 'status' GET param is
 * reinterpreted per $type rather than shared).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\dashboard_manager;
use local_financedepartment\feerecord_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

access_manager::require_manage('local/financedepartment:viewfinancereports');

$type = required_param('type', PARAM_ALPHA);
$format = required_param('format', PARAM_ALPHA);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$academicyear = optional_param('academicyear', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);

if (!in_array($type, ['feerecords', 'payments'], true)) {
    throw new moodle_exception('invalidparameter', 'debug', '', 'type');
}
if (!in_array($format, ['csv', 'excel', 'pdf'], true)) {
    throw new moodle_exception('invalidparameter', 'debug', '', 'format');
}

$filename = 'financedepartment_' . $type . '_' . userdate(time(), '%Y%m%d_%H%M%S');

if ($type === 'feerecords') {
    $records = dashboard_manager::get_feerecords_for_export($categoryid, $academicyear, $status);

    $columns = [
        get_string('student', 'local_financedepartment'),
        get_string('email'),
        get_string('category', 'local_financedepartment'),
        get_string('academicyear', 'local_financedepartment'),
        get_string('totalamount', 'local_financedepartment'),
        get_string('scholarshipamount', 'local_financedepartment'),
        get_string('discountamount', 'local_financedepartment'),
        get_string('paidamount', 'local_financedepartment'),
        get_string('balance', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
        get_string('assigneddate', 'local_financedepartment'),
    ];

    $callback = function ($row) {
        $out = new stdClass();
        $out->student = fullname($row);
        $out->email = $row->email;
        $out->category = format_string($row->categoryname);
        $out->academicyear = $row->academicyear;
        $out->totalamount = (float) $row->totalamount;
        $out->scholarshipamount = (float) $row->scholarshipamount;
        $out->discountamount = (float) $row->discountamount;
        $out->paidamount = (float) $row->paidamount;
        $out->balance = (float) $row->balance;
        $out->status = get_string('feestatus_' . feerecord_manager::display_status($row), 'local_financedepartment');
        $out->assigneddate = userdate($row->timecreated, get_string('strftimedatetimeshort', 'core_langconfig'));
        return $out;
    };
} else {
    $records = dashboard_manager::get_payments_for_export($categoryid, $academicyear, $status);

    $columns = [
        get_string('receiptnumber', 'local_financedepartment'),
        get_string('student', 'local_financedepartment'),
        get_string('email'),
        get_string('category', 'local_financedepartment'),
        get_string('academicyear', 'local_financedepartment'),
        get_string('paymenttype', 'local_financedepartment'),
        get_string('amount', 'local_financedepartment'),
        get_string('when', 'local_financedepartment'),
        get_string('paymentmethod', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
    ];

    $callback = function ($row) {
        $out = new stdClass();
        $out->receiptnumber = $row->receiptnumber;
        $out->student = fullname($row);
        $out->email = $row->email;
        $out->category = format_string($row->categoryname);
        $out->academicyear = $row->academicyear;
        $out->paymenttype = get_string('paymenttype_' . $row->paymenttype, 'local_financedepartment');
        $out->amount = (float) $row->amount;
        $out->when = userdate($row->paymentdate, get_string('strftimedatetimeshort', 'core_langconfig'));
        $out->paymentmethod = $row->paymentmethod ?? '';
        $out->status = get_string('paymentstatus_' . $row->status, 'local_financedepartment');
        return $out;
    };
}

\core\dataformat::download_data($filename, $format, $columns, $records, $callback);
