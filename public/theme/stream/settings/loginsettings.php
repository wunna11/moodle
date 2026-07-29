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
 * Implements login settings.
 *
 * @package    theme_stream
 * @copyright  2026 Hugo Ribeiro <ribeiro.hugo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Course tab.
$page = new admin_settingpage('theme_stream_login', get_string('login', 'core'));

// Remove left copy text.
$name = 'theme_stream/removelogincopy';
$title = get_string('removelogincopy', 'theme_stream');
$description = get_string('removelogincopy_desc', 'theme_stream');
$setting = new admin_setting_configcheckbox($name, $title, $description, '0');
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

$settings->add($page);
