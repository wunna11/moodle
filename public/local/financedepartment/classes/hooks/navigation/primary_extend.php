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

namespace local_financedepartment\hooks\navigation;

use local_financedepartment\access_manager;

/**
 * Adds the Finance Department entry to the site's TOP primary navigation
 * bar (Home/Dashboard/My courses/...) on Moodle versions that dispatch
 * core\hook\navigation\primary_extend - see db/hooks.php's docblock and
 * \local_financedepartment\access_manager::can_view_navigation_entry()'s
 * docblock for the full 2026-09-10 investigation of why this second
 * navigation entry point was needed alongside lib.php's classic
 * extend_navigation() callback.
 *
 * Uses the SAME access_manager::can_view_navigation_entry() check as
 * extend_navigation() so the two entry points can never show the node to
 * different sets of users - that exact kind of drift (one nav check
 * updated, the other left stale) is what caused the first
 * navigation-visibility bug fixed earlier the same day.
 *
 * @package   local_financedepartment
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
        if (!access_manager::can_view_navigation_entry()) {
            return;
        }

        $primarynav = $hook->get_primaryview();
        $name = access_manager::get_display_name();

        $primarynav->add(
            $name,
            new \moodle_url('/local/financedepartment/index.php'),
            \navigation_node::TYPE_CUSTOM,
            $name,
            'local_financedepartment',
            new \pix_icon('i/report', '')
        );
    }
}
