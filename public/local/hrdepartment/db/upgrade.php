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
 * Upgrade steps for the HR Department local plugin.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade function for local_hrdepartment.
 *
 * @param int $oldversion the version being upgraded from
 * @return bool always true on success
 */
function xmldb_local_hrdepartment_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Future upgrade steps go here, gated by $oldversion checks, e.g.:
    // if ($oldversion < 2026090100) {
    //     ... table/field changes via $dbman ...
    //     upgrade_plugin_savepoint(true, 2026090100, 'local', 'hrdepartment');
    // }

    return true;
}
