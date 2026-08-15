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
 * View a staff member's profile.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\output\staff_profile;
use local_hrdepartment\staff_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
require_capability('local/hrdepartment:managestaff', $context);

$staff = staff_manager::get_staff($id);
if (!$staff) {
    throw new moodle_exception('errorstaffnotfound', 'local_hrdepartment', new moodle_url('/local/hrdepartment/staff/index.php'));
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/staff/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($staff->fullname);
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

$renderer = $PAGE->get_renderer('local_hrdepartment');

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('staff');
echo $OUTPUT->tabtree($tabs, 'staff');

echo $renderer->render_staff_profile(new staff_profile($staff));

echo $OUTPUT->footer();
