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
 * Department listing: create/rename/delete hrdep_department rows. See
 * local_hrdepartment\department_manager and
 * access_manager::can_manage_departments() for why this is gated more
 * strictly than every other management section in this plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_hrdepartment\access_manager;
use local_hrdepartment\department_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage_departments();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/hrdepartment/departments/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('departments', 'local_hrdepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_hrdepartment'));

echo $OUTPUT->header();

echo local_hrdepartment_render_tab_bar('departments');

echo html_writer::start_div('local-hrdepartment-departments');

echo local_hrdepartment_render_page_hero(
    get_string('departments', 'local_hrdepartment'),
    get_string('departmentsdirectorysubtitle', 'local_hrdepartment'),
    [[
        'url' => new moodle_url('/local/hrdepartment/departments/edit.php'),
        'label' => get_string('adddepartment', 'local_hrdepartment'),
        'icon' => 'fa-plus',
    ]]
);

$departments = department_manager::get_all();

if (empty($departments)) {
    echo local_hrdepartment_render_empty_state(
        get_string('nodepartments', 'local_hrdepartment'),
        'fa-sitemap'
    );
} else {
    $table = new html_table();
    $table->head = [
        get_string('departmentname', 'local_hrdepartment'),
        get_string('departmentcode', 'local_hrdepartment'),
        get_string('employeecount', 'local_hrdepartment'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($departments as $department) {
        $actions = [];
        $actions[] = html_writer::link(
            new moodle_url('/local/hrdepartment/departments/edit.php', ['id' => $department->id]),
            get_string('edit')
        );

        // The Delete link is hidden entirely (not just refused server-side)
        // for a protected department name or one still in use - see
        // department_manager::is_protected()/is_in_use(). delete.php
        // still re-checks both server-side as a safety net for a stale
        // bookmarked link.
        if (!department_manager::is_protected($department->name) && !department_manager::is_in_use((int) $department->id)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/hrdepartment/departments/delete.php', ['id' => $department->id]),
                get_string('delete'),
                ['class' => 'text-danger']
            );
        }

        $table->data[] = [
            format_string($department->name),
            $department->code !== null ? s($department->code) : '-',
            (int) $department->employeecount,
            implode(' | ', $actions),
        ];
    }

    echo local_hrdepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
