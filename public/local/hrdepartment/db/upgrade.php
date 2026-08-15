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

    if ($oldversion < 2026081200) {
        // An earlier build of this step created hrdep_studentattendance
        // for a self-contained "mark attendance" feature. That approach
        // was replaced before release: student attendance is read
        // straight from the site's existing mod_attendance activity data
        // instead (see local_hrdepartment\student_attendance_manager), so
        // this plugin never owns or writes attendance data of its own.
        // Nothing to do here for a fresh install.
        upgrade_plugin_savepoint(true, 2026081200, 'local', 'hrdepartment');
    }

    if ($oldversion < 2026081300) {
        // Clean up hrdep_studentattendance for any site that did run the
        // earlier 2026081200 step before the approach changed - safe/no-op
        // if it was never created.
        $table = new xmldb_table('hrdep_studentattendance');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_plugin_savepoint(true, 2026081300, 'local', 'hrdepartment');
    }

    if ($oldversion < 2026081400) {
        // Task 6: Leave Management, scoped to students per the project's
        // Entity Scope Isolation rule (see hrdepartment-entity-scope
        // memory). These are new, self-contained tables - there is no
        // existing site plugin for student leave to integrate with
        // instead (unlike Attendance/mod_attendance), so this module
        // owns its own schema, similar to the original Task 1 design.
        // The pre-existing hrdep_leavetype/hrdep_leavebalance/
        // hrdep_leaveapplication tables are employee-scoped and are left
        // alone (unused/reserved).
        $table = new xmldb_table('hrdep_studentleavetype');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL);
            $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null);
            $table->add_field('maxdaysperyear', XMLDB_TYPE_NUMBER, '6, 2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('requiresapproval', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
            $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('idx_name', XMLDB_INDEX_UNIQUE, ['name']);
            $table->add_index('idx_active', XMLDB_INDEX_NOTUNIQUE, ['active']);
            $dbman->create_table($table);
        }

        $table = new xmldb_table('hrdep_studentleaveapp');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('studentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null);
            $table->add_field('leavetypeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('startdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('enddate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('totaldays', XMLDB_TYPE_NUMBER, '6, 2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('reason', XMLDB_TYPE_TEXT, null, null, null);
            $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'pending');
            $table->add_field('submittedby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('reviewedby', XMLDB_TYPE_INTEGER, '10', null, null);
            $table->add_field('reviewnote', XMLDB_TYPE_TEXT, null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('idx_studentid', XMLDB_INDEX_NOTUNIQUE, ['studentid']);
            $table->add_index('idx_leavetypeid', XMLDB_INDEX_NOTUNIQUE, ['leavetypeid']);
            $table->add_index('idx_courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
            $table->add_index('idx_status', XMLDB_INDEX_NOTUNIQUE, ['status']);
            $table->add_index('idx_dates', XMLDB_INDEX_NOTUNIQUE, ['startdate', 'enddate']);
            $dbman->create_table($table);
        }

        $table = new xmldb_table('hrdep_studentleavebalance');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('studentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('leavetypeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_field('academicyear', XMLDB_TYPE_CHAR, '9', null, XMLDB_NOTNULL);
            $table->add_field('allocated', XMLDB_TYPE_NUMBER, '6, 2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('used', XMLDB_TYPE_NUMBER, '6, 2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('remaining', XMLDB_TYPE_NUMBER, '6, 2', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('idx_student_type_year', XMLDB_INDEX_UNIQUE, ['studentid', 'leavetypeid', 'academicyear']);
            $dbman->create_table($table);
        }

        // Seed a few common student leave types so the module isn't
        // empty on first use - HR can rename/add/deactivate freely
        // afterwards via Leave > Leave types.
        if (!$DB->record_exists('hrdep_studentleavetype', [])) {
            $now = time();
            $defaults = [
                ['name' => 'Medical Leave', 'maxdaysperyear' => 10],
                ['name' => 'Personal Leave', 'maxdaysperyear' => 5],
                ['name' => 'Emergency Leave', 'maxdaysperyear' => 5],
                ['name' => 'Family Leave', 'maxdaysperyear' => 3],
            ];
            foreach ($defaults as $default) {
                $DB->insert_record('hrdep_studentleavetype', (object) [
                    'name' => $default['name'],
                    'description' => null,
                    'maxdaysperyear' => $default['maxdaysperyear'],
                    'requiresapproval' => 1,
                    'active' => 1,
                    'timecreated' => $now,
                    'timemodified' => $now,
                ]);
            }
        }

        upgrade_plugin_savepoint(true, 2026081400, 'local', 'hrdepartment');
    }

    if ($oldversion < 2026081500) {
        // Task 6 was corrected mid-build, the same way Task 5 (Attendance)
        // was: the user already marks a student "on leave" as an
        // attendance status (e.g. "Leave"/"Excused") when taking
        // attendance in the site's mod_attendance activity, and does not
        // want a second, separate leave workflow living in this plugin.
        // Leave is now a read-only report on top of that mod_attendance
        // data (see local_hrdepartment\student_leave_manager), so the
        // self-contained tables and write workflow from 2026081400 are
        // dropped here - safe/no-op on a fresh install that never had
        // them.
        foreach (['hrdep_studentleaveapp', 'hrdep_studentleavebalance', 'hrdep_studentleavetype'] as $tablename) {
            $table = new xmldb_table($tablename);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        upgrade_plugin_savepoint(true, 2026081500, 'local', 'hrdepartment');
    }

    // Future upgrade steps go here, gated by $oldversion checks, e.g.:
    // if ($oldversion < 2026090100) {
    //     ... table/field changes via $dbman ...
    //     upgrade_plugin_savepoint(true, 2026090100, 'local', 'hrdepartment');
    // }

    return true;
}
