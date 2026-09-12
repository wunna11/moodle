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
 * Thin redirect stub (2026-09-12). The Finance Dashboard & Reports page
 * that used to live here (Step 7.11: summary cards, charts, filter bar,
 * exports) was consolidated into ../../index.php itself, per the user's
 * own request to make the plugin's landing page the finance dashboard -
 * see that file's docblock and [[financedepartment-uipolish]] project
 * memory for the full write-up.
 *
 * This file is deliberately kept (not deleted outright, a discretionary
 * safety choice) so an old bookmark, saved link, or anything else still
 * pointing at pages/reports/index.php keeps working instead of 404ing -
 * it just forwards straight to index.php, preserving the same
 * categoryid/academicyear/status filter params if any were present.
 * pages/reports/export.php (the actual CSV/Excel/PDF download
 * dispatcher) is UNCHANGED and unaffected by this - only this summary
 * page moved.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

require_login();

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$academicyear = optional_param('academicyear', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);

redirect(new moodle_url('/local/financedepartment/index.php', [
    'categoryid' => $categoryid,
    'academicyear' => $academicyear,
    'status' => $status,
]));
