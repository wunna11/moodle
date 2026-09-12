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
 * View one discount definition's details and its edit history.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;
use local_financedepartment\audit_manager;
use local_financedepartment\constants;
use local_financedepartment\discount_manager;

require_once(__DIR__ . '/../../../../config.php');

require_login();
$PAGE->set_primary_active_tab('local_financedepartment');

$id = required_param('id', PARAM_INT);

$context = context_system::instance();
access_manager::require_manage('local/financedepartment:managediscounts');

$discount = discount_manager::get($id);
if (!$discount) {
    throw new moodle_exception(
        'errordiscountnotfound',
        'local_financedepartment',
        new moodle_url('/local/financedepartment/pages/discounts/index.php')
    );
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/pages/discounts/view.php', ['id' => $id]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(format_string($discount->name));
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('discounts');

echo html_writer::start_div('local-financedepartment-discounts-view');

echo local_financedepartment_render_back_link(
    new moodle_url('/local/financedepartment/pages/discounts/index.php'),
    get_string('backtodiscounts', 'local_financedepartment')
);

echo local_financedepartment_render_page_hero(
    format_string($discount->name),
    get_string('discountdetails', 'local_financedepartment'),
    [[
        'url' => new moodle_url('/local/financedepartment/pages/discounts/edit.php', ['id' => $id]),
        'label' => get_string('edit'),
        'icon' => 'fa-pencil-alt',
    ]]
);

// Details card.
echo html_writer::start_div('findept-detail-card');

$amount = (float) $discount->amountvalue;
$amountlabel = $discount->amounttype === constants::AMOUNT_TYPE_PERCENTAGE
    ? (rtrim(rtrim(number_format($amount, 2), '0'), '.') . '%')
    : local_financedepartment_format_money($amount);

$rows = [
    [get_string('discountname', 'local_financedepartment'), format_string($discount->name)],
    [get_string('discounttype', 'local_financedepartment'), get_string('discounttype_' . $discount->type, 'local_financedepartment')],
    [get_string('amounttype', 'local_financedepartment'), get_string('amounttype_' . $discount->amounttype, 'local_financedepartment')],
    [get_string('amountvalue', 'local_financedepartment'), $amountlabel],
    [get_string('status', 'local_financedepartment'), html_writer::span(
        get_string('status_' . $discount->status, 'local_financedepartment'),
        'badge badge-' . ($discount->status === constants::DISCOUNT_STATUS_ACTIVE ? 'success' : 'secondary')
    )],
    [get_string('description', 'local_financedepartment'), $discount->description !== '' ? format_text($discount->description, FORMAT_PLAIN) : '-'],
    [get_string('lastupdated', 'local_financedepartment'), userdate($discount->timemodified)],
];

echo html_writer::start_tag('dl', ['class' => 'findept-detail-list']);
foreach ($rows as [$label, $value]) {
    echo html_writer::tag('dt', $label);
    echo html_writer::tag('dd', $value);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div();

// History.
echo html_writer::tag('h3', get_string('discounthistory', 'local_financedepartment'), ['class' => 'findept-section-title']);

$history = audit_manager::get_history(constants::AUDIT_ENTITY_DISCOUNT, $id);

if (empty($history)) {
    echo local_financedepartment_render_empty_state(get_string('nohistoryyet', 'local_financedepartment'));
} else {
    $table = new html_table();
    $table->head = [
        get_string('when', 'local_financedepartment'),
        get_string('who', 'local_financedepartment'),
        get_string('action', 'local_financedepartment'),
        get_string('change', 'local_financedepartment'),
    ];
    $table->attributes['class'] = 'generaltable local-financedepartment-history-table';

    $fieldlabels = [
        'name' => get_string('discountname', 'local_financedepartment'),
        'type' => get_string('discounttype', 'local_financedepartment'),
        'amounttype' => get_string('amounttype', 'local_financedepartment'),
        'amountvalue' => get_string('amountvalue', 'local_financedepartment'),
        'description' => get_string('description', 'local_financedepartment'),
        'status' => get_string('status', 'local_financedepartment'),
    ];

    foreach ($history as $entry) {
        $user = \core_user::get_user($entry->userid);
        $who = $user ? fullname($user) : get_string('unknownuser', 'local_financedepartment');

        $old = $entry->olddata !== null ? json_decode($entry->olddata, true) : [];
        $new = $entry->newdata !== null ? json_decode($entry->newdata, true) : [];

        $changelines = [];
        if ($entry->action === constants::AUDIT_ACTION_CREATE) {
            $changelines[] = get_string('historycreated', 'local_financedepartment');
        } else {
            $fields = array_unique(array_merge(array_keys((array) $old), array_keys((array) $new)));
            foreach ($fields as $field) {
                $oldval = $old[$field] ?? '-';
                $newval = $new[$field] ?? '-';

                if ($field === 'amountvalue') {
                    $oldval = is_numeric($oldval) ? local_financedepartment_format_money($oldval) : $oldval;
                    $newval = is_numeric($newval) ? local_financedepartment_format_money($newval) : $newval;
                } else if ($field === 'type') {
                    $oldval = is_string($oldval) && $oldval !== '-' ? get_string('discounttype_' . $oldval, 'local_financedepartment') : $oldval;
                    $newval = is_string($newval) && $newval !== '-' ? get_string('discounttype_' . $newval, 'local_financedepartment') : $newval;
                } else if ($field === 'amounttype') {
                    $oldval = is_string($oldval) && $oldval !== '-' ? get_string('amounttype_' . $oldval, 'local_financedepartment') : $oldval;
                    $newval = is_string($newval) && $newval !== '-' ? get_string('amounttype_' . $newval, 'local_financedepartment') : $newval;
                } else if ($field === 'status') {
                    $oldval = is_string($oldval) && $oldval !== '-' ? get_string('status_' . $oldval, 'local_financedepartment') : $oldval;
                    $newval = is_string($newval) && $newval !== '-' ? get_string('status_' . $newval, 'local_financedepartment') : $newval;
                }

                $label = $fieldlabels[$field] ?? $field;
                $changelines[] = html_writer::tag('strong', $label) . ': ' . s((string) $oldval) . ' &rarr; ' . s((string) $newval);
            }
        }
        if (!empty($entry->reason)) {
            $changelines[] = html_writer::tag('em', get_string('reason', 'local_financedepartment') . ': ' . s($entry->reason));
        }

        $table->data[] = [
            userdate($entry->timecreated),
            $who,
            get_string('auditaction_' . $entry->action, 'local_financedepartment'),
            implode(html_writer::empty_tag('br'), $changelines),
        ];
    }

    echo local_financedepartment_render_table_card(html_writer::table($table));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
