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
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @copyright  2023 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Custom CSS and JS section.
if ($ADMIN->fulltree) {
    $page = new admin_settingpage('theme_adaptable_generic', get_string('customcssjssettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading('theme_adaptable_generic', get_string('genericsettingsheading', 'theme_adaptable'),
        format_text(get_string('genericsettingsdescription', 'theme_adaptable'), FORMAT_MARKDOWN)));

    // Custom CSS.
    $name = 'theme_adaptable/customcss';
    $title = get_string('customcss', 'theme_adaptable');
    $description = get_string('customcssdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Custom H5P CSS.
    $name = 'theme_adaptable/hvpcustomcss';
    $title = get_string('hvpcustomcss', 'theme_adaptable');
    $description = get_string('hvpcustomcssdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Section for javascript to be added e.g. Google Analytics.
    $name = 'theme_adaptable/jssection';
    $title = get_string('jssection', 'theme_adaptable');
    $description = get_string('jssectiondesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $page->add($setting);

    // Section for custom javascript, restricted by profile field.
    $name = 'theme_adaptable/jssectionrestricted';
    $title = get_string('jssectionrestricted', 'theme_adaptable');
    $description = get_string('jssectionrestricteddesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_adaptable/jssectionrestrictedprofilefield';
    $title = get_string('jssectionrestrictedprofilefield', 'theme_adaptable');
    $description = get_string('jssectionrestrictedprofilefielddesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
    $page->add($setting);

    $name = 'theme_adaptable/jssectionrestricteddashboardonly';
    $title = get_string('jssectionrestricteddashboardonly', 'theme_adaptable');
    $description = get_string('jssectionrestricteddashboardonlydesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    $asettings->add($page);
}
