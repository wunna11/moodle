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
 * Business logic for fee structure management (Step 7.2).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class feestructure_manager
 *
 * A fee structure is priced per course category + academic year, never
 * per individual course (see classes/constants.php's pricing-level
 * note). Every create/edit/status-change call writes a matching
 * audit_manager entry so pages/fees/view.php can show old-vs-current
 * amount history (Step 7.2's requirement) without a separate history
 * table.
 */
class feestructure_manager {

    /**
     * The editable fields snapshotted into the audit log on create/edit,
     * and diffed to build the "what changed" newdata array on edit.
     *
     * @var string[]
     */
    const AUDITED_FIELDS = ['categoryid', 'academicyear', 'amount', 'description', 'status'];

    /**
     * Returns one fee structure with its course category name joined in,
     * or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT f.*, cc.name AS categoryname
                  FROM {financedep_feestructure} f
                  JOIN {course_categories} cc ON cc.id = f.categoryid
                 WHERE f.id = :id";

        return $DB->get_record_sql($sql, ['id' => $id]);
    }

    /**
     * Whether an ACTIVE fee structure already exists for this category +
     * academic year, excluding one id (used when editing). Enforced at
     * the application layer, not a DB unique constraint (see
     * db/install.xml's idx_category_year comment) - editing an existing
     * structure's amount is the intended way to change a price, not
     * creating a second active row for the same category/year.
     *
     * @param int $categoryid
     * @param string $academicyear
     * @param int $excludeid
     * @return bool
     */
    public static function has_active_duplicate(int $categoryid, string $academicyear, int $excludeid = 0): bool {
        global $DB;

        $params = [
            'categoryid' => $categoryid,
            'academicyear' => $academicyear,
            'status' => constants::FEESTRUCTURE_STATUS_ACTIVE,
        ];
        $sql = 'categoryid = :categoryid AND academicyear = :academicyear AND status = :status';

        if ($excludeid) {
            $sql .= ' AND id <> :excludeid';
            $params['excludeid'] = $excludeid;
        }

        return $DB->record_exists_select('financedep_feestructure', $sql, $params);
    }

    /**
     * Creates a new fee structure and logs a CREATE audit entry.
     *
     * @param \stdClass $data form data: categoryid, academicyear, amount, description
     * @param int $usermodified
     * @return int the new fee structure id
     */
    public static function create(\stdClass $data, int $usermodified): int {
        global $DB;

        $now = time();

        $record = new \stdClass();
        $record->categoryid = (int) $data->categoryid;
        $record->academicyear = trim($data->academicyear);
        $record->amount = (float) $data->amount;
        $record->description = $data->description ?? '';
        $record->status = constants::FEESTRUCTURE_STATUS_ACTIVE;
        $record->timecreated = $now;
        $record->timemodified = $now;
        $record->usermodified = $usermodified;

        $id = $DB->insert_record('financedep_feestructure', $record);

        audit_manager::log(
            constants::AUDIT_ENTITY_FEESTRUCTURE,
            $id,
            constants::AUDIT_ACTION_CREATE,
            null,
            self::snapshot($record),
            $usermodified
        );

        return $id;
    }

    /**
     * Updates an existing fee structure and logs an EDIT audit entry
     * containing only the fields that actually changed (always including
     * amount if it changed, satisfying Step 7.2's "old vs current
     * amount" history requirement).
     *
     * @param int $id
     * @param \stdClass $data form data: categoryid, academicyear, amount, description
     * @param int $usermodified
     * @return void
     */
    public static function update(int $id, \stdClass $data, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_feestructure', ['id' => $id], '*', MUST_EXIST);

        $record = new \stdClass();
        $record->id = $id;
        $record->categoryid = (int) $data->categoryid;
        $record->academicyear = trim($data->academicyear);
        $record->amount = (float) $data->amount;
        $record->description = $data->description ?? '';
        $record->timemodified = time();
        $record->usermodified = $usermodified;

        $DB->update_record('financedep_feestructure', $record);

        $after = $DB->get_record('financedep_feestructure', ['id' => $id], '*', MUST_EXIST);

        [$old, $new] = self::diff($before, $after);
        if (!empty($new)) {
            audit_manager::log(
                constants::AUDIT_ENTITY_FEESTRUCTURE,
                $id,
                constants::AUDIT_ACTION_EDIT,
                $old,
                $new,
                $usermodified
            );
        }
    }

    /**
     * Sets a fee structure's status (deactivate/reactivate) and logs an
     * EDIT audit entry.
     *
     * @param int $id
     * @param string $status one of constants::FEESTRUCTURE_STATUS_*
     * @param int $usermodified
     * @return void
     */
    public static function set_status(int $id, string $status, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_feestructure', ['id' => $id], '*', MUST_EXIST);

        if ($before->status === $status) {
            return;
        }

        $DB->update_record('financedep_feestructure', (object) [
            'id' => $id,
            'status' => $status,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_FEESTRUCTURE,
            $id,
            constants::AUDIT_ACTION_EDIT,
            ['status' => $before->status],
            ['status' => $status],
            $usermodified
        );
    }

    /**
     * Returns categoryid => full category path options for the fee
     * structure form's category selector.
     *
     * @return array
     */
    public static function get_category_options(): array {
        return \core_course_category::make_categories_list();
    }

    /**
     * Builds the [old, new] audited-field diff between two fee structure
     * records, only including fields that actually changed.
     *
     * @param \stdClass $before
     * @param \stdClass $after
     * @return array [array $old, array $new]
     */
    protected static function diff(\stdClass $before, \stdClass $after): array {
        $old = [];
        $new = [];

        foreach (self::AUDITED_FIELDS as $field) {
            if ((string) ($before->$field ?? '') !== (string) ($after->$field ?? '')) {
                $old[$field] = $before->$field ?? null;
                $new[$field] = $after->$field ?? null;
            }
        }

        return [$old, $new];
    }

    /**
     * Extracts the audited fields from a fee structure record for use as
     * an audit_manager newdata snapshot.
     *
     * @param \stdClass $record
     * @return array
     */
    protected static function snapshot(\stdClass $record): array {
        $snapshot = [];
        foreach (self::AUDITED_FIELDS as $field) {
            $snapshot[$field] = $record->$field ?? null;
        }
        return $snapshot;
    }
}
