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
 * Colors
 *
 * @package    theme_adaptable
 * @copyright  2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2016 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Colors section.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_color', get_string('colorsettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_color',
        get_string('colorsettingsheading', 'theme_adaptable'),
        format_text(get_string('colordesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Main colors heading.
    $name = 'theme_adaptable/settingsmaincolors';
    $heading = get_string('settingsmaincolors', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    // Site main colour.
    $name = 'theme_adaptable/maincolour';
    $title = get_string('maincolour', 'theme_adaptable');
    $description = get_string('maincolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#fff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main Font colour.
    $name = 'theme_adaptable/fontcolour';
    $title = get_string('fontcolour', 'theme_adaptable');
    $description = get_string('fontcolourdesc', 'theme_adaptable');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Site primary colour.
    $name = 'theme_adaptable/primarycolour';
    $title = get_string('primarycolour', 'theme_adaptable');
    $description = get_string('primarycolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00796b', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Site secondary colour.
    $name = 'theme_adaptable/secondarycolour';
    $title = get_string('secondarycolour', 'theme_adaptable');
    $description = get_string('secondarycolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Site secondary colour text.
    $name = 'theme_adaptable/secondarycolourtext';
    $title = get_string('secondarycolourtext', 'theme_adaptable');
    $description = get_string('secondarycolourtextdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#fafafa', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main region background colour.
    $name = 'theme_adaptable/regionmaincolour';
    $title = get_string('regionmaincolour', 'theme_adaptable');
    $description = get_string('regionmaincolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#fff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main region text colour.
    $name = 'theme_adaptable/regionmaintextcolour';
    $title = get_string('regionmaintextcolour', 'theme_adaptable');
    $description = get_string('regionmaintextcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Link colour.
    $name = 'theme_adaptable/linkcolour';
    $title = get_string('linkcolour', 'theme_adaptable');
    $description = get_string('linkcolourdesc', 'theme_adaptable');
    $default = '#51666C';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Link hover colour.
    $name = 'theme_adaptable/linkhover';
    $title = get_string('linkhover', 'theme_adaptable');
    $description = get_string('linkhoverdesc', 'theme_adaptable');
    $default = '#009688';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dimmed text color.
    $name = 'theme_adaptable/dimmedtextcolour';
    $title = get_string('dimmedtextcolour', 'theme_adaptable');
    $description = get_string('dimmedtextcolourdesc', 'theme_adaptable');
    $default = '#6a737b';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Selection text color.
    $name = 'theme_adaptable/selectiontext';
    $title = get_string('selectiontext', 'theme_adaptable');
    $description = get_string('selectiontextdesc', 'theme_adaptable');
    $default = '#000000';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Selection background color.
    $name = 'theme_adaptable/selectionbackground';
    $title = get_string('selectionbackground', 'theme_adaptable');
    $description = get_string('selectionbackgrounddesc', 'theme_adaptable');
    $default = '#00B3A1';
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Activity colors.
    $name = 'theme_adaptable/activitiesheading';
    $heading = get_string('activitiesheading', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    $name = 'theme_adaptable/introboxbackgroundcolor';
    $title = get_string('introboxbackgroundcolor', 'theme_adaptable');
    $description = get_string('introboxbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Forum colors.
    $name = 'theme_adaptable/settingsforumheading';
    $heading = get_string('settingsforumheading', 'theme_adaptable');
    $setting = new admin_setting_heading($name, $heading, '');
    $page->add($setting);

    $name = 'theme_adaptable/forumheaderbackgroundcolor';
    $title = get_string('forumheaderbackgroundcolor', 'theme_adaptable');
    $description = get_string('forumheaderbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_adaptable/forumbodybackgroundcolor';
    $title = get_string('forumbodybackgroundcolor', 'theme_adaptable');
    $description = get_string('forumbodybackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
