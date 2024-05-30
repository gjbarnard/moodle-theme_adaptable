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
 * Custom Menus
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Custom CSS section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_custommenu',
         get_string('headernavbarcustommenuheading', 'theme_adaptable'));

    // Custom menu section.
    $page->add(new admin_setting_heading(
        'theme_adaptable_custommenu_heading',
        get_string('headernavbarcustommenuheading', 'theme_adaptable'),
        format_text(
            get_string('headernavbarcustommenuheadingdesc', 'theme_adaptable'),
            FORMAT_MARKDOWN
        )
    ));

    $name = 'theme_adaptable/disablecustommenu';
    $title = get_string('disablecustommenu', 'theme_adaptable');
    $description = get_string('disablecustommenudesc', 'theme_adaptable');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false, true, false);
    $page->add($setting);

    $name = 'theme_adaptable/custommenutitle';
    $title = get_string('custommenutitle', 'theme_adaptable');
    $description = get_string('custommenutitledesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_adaptable_custommenucore',
        get_string('headernavbarcustommenucoreheading', 'theme_adaptable'),
        format_text(get_string('headernavbarcustommenucoreheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    $page->add(new admin_setting_configtextarea(
        'custommenuitems',
        new lang_string('custommenuitems', 'admin'),
        get_string('custommenuitemscoredesc', 'theme_adaptable').'<br><br>'.
        get_string('fontawesomesettingdesc', 'theme_adaptable'),
        '',
        PARAM_RAW,
        '50',
        '10'
    ));

    $defaultsettingcustomusermenuitems = [
        'messages,message|/message/index.php|fa-regular fa-comment',
        'privatefiles,moodle|/user/files.php|folder-tree',
    ];
    $page->add(new admin_setting_configtextarea(
        'customusermenuitems',
        new lang_string('customusermenuitems', 'admin'),
        new lang_string('customusermenuitemscoredesc', 'theme_adaptable'),
        implode("\n", $defaultsettingcustomusermenuitems),
        PARAM_RAW,
        '50',
        '10'
    ));

    $asettings->add($page);
}
