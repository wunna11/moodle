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
 * Hook callbacks for the Finance Department local plugin.
 *
 * Added 2026-09-10: on this Moodle version (5.2), a plugin's classic
 * extend_navigation() callback (see lib.php) only ever populates
 * $PAGE->navigation - the site navigation tree/drawer. The TOP primary
 * navigation bar (Home/Dashboard/My courses/...) is built separately by
 * core\navigation\views\primary::initialise(), which then dispatches
 * this hook so plugins can add their own node to THAT bar. See
 * classes/hooks/navigation/primary_extend.php and
 * \local_financedepartment\access_manager::can_view_navigation_entry()'s
 * docblock for the full investigation - this file exists specifically
 * because extend_navigation() alone was confirmed insufficient to make
 * the plugin discoverable in this site's top nav bar.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\navigation\primary_extend::class,
        'callback' => \local_financedepartment\hooks\navigation\primary_extend::class . '::callback',
        'priority' => 0,
    ],
];
