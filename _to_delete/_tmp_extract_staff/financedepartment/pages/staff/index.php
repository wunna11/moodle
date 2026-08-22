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
 * Finance staff list, searchable and filterable by status.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\employee_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managestaff');

$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/staff/index.php', [
    'search' => $search, 'status' => $status, 'page' => $page,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('financestaff', 'local_financedepartment'));
$PAGE->set_heading(get_string('pluginname', 'local_financedepartment'));

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('staff');

echo html_writer::start_div('local-financedepartment-staff');

echo local_financedepartment_render_page_hero(
    get_string('financestaff', 'local_financedepartment'),
    get_string('financestaffdesc', 'local_financedepartment'),
    [[
        'url' => new moodle_url('/local/financedepartment/pages/staff/edit.php'),
        'label' => get_string('addfinancestaff', 'local_financedepartment'),
        'icon' => 'fa-plus',
    ]]
);

// Filter bar.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => $PAGE->url->out_omit_querystring(),
    'class' => 'findept-filter-bar',
]);

echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'placeholder' => get_string('search'),
    'class' => 'findept-filter-text',
]);

$statusoptions = [
    '' => get_string('allstatuses', 'local_financedepartment'),
    constants::EMPLOYEE_STATUS_ACTIVE => get_string('status_active', 'local_financedepartment'),
    constants::EMPLOYEE_STATUS_INACTIVE => get_string('status_inactive', 'local_financedepartment'),
];
echo html_writer::select($statusoptions, 'status', $status, false, ['class' => 'findept-filter-select']);

echo html_writer::tag('button', get_string('filter', 'local_financedepartment'), [
    'type' => 'submit',
    'class' => 'btn btn-primary findept-filter-submit',
]);
echo html_writer::link(
    $PAGE->url->out_omit_querystring(),
    get_string('reset', 'local_financedepartment'),
    ['class' => 'findept-filter-reset']
);

echo html_writer::end_tag('form');

$perpage = employee_manager::PAGE_SIZE;
$total = employee_manager::count($search, $status);
$list = employee_manager::get_list($search, $status, $page, $perpage);

if (empty($list)) {
    echo local_financedepartment_render_empty_state(get_string('nofinancestaff', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('fullname'),
        get_string('employeecode', 'local_financedepartment'),
        get_string('designation', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-staff-table';

    foreach ($list as $employee) {
        $statusbadge = html_writer::span(
            get_string('status_' . $employee->status, 'local_financedepartment'),
            'badge badge-' . ($employee->status === constants::EMPLOYEE_STATUS_ACTIVE ? 'success' : 'secondary')
        );

        $actions = [];
        $actions[] = html_writer::link(
            new moodle_url('/local/financedepartment/pages/staff/edit.php', ['id' => $employee->id]),
            get_string('edit')
        );
        if ($employee->status === constants::EMPLOYEE_STATUS_ACTIVE) {
            $actions[] = html_writer::link(
                new moodle_url('/local/financedepartment/pages/staff/deactivate.php', ['id' => $employee->id]),
                get_string('deactivate', 'local_financedepartment')
            );
        } else {
            $actions[] = html_writer::link(
                new moodle_url('/local/financedepartment/pages/staff/deactivate.php', ['id' => $employee->id, 'reactivate' => 1]),
                get_string('reactivate', 'local_financedepartment')
            );
        }

        $table->data[] = [
            format_string($employee->fullname),
            s($employee->employeecode),
            $employee->designation !== '' ? format_string($employee->designation) : '-',
            $statusbadge,
            implode(' | ', $actions),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));

    $pagingbar = new paging_bar($total, $page, $perpage, $PAGE->url);
    echo $OUTPUT->render($pagingbar);
}

echo html_writer::end_div();

echo $OUTPUT->footer();
