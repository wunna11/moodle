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
 * A student's own list of fee records (self-service, read-only) - added
 * 2026-09-10 per the user's request that a student be able to see their
 * own paid/payment info and installment plan(s) in their own account.
 * Gated on local/financedepartment:viewownfeerecord, checked directly
 * (not via access_manager::can_manage()) - same pattern already
 * established for submitscholarshiprequest/submitdiscountrequest, see
 * db/access.php's docblock. Deliberately a SEPARATE, read-only section
 * from pages/feerecords/*.php (the finance-staff admin pages, gated on
 * managefeerecords) rather than reusing those pages with extra
 * permission branching - matches this plugin's established pattern of
 * always giving student self-service its own pages (scholarshiprequests,
 * discountrequests) instead of retrofitting the admin ones.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\feerecord_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/financedepartment:viewownfeerecord', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/myfeerecord/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('myfeerecord', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('myfeerecord');

echo html_writer::start_div('local-financedepartment-myfeerecord-index');

echo local_financedepartment_render_page_hero(
    get_string('myfeerecord', 'local_financedepartment'),
    get_string('myfeerecorddesc', 'local_financedepartment')
);

$records = feerecord_manager::get_for_student($USER->id);

if (empty($records)) {
    echo local_financedepartment_render_empty_state(get_string('myfeerecordsempty', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('feestructure', 'local_financedepartment'),
        get_string('totalamount', 'local_financedepartment'),
        get_string('paidamount', 'local_financedepartment'),
        get_string('balance', 'local_financedepartment'),
        get_string('status', 'local_financedepartment'),
        '',
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-myfeerecord-table';

    foreach ($records as $record) {
        $viewurl = new moodle_url('/local/financedepartment/pages/myfeerecord/view.php', ['id' => $record->id]);

        $table->data[] = [
            format_string($record->categoryname) . ' - ' . s($record->academicyear),
            local_financedepartment_format_money($record->totalamount),
            local_financedepartment_format_money($record->paidamount),
            local_financedepartment_format_money($record->balance),
            local_financedepartment_feerecord_status_badge($record),
            html_writer::link($viewurl, get_string('view')),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
