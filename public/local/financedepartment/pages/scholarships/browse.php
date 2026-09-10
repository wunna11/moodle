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
 * Scholarship catalog - lets a viewer see which course categories/
 * programs currently have an active scholarship, before deciding whether
 * to submit a request. Added 2026-09-10 per the user's explicit request
 * ("students should be able to see which programs have a scholarship").
 *
 * Deliberately NOT gated on local/financedepartment:submitscholarshiprequest
 * or any manage/approve capability - confirmed via AskUserQuestion that
 * ANY logged-in user should be able to browse this catalog (a plain
 * require_login() is the only check). Scholarship DEFINITIONS are not
 * sensitive the way a request's own supporting documents are - see
 * lib.php's local_financedepartment_pluginfile() docblock for why THAT
 * stays capability-gated; this page shows no student-specific data at
 * all, only the shared scholarship definitions everyone is eligible to
 * be considered for.
 *
 * This is NOT a replacement for pages/scholarships/index.php (the
 * finance-staff CRUD list, still managescholarships-gated) - that page
 * lets finance staff edit/deactivate scholarships; this page is a
 * read-only catalog for everyone else, reusing
 * scholarship_manager::get_active_catalog() (system-wide, not scoped to
 * any one student's own categories - the user confirmed this scope via
 * AskUserQuestion too). The "Request this" link per row is only shown to
 * a viewer who actually holds submitscholarshiprequest, so a viewer
 * without it (e.g. that capability was revoked for their role) never
 * gets a link into a page they'd just be denied on.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\constants;
use local_financedepartment\scholarship_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/scholarships/browse.php'));
$PAGE->set_pagelayout('standard');
$title = get_string('scholarshipcatalog', 'local_financedepartment');
$PAGE->set_title($title);
$PAGE->set_heading(access_manager::get_display_name());

// Only used to decide whether to show a "Request this" link per row -
// see this file's docblock. A plain has_capability() check, same as
// everywhere else this self-service capability is used.
$cansubmit = has_capability('local/financedepartment:submitscholarshiprequest', $context);

$scholarships = scholarship_manager::get_active_catalog();

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('scholarships');

echo html_writer::start_div('local-financedepartment-scholarship-browse');

echo local_financedepartment_render_page_hero($title, get_string('scholarshipcatalogdesc', 'local_financedepartment'));

if (empty($scholarships)) {
    echo local_financedepartment_render_empty_state(get_string('nocatalogscholarships', 'local_financedepartment'));
} else {
    $table = new html_table();

    $head = [
        get_string('category', 'local_financedepartment'),
        get_string('scholarshipname', 'local_financedepartment'),
        get_string('amountvalue', 'local_financedepartment'),
        get_string('description', 'local_financedepartment'),
    ];
    if ($cansubmit) {
        $head[] = get_string('actions');
    }
    $table->head = $head;
    $table->attributes['class'] = 'generaltable local-financedepartment-scholarship-catalog-table';

    foreach ($scholarships as $scholarship) {
        $amount = (float) $scholarship->amountvalue;
        $amountlabel = $scholarship->amounttype === constants::AMOUNT_TYPE_PERCENTAGE
            ? (rtrim(rtrim(number_format($amount, 2), '0'), '.') . '%')
            : local_financedepartment_format_money($amount);

        $row = [
            format_string($scholarship->categoryname),
            format_string($scholarship->name),
            $amountlabel,
            $scholarship->description !== '' ? format_text($scholarship->description, FORMAT_PLAIN) : '-',
        ];

        if ($cansubmit) {
            // Presets the scholarshipid field on the submit form - see
            // classes/form/scholarshiprequest_form.php's `presetscholarshipid`
            // customdata handling.
            $row[] = html_writer::link(
                new moodle_url(
                    '/local/financedepartment/pages/scholarshiprequests/submit.php',
                    ['scholarshipid' => $scholarship->id]
                ),
                get_string('requestthisscholarship', 'local_financedepartment')
            );
        }

        $table->data[] = $row;
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
