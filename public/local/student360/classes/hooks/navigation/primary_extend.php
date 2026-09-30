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

namespace local_student360\hooks\navigation;

use local_student360\access_manager;

/**
 * Adds the Student 360 entry to the site's TOP primary navigation bar
 * (Home/Dashboard/My courses/...) - see db/hooks.php's docblock.
 *
 * Uses the SAME access_manager::can_view_navigation_entry() check as
 * lib.php's extend_navigation() so the two entry points can never show
 * the node to different sets of users.
 *
 * @package   local_student360
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
        $name = get_string('pluginname', 'local_student360');

        $primarynav->add(
            $name,
            new \moodle_url('/local/student360/pages/index.php'),
            \navigation_node::TYPE_CUSTOM,
            $name,
            'local_student360',
            new \pix_icon('i/report', '')
        );
    }
}
