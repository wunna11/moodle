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
 * Business logic for discount definitions (Step 7.5).
 *
 * @package   local_financedepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_financedepartment;

defined('MOODLE_INTERNAL') || die();

/**
 * Class discount_manager
 *
 * A discount definition is classified by type (earlypayment/promotional/
 * hardship, financedep_discount.type) and is NOT restricted to one course
 * category the way a scholarship is (financedep_discount has no
 * categoryid column) - any discount can be requested against any
 * student's fee record. See constants::DISCOUNT_TYPE_* for the full
 * 2026-09-06 scope story.
 *
 * financedep_discount also has isautomatic/rulejson columns for a
 * FUTURE rule-based auto-apply engine (e.g. "N days before the due
 * date" for an earlypayment discount). That engine is NOT built yet -
 * neither financedep_feerecord nor financedep_feestructure has a
 * due-date column for it to evaluate against, a gap discovered while
 * scoping this step. The user chose (2026-09-06 AskUserQuestion) to
 * defer automatic evaluation entirely for this first pass and build
 * only the manual/hardship request-and-approve workflow, mirroring
 * scholarship requests - so create()/update() below always write
 * isautomatic = 0 and rulejson = null, and discount_form.php does not
 * expose either field. Every discount, regardless of its `type`, is
 * manual-only for now.
 *
 * Same create/update/set_status/audit pattern as scholarship_manager -
 * see that class's docblock for the shared reasoning.
 */
class discount_manager {

    /**
     * The editable fields snapshotted into the audit log on create/edit,
     * and diffed to build the "what changed" newdata array on edit.
     *
     * @var string[]
     */
    const AUDITED_FIELDS = ['name', 'type', 'amounttype', 'amountvalue', 'description', 'status'];

    /**
     * Returns one discount definition, or false if not found.
     *
     * @param int $id
     * @return \stdClass|false
     */
    public static function get(int $id) {
        global $DB;

        return $DB->get_record('financedep_discount', ['id' => $id]);
    }

    /**
     * Returns id => label options for every ACTIVE discount - used by
     * discountrequest_form to only offer discounts that can actually be
     * requested.
     *
     * @return array
     */
    public static function get_active_options(): array {
        global $DB;

        $records = $DB->get_records('financedep_discount', [
            'status' => constants::DISCOUNT_STATUS_ACTIVE,
        ], 'name ASC');

        $options = [];
        foreach ($records as $record) {
            $amount = (float) $record->amountvalue;
            $label = $record->amounttype === constants::AMOUNT_TYPE_PERCENTAGE
                ? (rtrim(rtrim(number_format($amount, 2), '0'), '.') . '%')
                : (number_format($amount, (abs($amount - round($amount)) > 0.001) ? 2 : 0) . ' MMK');
            $options[$record->id] = format_string($record->name) . ' ('
                . get_string('discounttype_' . $record->type, 'local_financedepartment') . ' - ' . $label . ')';
        }

        return $options;
    }

    /**
     * Creates a new discount definition and logs a CREATE audit entry.
     * Always writes isautomatic = 0 and rulejson = null - see this
     * class's docblock for why (automatic rule evaluation is deferred,
     * every discount is manual-only for now).
     *
     * @param \stdClass $data form data: name, type, amounttype, amountvalue, description
     * @param int $usermodified
     * @return int the new discount id
     */
    public static function create(\stdClass $data, int $usermodified): int {
        global $DB;

        $now = time();

        $record = new \stdClass();
        $record->name = trim($data->name);
        $record->type = $data->type;
        $record->amounttype = $data->amounttype;
        $record->amountvalue = (float) $data->amountvalue;
        $record->isautomatic = 0;
        $record->rulejson = null;
        $record->description = $data->description ?? '';
        $record->status = constants::DISCOUNT_STATUS_ACTIVE;
        $record->timecreated = $now;
        $record->timemodified = $now;
        $record->usermodified = $usermodified;

        $id = $DB->insert_record('financedep_discount', $record);

        audit_manager::log(
            constants::AUDIT_ENTITY_DISCOUNT,
            $id,
            constants::AUDIT_ACTION_CREATE,
            null,
            self::snapshot($record),
            $usermodified
        );

        return $id;
    }

    /**
     * Updates an existing discount definition and logs an EDIT audit
     * entry containing only the fields that actually changed. Never
     * touches isautomatic/rulejson - see this class's docblock.
     *
     * @param int $id
     * @param \stdClass $data form data: name, type, amounttype, amountvalue, description
     * @param int $usermodified
     * @return void
     */
    public static function update(int $id, \stdClass $data, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_discount', ['id' => $id], '*', MUST_EXIST);

        $record = new \stdClass();
        $record->id = $id;
        $record->name = trim($data->name);
        $record->type = $data->type;
        $record->amounttype = $data->amounttype;
        $record->amountvalue = (float) $data->amountvalue;
        $record->description = $data->description ?? '';
        $record->timemodified = time();
        $record->usermodified = $usermodified;

        $DB->update_record('financedep_discount', $record);

        $after = $DB->get_record('financedep_discount', ['id' => $id], '*', MUST_EXIST);

        [$old, $new] = self::diff($before, $after);
        if (!empty($new)) {
            audit_manager::log(
                constants::AUDIT_ENTITY_DISCOUNT,
                $id,
                constants::AUDIT_ACTION_EDIT,
                $old,
                $new,
                $usermodified
            );
        }
    }

    /**
     * Sets a discount definition's status (deactivate/reactivate) and
     * logs an EDIT audit entry.
     *
     * @param int $id
     * @param string $status one of constants::DISCOUNT_STATUS_*
     * @param int $usermodified
     * @return void
     */
    public static function set_status(int $id, string $status, int $usermodified): void {
        global $DB;

        $before = $DB->get_record('financedep_discount', ['id' => $id], '*', MUST_EXIST);

        if ($before->status === $status) {
            return;
        }

        $DB->update_record('financedep_discount', (object) [
            'id' => $id,
            'status' => $status,
            'timemodified' => time(),
            'usermodified' => $usermodified,
        ]);

        audit_manager::log(
            constants::AUDIT_ENTITY_DISCOUNT,
            $id,
            constants::AUDIT_ACTION_EDIT,
            ['status' => $before->status],
            ['status' => $status],
            $usermodified
        );
    }

    /**
     * Builds the [old, new] audited-field diff between two discount
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
     * Extracts the audited fields from a discount record for use as an
     * audit_manager newdata snapshot.
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
