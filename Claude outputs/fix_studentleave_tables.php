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
 * ONE-OFF REPAIR SCRIPT - local_hrdepartment student-leave schema fix.
 *
 * WHY THIS EXISTS
 * ----------------
 * This site's copy of local_hrdepartment already has its plugin version
 * (mdl_config_plugins) stamped at/above 2026081400, but the three
 * student-leave tables that version implies were never actually created
 * - because db/install.xml was out of sync with db/upgrade.php (see this
 * plugin's 2026-09-12 fix, v2026090602/0.7.0, and the
 * hrdepartment-studentleave-schema-fix note in project memory). Every
 * logged-in page on this site calls student_leave_manager::is_approver(),
 * which queries hrdep_studentleaveapp - so until these tables exist,
 * EVERY page crashes with "Table hrdep_studentleaveapp does not exist".
 *
 * Moodle will not re-run upgrade.php's table-creation steps on its own
 * just because you deploy fixed code - it only looks at whether the
 * stored version number changed. So even after deploying the corrected
 * plugin code (which stops NEW installs from ever hitting this), THIS
 * site still needs its three missing tables created directly, once. That
 * is all this script does - it does NOT touch mdl_config_plugins, does
 * NOT call upgrade_plugin_savepoint(), and is safe to run more than once
 * (every step is guarded by table_exists()/field_exists()).
 *
 * HOW TO USE
 * ----------
 * 1. Copy this ONE file to: <moodle-root>/local/hrdepartment/cli/fix_studentleave_tables.php
 *    (i.e. alongside this plugin's other files on the server - moodle
 *    root is the folder containing config.php, e.g. "public/" in this
 *    project's checkout).
 * 2. SSH into the server and run:
 *        php local/hrdepartment/cli/fix_studentleave_tables.php
 *    (run it from the moodle root, or adjust the require path below if
 *    your shell's working directory differs).
 * 3. Read the output - it reports exactly what it created. Then reload
 *    the site; the crash should be gone immediately (no cache purge
 *    needed - this only changes the database, not code/strings).
 * 4. Delete this file from the server afterwards; it has done its job
 *    and should not be left lying around or reachable over the web.
 *
 * This intentionally mirrors, field-for-field, index-for-index, EXACTLY
 * what db/upgrade.php's 2026081400/2026081600/2026081900 steps create -
 * see that file (and db/install.xml, now fixed to match) for the
 * authoritative shape if this plugin's schema changes again later.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/ddllib.php');

global $DB;

$dbman = $DB->get_manager();
$created = [];
$skipped = [];

// --- hrdep_studentleavetype -------------------------------------------
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
    $created[] = 'hrdep_studentleavetype';
} else {
    $skipped[] = 'hrdep_studentleavetype (already exists)';
}

// --- hrdep_studentleaveapp ----------------------------------------------
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
    // approverid is positioned after submittedby to match the column
    // order an incrementally-upgraded site ends up with (it was added
    // by the later 2026081900 upgrade step, not the original create).
    $table->add_field('approverid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'submittedby');
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
    $table->add_index('idx_approverid', XMLDB_INDEX_NOTUNIQUE, ['approverid']);
    $dbman->create_table($table);
    $created[] = 'hrdep_studentleaveapp';
} else {
    $skipped[] = 'hrdep_studentleaveapp (already exists)';

    // Table exists but may pre-date the 2026081900 approverid addition
    // (e.g. it was created some other way). Add it if missing, same as
    // upgrade.php's own guarded step would.
    $field = new xmldb_field('approverid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'submittedby');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
        $created[] = 'hrdep_studentleaveapp.approverid field';

        $index = new xmldb_index('idx_approverid', XMLDB_INDEX_NOTUNIQUE, ['approverid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
    }
}

// --- hrdep_studentleavebalance ------------------------------------------
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
    $created[] = 'hrdep_studentleavebalance';
} else {
    $skipped[] = 'hrdep_studentleavebalance (already exists)';
}

// Seed the same default leave types upgrade.php seeds on a fresh create,
// so this repair leaves the site in the same state an incremental
// upgrade would have - only if the type table is empty (never overwrites
// anything HR may have already configured).
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
    $created[] = '4 default leave types seeded into hrdep_studentleavetype';
}

cli_writeln('local_hrdepartment student-leave schema repair finished.');
cli_writeln('');
if ($created) {
    cli_writeln('Created/changed:');
    foreach ($created as $item) {
        cli_writeln('  - ' . $item);
    }
} else {
    cli_writeln('Nothing to do - all three tables already existed with the expected shape.');
}
if ($skipped) {
    cli_writeln('');
    cli_writeln('Already present (left untouched):');
    foreach ($skipped as $item) {
        cli_writeln('  - ' . $item);
    }
}
cli_writeln('');
cli_writeln('Reload the site now - the crash should be gone. You can delete this file.');
