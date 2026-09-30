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
 * Capability definitions for the Student 360 Profile local plugin.
 *
 * Both capabilities are defined at CONTEXT_SYSTEM since the data they
 * gate (fee/attendance/leave records) is itself organisation-wide, not
 * tied to a Moodle course context - same rationale as
 * local_financedepartment's own db/access.php.
 *
 * local/student360:view is checked via
 * local_student360\access_manager::can_view_any(), which ADDITIONALLY
 * grants access to anyone local_hrdepartment/local_financedepartment
 * already recognise as department staff (see that class's docblock) -
 * this capability is an extra, independent way in, not the only path.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [

    // Search for and view any student's aggregated 360 profile.
    'local/student360:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],

    // Self-service: view the logged-in user's own aggregated profile.
    'local/student360:viewown' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'user' => CAP_ALLOW,
        ],
    ],
];
