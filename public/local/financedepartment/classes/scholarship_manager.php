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
 * Business logic for scholarship definitions (Step 7.4).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class scholarship_manager
 *
 * A scholarship definition is NOT classified by type (merit/need-based/
 * sibling/staff-ward/other, the original Step 7.4 doc wording) - that
 * classification was removed 2026-08-23 per the user's explicit request
 * and replaced with a program-level restriction instead: every
 * scholarship belongs to exactly ONE course category, the same pattern
 * financedep_feestructure already uses (see get_category_options(),
 * which is literally feestructure_manager's own method - no need for a
 * second copy of "list of categories"). That categoryid is what answers
 * "which program has a scholarship" - a category with no active
 * scholarship rows simply has none, and
 * scholarshiprequest_manager::validate_eligible() enforces that a
 * request can only use a scholarship whose categoryid matches the
 * student's fee record's own category (via its fee structure).
 *
 * Same create/update/set_status/audit pattern as feestructure_manager -
 * see that class's docblock for the shared reasoning.
 */
class scholarship_manager {

    /**
     * The editable fields snapshotted into the audit log on create/edit,
     * and diffed to build the "what changed" newdata array on edit.
     *
     * @var string[]
     */
    const AUDITED_FIELDS = ['name', 'categoryid', 'amounttype', 'amountvalue', 'description', 'status'];

    /**
     * Returns one scholarship definition with its course category name
     * joined in, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        $sql = "SELECT s.*, cc.name AS categoryname
                  FROM {financedep_scholarship} s
                  JOIN {course_categories} cc ON cc.id = s.categoryid
                 WHERE s.id = :id";

        return $DB->get_record_sql($sql, ['id' => $id]);
    }

    /**
     * Returns categoryid => full category path options, for the
     * scholarship form's category selector. Delegates to
     * feestructure_manager rather than duplicating the same
     * core_course_category::make_categories_list() call.
     *
     * @return array
     */
    public static function get_category_options(): array {
        return feestructure_manager::get_category_options();
    }

    /**
     * Returns id => label options for every ACTIVE scholarship
     * restricted to one specific category - used by
     * scholarshiprequest_form to only offer scholarships the student's
     * fee record is actually eligible for.
     *
     * @param int $categoryid
     * @return array
     */
    public static function get_options_for_category(int $categoryid): array {
        global $DB;

        $records = $DB->get_records('financedep_scholarship', [
            'categoryid' => $categoryid,
            'status' => constants::SCHOLARSHIP_STATUS_ACTIVE,
        ], 'name ASC');

        $options = [];
        foreach ($records as $record) {
            $amount = (float) $record->amountvalue;
            $label = $record->amounttype === constants::AMOUNT_TYPE_PERCENTAGE
                ? (rtrim(rtrim(number_format($amount, 2), '0'), '.') . '%')
                : (number_format($amount, (abs($amount - round($amount)) > 0.001) ? 2 : 0) . ' MMK');
            $options[$record->id] = format_string($record->name) . ' (' . $label . ')';
        }

        return $options;
    }

    /**
     * Creates a new scholarship definition and logs a CREATE audit entry.
     *
     * @param \stdClass $data form data: name, categoryid, amounttype, amountvalue, description
     * @param int $usermodified
     * @return int the new scholarship id
     */
    public static function create(\stdClass $data, int $usermodified): int {
        global $DB;

        $now = time();

        $record = new \stdClass();
        $record->name = trim($data->name);
        $record->categoryid = (int) $data->categoryid;
        $record->amounttype = $data->amounttype;
        $record->amountvalue = (float) $data->amountvalue;
        $record->description = $data->description ?? '';
        $record->status = constants::SCHOLARSHIP_STATUS_ACTIVE;
        $record->timecreated = $now;
        $record->timemodified = $now;
        $record->usermodified = $usermodified;

        $id = $DB->insert_record('financedep_scholarship', $record);

        audit_manager::log(
            constants::AUDIT_ENTITY_SCHOLARSHIP,
            $id,
            constants::AUDIT_ACTION_CREATE,
            null,
            self::snapshot($record),
            $usermodified
        );

        return $id;
    }

    /**
     * Updates an existing scholarship definition and logs an EDIT audit
     * entry containing only the fields that actually changed.
     *
     * @param int $id
     * @param \stdClass $data form data: name, categoryid, amounttype, amountvalue, description
     * @param int $usermodified
     * @return void
     */
    public static function update(int $id, \stdClass $data, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_scholarship', ['id' => $id], '*', MUST_EXIST);

        $record = new \stdClass();
        $record->id = $id;
        $record->name = trim($data->name);
        $record->categoryid = (int) $data->categoryid;
        $record->amounttype = $data->amounttype;
        $record->amountvalue = (float) $data->amountvalue;
        $record->description = $data->description ?? '';
        $record->timemodified = time();
        $record->usermodified = $usermodified;

        $DB->update_record('financedep_scholarship', $record);

        $after = $DB->get_record('financedep_scholarship', ['id' => $id], '*', MUST_EXIST);

        [$old, $new] = self::diff($before, $after);
        if (!empty($new)) {
            audit_manager::log(
                constants::AUDIT_ENTITY_SCHOLARSHIP,
                $id,
                constants::AUDIT_ACTION_EDIT,
                $old,
                $new,
                $usermodified
            );
        }
    }

    /**
     * Sets a scholarship definition's status (deactivate/reactivate) and
     * logs an EDIT audit entry. Deliberately never touches
     * financedep_scholarshipreq - a PENDING request submitted against
     * this scholarship before it was deactivated is left exactly as-is
     * (still PENDING, still referencing this scholarshipid). It is
     * scholarshiprequest_manager::approve() and
     * pages/scholarshiprequests/review.php that refuse to approve such
     * a request once this scholarship is inactive (added 2026-09-06 per
     * the user's explicit request, mirroring the same fix on the
     * discount side) - deactivating here does not cascade or
     * auto-reject anything.
     *
     * @param int $id
     * @param string $status one of constants::SCHOLARSHIP_STATUS_*
     * @param int $usermodified
     * @return void
     */
    public static function set_status(int $id, string $status, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_scholarship', ['id' => $id], '*', MUST_EXIST);

        if ($before->status === $status) {
            return;
        }

        $DB->update_record('financedep_scholarship', (object) [
            'id' => $id,
            'status' => $status,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_SCHOLARSHIP,
            $id,
            constants::AUDIT_ACTION_EDIT,
            ['status' => $before->status],
            ['status' => $status],
            $usermodified
        );
    }

    /**
     * Builds the [old, new] audited-field diff between two scholarship
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
     * Extracts the audited fields from a scholarship record for use as
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
