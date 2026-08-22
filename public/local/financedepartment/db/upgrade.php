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
 * Upgrade steps for the Finance Department local plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade function for local_financedepartment.
 *
 * @param int $oldversion the version being upgraded from
 * @return bool always true on success
 */
function xmldb_local_financedepartment_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026082201) {
        // This plugin's own finance-staff table + pages/staff/* UI
        // (built 2026-08-22, shipped as v2026082200/0.2.1) is reversed
        // here per the user's decision the same day to connect with
        // local_hrdepartment instead: "is finance staff" is now decided
        // by an hrdep_employee row (type=staff) whose department is
        // "Finance" - see classes/access_manager.php. Drop the leftover
        // table for any site that ran Notifications on 2026082200
        // before this reversal landed; safe/no-op on a fresh install
        // that never had it.
        $table = new xmldb_table('financedep_employee');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_plugin_savepoint(true, 2026082201, 'local', 'financedepartment');
    }

    // Future upgrade steps go here, gated by $oldversion checks, e.g.:
    // if ($oldversion < 2026090100) {
    //     ... table/field changes via $dbman ...
    //     upgrade_plugin_savepoint(true, 2026090100, 'local', 'financedepartment');
    // }

    return true;
}
