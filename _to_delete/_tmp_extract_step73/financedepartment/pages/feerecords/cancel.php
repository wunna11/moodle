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
 * Cancel a mistakenly-assigned fee record. One-directional (no
 * reactivate) - see feerecord_manager::cancel()'s docblock. Never
 * hard-deleted, so the audit trail stays intact.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\feerecord_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managefeerecords');

$feerecord = feerecord_manager::get($id);
if (!$feerecord) {
    throw new moodle_exception(
        'errorfeerecordnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/feerecords/index.php')
    );
}

if ($feerecord->status === constants::FEE_STATUS_CANCELLED) {
    redirect(new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $id]));
}

$returnurl = new moodle_url('/local/financedepartment/pages/feerecords/view.php', ['id' => $id]);
$actionurl = new moodle_url('/local/financedepartment/pages/feerecords/cancel.php', ['id' => $id]);

$PAGE->set_context($context);
$PAGE->set_url($actionurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_financedepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

if ($confirm && confirm_sesskey()) {
    feerecord_manager::cancel($id, $USER->id);

    redirect(
        $returnurl,
        get_string('feerecordcancelled', 'local_financedepartment'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('feerecords');

$label = format_string($feerecord->fullname) . ' - ' . format_string($feerecord->categoryname) . ' - ' . s($feerecord->academicyear);

echo $OUTPUT->confirm(
    get_string('confirmcancelfeerecord', 'local_financedepartment', $label),
    new moodle_url($actionurl, ['confirm' => 1, 'sesskey' => sesskey()]),
    $returnurl
);

echo $OUTPUT->footer();
