<?php
// Standalone one-off Moodle CLI script - NOT part of local_hrdepartment's
// codebase (same pattern as the 2026-09-12 fix_studentleave_tables.php
// repair script). Place this file in your Moodle ROOT (the same folder
// as config.php) and run it from there.
//
// Phase 1 backfill for the HR/Finance access-model migration - see
// project memory hrdepartment-access-migration-plan.md for the full
// plan, and local_hrdepartment\role_sync_manager (v2026091400/0.8.2)
// for the code that now does this automatically for every NEW or
// UPDATED hrdep_employee record going forward.
//
// This script assigns the Moodle role "hrdepartmentstaff" or
// "financedepartmentstaff" (system context) to every EXISTING active
// Staff-type hrdep_employee record whose department is "HR" or
// "Finance", so already-existing staff are covered too, not just future
// changes.
//
// Idempotent / safe to re-run: skips anyone who already holds the
// correct role. Read-only against hrdep_employee/hrdep_department -
// only writes to Moodle's own role_assignments table via the standard
// role_assign() API.
//
// IMPORTANT: this script has ZERO effect on live access by itself.
// access_manager::can_manage() (both plugins) still decides access the
// old way (department name string-match) until a future Phase 2 code
// deploy switches it over to reading these role assignments instead.
// Running this script early, before Phase 2 ships, is safe and is in
// fact the point - it lets you verify (e.g. via the existing Finance
// "Access" page / access_summary_manager) that the role-derived grants
// match the employee-derived ones BEFORE cutover.
//
// Usage (run from your Moodle root):
//   php backfill_department_roles.php             (applies the changes)
//   php backfill_department_roles.php --dry-run    (report only, no writes)
//   php backfill_department_roles.php --help

define('CLI_SCRIPT', true);
require(__DIR__ . '/config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params(
    ['dry-run' => false, 'help' => false],
    ['h' => 'help']
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognized));
}

if ($options['help']) {
    echo "Backfill HR/Finance department roles from existing hrdep_employee records.\n\n";
    echo "Usage:\n";
    echo "  php backfill_department_roles.php\n";
    echo "  php backfill_department_roles.php --dry-run\n";
    echo "  php backfill_department_roles.php --help\n";
    exit(0);
}

$dryrun = (bool) $options['dry-run'];

// Must match local_hrdepartment\role_sync_manager::DEPARTMENT_ROLE_MAP
// exactly - keep the two in step by hand if either ever changes.
$map = [
    'HR' => 'hrdepartmentstaff',
    'Finance' => 'financedepartmentstaff',
];

$context = context_system::instance();

$roleids = [];
foreach ($map as $name => $shortname) {
    $role = $DB->get_record('role', ['shortname' => $shortname]);
    if (!$role) {
        cli_error(
            "Role '$shortname' does not exist yet on this site. Create it first: " .
            "Site administration > Users > Permissions > Define roles (Phase 0 of the migration plan)."
        );
    }
    $roleids[$name] = (int) $role->id;
}

$sql = "SELECT e.id AS employeeid, e.userid, e.employmentstatus, d.name AS departmentname
          FROM {hrdep_employee} e
          JOIN {hrdep_department} d ON d.id = e.departmentid
         WHERE e.type = :type";
$employees = $DB->get_records_sql($sql, ['type' => 'staff']);

$assigned = 0;
$alreadyok = 0;
$skippedinactive = 0;
$notmapped = 0;

foreach ($employees as $employee) {
    $matchedname = null;
    foreach ($map as $name => $shortname) {
        if (strcasecmp(trim($employee->departmentname), $name) === 0) {
            $matchedname = $name;
            break;
        }
    }

    if ($matchedname === null) {
        $notmapped++;
        continue; // Not an HR/Finance department employee - nothing to do.
    }

    if ($employee->employmentstatus !== 'active') {
        $skippedinactive++;
        echo "SKIP (status={$employee->employmentstatus}): userid={$employee->userid}, department={$employee->departmentname}\n";
        continue;
    }

    $roleid = $roleids[$matchedname];

    $already = $DB->record_exists('role_assignments', [
        'roleid' => $roleid,
        'userid' => $employee->userid,
        'contextid' => $context->id,
    ]);

    if ($already) {
        $alreadyok++;
        continue;
    }

    echo "ASSIGN: userid={$employee->userid}, department={$employee->departmentname}, role={$map[$matchedname]}\n";

    if (!$dryrun) {
        role_assign($roleid, $employee->userid, $context->id);
    }
    $assigned++;
}

echo "\n";
echo "Done" . ($dryrun ? " [DRY RUN - no changes made]" : "") . ".\n";
echo "  Newly assigned:      $assigned\n";
echo "  Already correct:     $alreadyok\n";
echo "  Skipped (inactive):  $skippedinactive\n";
echo "  Not HR/Finance:      $notmapped\n";
