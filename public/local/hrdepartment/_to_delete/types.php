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
 * Leave types listing (HR configuration for Leave Balance Management and
 * the leave request form's leave type choices).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\student_leave_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/hrdepartment:managestudentleave', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/types.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leavetypes', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

$tabs = local_hrdepartment_get_tabs('leave');
echo $OUTPUT->tabtree($tabs, 'leave');

echo $OUTPUT->heading(get_string('leavetypes', 'local_hrdepartment'));

echo html_writer::link(
    new moodle_url('/local/hrdepartment/leave/types_edit.php'),
    get_string('addleavetype', 'local_hrdepartment'),
    ['class' => 'btn btn-primary mb-3']
);

$types = student_leave_manager::get_leave_types(false);

if (empty($types)) {
    echo $OUTPUT->notification(get_string('noleavetypes', 'local_hrdepartment'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('leavetypename', 'local_hrdepartment'),
        get_string('maxdaysperyear', 'local_hrdepartment'),
        get_string('requiresapproval', 'local_hrdepartment'),
        get_string('active', 'local_hrdepartment'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($types as $type) {
        $actions = [];
        $actions[] = html_writer::link(
            new moodle_url('/local/hrdepartment/leave/types_edit.php', ['id' => $type->id]),
            get_string('edit')
        );
        if (student_leave_manager::can_delete_leave_type($type->id)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/hrdepartment/leave/types_delete.php', ['id' => $type->id]),
                get_string('deleteleavetype', 'local_hrdepartment')
            );
        }

        $table->data[] = [
            format_string($type->name),
            $type->maxdaysperyear,
            $type->requiresapproval ? get_string('yes') : get_string('no'),
            $type->active ? get_string('yes') : get_string('no'),
            implode(' | ', $actions),
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
