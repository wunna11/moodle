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
 * Shared audit trail writer/reader for the Finance Department plugin.
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class audit_manager
 *
 * A single financedep_auditlog table (see db/install.xml) backs the
 * "history" requirement across every step of this plugin: fee structure
 * amount history (Step 7.2), fee record status-change history (Step
 * 7.8), payment edits/voids/refunds and scholarship/discount approvals
 * (Step 7.12's "full audit log of every financial change"). Every
 * manager class that creates/edits/cancels/approves a financedep_* row
 * should call log() right alongside the $DB write, in the same request -
 * this is intentionally not a DB trigger or event observer, so it is
 * only as complete as every call site remembers to be. If a new
 * financial change type is added later and it doesn't show up in a
 * record's history view, the first thing to check is whether its
 * manager method calls audit_manager::log() at all.
 */
class audit_manager {

    /**
     * Writes one audit trail entry.
     *
     * @param string $entitytype one of constants::AUDIT_ENTITY_*
     * @param int $entityid id of the row in the relevant financedep_* table
     * @param string $action one of constants::AUDIT_ACTION_*
     * @param array|null $olddata associative array snapshot before the change, null if not applicable (e.g. create)
     * @param array|null $newdata associative array snapshot after the change, null if not applicable (e.g. a pure delete/void with no replacement values)
     * @param int $userid who performed the action
     * @param string $reason optional human-entered reason (e.g. required for a payment void)
     * @return int the new financedep_auditlog id
     */
    public static function log(
        string $entitytype,
        int $entityid,
        string $action,
        ?array $olddata,
        ?array $newdata,
        int $userid,
        string $reason = ''
    ): int {
        global $DB;

        $record = new \stdClass();
        $record->entitytype = $entitytype;
        $record->entityid = $entityid;
        $record->action = $action;
        $record->olddata = $olddata !== null ? json_encode($olddata) : null;
        $record->newdata = $newdata !== null ? json_encode($newdata) : null;
        $record->reason = $reason !== '' ? $reason : null;
        $record->userid = $userid;
        $record->timecreated = time();

        return $DB->insert_record('financedep_auditlog', $record);
    }

    /**
     * Returns every audit trail entry for one entity, newest first.
     *
     * @param string $entitytype one of constants::AUDIT_ENTITY_*
     * @param int $entityid
     * @return \stdClass[] rows as stored, olddata/newdata still JSON-encoded strings (or null)
     */
    public static function get_history(string $entitytype, int $entityid): array {
        global $DB;

        return array_values($DB->get_records(
            'financedep_auditlog',
            ['entitytype' => $entitytype, 'entityid' => $entityid],
            'timecreated DESC'
        ));
    }
}
