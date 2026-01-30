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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2026 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

use admin_setting_configcheckbox;
use admin_setting_configselect;
use admin_setting_configtext;
use admin_setting_configcolourpicker;
use admin_setting_configmulticheckbox;
use admin_setting_configmultiselect;
use admin_setting_configtextarea;
use admin_setting_description;
use admin_setting_heading;
use admin_setting_confightmleditor as core_admin_setting_confightmleditor;
use admin_settingpage;
use lang_string;

/**
 * Settings.
 */
class settings {
    /**
     * Add the settings.
     */
    public static function add_settings() {
        global $ADMIN;

        $asettings = new admin_settingspage_tabs(
            'themesettingadaptable',
            get_string('configtabtitle', 'theme_adaptable'),
            501
        );
        $iesettings = new admin_settingpage('theme_adaptable_importexport', get_string('properties', 'theme_adaptable'));

        if ($ADMIN->fulltree) {
            // Add information and changes.
            $asettings->add(self::information_settings());
            $asettings->add(self::changes_settings());

            // The settings themselves.
            $settings = [
                'blocks' => null,
                'buttons' => null,
                'colours' => null,
                'courses' => null,
                'course_index' => null,
                'custom_css' => null,
                'custom_menus' => null,
                'dash_block_regions' => null,
                'fonts' => null,
                'footer' => null,
                'frontpage_block_regions' => null,
                'frontpage_courses' => null,
                'frontpage_slider' => null,
                'general' => null,
                'header' => null,
                'header_menus' => null,
                'header_search_social' => null,
                'header_user' => null,
                'information_blocks' => null,
                'layout' => null,
                'layout_responsive' => null,
                'marketing_blocks' => null,
                'navbar' => null,
                'navbar_links' => null,
                'navbar_styles' => null,
                'print' => null,
                'templates' => null,
            ];

            foreach ($settings as $settingname => $setting) {
                $settingmethod = $settingname . '_settings';
                $settings[$settingname] = self::$settingmethod($settings);
            }

            $localtoolbox = toolbox::get_local_toolbox();
            if (is_object($localtoolbox)) {
                if (method_exists($localtoolbox, 'get_settings')) { // Todo - Temporary until such time as not.
                    $settings = array_merge($settings, $localtoolbox->get_settings());
                }
            }

            ksort($settings);

            foreach ($settings as $setting) {
                $asettings->add($setting);
            }

            self::importexport_settings($iesettings);
        }

        $ADMIN->add('theme_adaptable', $asettings);
        $ADMIN->add('theme_adaptable', $iesettings);
    }

    /**
     * Information settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function information_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_information',
            get_string('settingsinformation', 'theme_adaptable')
        );

        // Information.md.
        $name = 'theme_adaptable/themeinformation';
        $title = get_string('themeinformation', 'theme_adaptable');
        $description = 'Information.md';
        $setting = new admin_setting_markdown($name, $title, $description, 'Information.md');
        $page->add($setting);

        // Readme.md.
        $name = 'theme_adaptable/themereadme';
        $title = get_string('themereadme', 'theme_adaptable');
        $description = 'Readme.md';
        $setting = new admin_setting_markdown($name, $title, $description, 'Readme.md');
        $page->add($setting);

        return $page;
    }

    /**
     * Changed settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function changes_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_changes',
            get_string('settingschanges', 'theme_adaptable')
        );

        // Changes.md.
        $name = 'theme_adaptable/themechanges';
        $title = get_string('themechanges', 'theme_adaptable');
        $description = 'Changes.md';
        $setting = new admin_setting_markdown($name, $title, $description, 'Changes.md');
        $page->add($setting);

        return $page;
    }

    /**
     * Blocks settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function blocks_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_blocks',
            get_string('settingspageblocksettings', 'theme_adaptable')
        );

        // Configuration.
        $name = 'theme_adaptable/settingsblocksconfiguration';
        $heading = get_string('settingsblocksconfiguration', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $name = 'theme_adaptable/blockregioneditingtitleshown';
        $title = get_string('blockregioneditingtitleshown', 'theme_adaptable');
        $description = get_string('blockregioneditingtitleshowndesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, true);
        $page->add($setting);

        // Side post drawer width.
        $name = 'theme_adaptable/sidepostdrawerwidth';
        $title = get_string('sidepostdrawerwidth', 'theme_adaptable');
        $description = get_string('sidepostdrawerwidthdesc', 'theme_adaptable');
        $default = '315px';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/frontpageuserblocksenabled';
        $title = get_string('frontpageuserblocksenabled', 'theme_adaptable');
        $description = get_string('frontpageuserblocksenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, true);
        $page->add($setting);

        // Show the navigation block on the course page.
        $name = 'theme_adaptable/shownavigationblockoncoursepage';
        $title = get_string('shownavigationblockoncoursepage', 'theme_adaptable');
        $description = get_string('shownavigationblockoncoursepagedesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Unaddable blocks.
        $name = 'theme_adaptable/unaddableblocks';
        $title = get_string('unaddableblocks', 'theme_boost');
        $description = get_string('unaddableblocks_desc', 'theme_boost');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $page->add($setting);

        // Colours.
        $name = 'theme_adaptable/settingscolors';
        $heading = get_string('settingscolors', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $name = 'theme_adaptable/blockbackgroundcolor';
        $title = get_string('blockbackgroundcolor', 'theme_adaptable');
        $description = get_string('blockbackgroundcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderbackgroundcolor';
        $title = get_string('blockheaderbackgroundcolor', 'theme_adaptable');
        $description = get_string('blockheaderbackgroundcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockbordercolor';
        $title = get_string('blockbordercolor', 'theme_adaptable');
        $description = get_string('blockbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#59585D', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockregionbackgroundcolor';
        $title = get_string('blockregionbackground', 'theme_adaptable');
        $description = get_string('blockregionbackgrounddesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, 'transparent', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Borders.
        $name = 'theme_adaptable/settingsborders';
        $heading = get_string('settingsborders', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderbordertopstyle';
        $title = get_string('blockheaderbordertopstyle', 'theme_adaptable');
        $description = get_string('blockheaderbordertopstyledesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'dashed',
            settings_toolbox::borderstyles()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheadertopradius';
        $title = get_string('blockheadertopradius', 'theme_adaptable');
        $description = get_string('blockheadertopradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderbottomradius';
        $title = get_string('blockheaderbottomradius', 'theme_adaptable');
        $description = get_string('blockheaderbottomradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderbordertop';
        $title = get_string('blockheaderbordertop', 'theme_adaptable');
        $description = get_string('blockheaderbordertopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            1,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderborderleft';
        $title = get_string('blockheaderborderleft', 'theme_adaptable');
        $description = get_string('blockheaderborderleftdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderborderright';
        $title = get_string('blockheaderborderright', 'theme_adaptable');
        $description = get_string('blockheaderborderrightdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockheaderborderbottom';
        $title = get_string('blockheaderborderbottom', 'theme_adaptable');
        $description = get_string('blockheaderborderbottomdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmainbordertopstyle';
        $title = get_string('blockmainbordertopstyle', 'theme_adaptable');
        $description = get_string('blockmainbordertopstyledesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'none',
            settings_toolbox::borderstyles()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmaintopradius';
        $title = get_string('blockmaintopradius', 'theme_adaptable');
        $description = get_string('blockmaintopradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmainbottomradius';
        $title = get_string('blockmainbottomradius', 'theme_adaptable');
        $description = get_string('blockmainbottomradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmainbordertop';
        $title = get_string('blockmainbordertop', 'theme_adaptable');
        $description = get_string('blockmainbordertopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmainborderleft';
        $title = get_string('blockmainborderleft', 'theme_adaptable');
        $description = get_string('blockmainborderleftdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmainborderright';
        $title = get_string('blockmainborderright', 'theme_adaptable');
        $description = get_string('blockmainborderrightdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/blockmainborderbottom';
        $title = get_string('blockmainborderbottom', 'theme_adaptable');
        $description = get_string('blockmainborderbottomdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Fonts heading.
        $name = 'theme_adaptable/settingsfonts';
        $heading = get_string('settingsfonts', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Block Header Font size.
        $name = 'theme_adaptable/fontblockheadersize';
        $title = get_string('fontblockheadersize', 'theme_adaptable');
        $description = get_string('fontblockheadersizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            22,
            settings_toolbox::fontsizes()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Block Header Font weight.
        $name = 'theme_adaptable/fontblockheaderweight';
        $title = get_string('fontblockheaderweight', 'theme_adaptable');
        $description = get_string('fontblockheaderweightdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            400,
            settings_toolbox::numbers(100, 900)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Block Header Font color.
        $name = 'theme_adaptable/fontblockheadercolor';
        $title = get_string('fontblockheadercolor', 'theme_adaptable');
        $description = get_string('fontblockheadercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Icons heading.
        $name = 'theme_adaptable/settingsblockicons';
        $heading = get_string('settingsblockicons', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Add icon to the title.
        $name = 'theme_adaptable/blockicons';
        $title = get_string('blockicons', 'theme_adaptable');
        $description = get_string('blockiconsdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Block Header Icon size.
        $name = 'theme_adaptable/blockiconsheadersize';
        $title = get_string('blockiconsheadersize', 'theme_adaptable');
        $description = get_string('blockiconsheadersizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            20,
            settings_toolbox::fontsizes()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Buttons settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function buttons_settings() {
        $page = new admin_settingpage('theme_adaptable_buttons', get_string('buttonsettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_header',
            get_string('buttonsettingsheading', 'theme_adaptable'),
            format_text(get_string('buttondesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/buttonradius';
        $title = get_string('buttonradius', 'theme_adaptable');
        $description = get_string('buttonradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect($name, $title, $description, 5, settings_toolbox::pixels(1, 6));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Buttons background color.
        $name = 'theme_adaptable/buttoncolor';
        $title = get_string('buttoncolor', 'theme_adaptable');
        $description = get_string('buttoncolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Buttons text color.
        $name = 'theme_adaptable/buttontextcolor';
        $title = get_string('buttontextcolor', 'theme_adaptable');
        $description = get_string('buttontextcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Buttons background hover color.
        $name = 'theme_adaptable/buttonhovercolor';
        $title = get_string('buttonhovercolor', 'theme_adaptable');
        $description = get_string('buttonhovercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Buttons text hover color.
        $name = 'theme_adaptable/buttontexthovercolor';
        $title = get_string('buttontexthovercolor', 'theme_adaptable');
        $description = get_string('buttontexthovercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eeeeee', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Buttons background focus color.
        $name = 'theme_adaptable/buttonfocuscolour';
        $title = get_string('buttonfocuscolour', 'theme_adaptable');
        $description = get_string('buttonfocuscolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f6cc0', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Buttons text focus color.
        $name = 'theme_adaptable/buttontextfocuscolour';
        $title = get_string('buttontextfocuscolour', 'theme_adaptable');
        $description = get_string('buttontextfocuscolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eeeeee', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Input buttons focus color.
        $name = 'theme_adaptable/inputbuttonfocuscolour';
        $title = get_string('inputbuttonfocuscolour', 'theme_adaptable');
        $description = get_string('inputbuttonfocuscolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f6cc0', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Input buttons focus color opacity.
        $ibfcoopactitychoices = [
            '0.0' => '0.0',
            '0.05' => '0.05',
            '0.1' => '0.1',
            '0.15' => '0.15',
            '0.2' => '0.2',
            '0.25' => '0.25',
            '0.3' => '0.3',
            '0.35' => '0.35',
            '0.4' => '0.4',
            '0.45' => '0.45',
            '0.5' => '0.5',
            '0.55' => '0.55',
            '0.6' => '0.6',
            '0.65' => '0.65',
            '0.7' => '0.7',
            '0.75' => '0.75',
            '0.8' => '0.8',
            '0.85' => '0.85',
            '0.9' => '0.9',
            '0.95' => '0.95',
            '1.0' => '1.0',
        ];

        $name = 'theme_adaptable/inputbuttonfocuscolouropacity';
        $title = get_string('inputbuttonfocuscolouropacity', 'theme_adaptable');
        $description = get_string('inputbuttonfocuscolouropacitydesc', 'theme_adaptable');
        $default = '0.75';
        $setting = new admin_setting_configselect($name, $title, $description, $default, $ibfcoopactitychoices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Secondary Buttons background color.
        $name = 'theme_adaptable/buttoncolorscnd';
        $title = get_string('buttoncolorscnd', 'theme_adaptable');
        $description = get_string('buttoncolordescscnd', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Secondary Buttons background hover color.
        $name = 'theme_adaptable/buttonhovercolorscnd';
        $title = get_string('buttonhovercolorscnd', 'theme_adaptable');
        $description = get_string('buttonhovercolordescscnd', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Secondary Buttons text color.
        $name = 'theme_adaptable/buttontextcolorscnd';
        $title = get_string('buttontextcolorscnd', 'theme_adaptable');
        $description = get_string('buttontextcolordescscnd', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Cancel Buttons background color.
        $name = 'theme_adaptable/buttoncolorcancel';
        $title = get_string('buttoncolorcancel', 'theme_adaptable');
        $description = get_string('buttoncolordesccancel', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#c64543', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Cancel Buttons background hover color.
        $name = 'theme_adaptable/buttonhovercolorcancel';
        $title = get_string('buttonhovercolorcancel', 'theme_adaptable');
        $description = get_string('buttonhovercolordesccancel', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e53935', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Cancel Buttons text color.
        $name = 'theme_adaptable/buttontextcolorcancel';
        $title = get_string('buttontextcolorcancel', 'theme_adaptable');
        $description = get_string('buttontextcolordesccancel', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/editonbk';
        $title = get_string('editonbk', 'theme_adaptable');
        $description = get_string('editonbkdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#4caf50', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/editoffbk';
        $title = get_string('editoffbk', 'theme_adaptable');
        $description = get_string('editoffbkdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f44336', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/editfont';
        $title = get_string('editfont', 'theme_adaptable');
        $description = get_string('editfontdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/edithorizontalpadding';
        $title = get_string('edithorizontalpadding', 'theme_adaptable');
        $description = get_string('edithorizontalpadding', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            4,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/buttonlogincolor';
        $title = get_string('buttonlogincolor', 'theme_adaptable');
        $description = get_string('buttonlogincolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#c64543', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/buttonloginhovercolor';
        $title = get_string('buttonloginhovercolor', 'theme_adaptable');
        $description = get_string('buttonloginhovercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e53935', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/buttonlogintextcolor';
        $title = get_string('buttonlogintextcolor', 'theme_adaptable');
        $description = get_string('buttonlogintextcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/buttonloginpadding';
        $title = get_string('buttonloginpadding', 'theme_adaptable');
        $description = get_string('buttonloginpaddingdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 8)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/buttonloginheight';
        $title = get_string('buttonloginheight', 'theme_adaptable');
        $description = get_string('buttonloginheightdesc', 'theme_adaptable');
        $radchoices = [
            16 => "16px",
            18 => "18px",
            20 => "20px",
            22 => "22px",
            24 => "24px",
            26 => "26px",
            28 => "28px",
            30 => "30px",
            32 => "32px",
            34 => "34px",
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 24, $radchoices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/buttonloginmargintop';
        $title = get_string('buttonloginmargintop', 'theme_adaptable');
        $description = get_string('buttonloginmargintopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            2,
            settings_toolbox::pixels(1, 12)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Enable drop shadow on bottom of button.
        $name = 'theme_adaptable/buttondropshadow';
        $title = get_string('buttondropshadow', 'theme_adaptable');
        $description = get_string('buttondropshadowdesc', 'theme_adaptable');
        $shadowchoices = [
            '0' => get_string('none', 'theme_adaptable'),
            '-1px' => get_string('slight', 'theme_adaptable'),
            '-2px' => get_string('standard', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, '0', $shadowchoices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Colours settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function colours_settings() {
        $page = new admin_settingpage('theme_adaptable_color', get_string('colorsettings', 'theme_adaptable'));

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

        return $page;
    }

    /**
     * Courses settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function courses_settings() {
        global $OUTPUT;

        $page = new admin_settingpage('theme_adaptable_course', get_string('coursesettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_course',
            get_string('coursesettingsheading', 'theme_adaptable'),
            format_text(get_string('coursesettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Course page, wide layout by moving sidebar to bottom.
        $page->add(new admin_setting_heading(
            'coursepagesidebarinfooterenabledsection',
            get_string('coursepagesidebarinfooterenabledsection', 'theme_adaptable'),
            format_text(get_string('coursepagesidebarinfooterenabledsectiondesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/coursepagesidebarinfooterenabled';
        $title = get_string('coursepagesidebarinfooterenabled', 'theme_adaptable');
        $description = get_string('coursepagesidebarinfooterenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        // Activity navigation.
        $name = 'theme_adaptable/courseactivitynavigationenabled';
        $title = get_string('courseactivitynavigationenabled', 'theme_adaptable');
        $description = get_string('courseactivitynavigationenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        // Course page top slider block region enabled.
        $page->add(new admin_setting_heading(
            'theme_adaptable_newsslider_heading',
            get_string('coursepageinformationblockregionheading', 'theme_adaptable'),
            format_text(get_string('coursepageinformationblockregionheadingdesc', 'theme_adaptable'))
        ));

        $name = 'theme_adaptable/coursepageblockinfoenabled';
        $title = get_string('coursepageblockinfoenabled', 'theme_adaptable');
        $description = get_string('coursepageblockinfoenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        // Activity end block region.
        $page->add(new admin_setting_heading(
            'theme_adaptable_activity_bottom_heading',
            get_string('coursepageactivitybottomblockregionheading', 'theme_adaptable'),
            format_text(get_string('coursepageactivitybottomblockregionheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/coursepageblockactivitybottomenabled';
        $title = get_string('coursepageblockactivitybottomenabled', 'theme_adaptable');
        $description = get_string('coursepageblockactivitybottomenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        // Course block layout settings.
        get_string('coursepageblockregionsettings', 'theme_adaptable');
        $page->add(new admin_setting_heading(
            'theme_adaptable_heading',
            get_string('coursepageblocklayoutbuilder', 'theme_adaptable'),
            format_text(get_string('coursepageblocklayoutbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Course page top / bottom block regions enabled.
        $name = 'theme_adaptable/coursepageblocksenabled';
        $title = get_string('coursepageblocksenabled', 'theme_adaptable');
        $description = get_string('coursepageblocksenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        // Heading for adding space between settings.
        $page->add(new admin_setting_heading('temp1', '', "<br>"));

        // Course page top block region builder.
        $noregions = 4; // Number of block regions defined in config.php.
        $totalblocks = 0;
        $imgblder = '';

        $settingname = 'coursepageblocklayouttoprow1';
        $name = 'theme_adaptable/' . $settingname;
        $title = get_string('coursepageblocklayouttoprow', 'theme_adaptable');
        $description = get_string('coursepageblocklayouttoprowdesc', 'theme_adaptable');
        $default = '0-0-0-0';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::rowlayouts()
        );
        $page->add($setting);

        $courseformatsetting = get_config('theme_adaptable', $settingname);
        if ($courseformatsetting === false) {
            $courseformatsetting = '0-0-0-0';
        }

        if ($courseformatsetting != '0-0-0-0') {
            $imgurl = $OUTPUT->image_url('layout-builder/' . $courseformatsetting, 'theme_adaptable');
            $imgblder .= '<img src="' . $imgurl . '" class="img-fluid">';
        }

        $vals = explode('-', $courseformatsetting);
        foreach ($vals as $val) {
            if ($val > 0) {
                $totalblocks++;
            }
        }
        $page->add(new admin_setting_heading(
            'layout_heading1',
            '',
            "<h4>" . get_string('layoutcheck', 'theme_adaptable') . "</h4>"
        ));

        $checkcountcolor = '#00695C';
        if ($totalblocks > $noregions) {
            $mktcountcolor = '#D7542A';
        }
        $mktcountmsg = '<span style="color: ' . $checkcountcolor . '; margin-bottom: 20px;">';
        $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') .
        '<strong>' . $noregions . '</strong>';
        $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') .
        '<strong>' . $totalblocks . '/' . $noregions . '</strong></span>.';

        $page->add(new admin_setting_heading('theme_adaptable_courselayouttopblockscount', '', $mktcountmsg));

        $page->add(new admin_setting_heading('theme_adaptable_courselayouttopbuilder', '', $imgblder));

        // Course page bottom  block region builder.
        $noregions = 4; // Number of block regions defined in config.php.
        $totalblocks = 0;
        $imgblder = '';

        $settingname = 'coursepageblocklayoutbottomrow1';
        $name = 'theme_adaptable/' . $settingname;
        $title = get_string('coursepageblocklayoutbottomrow', 'theme_adaptable');
        $description = get_string('coursepageblocklayoutbottomrowdesc', 'theme_adaptable');
        $default = '0-0-0-0';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::rowlayouts()
        );
        $page->add($setting);

        $courseformatsetting = get_config('theme_adaptable', $settingname);
        if ($courseformatsetting === false) {
            $courseformatsetting = '0-0-0-0';
        }

        if ($courseformatsetting != '0-0-0-0') {
            $imgurl = $OUTPUT->image_url('layout-builder/' . $courseformatsetting, 'theme_adaptable');
            $imgblder .= '<img src="' . $imgurl . '" class="img-fluid">';
        }

        $vals = explode('-', $courseformatsetting);
        foreach ($vals as $val) {
            if ($val > 0) {
                $totalblocks++;
            }
        }

        $page->add(new admin_setting_heading(
            'layout_heading2',
            '',
            "<h4>" . get_string('layoutcheck', 'theme_adaptable') . "</h4>"
        ));

        $checkcountcolor = '#00695C';
        if ($totalblocks > $noregions) {
            $mktcountcolor = '#D7542A';
        }
        $mktcountmsg = '<span style="color: ' . $checkcountcolor . '">';
        $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') .
        '<strong>' . $noregions . '</strong>';
        $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') .
        '<strong>' . $totalblocks . '/' . $noregions . '</strong></span>.';

        $page->add(new admin_setting_heading('theme_adaptable_courselayoutbottomblockscount', '', $mktcountmsg));

        $page->add(new admin_setting_heading('theme_adaptable_courselayoutbottombuilder', '', $imgblder . "<br><br>"));

        // Current course section background color.
        $name = 'theme_adaptable/coursesectionbgcolor';
        $title = get_string('coursesectionbgcolor', 'theme_adaptable');
        $description = get_string('coursesectionbgcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Courses course format heading.
        $name = 'theme_adaptable/settingscourses';
        $heading = get_string('settingscourses', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Course section heading background color.
        $name = 'theme_adaptable/coursesectionheaderbg';
        $title = get_string('coursesectionheaderbg', 'theme_adaptable');
        $description = get_string('coursesectionheaderbgdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section heading text color.
        $name = 'theme_adaptable/sectionheadingcolor';
        $title = get_string('sectionheadingcolor', 'theme_adaptable');
        $description = get_string('sectionheadingcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Current course section header background color.
        $name = 'theme_adaptable/currentcolor';
        $title = get_string('currentcolor', 'theme_adaptable');
        $description = get_string('currentcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d2f2ef', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Collapsed topics colour settings enabled.
        $name = 'theme_adaptable/collapsedtopicscoloursenabled';
        $title = get_string('collapsedtopicscoloursenabled', 'theme_adaptable');
        $description = get_string('collapsedtopicscoloursenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Topics / Weeks course format heading.
        $name = 'theme_adaptable/settingstopicsweeks';
        $heading = get_string('settingstopicsweeks', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Course section header border bottom style.
        $name = 'theme_adaptable/coursesectionheaderborderstyle';
        $title = get_string('coursesectionheaderborderstyle', 'theme_adaptable');
        $description = get_string('coursesectionheaderborderstyledesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'none',
            settings_toolbox::borderstyles()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section header border bottom color.
        $name = 'theme_adaptable/coursesectionheaderbordercolor';
        $title = get_string('coursesectionheaderbordercolor', 'theme_adaptable');
        $description = get_string('coursesectionheaderbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#F3F3F3', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section header border bottom width.
        $name = 'theme_adaptable/coursesectionheaderborderwidth';
        $title = get_string('coursesectionheaderborderwidth', 'theme_adaptable');
        $description = get_string('coursesectionheaderborderwidthdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section border radius.
        $name = 'theme_adaptable/coursesectionheaderborderradiustop';
        $title = get_string('coursesectionheaderborderradiustop', 'theme_adaptable');
        $description = get_string('coursesectionheaderborderradiustopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 50)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section border radius.
        $name = 'theme_adaptable/coursesectionheaderborderradiusbottom';
        $title = get_string('coursesectionheaderborderradiusbottom', 'theme_adaptable');
        $description = get_string('coursesectionheaderborderradiusbottomdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 50)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section border style.
        $name = 'theme_adaptable/coursesectionborderstyle';
        $title = get_string('coursesectionborderstyle', 'theme_adaptable');
        $description = get_string('coursesectionborderstyledesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'solid',
            settings_toolbox::borderstyles()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section border width.
        $name = 'theme_adaptable/coursesectionborderwidth';
        $title = get_string('coursesectionborderwidth', 'theme_adaptable');
        $description = get_string('coursesectionborderwidthdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            1,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section border color.
        $name = 'theme_adaptable/coursesectionbordercolor';
        $title = get_string('coursesectionbordercolor', 'theme_adaptable');
        $description = get_string('coursesectionbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e8eaeb', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course section border radius.
        $name = 'theme_adaptable/coursesectionborderradius';
        $title = get_string('coursesectionborderradius', 'theme_adaptable');
        $description = get_string('coursesectionborderradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 50)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Activity display colours.
        // Course Activity section heading.
        $name = 'theme_adaptable/coursesectionactivitycolors';
        $heading = get_string('coursesectionactivitycolors', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Default icon size.
        $name = 'theme_adaptable/coursesectionactivityiconsize';
        $title = get_string('coursesectionactivityiconsize', 'theme_adaptable');
        $description = get_string('coursesectionactivityiconsizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            24,
            settings_toolbox::pixels(4, 72)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course Activity heading colour.
        $name = 'theme_adaptable/coursesectionactivityheadingcolour';
        $title = get_string('coursesectionactivityheadingcolour', 'theme_adaptable');
        $description = get_string('coursesectionactivityheadingcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0066cc', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course Activity section bottom border width.
        $name = 'theme_adaptable/coursesectionactivitybottomborderwidth';
        $title = get_string('coursesectionactivitybottomborderwidth', 'theme_adaptable');
        $description = get_string('coursesectionactivitybottomborderwidthdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            2,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course Activity section bottom border style.
        $name = 'theme_adaptable/coursesectionactivityborderstyle';
        $title = get_string('coursesectionactivityborderstyle', 'theme_adaptable');
        $description = get_string('coursesectionactivityborderstyledesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'dashed',
            settings_toolbox::borderstyles()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course Activity section bottom border colour.
        $name = 'theme_adaptable/coursesectionactivitybordercolor';
        $title = get_string('coursesectionactivitybordercolor', 'theme_adaptable');
        $description = get_string('coursesectionactivitybordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eeeeee', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course Activity section left border width.  Controls width of all left borders.
        $name = 'theme_adaptable/coursesectionactivityleftborderwidth';
        $title = get_string('coursesectionactivityleftborderwidth', 'theme_adaptable');
        $description = get_string('coursesectionactivityleftborderwidthdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            3,
            settings_toolbox::pixels(1, 6)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Assign Activity display colours.
        $name = 'theme_adaptable/coursesectionactivityassignleftbordercolor';
        $title = get_string('coursesectionactivityassignleftbordercolor', 'theme_adaptable');
        $description = get_string('coursesectionactivityassignleftbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0066cc', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Assign Activity background colour.
        $name = 'theme_adaptable/coursesectionactivityassignbgcolor';
        $title = get_string('coursesectionactivityassignbgcolor', 'theme_adaptable');
        $description = get_string('coursesectionactivityassignbgcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Forum Activity display colours.
        $name = 'theme_adaptable/coursesectionactivityforumleftbordercolor';
        $title = get_string('coursesectionactivityforumleftbordercolor', 'theme_adaptable');
        $description = get_string('coursesectionactivityforumleftbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#990099', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Forum Activity background colour.
        $name = 'theme_adaptable/coursesectionactivityforumbgcolor';
        $title = get_string('coursesectionactivityforumbgcolor', 'theme_adaptable');
        $description = get_string('coursesectionactivityforumbgcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Quiz Activity display colours.
        $name = 'theme_adaptable/coursesectionactivityquizleftbordercolor';
        $title = get_string('coursesectionactivityquizleftbordercolor', 'theme_adaptable');
        $description = get_string('coursesectionactivityquizleftbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FF3333', $previewconfig);
        $page->add($setting);

        // Quiz Activity background colour.
        $name = 'theme_adaptable/coursesectionactivityquizbgcolor';
        $title = get_string('coursesectionactivityquizbgcolor', 'theme_adaptable');
        $description = get_string('coursesectionactivityquizbgcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $page->add($setting);

        // Top and bottom margin spacing between activities.
        $name = 'theme_adaptable/coursesectionactivitymargintop';
        $title = get_string('coursesectionactivitymargintop', 'theme_adaptable');
        $description = get_string('coursesectionactivitymargintopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            2,
            settings_toolbox::pixels(1, 12)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/coursesectionactivitymarginbottom';
        $title = get_string('coursesectionactivitymarginbottom', 'theme_adaptable');
        $description = get_string('coursesectionactivitymarginbottomdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            2,
            settings_toolbox::pixels(1, 12)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // One Topic course format heading.
        $name = 'theme_adaptable/onetopicheading';
        $heading = get_string('onetopicheading', 'theme_adaptable');
        $description = get_string('onetopicdesc', 'theme_adaptable', 'https://moodle.org/plugins/format_onetopic');
        $setting = new admin_setting_heading($name, $heading, $description);
        $page->add($setting);

        // One Topic active tab background color.
        $name = 'theme_adaptable/onetopicactivetabbackgroundcolor';
        $title = get_string('onetopicactivetabbackgroundcolor', 'theme_adaptable');
        $description = get_string('onetopicactivetabbackgroundcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d9edf7', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // One Topic active tab text color.
        $name = 'theme_adaptable/onetopicactivetabtextcolor';
        $title = get_string('onetopicactivetabtextcolor', 'theme_adaptable');
        $description = get_string('onetopicactivetabtextcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Course index settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function course_index_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_course_index',
            get_string('courseindexsettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_courseindex',
            get_string('courseindexsettingsheading', 'theme_adaptable'),
            format_text(get_string('courseindexsettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Enabled.
        $name = 'theme_adaptable/courseindexenabled';
        $title = get_string('courseindexenabled', 'theme_adaptable');
        $description = get_string('courseindexenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, true);
        $page->add($setting);

        // Item.
        $name = 'theme_adaptable/courseindexitemcolor';
        $title = get_string('courseindexitemcolor', 'theme_adaptable');
        $description = get_string('courseindexitemcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#495057', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Item hover.
        $name = 'theme_adaptable/courseindexitemhovercolor';
        $title = get_string('courseindexitemhovercolor', 'theme_adaptable');
        $description = get_string('courseindexitemhovercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Page item.
        $name = 'theme_adaptable/courseindexpageitemcolor';
        $title = get_string('courseindexpageitemcolor', 'theme_adaptable');
        $description = get_string('courseindexpageitemcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Page item background.
        $name = 'theme_adaptable/courseindexpageitembgcolor';
        $title = get_string('courseindexpageitembgcolor', 'theme_adaptable');
        $description = get_string('courseindexpageitembgcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#0f6cbf', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Custom CSS settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function custom_css_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_customcss',
            get_string('customcsssettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_customcss',
            get_string('customcssjssettingsheading', 'theme_adaptable'),
            format_text(get_string('customcsssettingsdescription', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

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

        return $page;
    }

    /**
     * Custom menus settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function custom_menus_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_custommenu',
            get_string('headernavbarcustommenuheading', 'theme_adaptable')
        );

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

        $custommenuitems = get_config('core', 'custommenuitems');
        if (empty($custommenuitems)) {
            $custommenuitems = get_string('headernavbarcustommenucoreempty', 'theme_adaptable', 'custommenuitems');
        } else {
            $custommenuitems = get_string('headernavbarcustommenucorenotempty', 'theme_adaptable', 'custommenuitems') .
            '<small>' . nl2br($custommenuitems) . '</small>';
        }
        $page->add(new admin_setting_description(
            'theme_adaptable/custommenuitems',
            new lang_string('custommenuitems', 'admin'),
            $custommenuitems . '<br><br>' .
            get_string('custommenuitemscoredesc', 'theme_adaptable') . '<br>' .
            get_string('fontawesomesettingdesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free')
        ));

        $customusermenuitems = get_config('core', 'customusermenuitems');
        if (empty($customusermenuitems)) {
            $customusermenuitems = get_string('headernavbarcustommenucoreempty', 'theme_adaptable', 'customusermenuitems');
        } else {
            $customusermenuitems = get_string('headernavbarcustommenucorenotempty', 'theme_adaptable', 'customusermenuitems') .
            '<small>' . nl2br($customusermenuitems) . '</small>';
        }
        $page->add(new admin_setting_description(
            'theme_adaptable/customusermenuitems',
            new lang_string('customusermenuitems', 'admin'),
            $customusermenuitems . '<br><br>' .
            get_string('customusermenuitemscoredesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free')
        ));

        return $page;
    }

    /**
     * Dash block regions settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function dash_block_regions_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_dash_block_regions',
            get_string('dashboardblockregionsettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_heading',
            get_string('dashblocklayoutbuilder', 'theme_adaptable'),
            format_text(get_string('dashblocklayoutbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/dashblocksenabled';
        $title = get_string('dashblocksenabled', 'theme_adaptable');
        $description = get_string('dashblocksenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        $name = 'theme_adaptable/dashblocksposition';
        $title = get_string('dashblocksposition', 'theme_adaptable');
        $description = get_string('dashblockspositiondesc', 'theme_adaptable');
        $default = 'abovecontent';
        $choices = [
        'abovecontent' => new lang_string('dashblocksabovecontent', 'theme_adaptable'),
        'belowcontent' => new lang_string('dashblocksbelowcontent', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $page->add($setting);

        // Dashboard block region builder.
        $noregions = 20; // Number of block regions defined in config.php - frnt-market- etc.
        ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = toolbox::admin_settings_layout_builder(
            $page,
            'dashblocklayoutlayoutrow',
            5,
            ['3-3-3-3', '4-4-4-0', '3-3-3-3', '0-0-0-0', '0-0-0-0'],
            settings_toolbox::rowlayouts()
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_dashblocklayoutcheck',
            get_string('layoutcheck', 'theme_adaptable'),
            format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $checkcountcolor = '#00695C';
        if ($totalblocks > $noregions) {
            $mktcountcolor = '#D7542A';
        }
        $mktcountmsg = '<span style="color: ' . $checkcountcolor . '">';
        $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') . '<strong>' . $noregions . '</strong>';
        $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') . '<strong>' . $totalblocks . '/' . $noregions . '</strong>.';

        $page->add(new admin_setting_heading('theme_adaptable_dashlayoutblockscount', '', $mktcountmsg));

        $page->add(new admin_setting_heading('theme_adaptable_dashlayoutbuilder', '', $imgblder));

        return $page;
    }

    /**
     * Fonts settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function fonts_settings() {
        $page = new admin_settingpage('theme_adaptable_font', get_string('fontsettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_font',
            get_string('fontsettingsheading', 'theme_adaptable'),
            format_text(get_string('fontdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Font Awesome Free.
        $name = 'theme_adaptable/fav';
        $title = get_string('fav', 'theme_adaptable');
        $description = get_string('favdesc', 'theme_adaptable');
        $default = 0;
        $choices = [
        0 => new \lang_string('favoff', 'theme_adaptable'),
        2 => new \lang_string('fa6name', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('purge_all_caches');
        $page->add($setting);

        // Font Awesome Free v4 shims.
        $name = 'theme_adaptable/faiv';
        $title = get_string('faiv', 'theme_adaptable');
        $description = get_string('faivdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('purge_all_caches');
        $page->add($setting);

        // Fonts heading.
        $name = 'theme_adaptable/settingsfonts';
        $heading = get_string('settingsfonts', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Google fonts.
        $name = 'theme_adaptable/googlefonts';
        $title = get_string('googlefonts', 'theme_adaptable');
        $description = get_string('googlefontsdesc', 'theme_adaptable', 'https://www.google.com/fonts');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main Font Name.
        $name = 'theme_adaptable/fontname';
        $title = get_string('fontname', 'theme_adaptable');
        $description = get_string('fontnamedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'default',
            settings_toolbox::fonts()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main Font Subset.
        $name = 'theme_adaptable/fontsubset';
        $title = get_string('fontsubset', 'theme_adaptable');
        $description = get_string('fontsubsetdesc', 'theme_adaptable');
        $default = '';
        $setting = new admin_setting_configmulticheckbox($name, $title, $description, $default, [
            'latin-ext' => "Latin Extended",
            'cyrillic' => "Cyrillic",
            'cyrillic-ext' => "Cyrillic Extended",
            'greek' => "Greek",
            'greek-ext' => "Greek Extended",
            'vietnamese' => "Vietnamese",
            'arabic' => "Arabic",
            'hebrew' => "Hebrew",
            'japanese' => "Japanese",
            'korean' => "Korean",
            'tamil' => "Tamil",
            'thai' => "Thai",
        ]);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main Font size.
        $name = 'theme_adaptable/fontsize';
        $title = get_string('fontsize', 'theme_adaptable');
        $description = get_string('fontsizedesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '95%', false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main Font weight.
        $name = 'theme_adaptable/fontweight';
        $title = get_string('fontweight', 'theme_adaptable');
        $description = get_string('fontweightdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            400,
            settings_toolbox::numbers(100, 900)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Navbar Menu Font Size.
        $name = 'theme_adaptable/menufontsize';
        $title = get_string('menufontsize', 'theme_adaptable');
        $description = get_string('menufontsizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            14,
            settings_toolbox::fontsizes()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Navbar Menu Padding.
        $name = 'theme_adaptable/menufontpadding';
        $title = get_string('menufontpadding', 'theme_adaptable');
        $description = get_string('menufontpaddingdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            20,
            settings_toolbox::pixels(10, 30, false)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Font Name.
        $name = 'theme_adaptable/fontheadername';
        $title = get_string('fontheadername', 'theme_adaptable');
        $description = get_string('fontheadernamedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'default',
            settings_toolbox::fonts()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header One Font Size.
        $name = 'theme_adaptable/fontheaderlevel1';
        $title = get_string('fontheaderlevel1', 'theme_adaptable');
        $description = get_string('fontsizemultiplerdesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '2.5');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Two Font Size.
        $name = 'theme_adaptable/fontheaderlevel2';
        $title = get_string('fontheaderlevel2', 'theme_adaptable');
        $description = get_string('fontsizemultiplerdesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '2');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Three Font Size.
        $name = 'theme_adaptable/fontheaderlevel3';
        $title = get_string('fontheaderlevel3', 'theme_adaptable');
        $description = get_string('fontsizemultiplerdesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '1.75');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Four Font Size.
        $name = 'theme_adaptable/fontheaderlevel4';
        $title = get_string('fontheaderlevel4', 'theme_adaptable');
        $description = get_string('fontsizemultiplerdesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '1.5');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Five Font Size.
        $name = 'theme_adaptable/fontheaderlevel5';
        $title = get_string('fontheaderlevel5', 'theme_adaptable');
        $description = get_string('fontsizemultiplerdesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '1.25');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Six Font Size.
        $name = 'theme_adaptable/fontheaderlevel6';
        $title = get_string('fontheaderlevel6', 'theme_adaptable');
        $description = get_string('fontsizemultiplerdesc', 'theme_adaptable');
        $setting = new admin_setting_font($name, $title, $description, '1');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header Font weight.
        $name = 'theme_adaptable/fontheaderweight';
        $title = get_string('fontheaderweight', 'theme_adaptable');
        $description = get_string('fontheaderweightdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            400,
            settings_toolbox::numbers(100, 900)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header font color.
        $name = 'theme_adaptable/fontheadercolor';
        $title = get_string('fontheadercolor', 'theme_adaptable');
        $description = get_string('fontheadercolordesc', 'theme_adaptable');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', null);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Footer settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function footer_settings() {
        $page = new admin_settingpage('theme_adaptable_footer', get_string('footersettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_footer',
            get_string('footersettingsheading', 'theme_adaptable'),
            format_text(get_string('footerdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Show moodle docs link.
        $name = 'theme_adaptable/moodledocs';
        $title = get_string('moodledocs', 'theme_adaptable');
        $description = get_string('moodledocsdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/footerblocksplacement';
        $title = get_string('footerblocksplacement', 'theme_adaptable');
        $description = get_string('footerblocksplacementdesc', 'theme_adaptable');
        $choices = [
            1 => get_string('footerblocksplacement1', 'theme_adaptable'),
            2 => get_string('footerblocksplacement2', 'theme_adaptable'),
            3 => get_string('footerblocksplacement3', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 1, $choices);
        $page->add($setting);

        // Show Footer blocks.
        $name = 'theme_adaptable/showfooterblocks';
        $title = get_string('showfooterblocks', 'theme_adaptable');
        $description = get_string('showfooterblocksdesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
        $page->add($setting);

        // Footer colors heading.
        $name = 'theme_adaptable/settingsfootercolours';
        $heading = get_string('settingsfootercolours', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $name = 'theme_adaptable/footerbkcolor';
        $title = get_string('footerbkcolor', 'theme_adaptable');
        $description = get_string('footerbkcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#424242', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/footertextcolor';
        $title = get_string('footertextcolor', 'theme_adaptable');
        $description = get_string('footertextcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/footertextcolor2';
        $title = get_string('footertextcolor2', 'theme_adaptable');
        $description = get_string('footertextcolor2desc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/footerlinkcolor';
        $title = get_string('footerlinkcolor', 'theme_adaptable');
        $description = get_string('footerlinkcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/footerdividingline';
        $title = get_string('footerdividingline', 'theme_adaptable');
        $description = get_string('footerdividinglinedesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $page->add(new admin_setting_heading(
            'theme_adaptable_footerbuilder',
            get_string('footerbuilderheading', 'theme_adaptable'),
            format_text(get_string('footerbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Footer block region builder.
        ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = toolbox::admin_settings_layout_builder(
            $page,
            'footerlayoutrow',
            3,
            ['3-3-3-3', '0-0-0-0', '0-0-0-0'],
            settings_toolbox::rowlayouts()
        );

        if ($totalblocks > 0) {
            $page->add(new admin_setting_heading(
                'theme_adaptable_footerlayoutcheck',
                get_string('layoutcheck', 'theme_adaptable'),
                format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
            ));

            $page->add(new admin_setting_heading('theme_adaptable_footerlayoutbuilder', '', $imgblder));
        }

        $blkcontmsg = get_string('layoutaddcontentdesc1', 'theme_adaptable');
        $blkcontmsg .= $totalblocks;
        $blkcontmsg .= get_string('layoutaddcontentdesc2', 'theme_adaptable');

        $page->add(new admin_setting_heading(
            'theme_adaptable_footerlayoutaddcontent',
            get_string('layoutaddcontent', 'theme_adaptable'),
            format_text($blkcontmsg, FORMAT_MARKDOWN)
        ));

        for ($i = 1; $i <= $totalblocks; $i++) {
            $name = 'theme_adaptable/footer' . $i . 'header';
            $title = get_string('footerheader', 'theme_adaptable') . $i;
            $description = get_string('footerdesc', 'theme_adaptable') . $i;
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $page->add($setting);

            $name = 'theme_adaptable/footer' . $i . 'content';
            $title = get_string('footercontent', 'theme_adaptable') . $i;
            $description = get_string('footercontentdesc', 'theme_adaptable') . $i;
            $default = '';
            $setting = new admin_setting_confightmleditor(
                $name,
                $title,
                $description,
                $default,
                'shed_footercontent',
                $i
            );
            $page->add($setting);
        }

        // Social icons.
        $name = 'theme_adaptable/hidefootersocial';
        $title = get_string('hidefootersocial', 'theme_adaptable');
        $description = get_string('hidefootersocialdesc', 'theme_adaptable');
        $radchoices = [
        0 => get_string('hide', 'theme_adaptable'),
        1 => get_string('show', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
        $page->add($setting);

        // Show Data retention button link.
        $name = 'theme_adaptable/gdprbutton';
        $title = get_string('gdprbutton', 'theme_adaptable');
        $description = get_string('gdprbuttondesc', 'theme_adaptable');
        $radchoices = [
        'none' => get_string('hide', 'theme_adaptable'),
        'inline' => get_string('show', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Footnote.
        $name = 'theme_adaptable/footnote';
        $title = get_string('footnote', 'theme_adaptable');
        $description = get_string('footnotedesc', 'theme_adaptable');
        $default = '';
        $setting = new admin_setting_confightmleditor(
            $name,
            $title,
            $description,
            $default,
            'shed_footnote'
        );
        $page->add($setting);

        return $page;
    }

    /**
     * Frontpage block regions settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function frontpage_block_regions_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_frontpage_block_regions',
            get_string('settingspagefrontpageblockregionsettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_marketing',
            get_string('blocklayoutbuilder', 'theme_adaptable'),
            format_text(get_string('blocklayoutbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/frontpageblocksenabled';
        $title = get_string('frontpageblocksenabled', 'theme_adaptable');
        $description = get_string('frontpageblocksenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, false);
        $page->add($setting);

        // Block region builder.
        $noregions = 20; // Number of block regions defined in config.php.
        ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = toolbox::admin_settings_layout_builder(
            $page,
            'blocklayoutlayoutrow',
            5,
            ['3-3-3-3', '4-4-4-0', '3-3-3-3', '0-0-0-0', '0-0-0-0'],
            settings_toolbox::rowlayouts()
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_blockslayoutcheck',
            get_string('layoutcheck', 'theme_adaptable'),
            format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $checkcountcolor = '#00695C';
        if ($totalblocks > $noregions) {
            $mktcountcolor = '#D7542A';
        }
        $mktcountmsg = '<span style="color: ' . $checkcountcolor . '">';
        $mktcountmsg .= get_string('layoutcount1', 'theme_adaptable') . '<strong>' . $noregions . '</strong>';
        $mktcountmsg .= get_string('layoutcount2', 'theme_adaptable') . '<strong>' . $totalblocks . '/' . $noregions . '</strong>.';

        $page->add(new admin_setting_heading('theme_adaptable_blockslayoutblockscount', '', $mktcountmsg));

        $page->add(new admin_setting_heading('theme_adaptable_blockslayoutbuilder', '', $imgblder));

        return $page;
    }

    /**
     * Frontpage courses settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function frontpage_courses_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_frontpage_courses',
            get_string('frontpagecoursesettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_frontpage_courses',
            get_string('frontpagesettingsheading', 'theme_adaptable'),
            format_text(get_string('frontpagedesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/frontpagerenderer';
        $title = get_string('frontpagerenderer', 'theme_adaptable');
        $description = get_string('frontpagerendererdesc', 'theme_adaptable');
        $choices = [
            1 => get_string('frontpagerendereroption1', 'theme_adaptable'),
            2 => get_string('frontpagerendereroption2', 'theme_adaptable'),
            3 => get_string('frontpagerendereroption3', 'theme_adaptable'),
            4 => get_string('frontpagerendereroption4', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 2, $choices);
        $page->add($setting);

        // Number of tiles per row: 12=1 tile / 6=2 tiles / 4 (default)=3 tiles / 3=4 tiles / 2=6 tiles.
        $name = 'theme_adaptable/frontpagenumbertiles';
        $title = get_string('frontpagenumbertiles', 'theme_adaptable');
        $description = get_string('frontpagenumbertilesdesc', 'theme_adaptable');
        $choices = [
            12 => get_string('frontpagetiles1', 'theme_adaptable'),
            6 => get_string('frontpagetiles2', 'theme_adaptable'),
            4 => get_string('frontpagetiles3', 'theme_adaptable'),
            3 => get_string('frontpagetiles4', 'theme_adaptable'),
            2 => get_string('frontpagetiles6', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 4, $choices);
        $page->add($setting);

        // Default image for 'Tiles with overlay' on 'frontpagerenderer' setting.
        $name = 'theme_adaptable/frontpagerendererdefaultimage';
        $title = get_string('frontpagerendererdefaultimage', 'theme_adaptable');
        $description = get_string('frontpagerendererdefaultimagedesc', 'theme_adaptable');
        $setting = new admin_setting_configstoredfiles(
            $name,
            $title,
            $description,
            'frontpagerendererdefaultimage',
            ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
        );
        $page->add($setting);

        // Show course contacts.
        $name = 'theme_adaptable/tilesshowcontacts';
        $title = get_string('tilesshowcontacts', 'theme_adaptable');
        $description = get_string('tilesshowcontactsdesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
        $page->add($setting);

        $name = 'theme_adaptable/tilesshowallcontacts';
        $title = get_string('tilesshowallcontacts', 'theme_adaptable');
        $description = get_string('tilesshowallcontactsdesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/tilescontactstitle';
        $title = get_string('tilescontactstitle', 'theme_adaptable');
        $description = get_string('tilescontactstitledesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
        $page->add($setting);

        $name = 'theme_adaptable/covhidebutton';
        $title = get_string('covhidebutton', 'theme_adaptable');
        $description = get_string('covhidebuttondesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
        $page->add($setting);

        // Show 'Available Courses' label.
        $name = 'theme_adaptable/enableavailablecourses';
        $title = get_string('enableavailablecourses', 'theme_adaptable');
        $description = get_string('enableavailablecoursesdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            [
                'inherit' => get_string('show'),
                'none' => get_string('hide'),
            ]
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/tilesbordercolor';
        $title = get_string('tilesbordercolor', 'theme_adaptable');
        $description = get_string('tilesbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Overlay tiles colors heading.
        $name = 'theme_adaptable/settingscoventryoverlaycolors';
        $heading = get_string('settingscoventryoverlaycolors', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $name = 'theme_adaptable/covbkcolor';
        $title = get_string('covbkcolor', 'theme_adaptable');
        $description = get_string('covbkcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/covfontcolor';
        $title = get_string('covfontcolor', 'theme_adaptable');
        $description = get_string('covfontcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/rendereroverlaycolor';
        $title = get_string('rendereroverlaycolor', 'theme_adaptable');
        $description = get_string('rendereroverlaycolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3A454b', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/rendereroverlayfontcolor';
        $title = get_string('rendereroverlayfontcolor', 'theme_adaptable');
        $description = get_string('rendereroverlayfontcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Frontpage slider settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function frontpage_slider_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_frontpage_slider',
            get_string('frontpageslidersettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_slideshow',
            get_string('slideshowsettingsheading', 'theme_adaptable'),
            format_text(
                get_string('slideshowdesc', 'theme_adaptable'),
                FORMAT_MARKDOWN
            )
        ));

        $name = 'theme_adaptable/sliderenabled';
        $title = get_string('sliderenabled', 'theme_adaptable');
        $description = get_string('sliderenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
        $page->add($setting);

        $name = 'theme_adaptable/slidervisible';
        $title = get_string('slidervisible', 'theme_adaptable');
        $description = get_string('slidervisibledesc', 'theme_adaptable');
        $options = [
            1 => get_string('slidervisibleloggedout', 'theme_adaptable'),
            2 => get_string('slidervisibleloggedin', 'theme_adaptable'),
            3 => get_string('slidervisibleloggedinout', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 3, $options);
        $page->add($setting);

        $name = 'theme_adaptable/sliderfullscreen';
        $title = get_string('sliderfullscreen', 'theme_adaptable');
        $description = get_string('sliderfullscreendesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
        $page->add($setting);

        $name = 'theme_adaptable/slidermargintop';
        $title = get_string('slidermargintop', 'theme_adaptable');
        $description = get_string('slidermargintopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            20,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/slidermarginbottom';
        $title = get_string('slidermarginbottom', 'theme_adaptable');
        $description = get_string('slidermarginbottomdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            20,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/slideroption2';
        $title = get_string('slideroption2', 'theme_adaptable');
        $description = get_string('slideroption2desc', 'theme_adaptable');
        $radchoices = [
            'slider1' => new lang_string('sliderstyle1', 'theme_adaptable'),
            'slider2' => new lang_string('sliderstyle2', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'nocaptions', $radchoices);
        $page->add($setting);

        $slideroption2 = get_config('theme_adaptable', 'slideroption2');
        if ($slideroption2 === false) {
            $slideroption2 = 'slider1';
        }

        if ($slideroption2 == 'slider1') {
            $name = 'theme_adaptable/sliderh3color';
            $title = get_string('sliderh3color', 'theme_adaptable');
            $description = get_string('sliderh3colordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/sliderh4color';
            $title = get_string('sliderh4color', 'theme_adaptable');
            $description = get_string('sliderh4colordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slidersubmitcolor';
            $title = get_string('slidersubmitcolor', 'theme_adaptable');
            $description = get_string('slidersubmitcolordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slidersubmitbgcolor';
            $title = get_string('slidersubmitbgcolor', 'theme_adaptable');
            $description = get_string('slidersubmitbgcolordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
        }

        if ($slideroption2 == 'slider2') {
            $name = 'theme_adaptable/slider2h3color';
            $title = get_string('slider2h3color', 'theme_adaptable');
            $description = get_string('slider2h3colordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slider2h3bgcolor';
            $title = get_string('slider2h3bgcolor', 'theme_adaptable');
            $description = get_string('slider2h3bgcolordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slider2h4color';
            $title = get_string('slider2h4color', 'theme_adaptable');
            $description = get_string('slider2h4colordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#000000', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slider2h4bgcolor';
            $title = get_string('slider2h4bgcolor', 'theme_adaptable');
            $description = get_string('slider2h4bgcolordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slideroption2submitcolor';
            $title = get_string('slideroption2submitcolor', 'theme_adaptable');
            $description = get_string('slideroption2submitcolordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slideroption2color';
            $title = get_string('slideroption2color', 'theme_adaptable');
            $description = get_string('slideroption2colordesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            $name = 'theme_adaptable/slideroption2a';
            $title = get_string('slideroption2a', 'theme_adaptable');
            $description = get_string('slideroption2adesc', 'theme_adaptable');
            $previewconfig = null;
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#51666C', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
        }

        // Number of slides.
        $name = 'theme_adaptable/slidercount';
        $title = get_string('slidercount', 'theme_adaptable');
        $description = get_string('slidercountdesc', 'theme_adaptable');
        $default = 3;
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::numbers(0, 12)
        );
        $page->add($setting);

        $slidercount = get_config('theme_adaptable', 'slidercount');

        // If we don't have a slide count yet, then default to the default.
        if ($slidercount === false) {
            $slidercount = $default;
        }

        for ($sliderindex = 1; $sliderindex <= $slidercount; $sliderindex++) {
            $fileid = 'p' . $sliderindex;
            $name = 'theme_adaptable/p' . $sliderindex;
            $title = get_string('sliderimage', 'theme_adaptable');
            $description = get_string('sliderimagedesc', 'theme_adaptable');
            $setting = new admin_setting_configstoredfiles(
                $name,
                $title,
                $description,
                $fileid,
                ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
            );
            $page->add($setting);

            $name = 'theme_adaptable/p' . $sliderindex . 'url';
            $title = get_string('sliderurl', 'theme_adaptable');
            $description = get_string('sliderurldesc', 'theme_adaptable');
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $page->add($setting);

            $name = 'theme_adaptable/p' . $sliderindex . 'cap';
            $title = get_string('slidercaption', 'theme_adaptable');
            $description = get_string('slidercaptiondesc', 'theme_adaptable');
            $default = '';
            $setting = new admin_setting_confightmleditor(
                $name,
                $title,
                $description,
                $default,
                'shed_pcap',
                $sliderindex
            );
            $page->add($setting);
        }

        return $page;
    }

    /**
     * General settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function general_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_general',
            get_string('settingspagegeneralsettings', 'theme_adaptable')
        );

        // Favicon file setting.
        $name = 'theme_adaptable/favicon';
        $title = get_string('favicon', 'theme_adaptable');
        $description = get_string('favicondesc', 'theme_adaptable');
        $setting = new admin_setting_description($name, $title, $description);
        $page->add($setting);

        // Enable save / cancel overlay at top of page.
        $name = 'theme_adaptable/enablesavecanceloverlay';
        $title = get_string('enablesavecanceloverlay', 'theme_adaptable');
        $description = get_string('enablesavecanceloverlaydesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/pageloadingprogress';
        $title = get_string('pageloadingprogress', 'theme_adaptable');
        $description = get_string('pageloadingprogressdesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, true);
        $page->add($setting);

        $name = 'theme_adaptable/pageloadingprogresstheme';
        $title = get_string('pageloadingprogresstheme', 'theme_adaptable');
        $description = get_string('pageloadingprogressthemedesc', 'theme_adaptable');
        $choices = [
            'minimal' => get_string('pageloadingprogressthememinimal', 'theme_adaptable'),
            'barber_shop' => get_string('pageloadingprogressthemebarbershop', 'theme_adaptable'),
            'big_counter' => get_string('pageloadingprogressthemebigcounter', 'theme_adaptable'),
            'bounce' => get_string('pageloadingprogressthemebounce', 'theme_adaptable'),
            'center_atom' => get_string('pageloadingprogressthemecenteratom', 'theme_adaptable'),
            'center_circle' => get_string('pageloadingprogressthemecentercircle', 'theme_adaptable'),
            'center_radar' => get_string('pageloadingprogressthemecenterradar', 'theme_adaptable'),
            'center_simple' => get_string('pageloadingprogressthemecentersimple', 'theme_adaptable'),
            'corner_indicator' => get_string('pageloadingprogressthemecornerindicator', 'theme_adaptable'),
            'fill_left' => get_string('pageloadingprogressthemefillleft', 'theme_adaptable'),
            'flash' => get_string('pageloadingprogressthemeflash', 'theme_adaptable'),
            'flat_top' => get_string('pageloadingprogressthemeflattop', 'theme_adaptable'),
            'loading_bar' => get_string('pageloadingprogressthemeloadingbar', 'theme_adaptable'),
            'mac_osx' => get_string('pageloadingprogressthememacosx', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'minimal', $choices);
        $page->add($setting);

        // Loading bar color.
        $name = 'theme_adaptable/loadingcolor';
        $title = get_string('loadingcolor', 'theme_adaptable');
        $description = get_string('loadingcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);
        return $page;
    }

    /**
     * Header settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function header_settings() {
        $page = new admin_settingpage('theme_adaptable_header', get_string('headersettings', 'theme_adaptable'));

        // Header layout section.
        $page->add(new admin_setting_heading(
            'theme_adaptable_headerstyle_heading',
            get_string('headerstyleheading', 'theme_adaptable'),
            format_text(get_string('headerstyleheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Adaptable header style selection.
        $name = 'theme_adaptable/headerstyle';
        $title = get_string('headerstyle', 'theme_adaptable');
        $description = get_string('headerstyledesc', 'theme_adaptable');
        $radchoices = [
            'style1' => get_string('headerstyle1', 'theme_adaptable'),
            'style2' => get_string('headerstyle2', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'style1', $radchoices);
        $page->add($setting);

        // Page header layout for header one.
        $name = 'theme_adaptable/pageheaderlayout';
        $title = get_string('pageheaderlayout', 'theme_adaptable');
        $description = get_string('pageheaderlayoutdesc', 'theme_adaptable');
        $radchoices = [
            'original' => get_string('pageheaderoriginal', 'theme_adaptable'),
            'alternative' => get_string('pageheaderalternative', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'original', $radchoices);
        $page->add($setting);

        // Page header layout for header two.
        $name = 'theme_adaptable/pageheaderlayouttwo';
        $title = get_string('pageheaderlayouttwo', 'theme_adaptable');
        $description = get_string('pageheaderlayouttwodesc', 'theme_adaptable');
        $radchoices = [
            'original' => get_string('pageheaderoriginal', 'theme_adaptable'),
            'nosearch' => get_string('pageheadernosearch', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'original', $radchoices);
        $page->add($setting);

        // Top header row background color.
        $name = 'theme_adaptable/headertoprowbkcolour';
        $title = get_string('headertoprowbkcolour', 'theme_adaptable');
        $description = get_string('headertoprowbkcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00796B', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Top header row text color.
        $name = 'theme_adaptable/headertoprowtextcolour';
        $title = get_string('headertoprowtextcolour', 'theme_adaptable');
        $description = get_string('headertoprowtextcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Top header row dividing line colour.
        $name = 'theme_adaptable/headertoprowdividingline';
        $title = get_string('headertoprowdividingline', 'theme_adaptable');
        $description = get_string('headertoprowdividinglinedesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main header row background color.
        $name = 'theme_adaptable/headermainrowbkcolour';
        $title = get_string('headermainrowbkcolour', 'theme_adaptable');
        $description = get_string('headermainrowbkcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main header row text color.
        $name = 'theme_adaptable/headermainrowtextcolour';
        $title = get_string('headermainrowtextcolour', 'theme_adaptable');
        $description = get_string('headermainrowtextcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header main row minimum height.
        $name = 'theme_adaptable/headermainrowminheight';
        $title = get_string('headermainrowminheight', 'theme_adaptable');
        $description = get_string('headermainrowminheightdesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '72px');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $page->add(new admin_setting_heading(
            'theme_adaptable_header',
            get_string('headersettingsheading', 'theme_adaptable'),
            format_text(get_string('headerdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Site title.
        $name = 'theme_adaptable/sitetitle';
        $title = get_string('sitetitle', 'theme_adaptable');
        $description = get_string('sitetitledesc', 'theme_adaptable');
        $radchoices = [
            'disabled' => get_string('sitetitleoff', 'theme_adaptable'),
            'default' => get_string('sitetitledefault', 'theme_adaptable'),
            'custom' => get_string('sitetitlecustom', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'default', $radchoices);
        $page->add($setting);

        // Site title text.
        $name = 'theme_adaptable/sitetitletext';
        $title = get_string('sitetitletext', 'theme_adaptable');
        $description = get_string('sitetitletextdesc', 'theme_adaptable');
        $default = '';
        $setting = new core_admin_setting_confightmleditor($name, $title, $description, $default);
        $page->add($setting);

        // Display Course title.
        $name = 'theme_adaptable/enablecoursetitle';
        $title = get_string('enablecoursetitle', 'theme_adaptable');
        $description = get_string('enablecoursetitledesc', 'theme_adaptable');
        $radchoices = [
            'fullname' => get_string('coursetitlefullname', 'theme_adaptable'),
            'shortname' => get_string('coursetitleshortname', 'theme_adaptable'),
        'off' => get_string('hide'),
        ];
        $default = 'fullname';
        $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
        $page->add($setting);

        // Course Title Maximum Width.
        $name = 'theme_adaptable/coursetitlemaxwidth';
        $title = get_string('coursetitlemaxwidth', 'theme_adaptable');
        $description = get_string('coursetitlemaxwidthdesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, 20, PARAM_INT);
        $page->add($setting);

        // Title Font Name.
        $name = 'theme_adaptable/fonttitlename';
        $title = get_string('fonttitlename', 'theme_adaptable');
        $description = get_string('fonttitlenamedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            'default',
            settings_toolbox::fonts()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Title Font size.
        $name = 'theme_adaptable/fonttitlesize';
        $title = get_string('fonttitlesize', 'theme_adaptable');
        $description = get_string('fonttitlesizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            48,
            settings_toolbox::fontsizes()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Title Font weight.
        $name = 'theme_adaptable/fonttitleweight';
        $title = get_string('fonttitleweight', 'theme_adaptable');
        $description = get_string('fonttitleweightdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            400,
            settings_toolbox::numbers(100, 900)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Title font color.
        $name = 'theme_adaptable/fonttitlecolor';
        $title = get_string('fonttitlecolor', 'theme_adaptable');
        $description = get_string('fonttitlecolordesc', 'theme_adaptable');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', null);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header image.
        $name = 'theme_adaptable/headerbgimage';
        $title = get_string('headerbgimage', 'theme_adaptable');
        $description = get_string('headerbgimagedesc', 'theme_adaptable');
        $setting = new admin_setting_configstoredfiles(
            $name,
            $title,
            $description,
            'headerbgimage',
            ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
        );
        $page->add($setting);

        // Header image text colour.
        $name = 'theme_adaptable/headerbgimagetextcolour';
        $title = get_string('headerbgimagetextcolour', 'theme_adaptable');
        $description = get_string('headerbgimagetextcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Logo.
        $name = 'theme_adaptable/logo';
        $title = get_string('logo', 'theme_adaptable');
        $description = get_string('logodesc', 'theme_adaptable');
        $setting = new admin_setting_configstoredfiles(
            $name,
            $title,
            $description,
            'logo',
            ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
        );
        $page->add($setting);

        // Logo description text.
        $name = 'theme_adaptable/logoalt';
        $title = get_string('logoalt', 'theme_adaptable');
        $description = get_string('logoaltdesc', 'theme_adaptable');
        $default = get_string('logo', 'theme_adaptable');
        $setting = new admin_setting_configtextarea($name, $title, $description, $default);
        $page->add($setting);

        // Select type of login.
        $name = 'theme_adaptable/displaylogin';
        $title = get_string('displaylogin', 'theme_adaptable');
        $description = get_string('displaylogindesc', 'theme_adaptable');
        $choices = [
            'button' => get_string('displayloginbutton', 'theme_adaptable'),
            'box' => get_string('displayloginbox', 'theme_adaptable'),
            'no' => get_string('displayloginno', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'button', $choices);
        $page->add($setting);

        // Show username.
        $name = 'theme_adaptable/showusername';
        $title = get_string('showusername', 'theme_adaptable');
        $description = get_string('showusernamedesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Have mobile primary navigation.
        $name = 'theme_adaptable/mobileprimarynav';
        $title = get_string('mobileprimarynav', 'theme_adaptable');
        $description = get_string('mobileprimarynavdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Course page header title.
        $name = 'theme_adaptable/coursepageheaderhidetitle';
        $title = get_string('coursepageheaderhidetitle', 'theme_adaptable');
        $description = get_string('coursepageheaderhidetitledesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $page->add($setting);

        $page->add(new admin_setting_heading(
            'theme_adaptable_messnotheader',
            get_string('headermessnot', 'theme_adaptable'),
            format_text(get_string('headermessnotdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Header notifications badge colour.
        $name = 'theme_adaptable/notbadgecolour';
        $title = get_string('notbadgecolour', 'theme_adaptable');
        $description = get_string('notbadgecolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Header notifications badge background colour.
        $name = 'theme_adaptable/notbadgebackgroundcolour';
        $title = get_string('notbadgebackgroundcolour', 'theme_adaptable');
        $description = get_string('notbadgebackgroundcolourdesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e53935', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Messages main chat window background colour.
        $name = 'theme_adaptable/messagingbackgroundcolor';
        $title = get_string('messagingbackgroundcolor', 'theme_adaptable');
        $description = get_string('messagingbackgroundcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Messages pop-up background color.
        $name = 'theme_adaptable/messagepopupbackground';
        $title = get_string('messagepopupbackground', 'theme_adaptable');
        $description = get_string('messagepopupbackgrounddesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#fff000', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Messages pop-up text color.
        $name = 'theme_adaptable/messagepopupcolor';
        $title = get_string('messagepopupcolor', 'theme_adaptable');
        $description = get_string('messagepopupcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $page->add(new admin_setting_heading(
            'theme_adaptable_breadcrumbheader',
            get_string('headerbreadcrumb', 'theme_adaptable'),
            format_text(get_string('headerbreadcrumbdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Display Breadcrumb or Course title where the breadcrumb normally is.
        $name = 'theme_adaptable/breadcrumbdisplay';
        $title = get_string('breadcrumbdisplay', 'theme_adaptable');
        $description = get_string('breadcrumbdisplaydesc', 'theme_adaptable');
        $radchoices = [
            'breadcrumb' => get_string('breadcrumb', 'theme_adaptable'),
            'fullname' => get_string('coursetitlefullname', 'theme_adaptable'),
            'shortname' => get_string('coursetitleshortname', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'breadcrumb', $radchoices);
        $page->add($setting);

        // Breadcrumb home.
        $name = 'theme_adaptable/breadcrumbhome';
        $title = get_string('breadcrumbhome', 'theme_adaptable');
        $description = get_string('breadcrumbhomedesc', 'theme_adaptable');
        $radchoices = [
            'text' => get_string('breadcrumbhometext', 'theme_adaptable'),
            'icon' => get_string('breadcrumbhomeicon', 'theme_adaptable'),
            'off' => get_string('breadcrumbhomeoff', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'icon', $radchoices);
        $page->add($setting);

        // Breadcrumb separator.
        $name = 'theme_adaptable/breadcrumbseparator';
        $title = get_string('breadcrumbseparator', 'theme_adaptable');
        $description = get_string('breadcrumbseparatordesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free');
        $setting = new admin_setting_configtext($name, $title, $description, 'angle-right');
        $page->add($setting);

        // Breadcrumb background color.
        $name = 'theme_adaptable/breadcrumb';
        $title = get_string('breadcrumbbackgroundcolor', 'theme_adaptable');
        $description = get_string('breadcrumbbackgroundcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f5f5f5', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Breadcrumb text color.
        $name = 'theme_adaptable/breadcrumbtextcolor';
        $title = get_string('breadcrumbtextcolor', 'theme_adaptable');
        $description = get_string('breadcrumbtextcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#444444', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Header menus settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function header_menus_settings() {
        $page = new admin_settingpage('theme_adaptable_menus', get_string('menusettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_menus',
            get_string('menusheading', 'theme_adaptable'),
            format_text(
                get_string('menustitledesc', 'theme_adaptable') . '<br><br>' .
                get_string('fontawesomesettingdesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free'),
                FORMAT_MARKDOWN
            )
        ));

        // Settings for top header menus.
        $page->add(new admin_setting_heading(
            'theme_adaptable_menus_visibility',
            get_string('menusheadingvisibility', 'theme_adaptable'),
            format_text(get_string('menusheadingvisibilitydesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/enablemenus';
        $title = get_string('enablemenus', 'theme_adaptable');
        $description = get_string('enablemenusdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/menuslinkright';
        $title = get_string('menuslinkright', 'theme_adaptable');
        $description = get_string('menuslinkrightdesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Links menu icon. Default is "fa-link".
        $name = 'theme_adaptable/menuslinkicon';
        $title = get_string('menuslinkicon', 'theme_adaptable');
        $description = get_string('menuslinkicondesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, 'fa-link', PARAM_TEXT, '30');
        $page->add($setting);

        $name = 'theme_adaptable/disablemenuscoursepages';
        $title = get_string('disablemenuscoursepages', 'theme_adaptable');
        $description = get_string('disablemenuscoursepagesdesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/menusession';
        $title = get_string('menusession', 'theme_adaptable');
        $description = get_string('menusessiondesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, true, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/menusessionttl';
        $title = get_string('menusessionttl', 'theme_adaptable');
        $description = get_string('menusessionttldesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '30', PARAM_INT);
        $page->add($setting);

        $name = 'theme_adaptable/menuuseroverride';
        $title = get_string('menuuseroverride', 'theme_adaptable');
        $description = get_string('menuuseroverridedesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/menuoverrideprofilefield';
        $title = get_string('menuoverrideprofilefield', 'theme_adaptable');
        $description = get_string('menuoverrideprofilefielddesc', 'theme_adaptable');
        $default = get_string('menuoverrideprofilefielddefault', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
        $page->add($setting);

        // Number of menus.
        $name = 'theme_adaptable/topmenuscount';
        $title = get_string('topmenuscount', 'theme_adaptable');
        $description = get_string('topmenuscountdesc', 'theme_adaptable');
        $default = 1;
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::numbers(0, 12)
        );
        $page->add($setting);

        // If we don't have a menuscount yet, default to the preset.
        $topmenuscount = get_config('theme_adaptable', 'topmenuscount');
        if ($topmenuscount === false) {
            $topmenuscount = $default;
        }

        for ($topmenusindex = 1; $topmenusindex <= $topmenuscount; $topmenusindex++) {
            $page->add(new admin_setting_heading(
                'theme_adaptable_menus' . $topmenusindex,
                get_string('newmenuheading', 'theme_adaptable') . $topmenusindex,
                format_text(get_string('menusdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
            ));

            $name = 'theme_adaptable/newmenu' . $topmenusindex . 'title';
            $title = get_string('newmenutitle', 'theme_adaptable');
            $description = get_string('newmenutitledesc', 'theme_adaptable');
            $default = get_string('newmenutitledefault', 'theme_adaptable') . ' ' . $topmenusindex;
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW);
            $page->add($setting);

            $name = 'theme_adaptable/newmenu' . $topmenusindex;
            $title = get_string('newmenu', 'theme_adaptable') . $topmenusindex;
            $description = get_string('newmenudesc', 'theme_adaptable');
            $setting = new admin_setting_configtextarea($name, $title, $description, '', PARAM_RAW, '50', '10');
            $page->add($setting);

            $name = 'theme_adaptable/newmenu' . $topmenusindex . 'requirelogin';
            $title = get_string('newmenurequirelogin', 'theme_adaptable');
            $description = get_string('newmenurequirelogindesc', 'theme_adaptable');
            $default = false;
            $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
            $page->add($setting);

            $name = 'theme_adaptable/newmenu' . $topmenusindex . 'field';
            $title = get_string('newmenufield', 'theme_adaptable');
            $description = get_string('newmenufielddesc', 'theme_adaptable');
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
            $page->add($setting);
        }
        return $page;
    }

    /**
     * Header search social settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function header_search_social_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_search_social',
            get_string('searchsocialsettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_search_social',
            get_string('searchsocialheading', 'theme_adaptable'),
            format_text(get_string('searchsocialtitledesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Choose what to do with the search box and social icons.
        $name = 'theme_adaptable/headersearchandsocial';
        $title = get_string('headersearchandsocial', 'theme_adaptable');
        $description = get_string('headersearchandsocialdesc', 'theme_adaptable');
        $choices = [
            'none' => get_string('headersearchandsocialnone', 'theme_adaptable'),
            'searchmobilenav' => get_string('headersearchandsocialsearchmobilenav', 'theme_adaptable'),
            'searchheader' => get_string('headersearchandsocialsearchheader', 'theme_adaptable'),
            'socialheader' => get_string('headersearchandsocialsocialheader', 'theme_adaptable'),
            'searchnavbar' => get_string('headersearchandsocialsearchnavbar', 'theme_adaptable'),
            'searchnavbarsocialheader' => get_string('headersearchandsocialsearchnavbarsocialheader', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'searchmobilenav', $choices);
        $page->add($setting);

        // Search box padding.
        $name = 'theme_adaptable/searchboxpadding';
        $title = get_string('searchboxpadding', 'theme_adaptable');
        $description = get_string('searchboxpaddingdesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '0 0 0 0');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/socialsize';
        $title = get_string('socialsize', 'theme_adaptable');
        $description = get_string('socialsizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            37,
            settings_toolbox::pixels(14, 46, false)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/socialpaddingside';
        $title = get_string('socialpaddingside', 'theme_adaptable');
        $description = get_string('socialpaddingsidedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            16,
            settings_toolbox::pixels(10, 30, false)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/socialpaddingtop';
        $title = get_string('socialpaddingtop', 'theme_adaptable');
        $description = get_string('socialpaddingtopdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            '0%',
            settings_toolbox::percentages(0.0, 2.5, 0.1)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/socialtarget';
        $title = get_string('socialtarget', 'theme_adaptable');
        $description = get_string('socialtargetdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            '_self',
            [
                '_blank' => new lang_string('targetnewwindow', 'theme_adaptable'),
                '_self' => new lang_string('targetsamewindow', 'theme_adaptable'),
            ]
        );
        $page->add($setting);

        $name = 'theme_adaptable/socialiconlist';
        $title = get_string('socialiconlist', 'theme_adaptable');
        $default = '';
        $description = get_string('socialiconlistdesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free');
        $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_RAW, '50', '10');
        $page->add($setting);
        return $page;
    }

    /**
     * Header user settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function header_user_settings() {
        $page = new admin_settingpage('theme_adaptable_usernav', get_string('usernav', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_usernav',
            get_string('usernavheading', 'theme_adaptable'),
            format_text(get_string('usernavdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Position of the username.
        $name = 'theme_adaptable/usernameposition';
        $title = get_string('usernameposition', 'theme_adaptable');
        $description = get_string('usernamepositiondesc', 'theme_adaptable');
        $poschoices = [
            'left' => get_string('left', 'editor'),
            'right' => get_string('right', 'editor'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'left', $poschoices);
        $page->add($setting);

        $name = 'theme_adaptable/hideinforum';
        $title = get_string('hideinforum', 'theme_adaptable');
        $description = get_string('hideinforumdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable My.
        $name = 'theme_adaptable/enablemy';
        $title = get_string('enablemy', 'theme_adaptable');
        $description = get_string('enablemydesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable View Profile.
        $name = 'theme_adaptable/enableprofile';
        $title = get_string('enableprofile', 'theme_adaptable');
        $description = get_string('enableprofiledesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Edit Profile.
        $name = 'theme_adaptable/enableeditprofile';
        $title = get_string('enableeditprofile', 'theme_adaptable');
        $description = get_string('enableeditprofiledesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Calendar.
        $name = 'theme_adaptable/enablecalendar';
        $title = get_string('enablecalendar', 'theme_adaptable');
        $description = get_string('enablecalendardesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Private Files.
        $name = 'theme_adaptable/enableprivatefiles';
        $title = get_string('enableprivatefiles', 'theme_adaptable');
        $description = get_string('enableprivatefilesdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Grades.
        $name = 'theme_adaptable/enablegrades';
        $title = get_string('enablegrades', 'theme_adaptable');
        $description = get_string('enablegradesdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Badges.
        $name = 'theme_adaptable/enablebadges';
        $title = get_string('enablebadges', 'theme_adaptable');
        $description = get_string('enablebadgesdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Preferences.
        $name = 'theme_adaptable/enablepref';
        $title = get_string('enablepref', 'theme_adaptable');
        $description = get_string('enableprefdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Notes.
        $name = 'theme_adaptable/enablenote';
        $title = get_string('enablenote', 'theme_adaptable');
        $description = get_string('enablenotedesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Blog.
        $name = 'theme_adaptable/enableblog';
        $title = get_string('enableblog', 'theme_adaptable');
        $description = get_string('enableblogdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Forum posts.
        $name = 'theme_adaptable/enableposts';
        $title = get_string('enableposts', 'theme_adaptable');
        $description = get_string('enablepostsdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable My Feedback.
        $name = 'theme_adaptable/enablefeed';
        $title = get_string('enablefeed', 'theme_adaptable');
        $description = get_string('enablefeeddesc', 'theme_adaptable', 'https://moodle.org/plugins/report_myfeedback');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Enable Accessibility Tool.
        $name = 'theme_adaptable/enableaccesstool';
        $title = get_string('enableaccesstool', 'theme_adaptable');
        $description = get_string(
            'enableaccesstooldesc',
            'theme_adaptable',
            'https://github.com/sharpchi/moodle-local_accessibilitytool'
        );
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        return $page;
    }

    /**
     * Import export settings.
     *
     * @param $page The page to add the settings to.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function importexport_settings($page) {
        $page->add(new admin_setting_heading(
            'theme_adaptable_importexport',
            get_string('propertiessub', 'theme_adaptable'),
            format_text(get_string('propertiesdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $page->add(new admin_setting_getprops(
            'theme_adaptable/getprops',
            get_string('propertiesproperty', 'theme_adaptable'),
            get_string('propertiesvalue', 'theme_adaptable'),
            'theme_adaptable',
            'theme_adaptable_importexport',
            get_string('propertiesreturn', 'theme_adaptable'),
            get_string('propertiesexport', 'theme_adaptable'),
            get_string('propertiesexportfilestoo', 'theme_adaptable'),
            get_string('propertiesexportfilestoofile', 'theme_adaptable')
        ));

        $name = 'theme_adaptable/propertyfiles';
        $title = get_string('propertyfiles', 'theme_adaptable');
        $description = get_string('propertyfilesdesc', 'theme_adaptable');
        $setting = new admin_setting_configstoredfiles(
            $name,
            $title,
            $description,
            'propertyfiles',
            ['accepted_types' => '*.json', 'maxfiles' => 8]
        );
        $page->add($setting);

        // Import theme settings section (put properties).
        $name = 'theme_adaptable/theme_adaptable_putprops_import_heading';
        $heading = get_string('putpropertiesheading', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        $fileputpropssetting = new admin_setting_configstoredfile_putprops(
            'theme_adaptable/fileputprops',
            get_string('putpropertiesfilename', 'theme_adaptable'),
            get_string('putpropertiesfiledesc', 'theme_adaptable'),
            'fileputprops',
            'Adaptable',
            'theme_adaptable',
            '\theme_adaptable\toolbox::put_properties',
            'putprops',
            ['accepted_types' => '*.json', 'maxfiles' => 1]
        );
        $fileputpropssetting->set_updatedcallback('purge_all_caches');
        $page->add($fileputpropssetting);

        $setting = new admin_setting_putprops(
            'theme_adaptable/putprops',
            get_string('putpropertiesname', 'theme_adaptable'),
            get_string('putpropertiesdesc', 'theme_adaptable'),
            'Adaptable',
            'theme_adaptable',
            '\theme_adaptable\toolbox::put_properties'
        );
        $setting->set_updatedcallback('purge_all_caches');
        $fileputpropssetting->set_admin_setting_putprops($setting);
        $page->add($setting);

        return $page;
    }

    /**
     * Information blocks settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function information_blocks_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_information_blocks',
            get_string('informationblocksettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_information',
            get_string('informationsettingsheading', 'theme_adaptable'),
            format_text(get_string('informationsettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/informationblocksenabled';
        $title = get_string('informationblocksenabled', 'theme_adaptable');
        $description = get_string('informationblocksenableddesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/informationblocksvisible';
        $title = get_string('informationblocksvisible', 'theme_adaptable');
        $description = get_string('informationblocksvisibledesc', 'theme_adaptable');
        $options = [
            1 => get_string('informationblocksvisibleloggedout', 'theme_adaptable'),
            2 => get_string('informationblocksvisibleloggedin', 'theme_adaptable'),
            3 => get_string('informationblocksvisibleloggedinout', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 3, $options);
        $page->add($setting);

        $page->add(new admin_setting_heading(
            'theme_adaptable_informationblocksbuilder',
            get_string('informationblocksbuilderheading', 'theme_adaptable'),
            format_text(get_string('informationblocksbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Information block region builder.
        ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = toolbox::admin_settings_layout_builder(
            $page,
            'informationblockslayoutrow',
            5,
            ['3-3-3-3', '0-0-0-0', '0-0-0-0', '0-0-0-0', '0-0-0-0'],
            settings_toolbox::rowlayouts()
        );

        if ($totalblocks > 0) {
            $page->add(new admin_setting_heading(
                'theme_adaptable_informationblocklayoutcheck',
                get_string('layoutcheck', 'theme_adaptable'),
                format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
            ));

            $page->add(new admin_setting_heading('theme_adaptable_informationlayoutbuilder', '', $imgblder));
        }

        return $page;
    }

    /**
     * Layout settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function layout_settings() {
        $page = new admin_settingpage('theme_adaptable_layout', get_string('layoutsettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_layout',
            get_string('layoutsettingsheading', 'theme_adaptable'),
            format_text(get_string('layoutdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Background Image.
        $name = 'theme_adaptable/homebk';
        $title = get_string('homebk', 'theme_adaptable');
        $description = get_string('homebkdesc', 'theme_adaptable');
        $setting = new admin_setting_configstoredfiles(
            $name,
            $title,
            $description,
            'homebk',
            ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Display block in the Left/Right side.
        $name = 'theme_adaptable/blockside';
        $title = get_string('blockside', 'theme_adaptable');
        $description = get_string('blocksidedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            [
                0 => get_string('rightblocks', 'theme_adaptable'),
                1 => get_string('leftblocks', 'theme_adaptable'),
            ]
        );
        $page->add($setting);

        // Fullscreen width.
        $name = 'theme_adaptable/fullscreenwidth';
        $title = get_string('fullscreenwidth', 'theme_adaptable');
        $description = get_string('fullscreenwidthdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            '95%',
            settings_toolbox::percentages(95, 100)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Standard screen width.
        $name = 'theme_adaptable/standardscreenwidth';
        $title = get_string('standardscreenwidth', 'theme_adaptable');
        $description = get_string('standardscreenwidthdesc', 'theme_adaptable');
        $choices = [
            'standard' => '1170px',
            'narrow' => '1000px',
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'standard', $choices);
        $page->add($setting);

        // Emoticons size.
        $name = 'theme_adaptable/emoticonsize';
        $title = get_string('emoticonsize', 'theme_adaptable');
        $description = get_string('emoticonsizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            16,
            settings_toolbox::fontsizes()
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Info icon colour.
        $name = 'theme_adaptable/infoiconcolor';
        $title = get_string('infoiconcolor', 'theme_adaptable');
        $description = get_string('infoiconcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#5bc0de', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Danger icon colour.
        $name = 'theme_adaptable/dangericoncolor';
        $title = get_string('dangericoncolor', 'theme_adaptable');
        $description = get_string('dangericoncolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#d9534f', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Adaptable Tabbed layout changes.
        $name = 'theme_adaptable/tabbedlayoutheading';
        $heading = get_string('tabbedlayoutheading', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Course page tabbed layout.
        $name = 'theme_adaptable/tabbedlayoutcoursepage';
        $title = get_string('tabbedlayoutcoursepage', 'theme_adaptable');
        $description = get_string('tabbedlayoutcoursepagedesc', 'theme_adaptable');
        $default = 0;
        $courselabel = new lang_string('tabbedlayouttablabelcourse', 'theme_adaptable');
        $tab1label = new lang_string('tabbedlayouttablabelcourse1', 'theme_adaptable');
        $tab2label = new lang_string('tabbedlayouttablabelcourse2', 'theme_adaptable');
        $choices = [
            '0' => get_string('disabled', 'theme_adaptable'),
            '0-1' => $courselabel . ' + ' . $tab1label,
            '1-0' => $tab1label . ' + ' . $courselabel,
            '0-1-2' => $courselabel . ' + ' . $tab1label . ' + ' . $tab2label,
            '1-0-2' => $tab1label . ' + ' . $courselabel . ' + ' . $tab2label,
            '1-2-0' => $tab1label . ' + ' . $tab2label . ' + ' . $courselabel,
            '0-2-1' => $courselabel . ' + ' . $tab2label . ' + ' . $tab1label,
        ];
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $page->add($setting);

        // Have a link back to the course page in the course tabs.
        $name = 'theme_adaptable/tabbedlayoutcoursepagelink';
        $title = get_string('tabbedlayoutcoursepagelink', 'theme_adaptable');
        $description = get_string('tabbedlayoutcoursepagelinkdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Course page tab colour selected.
        $name = 'theme_adaptable/tabbedlayoutcoursepagetabcolorselected';
        $title = get_string('tabbedlayoutcoursepagetabcolorselected', 'theme_adaptable');
        $description = get_string('tabbedlayoutcoursepagetabcolorselecteddesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#06c', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course page tab colour unselected.
        $name = 'theme_adaptable/tabbedlayoutcoursepagetabcolorunselected';
        $title = get_string('tabbedlayoutcoursepagetabcolorunselected', 'theme_adaptable');
        $description = get_string('tabbedlayoutcoursepagetabcolorunselecteddesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eee', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Course home page tab persistence time.
        $name = 'theme_adaptable/tabbedlayoutcoursepagetabpersistencetime';
        $title = get_string('tabbedlayoutcoursepagetabpersistencetime', 'theme_adaptable');
        $description = get_string('tabbedlayoutcoursepagetabpersistencetimedesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '30', PARAM_INT);
        $page->add($setting);

        // Dashboard page tabbed layout.
        $name = 'theme_adaptable/tabbedlayoutdashboard';
        $title = get_string('tabbedlayoutdashboard', 'theme_adaptable');
        $description = get_string('tabbedlayoutdashboarddesc', 'theme_adaptable');
        $default = 0;
        $dashboardlabel = new lang_string('tabbedlayouttablabeldashboard', 'theme_adaptable');
        $tab1label = new lang_string('tabbedlayouttablabeldashboard1', 'theme_adaptable');
        $tab2label = new lang_string('tabbedlayouttablabeldashboard2', 'theme_adaptable');
        $choices = [
            '0' => get_string('disabled', 'theme_adaptable'),
            '0-1' => $dashboardlabel . ' + ' . $tab1label,
            '1-0' => $tab1label . ' + ' . $dashboardlabel,
            '0-1-2' => $dashboardlabel . ' + ' . $tab1label . ' + ' . $tab2label,
            '1-0-2' => $tab1label . ' + ' . $dashboardlabel . ' + ' . $tab2label,
            '1-2-0' => $tab1label . ' + ' . $tab2label . ' + ' . $dashboardlabel,
            '0-2-1' => $dashboardlabel . ' + ' . $tab2label . ' + ' . $tab1label,
        ];
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $page->add($setting);

        // Dashboard page tab colour selected.
        $name = 'theme_adaptable/tabbedlayoutdashboardcolorselected';
        $title = get_string('tabbedlayoutdashboardtabcolorselected', 'theme_adaptable');
        $description = get_string('tabbedlayoutdashboardtabcolorselecteddesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#06c', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dashboard page tab colour unselected.
        $name = 'theme_adaptable/tabbedlayoutdashboardcolorunselected';
        $title = get_string('tabbedlayoutdashboardtabcolorunselected', 'theme_adaptable');
        $description = get_string('tabbedlayoutdashboardtabcolorunselecteddesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#eee', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/tabbedlayoutdashboardtab1condition';
        $title = get_string('tabbedlayoutdashboardtab1condition', 'theme_adaptable');
        $description = get_string('tabbedlayoutdashboardtab1conditiondesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW, '');
        $page->add($setting);

        $name = 'theme_adaptable/tabbedlayoutdashboardtab2condition';
        $title = get_string('tabbedlayoutdashboardtab2condition', 'theme_adaptable');
        $description = get_string('tabbedlayoutdashboardtab2conditiondesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW, '');
        $page->add($setting);

        return $page;
    }

    /**
     * Layout responsive settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function layout_responsive_settings() {
        $page = new admin_settingpage('theme_adaptable_mobile', get_string('responsivesettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_mobile',
            get_string('responsivesettingsheading', 'theme_adaptable'),
            format_text(get_string(
                'responsivesettingsdesc',
                'theme_adaptable',
                'https://getbootstrap.com/docs/5.3/utilities/display/'
            ), FORMAT_MARKDOWN)
        ));

        // Hide Full Header.
        $name = 'theme_adaptable/responsiveheader';
        $title = get_string('responsiveheader', 'theme_adaptable');
        $description = get_string('responsiveheaderdesc', 'theme_adaptable');
        $default = 'd-none d-lg-flex';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('flex')
        );
        $page->add($setting);

        // Hide Social icons.
        $name = 'theme_adaptable/responsivesocial';
        $title = get_string('responsivesocial', 'theme_adaptable');
        $description = get_string('responsivesocialdesc', 'theme_adaptable');
        $default = 'd-none d-lg-block';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('block')
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/responsivesocialsize';
        $title = get_string('responsivesocialsize', 'theme_adaptable');
        $description = get_string('responsivesocialsizedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            34,
            settings_toolbox::pixels(14, 46, false)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Hide Logo.
        $name = 'theme_adaptable/responsivelogo';
        $title = get_string('responsivelogo', 'theme_adaptable');
        $description = get_string('responsivelogodesc', 'theme_adaptable');
        $default = 'd-none d-lg-inline-block';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('inline-block')
        );
        $page->add($setting);

        // Hide header title.
        $name = 'theme_adaptable/responsiveheadertitle';
        $title = get_string('responsiveheadertitle', 'theme_adaptable');
        $description = get_string('responsiveheadertitledesc', 'theme_adaptable');
        $default = 'd-none d-lg-inline-block';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('inline-block')
        );
        $page->add($setting);

        // Hide activity / section navigation.
        $name = 'theme_adaptable/responsivesectionnav';
        $title = get_string('responsivesectionnav', 'theme_adaptable');
        $description = get_string('responsivesectionnavdesc', 'theme_adaptable');
        $radchoices = [
            0 => get_string('show', 'theme_adaptable'),
            1 => get_string('hide', 'theme_adaptable'),
        ];
        $default = 1;
        $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
        $page->add($setting);

        // Hide Ticker.
        $name = 'theme_adaptable/responsiveticker';
        $title = get_string('responsiveticker', 'theme_adaptable');
        $description = get_string('responsivetickerdesc', 'theme_adaptable');
        $default = 'd-none d-lg-block';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('block')
        );
        $page->add($setting);

        // Hide breadcrumbs on small screens.
        $name = 'theme_adaptable/responsivebreadcrumb';
        $title = get_string('responsivebreadcrumb', 'theme_adaptable');
        $description = get_string('responsivebreadcrumbdesc', 'theme_adaptable');
        $default = 'd-none d-md-flex';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('flex')
        );
        $page->add($setting);

        // Hide Slider.
        $name = 'theme_adaptable/responsiveslider';
        $title = get_string('responsiveslider', 'theme_adaptable');
        $description = get_string('responsivesliderdesc', 'theme_adaptable');
        $default = 'd-none d-lg-block';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('block')
        );
        $page->add($setting);

        // Hide Footer.
        $name = 'theme_adaptable/responsivepagefooter';
        $title = get_string('responsivepagefooter', 'theme_adaptable');
        $description = get_string('responsivepagefooterdesc', 'theme_adaptable');
        $default = 'd-none d-lg-block';
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::screensize('block')
        );
        $page->add($setting);

        // Mobile colors heading.
        $name = 'theme_adaptable/settingsmobilecolors';
        $heading = get_string('settingsmobilecolors', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Mobile menu background color.
        $name = 'theme_adaptable/mobilemenubkcolor';
        $title = get_string('mobilemenubkcolor', 'theme_adaptable');
        $description = get_string('mobilemenubkcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#F9F9F9', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Marketing blocks settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function marketing_blocks_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_frontpage_blocks',
            get_string('frontpageblocksettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_marketing',
            get_string('marketingsettingsheading', 'theme_adaptable'),
            format_text(get_string('marketingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/infobox';
        $title = get_string('infobox', 'theme_adaptable');
        $description = get_string('infoboxdesc', 'theme_adaptable');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default, 'shed_infobox', 1);
        $page->add($setting);

        $name = 'theme_adaptable/infobox2';
        $title = get_string('infobox2', 'theme_adaptable');
        $description = get_string('infobox2desc', 'theme_adaptable');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default, 'shed_infobox', 2);
        $page->add($setting);

        $name = 'theme_adaptable/infoboxfullscreen';
        $title = get_string('infoboxfullscreen', 'theme_adaptable');
        $description = get_string('infoboxfullscreendesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/frontpagemarketenabled';
        $title = get_string('frontpagemarketenabled', 'theme_adaptable');
        $description = get_string('frontpagemarketenableddesc', 'theme_adaptable');
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/marketingvisible';
        $title = get_string('marketingvisible', 'theme_adaptable');
        $description = get_string('marketingvisibledesc', 'theme_adaptable');
        $options = [
            1 => get_string('marketingvisibleloggedout', 'theme_adaptable'),
            2 => get_string('marketingvisibleloggedin', 'theme_adaptable'),
            3 => get_string('marketingvisibleloggedinout', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 3, $options);
        $page->add($setting);

        $name = 'theme_adaptable/frontpagemarketoption';
        $title = get_string('frontpagemarketoption', 'theme_adaptable');
        $description = get_string('frontpagemarketoptiondesc', 'theme_adaptable');
        $choices = [
            '' => get_string('nostyle', 'theme_adaptable'),
            'internalmarket' => new lang_string('bcustyle', 'theme_adaptable'),
            'covtiles' => new lang_string('coventrystyle', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'covtiles', $choices);
        $page->add($setting);

        // Market blocks colors heading.
        $name = 'theme_adaptable/settingsmarketingcolors';
        $heading = get_string('settingsmarketingcolors', 'theme_adaptable');
        $setting = new admin_setting_heading($name, $heading, '');
        $page->add($setting);

        // Market blocks border color.
        $name = 'theme_adaptable/marketblockbordercolor';
        $title = get_string('marketblockbordercolor', 'theme_adaptable');
        $description = get_string('marketblockbordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e8eaeb', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Market blocks background color.
        $name = 'theme_adaptable/marketblocksbackgroundcolor';
        $title = get_string('marketblocksbackgroundcolor', 'theme_adaptable');
        $description = get_string('marketblocksbackgroundcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, 'transparent', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Marketing block region builder.
        $page->add(new admin_setting_heading(
            'theme_adaptable_marketingbuilder',
            get_string('marketingbuilderheading', 'theme_adaptable'),
            format_text(get_string('marketingbuilderdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        ['imgblder' => $imgblder, 'totalblocks' => $totalblocks] = toolbox::admin_settings_layout_builder(
            $page,
            'marketlayoutrow',
            5,
            ['3-3-3-3', '0-0-0-0', '0-0-0-0', '0-0-0-0', '0-0-0-0'],
            settings_toolbox::rowlayouts()
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_marketingblocklayoutcheck',
            get_string('layoutcheck', 'theme_adaptable'),
            format_text(get_string('layoutcheckdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $page->add(new admin_setting_heading('theme_adaptable_marketinglayoutbuilder', '', $imgblder));

        $blkcontmsg = get_string('layoutaddcontentdesc1', 'theme_adaptable');
        $blkcontmsg .= $totalblocks;
        $blkcontmsg .= get_string('layoutaddcontentdesc2', 'theme_adaptable');

        $page->add(new admin_setting_heading(
            'theme_adaptable_blocklayoutaddcontent',
            get_string('layoutaddcontent', 'theme_adaptable'),
            format_text($blkcontmsg, FORMAT_MARKDOWN)
        ));

        for ($i = 1; $i <= $totalblocks; $i++) {
            $name = 'theme_adaptable/market' . $i;
            $title = get_string('market', 'theme_adaptable', $i);
            $description = get_string('marketdesc', 'theme_adaptable');
            $default = '';
            $setting = new admin_setting_confightmleditor(
                $name,
                $title,
                $description,
                $default,
                'shed_market',
                $i
            );
            $page->add($setting);
        }

        return $page;
    }

    /**
     * Navbar settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function navbar_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_navbar_settings',
            get_string('navbarsettings', 'theme_adaptable')
        );

        $page->add(
            new admin_setting_heading(
                'theme_adaptable_navbar_settings',
                get_string('navbarsettingsheading', 'theme_adaptable'),
                ''
            )
        );

        // Sticky Navbar at the top. See issue #278.
        $name = 'theme_adaptable/stickynavbar';
        $title = get_string('stickynavbar', 'theme_adaptable');
        $description = get_string('stickynavbardesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/navbardisplayicons';
        $title = get_string('navbardisplayicons', 'theme_adaptable');
        $description = get_string('navbardisplayiconsdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/navbardisplaytitles';
        $title = get_string('navbardisplaytitles', 'theme_adaptable');
        $description = get_string('navbardisplaytitlesdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/navbardisplaymenuarrow';
        $title = get_string('navbardisplaymenuarrow', 'theme_adaptable');
        $description = get_string('navbardisplaymenuarrowdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Enable/Disable menu items.
        $name = 'theme_adaptable/enablehome';
        $title = get_string('home');
        $description = get_string('enablehomedesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablehomeredirect';
        $title = get_string('enablehomeredirect', 'theme_adaptable');
        $description = get_string('enablehomeredirectdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablemyhome';
        $title = get_string('myhome');
        $description = get_string('enablemydesc', 'theme_adaptable', $title);
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablemycourses';
        $title = get_string('courses');
        $description = get_string('enablemydesc', 'theme_adaptable', $title);
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enableevents';
        $title = get_string('events', 'theme_adaptable');
        $description = get_string('enableeventsdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablethiscourse';
        $title = get_string('thiscourse', 'theme_adaptable');
        $description = get_string('enablethiscoursedesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablecoursesections';
        $title = get_string('coursesections', 'theme_adaptable');
        $description = get_string('enablecoursesectionsdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablecompetencieslink';
        $title = get_string('enablecompetencieslink', 'theme_adaptable');
        $description = get_string('enablecompetencieslinkdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/enablezoom';
        $title = get_string('enablezoom', 'theme_adaptable');
        $description = get_string('enablezoomdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/defaultzoom';
        $title = get_string('defaultzoom', 'theme_adaptable');
        $description = get_string('defaultzoomdesc', 'theme_adaptable');
        $choices = [
            'normal' => get_string('normal', 'theme_adaptable'),
            'wide' => get_string('wide', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'wide', $choices);
        $page->add($setting);

        $name = 'theme_adaptable/enablenavbarwhenloggedout';
        $title = get_string('enablenavbarwhenloggedout', 'theme_adaptable');
        $description = get_string('enablenavbarwhenloggedoutdesc', 'theme_adaptable');
        $default = false;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // Settings icon and Edit button.
        $name = 'theme_adaptable/editsettingsbutton';
        $title = get_string('editsettingsbutton', 'theme_adaptable');
        $description = get_string('editsettingsbuttondesc', 'theme_adaptable');
        $choices = [
            'cog' => get_string('editsettingsbuttonshowcog', 'theme_adaptable'),
            'button' => get_string('editsettingsbuttonshowbutton', 'theme_adaptable'),
            'cogandbutton' => get_string('editsettingsbuttonshowcogandbutton', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'cog', $choices);
        $page->add($setting);

        // Show the cog to non-editing teachers.
        $name = 'theme_adaptable/editcognocourseupdate';
        $title = get_string('editcognocourseupdate', 'theme_adaptable');
        $description = get_string('editcognocourseupdatedesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        $name = 'theme_adaptable/displayeditingbuttontext';
        $title = get_string('displayeditingbuttontext', 'theme_adaptable');
        $description = get_string('displayeditingbuttontextdesc', 'theme_adaptable');
        $default = true;
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
        $page->add($setting);

        // This course section.
        $page->add(new admin_setting_heading(
            'theme_adaptable_thiscourse_heading',
            get_string('headernavbarthiscourseheading', 'theme_adaptable'),
            format_text(get_string('headernavbarthiscourseheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Display participants.
        $name = 'theme_adaptable/displayparticipants';
        $title = get_string('displayparticipants', 'theme_adaptable');
        $description = get_string('displayparticipantsdesc', 'theme_adaptable');
        $radchoices = [
            0 => get_string('hide', 'theme_adaptable'),
            1 => get_string('show', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
        $page->add($setting);

        // Display Grades.
        $name = 'theme_adaptable/displaygrades';
        $title = get_string('displaygrades', 'theme_adaptable');
        $description = get_string('displaygradesdesc', 'theme_adaptable');
        $radchoices = [
            0 => get_string('hide', 'theme_adaptable'),
            1 => get_string('show', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 1, $radchoices);
        $page->add($setting);

        return $page;
    }

    /**
     * Navbar links settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function navbar_links_settings() {
        $page = new admin_settingpage(
            'theme_adaptable_navbar_links',
            get_string('navbarlinkssettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_navbar',
            get_string('navbarlinksettingsheading', 'theme_adaptable'),
            format_text(get_string('navbarlinksettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Help section.
        $page->add(new admin_setting_heading(
            'theme_adaptable_help_heading',
            get_string('headernavbarhelpheading', 'theme_adaptable'),
            format_text(get_string('headernavbarhelpheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/helptarget';
        $title = get_string('helptarget', 'theme_adaptable');
        $description = get_string('helptargetdesc', 'theme_adaptable');
        $choices = [
            '_blank' => get_string('targetnewwindow', 'theme_adaptable'),
            '_self' => get_string('targetsamewindow', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, '_blank', $choices);
        $page->add($setting);

        // Number of help links.
        $name = 'theme_adaptable/helplinkscount';
        $title = get_string('helplinkscount', 'theme_adaptable');
        $description = get_string('helplinkscountdesc', 'theme_adaptable');
        $default = 2;
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            $default,
            settings_toolbox::numbers(0, 12)
        );
        $page->add($setting);

        $helplinkscount = get_config('theme_adaptable', 'helplinkscount');
        if ($helplinkscount === false) {
            $helplinkscount = $default;
        }

        for ($helpcount = 1; $helpcount <= $helplinkscount; $helpcount++) {
            // Enable help link.
            $name = 'theme_adaptable/enablehelp' . $helpcount;
            $title = get_string('enablehelp', 'theme_adaptable', ['number' => $helpcount]);
            $description = get_string('enablehelpdesc', 'theme_adaptable', ['number' => $helpcount]);
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $page->add($setting);

            // Help link title.
            $name = 'theme_adaptable/helplinktitle' . $helpcount;
            $title = get_string('helplinktitle', 'theme_adaptable', ['number' => $helpcount]);
            $description = get_string('helplinktitledesc', 'theme_adaptable', ['number' => $helpcount]);
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
            $page->add($setting);

            $name = 'theme_adaptable/helpprofilefield' . $helpcount;
            $title = get_string('helpprofilefield', 'theme_adaptable', ['number' => $helpcount]);
            $description = get_string('helpprofilefielddesc', 'theme_adaptable', ['number' => $helpcount]);
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_RAW);
            $page->add($setting);
        }

        return $page;
    }

    /**
     * Navbar styles settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function navbar_styles_settings() {
        $page = new admin_settingpage('theme_adaptable_navbar_styles', get_string('navbarstyles', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_navbar_styles',
            get_string('navbarstylesheading', 'theme_adaptable'),
            format_text(get_string('navbarstylesdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        // Main menu background color.
        $name = 'theme_adaptable/menubkcolor';
        $title = get_string('menubkcolor', 'theme_adaptable');
        $description = get_string('menubkcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main menu text color.
        $name = 'theme_adaptable/menufontcolor';
        $title = get_string('menufontcolor', 'theme_adaptable');
        $description = get_string('menufontcolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#222222', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main menu background hover color.
        $name = 'theme_adaptable/menubkhovercolor';
        $title = get_string('menubkhovercolor', 'theme_adaptable');
        $description = get_string('menubkhovercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main menu text color.
        $name = 'theme_adaptable/menufonthovercolor';
        $title = get_string('menufonthovercolor', 'theme_adaptable');
        $description = get_string('menufonthovercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Main menu bottom border color.
        $name = 'theme_adaptable/menubordercolor';
        $title = get_string('menubordercolor', 'theme_adaptable');
        $description = get_string('menubordercolordesc', 'theme_adaptable');
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00B3A1', $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dropdown border radius.
        $name = 'theme_adaptable/navbardropdownborderradius';
        $title = get_string('navbardropdownborderradius', 'theme_adaptable');
        $description = get_string('navbardropdownborderradiusdesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            0,
            settings_toolbox::pixels(1, 20)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dropdown Menu Item Link background hover colour.
        $name = 'theme_adaptable/navbardropdownhovercolor';
        $title = get_string('navbardropdownhovercolor', 'theme_adaptable');
        $description = get_string('navbardropdownhovercolordesc', 'theme_adaptable');
        $default = '#EEE';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dropdown Menu Item Link text colour.
        $name = 'theme_adaptable/navbardropdowntextcolor';
        $title = get_string('navbardropdowntextcolor', 'theme_adaptable');
        $description = get_string('navbardropdowntextcolordesc', 'theme_adaptable');
        $default = '#007';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dropdown Menu Item Link text hover colour.
        $name = 'theme_adaptable/navbardropdowntexthovercolor';
        $title = get_string('navbardropdowntexthovercolor', 'theme_adaptable');
        $description = get_string('navbardropdowntexthovercolordesc', 'theme_adaptable');
        $default = '#000';
        $previewconfig = null;
        $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Dropdown transition time.
        $name = 'theme_adaptable/navbardropdowntransitiontime';
        $title = get_string('navbardropdowntransitiontime', 'theme_adaptable');
        $description = get_string('navbardropdowntransitiontimedesc', 'theme_adaptable');
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            '0.2s',
            settings_toolbox::seconds(0.0, 1.0, 0.1)
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Print settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function print_settings() {
        $page = new admin_settingpage('theme_adaptable_print', get_string('printsettings', 'theme_adaptable'));

        $page->add(new admin_setting_heading(
            'theme_adaptable_print',
            get_string('printsettingsheading', 'theme_adaptable'),
            format_text(get_string('printsettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        $name = 'theme_adaptable/printpageorientation';
        $title = get_string('printpageorientation', 'theme_adaptable');
        $description = get_string('printpageorientationdesc', 'theme_adaptable');
        $choices = [
        'landscape' => get_string('landscape', 'theme_adaptable'),
        'portrait' => get_string('portrait', 'theme_adaptable'),
        ];
        $setting = new admin_setting_configselect($name, $title, $description, 'landscape', $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/printbodyfontsize';
        $title = get_string('printbodyfontsize', 'theme_adaptable');
        $description = get_string('printbodyfontsizedesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '11pt', PARAM_TEXT);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/printmargin';
        $title = get_string('printmargin', 'theme_adaptable');
        $description = get_string('printmargindesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '2cm 1cm 2cm 2cm', PARAM_TEXT);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_adaptable/printlineheight';
        $title = get_string('printlineheight', 'theme_adaptable');
        $description = get_string('printlineheightdesc', 'theme_adaptable');
        $setting = new admin_setting_configtext($name, $title, $description, '1.2', PARAM_TEXT);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        return $page;
    }

    /**
     * Templates settings.
     *
     * @param $settings array The list of settings.
     *
     * @return admin_settingpage The setting page.
     */
    protected static function templates_settings(&$settings) {
        $page = new admin_settingpage(
            'theme_adaptable_templates',
            get_string('templatessettings', 'theme_adaptable')
        );

        $page->add(new admin_setting_heading(
            'theme_adaptable_templates_heading',
            get_string('templatesheading', 'theme_adaptable'),
            format_text(get_string('templatesheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
        ));

        static $templates = [
            'mod_forum/forum_post_email_htmlemail' => 'mod_forum/forum_post_email_htmlemail',
            'mod_forum/forum_post_email_htmlemail_body' => 'mod_forum/forum_post_email_htmlemail_body',
            'mod_forum/forum_post_email_textemail' => 'mod_forum/forum_post_email_textemail',
            'mod_forum/forum_post_emaildigestbasic_htmlemail' => 'mod_forum/forum_post_emaildigestbasic_htmlemail',
            'mod_forum/forum_post_emaildigestbasic_textemail' => 'mod_forum/forum_post_emaildigestbasic_textemail',
            'mod_forum/forum_post_emaildigestfull_htmlemail' => 'mod_forum/forum_post_emaildigestfull_htmlemail',
            'mod_forum/forum_post_emaildigestfull_textemail' => 'mod_forum/forum_post_emaildigestfull_textemail',
        ];
        $name = 'theme_adaptable/templatessel';
        $title = get_string('templatessel', 'theme_adaptable');
        $description = get_string('templatesseldesc', 'theme_adaptable');
        $default = [];
        $setting = new admin_setting_configmultiselect($name, $title, $description, $default, $templates);
        $page->add($setting);

        $overridetemplates = get_config('theme_adaptable', 'templatessel');
        if ($overridetemplates) {
            $overridetemplates = explode(',', $overridetemplates);
            foreach ($overridetemplates as $overridetemplate) {
                $overridetemplatesetting = str_replace('/', '_', $overridetemplate);
                $temppage = new admin_settingpage(
                    'theme_adaptable_templates_' . $overridetemplatesetting,
                    get_string('overridetemplate', 'theme_adaptable', $overridetemplate)
                );

                $name = 'theme_adaptable/activatetemplateoverride_' . $overridetemplatesetting;
                $title = get_string('activatetemplateoverride', 'theme_adaptable', $overridetemplate);
                $description = get_string(
                    'activatetemplateoverridedesc',
                    'theme_adaptable',
                    ['template' => $overridetemplate, 'setting' => $overridetemplatesetting]
                );
                $setting = new admin_setting_configcheckbox($name, $title, $description, false);
                $temppage->add($setting);

                $name = 'theme_adaptable/overriddentemplate_' . $overridetemplatesetting;
                $title = get_string('overriddentemplate', 'theme_adaptable', $overridetemplate);
                $description = get_string('overriddentemplatedesc', 'theme_adaptable', $overridetemplate);
                $default = '';
                $setting = new admin_setting_configtemplate(
                    $name,
                    $title,
                    $description,
                    $default,
                    $overridetemplate
                );
                $temppage->add($setting);

                $settings['templates_' . $overridetemplatesetting] = $temppage;
            }
        }

        return $page;
    }
}
