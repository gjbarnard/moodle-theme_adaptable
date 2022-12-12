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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2021 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

use theme_adaptable\admin_setting_markdown;

// Information Section.
if ($ADMIN->fulltree) {
    $page = new admin_settingpage('theme_adaptable_information',
        get_string('settingsinformation', 'theme_adaptable'));

    // Support.md.
    $name = 'theme_adaptable/themesupport';
    $title = get_string('themesupport', 'theme_adaptable');
    $description = 'Support.md';
    $setting = new admin_setting_markdown($name, $title, $description, 'Support.md');
    $page->add($setting);

    // Changes.md.
    $name = 'theme_adaptable/themechanges';
    $title = get_string('themechanges', 'theme_adaptable');
    $description = 'Changes.md';
    $setting = new admin_setting_markdown($name, $title, $description, 'Changes.md');
    $page->add($setting);

    // Readme.md.
    $name = 'theme_adaptable/themereadme';
    $title = get_string('themereadme', 'theme_adaptable');
    $description = 'Readme.md';
    $setting = new admin_setting_markdown($name, $title, $description, 'Readme.md');
    $page->add($setting);

    $asettings->add($page);
}
