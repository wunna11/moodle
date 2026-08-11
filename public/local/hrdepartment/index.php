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
 * HR Department landing page.
 *
 * Users with local/hrdepartment:managedashboard see the organisation-wide
 * summary (lecturer/staff counts, payroll totals, leave stats). Users who
 * only hold self-service capabilities see a personal "My HR" snapshot
 * instead of being blocked outright.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\output\dashboard_summary;
use local_hrdepartment\output\my_summary;

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$renderer = $PAGE->get_renderer('local_hrdepartment');

$canviewdashboard = has_capability('local/hrdepartment:managedashboard', $context);
$canselfservice = has_capability('local/hrdepartment:viewownattendance', $context)
    || has_capability('local/hrdepartment:applyownleave', $context)
    || has_capability('local/hrdepartment:viewownpayroll', $context);

if (!$canviewdashboard && !$canselfservice) {
    // Neither the org-wide dashboard nor any self-service capability is
    // available to this user, so there is nothing on this page for them.
    require_capability('local/hrdepartment:managedashboard', $context);
}

echo $OUTPUT->header();

if ($canviewdashboard) {
    $page = new dashboard_summary();
    echo $renderer->render_dashboard_summary($page);
} else {
    $page = new my_summary((int) $USER->id);
    echo $renderer->render_my_summary($page);
}

echo $OUTPUT->footer();
