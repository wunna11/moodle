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
 * Version file for the Finance Department local plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$plugin->component = 'local_financedepartment';
$plugin->version   = 2026090603;
$plugin->requires  = 2024042200; // Moodle 4.4+.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.6.1';

// 2026-09-06 (post-deploy fix #7): a PENDING scholarship/discount request
// could still be approved (and its amount deducted from the fee record)
// after the underlying scholarship/discount was deactivated, since
// set_status() never re-validates existing requests. Fixed with a
// deactivated-item guard mirroring the same day's self-approval guard
// pattern: scholarshiprequest_manager::approve() /
// discountrequest_manager::approve() now refuse (silent no-op) if the
// referenced item is no longer ACTIVE; review.php redirects with a clear
// warning as the primary user-facing gate; the Approve action is hidden
// in both the row-actions table and the view.php page (Reject stays
// available either way, so a stale request can still be cleared). See
// [[financedepartment-schema]] project memory.

// 2026-09-06: Step 7.5 (discount management) built - classes/discount_manager.php,
// classes/discountrequest_manager.php, pages/discounts/*.php,
// pages/discountrequests/*.php. Manual/hardship request-and-approve
// workflow only for this first pass (automatic rule-based discounts
// deferred - no due-date field exists yet on feerecord/feestructure to
// evaluate a rule against). See [[financedepartment-schema]] project
// memory for the full 2026-09-06 scope decision.

// 2026-08-22: this plugin now reuses local_hrdepartment's staff/department
// model instead of maintaining its own financedep_employee table - see
// classes/access_manager.php's docblock and [[financedepartment-schema]]
// project memory. Requires HR's departmentid support on hrdep_employee.
$plugin->dependencies = [
    'local_hrdepartment' => 2026081908,
];
