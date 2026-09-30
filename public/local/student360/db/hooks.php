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
 * Hook callbacks for the Student 360 Profile local plugin.
 *
 * Registers the top primary-navigation-bar hook - on this site's Moodle
 * version (5.2), a plugin's classic extend_navigation() callback (see
 * lib.php) only populates $PAGE->navigation, NOT the top primary nav
 * bar - see classes/hooks/navigation/primary_extend.php and
 * local_financedepartment's own db/hooks.php (financedepartment-step711
 * project memory), where this exact gap was first found and fixed.
 *
 * @package   local_student360
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\navigation\primary_extend::class,
        'callback' => \local_student360\hooks\navigation\primary_extend::class . '::callback',
        'priority' => 0,
    ],
];
