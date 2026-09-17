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

namespace local_hrdepartment\hooks\navigation;

use local_hrdepartment\access_manager;

/**
 * Adds the HR Department entry to the site's TOP primary navigation bar
 * (Home/Dashboard/My courses/...) - added 2026-09-12 per a user report
 * that this plugin was invisible in the top nav bar while
 * local_financedepartment's "Scholarship"/"Finance Department" entry
 * (added 2026-09-10, see that plugin's db/hooks.php) WAS showing there.
 *
 * Root cause: this plugin only ever had the classic extend_navigation()
 * callback (lib.php), which - on this Moodle version (5.2) - populates
 * $PAGE->navigation (the site navigation tree/side drawer) only. The TOP
 * bar is built separately by core\navigation\views\primary::initialise(),
 * which dispatches THIS hook for plugins to add a node to that bar
 * specifically - a mechanism this plugin never implemented until now,
 * exactly the gap local_financedepartment already hit and fixed first.
 * See local_financedepartment/db/hooks.php's docblock for the original
 * investigation this mirrors.
 *
 * HISTORY: this was originally (2026-09-12) HR-staff/admin-only, with
 * its own inline copy of that narrower check, deliberately DIFFERENT
 * from lib.php's broader self-service-inclusive check at the time. That
 * turned out to be a real bug, not a real design difference: a plain
 * student had no OTHER path to their own leave/attendance pages (the
 * side-drawer entry lib.php adds doesn't render on this site's custom
 * theme), so the 2026-09-13 fix widened this hook to also show a
 * (differently-labelled) entry for a self-service-only viewer - see the
 * callback()'s own docblock below. After that fix, this hook's
 * visibility condition and lib.php's were actually IDENTICAL, just
 * maintained as two separate copies of the same OR chain - exactly the
 * kind of drift that caused the original bug in the first place, and
 * this class's own docblock kept claiming a "deliberate narrower" split
 * that was no longer true even by v0.8.1.
 *
 * FIXED 2026-09-14 (Phase 4 of the access-model migration, project
 * memory hrdepartment-access-migration-plan.md): both this hook and
 * lib.php's local_hrdepartment_extend_navigation() now call
 * access_manager::can_view_navigation_entry() for the "should anything
 * show at all" gate, so they share one implementation and cannot drift
 * apart again - mirrors local_financedepartment's own
 * can_view_navigation_entry() pattern, which this plugin now also
 * follows. The LABEL choice ("HR Department" vs "My HR") is still
 * decided independently here (this hook alone shows the self-service
 * label; lib.php's side-drawer entry always shows the plain "HR
 * Department" label regardless of viewer) - that inconsistency
 * pre-dates this refactor and was deliberately left alone, since
 * unifying it would be a user-facing behaviour change, not a pure
 * consolidation.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_extend {

    /**
     * Hook callback - see db/hooks.php.
     *
     * @param \core\hook\navigation\primary_extend $hook
     * @return void
     */
    public static function callback(\core\hook\navigation\primary_extend $hook): void {
        global $USER;

        if (!access_manager::can_view_navigation_entry()) {
            return;
        }

        $ismanagement = access_manager::can_access_hr_department((int) $USER->id);
        $name = $ismanagement
            ? get_string('pluginname', 'local_hrdepartment')
            : get_string('pluginnameselfservice', 'local_hrdepartment');

        $primarynav = $hook->get_primaryview();
        $primarynav->add(
            $name,
            new \moodle_url('/local/hrdepartment/index.php'),
            \navigation_node::TYPE_CUSTOM,
            $name,
            'local_hrdepartment',
            new \pix_icon('i/report', '')
        );
    }
}
