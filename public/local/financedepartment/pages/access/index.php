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
 * Step 7.12 (Access): a read-only permission summary page - this
 * plugin's own capabilities with which Moodle roles currently hold each
 * one, plus the actual Finance Department staff list (the PRIMARY
 * real-world access path, via access_manager's blanket hrdep_employee
 * grant - see that class's docblock).
 *
 * Deliberately READ-ONLY - the user was offered a choice between this
 * (Option 1) and a custom permission-editing UI (Option 2, which would
 * have meant either reimplementing chunks of Moodle's own role/
 * capability editor, or letting this page write role_capabilities/
 * hrdep_employee rows directly - both judged unnecessary risk for what
 * the user actually asked for: visibility into who currently has
 * access, not a new way to grant it). Editing still goes through
 * Moodle's stock "Define roles" UI (linked from this page) for role
 * capabilities, or local_hrdepartment's Staff pages for Finance staff
 * membership. See [[financedepartment-step712]] project memory for the
 * full AskUserQuestion scope decision (gating, page content, navigation
 * placement - all three "Recommended" options were chosen).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\access_summary_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:viewfinancereports');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/access/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('accesssummary', 'local_financedepartment'));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('access');

echo html_writer::start_div('local-financedepartment-access');

echo local_financedepartment_render_page_hero(
    get_string('accesssummary', 'local_financedepartment'),
    get_string('accesssummarydesc', 'local_financedepartment')
);

// Finance staff list - the primary, real-world access grant (see
// access_summary_manager::get_finance_staff_list()'s docblock) - shown
// FIRST and with its own explanatory note, so this isn't misread as "the
// only way in is a role assignment" when it's actually secondary here.
echo html_writer::tag('h3', get_string('financestafflist', 'local_financedepartment'), ['class' => 'findept-section-title']);
echo html_writer::tag('p', get_string('financestafflistdesc', 'local_financedepartment'), ['class' => 'findept-form-intro']);

$staff = access_summary_manager::get_finance_staff_list();

if (empty($staff)) {
    echo local_financedepartment_render_empty_state(get_string('nofinancestaff', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('staffname', 'local_financedepartment'),
        get_string('staffemail', 'local_financedepartment'),
        get_string('staffdesignation', 'local_financedepartment'),
        get_string('staffemployeecode', 'local_financedepartment'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-access-staff-table';

    foreach ($staff as $row) {
        $table->data[] = [
            s(fullname($row)),
            s($row->email),
            $row->designation !== null && $row->designation !== '' ? s($row->designation) : '-',
            s($row->employeecode),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

// Capability / role summary.
echo html_writer::tag('h3', get_string('capabilitysummary', 'local_financedepartment'), ['class' => 'findept-section-title']);
echo html_writer::tag('p', get_string('capabilitysummarydesc', 'local_financedepartment'), ['class' => 'findept-form-intro']);

if (is_siteadmin()) {
    echo html_writer::div(
        html_writer::link(
            new moodle_url('/admin/roles/manage.php'),
            html_writer::tag('i', '', ['class' => 'icon fa fa-external-link', 'aria-hidden' => 'true']) . ' '
                . get_string('managerolesinmoodle', 'local_financedepartment'),
            ['class' => 'findept-page-hero-btn']
        ),
        '',
        ['style' => 'margin-bottom: 1rem;']
    );
}

$capabilities = access_summary_manager::get_capability_summary();

$table = new html_table();
$table->head = [
    get_string('capabilityname', 'local_financedepartment'),
    get_string('capabilitydescription', 'local_financedepartment'),
    get_string('assignedroles', 'local_financedepartment'),
];
$table->attributes['class'] = 'generaltable local-financedepartment-access-capability-table';

foreach ($capabilities as $cap) {
    $typebadge = html_writer::span(
        get_string('capabilitytype_' . $cap->captype, 'local_financedepartment'),
        'badge badge-' . ($cap->captype === 'write' ? 'warning' : 'secondary')
    );

    $namecell = html_writer::tag('strong', s($cap->displayname))
        . html_writer::tag('div', s($cap->name), ['class' => 'findept-form-intro', 'style' => 'font-size:0.8rem;'])
        . ' ' . $typebadge;

    if (empty($cap->roles)) {
        $rolescell = html_writer::span(get_string('noroles', 'local_financedepartment'), 'text-muted');
    } else {
        $rolescell = '';
        foreach ($cap->roles as $rolename) {
            $rolescell .= html_writer::span(s($rolename), 'badge badge-info', ['style' => 'margin: 0 0.25rem 0.25rem 0;']);
        }
    }

    $table->data[] = [
        $namecell,
        s($cap->description),
        $rolescell,
    ];
}

echo local_financedepartment_render_table_card(html_writer::table($table));

echo html_writer::end_div();

echo $OUTPUT->footer();
