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

    if ($oldversion < 2026082400) {
        // Step 7.4 (Scholarship Management) originally shipped its
        // schema in Step 7.1 with a `type` classification column
        // (merit|needbased|sibling|staffward|other). Per the user's
        // 2026-08-23 request, that classification is removed and
        // replaced with a program-level restriction instead: a
        // scholarship now belongs to exactly one course category, the
        // same pattern financedep_feestructure already uses - see
        // classes/scholarship_manager.php. financedep_scholarship had no
        // working create UI before this step, so this table is expected
        // to be empty on every real site; still handled properly (not
        // just assumed) in case a row was ever inserted directly.
        $table = new xmldb_table('financedep_scholarship');

        $index = new xmldb_index('idx_type', XMLDB_INDEX_NOTUNIQUE, ['type']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $typefield = new xmldb_field('type', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $typefield)) {
            $dbman->drop_field($table, $typefield);
        }

        $categoryfield = new xmldb_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, 'name');
        if (!$dbman->field_exists($table, $categoryfield)) {
            $dbman->add_field($table, $categoryfield);
        }

        $categoryindex = new xmldb_index('idx_categoryid', XMLDB_INDEX_NOTUNIQUE, ['categoryid']);
        if (!$dbman->index_exists($table, $categoryindex)) {
            $dbman->add_index($table, $categoryindex);
        }

        upgrade_plugin_savepoint(true, 2026082400, 'local', 'financedepartment');
    }

    if ($oldversion < 2026091004) {
        // The user reported a scholarship request shouldn't need a fee
        // record picked at all (v2026091004/0.8.0) - the field is gone
        // from scholarshiprequest_form entirely, so
        // financedep_scholarshipreq.feerecordid must become nullable to
        // let scholarshiprequest_manager::submit() store null going
        // forward. Existing rows (which all have a real feerecordid from
        // before this change) are left completely untouched - only the
        // column's NOT NULL constraint changes, see
        // scholarshiprequest_manager's class docblock for how approve()/
        // delete() branch on whether a row's feerecordid is null.
        //
        // financedep_scholarshipreq.feerecordid has a non-unique index
        // (idx_feerecordid, see db/install.xml) - change_field_notnull()
        // refuses to alter a field with a dependent index attached
        // (ddl_dependency_exception, thrown by
        // database_manager::check_field_dependencies() before anything is
        // actually altered) on some DB drivers. The index has to be
        // dropped first, the field altered, then the index re-added -
        // this exact three-step dance is the fix for that error; simply
        // re-running the original single-step version against the same
        // schema will hit the same exception every time.
        $table = new xmldb_table('financedep_scholarshipreq');
        $field = new xmldb_field('feerecordid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'studentid');
        $index = new xmldb_index('idx_feerecordid', XMLDB_INDEX_NOTUNIQUE, ['feerecordid']);

        if ($dbman->field_exists($table, $field)) {
            $indexexisted = $dbman->index_exists($table, $index);
            if ($indexexisted) {
                $dbman->drop_index($table, $index);
            }

            $dbman->change_field_notnull($table, $field);

            if ($indexexisted && !$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        upgrade_plugin_savepoint(true, 2026091004, 'local', 'financedepartment');
    }

    // Future upgrade steps go here, gated by $oldversion checks, e.g.:
    // if ($oldversion < 2026090100) {
    //     ... table/field changes via $dbman ...
    //     upgrade_plugin_savepoint(true, 2026090100, 'local', 'financedepartment');
    // }

    return true;
}
