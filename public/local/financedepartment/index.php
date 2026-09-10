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
 * Finance Department landing page. A lightweight hub for now - just a
 * quicklink to Fee Structures - that grows into the full dashboard
 * (summary cards, reports) once Step 7.11 is built.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_financedepartment\access_manager;

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

$canmanagefees = access_manager::can_manage('local/financedepartment:managefeestructures');
$canmanagefeerecords = access_manager::can_manage('local/financedepartment:managefeerecords');
$canmanagescholarships = access_manager::can_manage('local/financedepartment:managescholarships');
$canapprovescholarships = access_manager::can_manage('local/financedepartment:approvescholarships');
$canmanagediscounts = access_manager::can_manage('local/financedepartment:managediscounts');
$canapprovediscounts = access_manager::can_manage('local/financedepartment:approvediscounts');
$canmanageinstallments = access_manager::can_manage('local/financedepartment:manageinstallments');
$canrecordpayments = access_manager::can_manage('local/financedepartment:recordpayments');
$canmanagerefunds = access_manager::can_manage('local/financedepartment:managerefunds');
$canviewreports = has_capability('local/financedepartment:viewfinancereports', $context);
$canviewown = has_capability('local/financedepartment:viewownfeerecord', $context);
// Plain per-user self-service capabilities (2026-09-09 fix), NOT routed
// through access_manager - see db/access.php's docblock and
// pages/scholarshiprequests/submit.php's docblock for the full history.
$cansubmitscholarship = has_capability('local/financedepartment:submitscholarshiprequest', $context);
$cansubmitdiscount = has_capability('local/financedepartment:submitdiscountrequest', $context);

if (!$canmanagefees && !$canmanagefeerecords && !$canmanagescholarships && !$canapprovescholarships
        && !$canmanagediscounts && !$canapprovediscounts && !$canmanageinstallments
        && !$canrecordpayments && !$canmanagerefunds && !$canviewreports && !$canviewown
        && !$cansubmitscholarship && !$cansubmitdiscount) {
    throw new moodle_exception('nopermissions', 'error', '', get_string('pluginname', 'local_financedepartment'));
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/financedepartment/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(access_manager::get_display_name());
$PAGE->set_heading(access_manager::get_display_name());

echo $OUTPUT->header();

echo local_financedepartment_render_tab_bar('');

echo html_writer::start_div('local-financedepartment-dashboard');

echo local_financedepartment_render_page_hero(
    access_manager::get_display_name(),
    get_string('dashboardsubtitle', 'local_financedepartment')
);

echo html_writer::start_div('findept-quicklink-grid');

if ($canmanagefees) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/fees/index.php'),
        get_string('feestructures', 'local_financedepartment'),
        'fa-list-alt'
    );
}

if ($canmanagefeerecords) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/feerecords/index.php'),
        get_string('feerecords', 'local_financedepartment'),
        'fa-id-card'
    );
} else if ($canviewown) {
    // Student self-service (2026-09-10, per the user's request) - same
    // routing split as the scholarships/discounts tiles below.
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/myfeerecord/index.php'),
        get_string('myfeerecord', 'local_financedepartment'),
        'fa-id-card'
    );
}

if ($canmanagescholarships || $canapprovescholarships) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/scholarships/index.php'),
        get_string('scholarships', 'local_financedepartment'),
        'fa-graduation-cap'
    );
} else if ($cansubmitscholarship) {
    // Student self-service (2026-09-09 fix) - a plain student can't
    // reach the scholarship DEFINITIONS page (managescholarships-gated),
    // so their tile routes straight to their own request list instead.
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/scholarshiprequests/index.php'),
        get_string('scholarships', 'local_financedepartment'),
        'fa-graduation-cap'
    );
}

if ($canmanagediscounts || $canapprovediscounts) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/discounts/index.php'),
        get_string('discounts', 'local_financedepartment'),
        'fa-tags'
    );
} else if ($cansubmitdiscount) {
    // Same routing split as scholarships above.
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/discountrequests/index.php'),
        get_string('discounts', 'local_financedepartment'),
        'fa-tags'
    );
}

if ($canmanageinstallments) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/installments/index.php'),
        get_string('installmentplans', 'local_financedepartment'),
        'fa-calendar-check-o'
    );
}

if ($canrecordpayments || $canmanagerefunds) {
    echo local_financedepartment_render_quicklink(
        new moodle_url('/local/financedepartment/pages/payments/index.php'),
        get_string('payments', 'local_financedepartment'),
        'fa-money'
    );
}

echo html_writer::end_div();

if (!$canmanagefees && !$canmanagefeerecords && !$canmanagescholarships && !$canapprovescholarships
        && !$canmanagediscounts && !$canapprovediscounts && !$canmanageinstallments
        && !$canrecordpayments && !$canmanagerefunds && !$cansubmitscholarship && !$cansubmitdiscount
        && !$canviewown) {
    echo local_financedepartment_render_empty_state(get_string('nosectionsyet', 'local_financedepartment'));
}

echo html_writer::end_div();

echo $OUTPUT->footer();
