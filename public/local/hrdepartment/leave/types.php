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
require_capability(student_leave_manager::CAP_MANAGE, $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/leave/types.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('leavetypes', 'local_hrdepartment'));
$PAGE->set_heading(student_leave_manager::get_page_heading());

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('leave');

echo html_writer::start_div('local-hrdepartment-leave');

echo local_hrdepartment_render_page_hero(
    get_string('leavetypes', 'local_hrdepartment'),
    get_string('leavetypessubtitle', 'local_hrdepartment'),
    [[
        'url' => new moodle_url('/local/hrdepartment/leave/types_edit.php'),
        'label' => get_string('addleavetype', 'local_hrdepartment'),
        'icon' => 'fa-plus',
    ]]
);

$types = student_leave_manager::get_leave_types(false);

if (empty($types)) {
    echo local_hrdepartment_render_empty_state(
        get_string('noleavetypes', 'local_hrdepartment'),
        'fa-tags'
    );
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

    echo local_hrdepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
