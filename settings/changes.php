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
 * Changes.
 *
 * @package    theme_adaptable
 * @copyright  2025 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

use theme_adaptable\admin_setting_markdown;

// Changes section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_changes',
        get_string('settingschanges', 'theme_adaptable')
    );

    // Changes.md.
    $name = 'theme_adaptable/themechanges';
    $title = get_string('themechanges', 'theme_adaptable');
    $description = 'Changes.md';
    $setting = new admin_setting_markdown($name, $title, $description, 'Changes.md');
    $page->add($setting);

    $asettings->add($page);
}
