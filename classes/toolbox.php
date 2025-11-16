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
 *
 * File for toolbox class.
 *
 * @package    theme_adaptable
 * @copyright  2018 G J Barnard.
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

defined('MOODLE_INTERNAL') || die;

use core\url;
use core\output\html_writer;
use core\output\theme_config;
use stdClass;

global $CFG;
require_once($CFG->dirroot . '/theme/boost/lib.php');

/**
 * Class definition for toolbox.
 */
class toolbox {
    /**
     * @var toolbox Singleton instance of us.
     */
    protected static $instance = null;

    /**
     * @var themeconfigs Theme configurations.
     */
    protected static $themeconfigs = [];

    /**
     * @var toolbox Singleton instance of the local Adaptable toolbox.
     */
    private static $localinstance = null;

    /**
     * @var string Props.
     */
    public const PROPS = 'props';

    /**
     * @var string File Prop Names.
     */
    public const FILEPROPNAMES = 'filepropnames';

    /**
     * @var array File Property Names - using 'theme_adaptable_pluginfile' in lib.php as a reference.
     */
    private const FILEPROPERTYNAMES = ['logo', 'customjsfiles', 'favicon', 'homebk', 'frontpagerendererdefaultimage',
        'headerbgimage', 'loginbgimage', ];

    /**
     * Gets the toolbox singleton.
     *
     * @return toolbox The toolbox instance.
     */
    public static function get_instance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets the setting url for the given setting if it exists and set.
     *
     * See: https://moodle.org/mod/forum/discuss.php?d=371252#p1516474 and change if theme_config::setting_file_url
     * changes.
     * My need to do: $url = preg_replace('|^https?://|i', '//', $url->out(false)); separately.
     *
     * @param string $setting Setting
     * @param Obj $theconfig
     *
     * @return string Setting url
     */
    public static function get_setting_url($setting, $theconfig = null) {
        $settingurl = null;

        if (empty($theconfig)) {
            $theconfig = theme_config::load('adaptable');
        }
        if ($theconfig != null) {
            $thesetting = $theconfig->settings->$setting;
            if (!empty($thesetting)) {
                global $CFG;
                $itemid = \theme_get_revision();
                $syscontext = \context_system::instance();

                $settingurl = url::make_file_url(
                    "$CFG->wwwroot/pluginfile.php",
                    "/$syscontext->id/theme_$theconfig->name/$setting/$itemid" . $thesetting
                );
            }
        }
        return $settingurl;
    }

    /**
     * Finds the given setting in the theme using the get_config core function for when the
     * theme_config object has not been created.
     *
     * @param string $settingname Setting name.
     * @param string $themename null(default of 'adaptable' used)|theme name.
     * @param string $settingdefault null|supplied default.
     *
     * @return any null|value of setting.
     */
    public static function get_config_setting($settingname, $themename = null, $settingdefault = null) {
        if (empty($themename)) {
            $themename = 'adaptable';
        }
        $settingvalue = get_config('theme_' . $themename, $settingname);
        return (!empty($settingvalue)) ? $settingvalue : $settingdefault;
    }

    /**
     * Finds the given setting in the theme.
     *
     * @param string $settingname Setting name.
     * @param string $format format_text format or false.
     * @param string $themename null(default of 'adaptable' used)|theme name.
     * @param string $settingdefault null|supplied default.
     *
     * @return any null|value of setting.
     */
    public static function get_setting($settingname, $format = false, $themename = null, $settingdefault = null) {

        if (empty($themename)) {
            $themename = 'adaptable';
        }
        if (empty(self::$themeconfigs[$themename])) {
            self::$themeconfigs[$themename] = theme_config::load($themename);
        }

        $setting = (!empty(self::$themeconfigs[$themename]->settings->$settingname)) ?
            self::$themeconfigs[$themename]->settings->$settingname : $settingdefault;

        if (!$format) {
            return $setting;
        } else if ($format === 'format_text') {
            return format_text($setting, FORMAT_PLAIN);
        } else if ($format === 'format_moodle') {
            return format_text($setting, FORMAT_MOODLE);
        } else if ($format === 'format_html') {
            return format_text($setting, FORMAT_HTML);
        } else {
            return format_string($setting);
        }
    }

    /**
     * Returns all of the settings for the given theme.
     *
     * @param string $themename null(default of 'adaptable' used)|theme name.
     *
     * @return any null|settings stdClass.
     */
    public static function get_settings($themename = null) {
        return self::get_theme($themename)->settings;
    }

    /**
     * Config text constructor
     *
     * @param string $name Setting name.
     * @param string $themename Frankenstyle.
     * @param mixed $value Setting value.
     * @param mixed $paramtype int Means PARAM_XXX type, PARAM_RAW and regex NOT supported!
     * @param bool $throwexception Throw a moodle_exception with the error message generated.
     * @return mixed true or error message string.
     */
    public static function validate_setting($name, $themename, $value, $paramtype, $throwexception = true) {
        $validated = true;

        $cleaned = clean_param($value, $paramtype);
        if ("$value" !== "$cleaned") {
            $errordata = ['name' => $themename . '|' . $name, 'value' => $value];
            if ($throwexception) {
                throw new \core\exception\moodle_exception(
                    'invalidsettingvalue',
                    'theme_adaptable',
                    '',
                    $errordata
                );
            } else {
                $validated = get_string('invalidsettingvalue', 'theme_adaptable', $errordata);
            }
        }

        return $validated;
    }

    /**
     * Returns the given theme.
     *
     * @param string $themename null(default of 'adaptable' used)|theme name.
     *
     * @return any null|theme_config class.
     */
    public static function get_theme($themename = null) {

        if (empty($themename)) {
            $themename = 'adaptable';
        }
        if (empty(self::$themeconfigs[$themename])) {
            self::$themeconfigs[$themename] = theme_config::load($themename);
        }

        return self::$themeconfigs[$themename];
    }

    /**
     * Gets the Local Adaptable toolbox singleton.
     *
     * @return toolbox The toolbox instance or false if the local plugin does not exist.
     */
    public static function get_local_toolbox() {
        if (is_null(self::$localinstance)) {
            if (class_exists('\\local_adaptable\\toolbox')) {
                self::$localinstance = \local_adaptable\toolbox::get_instance();
            } else {
                self::$localinstance = false; // We have tried to get it and it's not there!
            }
        }
        return self::$localinstance;
    }

    /**
     * Gets the pre SCSS for the theme.
     *
     * @param theme_config $theme The theme configuration object.
     * @return string SCSS.
     */
    public static function pre_scss($theme) {
        $regionmaincolour = self::get_setting('regionmaincolour', false, $theme->name, '#ffffff');
        $fontcolour = self::get_setting('fontcolour', false, $theme->name, '#333333');
        $fontcolorrgba = self::hex2rgba($fontcolour, 0.25);
        $prescss = '$body-bg: ' . $regionmaincolour . ';' . PHP_EOL;
        $prescss = '$body-color: ' . $fontcolour . ';' . PHP_EOL;
        $prescss .= '$primary: ' .
            self::get_setting('primarycolour', false, $theme->name, '#00796b') . ';' . PHP_EOL;
        $prescss .= '$secondary: ' .
            self::get_setting('secondarycolour', false, $theme->name, '#009688') . ';' . PHP_EOL;
        $prescss .= '$ad-secondary-text: ' .
            self::get_setting('secondarycolourtext', false, $theme->name, '#fafafa') . ';' . PHP_EOL;
        $prescss .= '$loadingcolor: ' .
            self::get_setting('loadingcolor', false, $theme->name, '#00B3A1') . ';' . PHP_EOL;
        $loadingcolor = self::get_setting('loadingcolor', false, $theme->name, '#00B3A1');
        $loadingcolorrgba = self::hex2rgba($loadingcolor, 0.2);
        $prescss .= '$loadingcolor: ' . $loadingcolor . ';' . PHP_EOL;
        $prescss .= '$loadingcolorrgba: ' . $loadingcolorrgba . ';' . PHP_EOL;
        $prescss .= '$nav-tabs-border-color: $secondary;' . PHP_EOL;
        $prescss .= '$dialogue-base-bg: ' . $regionmaincolour . ';' . PHP_EOL;
        $prescss .= '$nav-tabs-link-active-border-color: ' . $fontcolorrgba . ' ' . $fontcolorrgba . ' transparent;' . PHP_EOL;
        $prescss .= '$nav-tabs-link-hover-border-color: transparent transparent ' . $fontcolour . ';' . PHP_EOL;
        $prescss .= '$courseindex-link-color: ' .
            self::get_setting('courseindexitemcolor', false, $theme->name, '#495057') . ';' . PHP_EOL;
        $prescss .= '$courseindex-link-hover-color: ' .
            self::get_setting('courseindexitemhovercolor', false, $theme->name, '#e6e6e6') . ';' . PHP_EOL;
        $prescss .= '$courseindex-link-color-selected: ' .
            self::get_setting('courseindexpageitemcolor', false, $theme->name, '#ffffff') . ';' . PHP_EOL;
        $prescss .= '$courseindex-item-page-bg: ' .
            self::get_setting('courseindexpageitembgcolor', false, $theme->name, '#0f6cbf') . ';' . PHP_EOL;
        $prescss .= '$drawer-bg-color: #fff;';  // Currently no setting for 'block region' background.
        $prescss .= '$input-btn-focus-color: rgba(' .
            self::get_setting('inputbuttonfocuscolour', false, $theme->name, '#0f6cc0') . ', ' .
            self::get_setting('inputbuttonfocuscolouropacity', false, $theme->name, '0.75') . ');' . PHP_EOL;
        $prescss .= '$drawer-right-width: ' .
            self::get_setting('sidepostdrawerwidth', false, $theme->name, '315px') . ';' . PHP_EOL;

        // Adaptable specific settings.
        $prescss .= '$ad-main-colour: ' .
            self::get_setting('maincolour', false, $theme->name, '#fff') . ';' . PHP_EOL;
        $prescss .= '$ad-regionmain-colour: ' . $regionmaincolour . ';' . PHP_EOL;
        $prescss .= '$ad-regionmaintext-colour: ' .
            self::get_setting('regionmaintextcolour', false, $theme->name, '#000') . ';' . PHP_EOL;
        $prescss .= '$ad-font-colour: ' . $fontcolour . ';' . PHP_EOL;
        $prescss .= '$ad-link-colour: ' .
            self::get_setting('linkcolour', false, $theme->name, '#51666C') . ';' . PHP_EOL;
        $prescss .= '$ad-linkhover-colour: ' .
            self::get_setting('linkhover', false, $theme->name, '#009688') . ';' . PHP_EOL;
        $prescss .= '$ad-dimmedtext-colour: ' .
            self::get_setting('dimmedtextcolour', false, $theme->name, '#6A737B') . ';' . PHP_EOL;
        $prescss .= '$ad-selectiontext-colour: ' .
            self::get_setting('selectiontext', false, $theme->name, '#000') . ';' . PHP_EOL;
        $prescss .= '$ad-selectionbackground-colour: ' .
            self::get_setting('selectionbackground', false, $theme->name, '#00B3A1') . ';' . PHP_EOL;

        // Font sizes.
        $fontsize = self::get_setting('fontsize', false, $theme->name, '95%');
        $fontsize = admin_setting_font::validate_and_convert($fontsize);
        if (!empty($fontsize)) { // Unlikely that an invalid value is stored, otherwise don't use!
            if (is_numeric($fontsize)) {
                // Stored as a multiplier, so just add 'rem'.
                $fontsize = $fontsize . 'rem';
            }
            $prescss .= '$font-size-base: ' . $fontsize . ';' . PHP_EOL;
        }

        $prescss .= self::process_header_font_size(1, 'fontheaderlevel1', $theme->name, '2.5');
        $prescss .= self::process_header_font_size(2, 'fontheaderlevel2', $theme->name, '2');
        $prescss .= self::process_header_font_size(3, 'fontheaderlevel3', $theme->name, '1.75');
        $prescss .= self::process_header_font_size(4, 'fontheaderlevel4', $theme->name, '1.5');
        $prescss .= self::process_header_font_size(5, 'fontheaderlevel5', $theme->name, '1.25');
        $prescss .= self::process_header_font_size(6, 'fontheaderlevel6', $theme->name, '1');

        return $prescss;
    }

    /**
     * Process a header setting.
     *
     * @param int $level Level number.
     * @param string $settingname Setting name.
     * @param string $themename Theme name.
     * @param string $default Default.
     *
     * @return string SCSS.
     */
    private static function process_header_font_size($level, $settingname, $themename, $default) {
        $scss = '';

        $setting = self::get_setting($settingname, false, $themename, $default);
        $setting = admin_setting_font::validate_and_convert($setting);
        if (!empty($setting)) { // Unlikely that an invalid value is stored, otherwise don't use!
            if (is_numeric($setting)) {
                // Stored as a multiplier.
                $scss = '$h' . $level . '-font-size: $font-size-base * ' . $setting . ';' . PHP_EOL;
            } else {
                // Stored as a size.
                $scss = '$h' . $level . '-font-size: ' . $setting . ';' . PHP_EOL;
            }
        }

        return $scss;
    }

    /**
     * Returns the main SCSS content.
     *
     * @param theme_config $theme The theme config object.
     * @return string SCSS.
     */
    public static function get_main_scss_content($theme) {
        global $CFG;

        static $boosttheme = null;
        if (empty($boosttheme)) {
            $boosttheme = theme_config::load('boost'); // Needs to be the Boost theme so that we get its settings.
        }

        $scss = '$enable-rounded: false !default;'; // Todo: A setting?

        $fav = (!empty(self::get_setting('fav')));
        if ($fav) {
            $scss .= '// Import Theme FontAwesome part one.' . PHP_EOL;
            $scss .= '@import "fontawesome/functions";' . PHP_EOL;
            $scss .= '@import "fontawesome/variables";' . PHP_EOL;
        }

        $scss .= theme_boost_get_main_scss_content($boosttheme);

        // Our replacement mixins and fixes both versions of Font Awesome with us.
        $scss .= '@import "fontawesome/mixins";' . PHP_EOL;
        $scss .= '@import "fontawesome/fixes";' . PHP_EOL;

        $basedir = ((!empty($CFG->themedir)) && (is_dir($CFG->themedir . '/adaptable')))
            ? $CFG->themedir : $CFG->dirroot . '/theme';
        $basedir .= '/adaptable';

        if ($fav) {
            // Stop core from being imported, but we've imported our functions / variables for the Moodle scss to use.
            $scss .= '// Remove Core FontAwesome.' . PHP_EOL;
            $scss = str_replace('@import "fontawesome";', '//@import "fontawesome";', $scss);
            $scss .= '// Import Theme FontAwesome part two.' . PHP_EOL;
            $scss .= file_get_contents($basedir . '/scss/fontawesome/fontawesome.css');
            $scss .= file_get_contents($basedir . '/scss/fontawesome/brands.css');
            $scss .= file_get_contents($basedir . '/scss/fontawesome/regular.css');
            $scss .= file_get_contents($basedir . '/scss/fontawesome/solid.css');
            if (!empty(self::get_setting('faiv'))) {
                $scss .= file_get_contents($basedir . '/scss/fontawesome/v4-font-face.css');
                $scss .= file_get_contents($basedir . '/scss/fontawesome/v4-shims.css');
            }
        }

        $scss .= file_get_contents($basedir . '/scss/main.scss');

        static $settingssheets = [
            'adaptable',
            'admin',
            'alerts',
            'blocks',
            'button',
            'core',
            'course',
            'extras',
            'footer',
            'form',
            'grade',
            'header',
            'login',
            'menu',
            'misc',
            'modal',
            'navigation',
            'news-ticker',
            'notifications',
            'quiz',
            'responsive',
            'search-social',
            'secondarynavigation',
            'tabs',
            'print',
            'categorycustom',
        ];

        $settingsscss = '';
        foreach ($settingssheets as $settingsheet) {
            $settingsscss .= file_get_contents($basedir . '/scss/settings/' . $settingsheet . '.scss');
        }

        $scss .= self::process_scss($settingsscss, $theme);

        return $scss;
    }

    /**
     * Parses SCSS before it is parsed by the SCSS compiler.
     *
     * This function can make alterations and replace patterns within the SCSS.
     *
     * @param string $scss The SCSS.
     * @param theme_config $theme The theme config object.
     * @return string The parsed SCSS.
     */
    private static function process_scss($scss, $theme) {

        // Set category custom CSS.
        $localtoolbox = self::get_local_toolbox();
        if (is_object($localtoolbox)) {
            $scss = $localtoolbox->set_categorycustomcss($scss, $theme->settings);
        }

        // Collapsed Topics colours.
        if (empty($theme->settings->collapsedtopicscoloursenabled)) {
            $scss .= '.theme_adaptable .course-content ul.ctopics li.section {' . PHP_EOL;
            $scss .= '.content .toggle span.the_toggle h3.sectionname,' . PHP_EOL;
            $scss .= '.content .toggle span.the_toggle h3.sectionname a,' . PHP_EOL;
            $scss .= '.content .toggle span.the_toggle h3.sectionname a:hover,' . PHP_EOL;
            $scss .= '.content .toggle span.the_toggle h3.sectionname a:focus,' . PHP_EOL;
            $scss .= '.content.sectionhidden h3.sectionname' . PHP_EOL;
            $scss .= '.content.sectionhidden h3.sectionname a,' . PHP_EOL;
            $scss .= '.content.sectionhidden h3.sectionname a:hover,' . PHP_EOL;
            $scss .= '.content.sectionhidden h3.sectionname a:focus {' . PHP_EOL;
            $scss .= '    color: [[setting:sectionheadingcolor]];' . PHP_EOL;
            $scss .= '}' . PHP_EOL;
            $scss .= '}' . PHP_EOL;

            $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content {' . PHP_EOL;
            $scss .= 'div.toggle,' . PHP_EOL;
            $scss .= 'div.toggle:hover,' . PHP_EOL;
            $scss .= 'div.toggle:focus {' . PHP_EOL;
            $scss .= '    background-color: [[setting:coursesectionheaderbg]];' . PHP_EOL;
            $scss .= '}' . PHP_EOL;
            $scss .= '}' . PHP_EOL;

            $scss .= '.theme_adaptable .course-content ul.ctopics li.section {' . PHP_EOL;
            $scss .= '.content .toggle span,' . PHP_EOL;
            $scss .= '.content .toggle span:hover,' . PHP_EOL;
            $scss .= '.content .toggle span:focus,' . PHP_EOL;
            $scss .= '.content.sectionhidden,' . PHP_EOL;
            $scss .= '.content.sectionhidden:hover,' . PHP_EOL;
            $scss .= '.content.sectionhidden:focus {' . PHP_EOL;
            $scss .= '    color: inherit;' . PHP_EOL;
            $scss .= '}' . PHP_EOL;
            $scss .= '}' . PHP_EOL;
        }

        // Define the default settings for the theme in case they've not been set.
        static $defaults = [
            '[[setting:primarycolour]]' => '#00796b',
            '[[setting:secondarycolour]]' => '#009688',
            '[[setting:rendereroverlaycolor]]' => '#3A454b',
            '[[setting:rendereroverlayfontcolor]]' => '#FFFFFF',
            '[[setting:buttoncolor]]' => '#51666C',
            '[[setting:buttontextcolor]]' => '#ffffff',
            '[[setting:buttonhovercolor]]' => '#009688',
            '[[setting:buttontexthovercolor]]' => '#eeeeee',
            '[[setting:buttonfocuscolour]]' => '#0f6cc0',
            '[[setting:buttontextfocuscolour]]' => '#eeeeee',
            '[[setting:buttoncolorscnd]]' => '#51666C',
            '[[setting:buttontextcolorscnd]]' => '#ffffff',
            '[[setting:buttonhovercolorscnd]]' => '#009688',
            '[[setting:buttoncolorcancel]]' => '#c64543',
            '[[setting:buttontextcolorcancel]]' => '#ffffff',
            '[[setting:buttonhovercolorcancel]]' => '#e53935',
            '[[setting:buttonlogincolor]]' => '#c64543',
            '[[setting:buttonloginhovercolor]]' => '#e53935',
            '[[setting:buttonlogintextcolor]]' => '#0084c2',
            '[[setting:buttonloginpadding]]' => '0',
            '[[setting:buttonloginheight]]' => '24px',
            '[[setting:buttonloginmargintop]]' => '2px',
            '[[setting:buttonradius]]' => '5px',
            '[[setting:buttondropshadow]]' => '0',
            '[[setting:headertoprowdividingline]]' => '#ffffff',
            '[[setting:footerdividingline]]' => '#ffffff',
            '[[setting:breadcrumb]]' => '#b4bbbf',
            '[[setting:breadcrumbtextcolor]]' => '#444444',
            '[[setting:breadcrumbseparator]]' => 'angle-right',
            '[[setting:loadingcolor]]' => '#00B3A1',
            '[[setting:messagepopupbackground]]' => '#fff000',
            '[[setting:messagepopupcolor]]' => '#333333',
            '[[setting:messagingbackgroundcolor]]' => '#FFFFFF',
            '[[setting:footerbkcolor]]' => '#424242',
            '[[setting:footertextcolor]]' => '#ffffff',
            '[[setting:footertextcolor2]]' => '#ffffff',
            '[[setting:footerlinkcolor]]' => '#ffffff',
            '[[setting:headertoprowbkcolour]]' => '#00796B',
            '[[setting:headermainrowbkcolour]]' => '#009688',
            '[[setting:headerbgimagetextcolour]]' => '#ffffff',
            '[[setting:headertoprowtextcolour]]' => '#ffffff',
            '[[setting:headermainrowtextcolour]]' => '#ffffff',
            '[[setting:notbadgecolour]]' => '#ffffff',
            '[[setting:notbadgebackgroundcolour]]' => '#e53935',
            '[[setting:blockbackgroundcolor]]' => '#FFFFFF',
            '[[setting:blockheaderbackgroundcolor]]' => '#FFFFFF',
            '[[setting:blockbordercolor]]' => '#59585D',
            '[[setting:blockregionbackgroundcolor]]' => 'transparent',
            '[[setting:marketblockbordercolor]]' => '#e8eaeb',
            '[[setting:marketblocksbackgroundcolor]]' => 'transparent',
            '[[setting:blockheaderbordertop]]' => '1px',
            '[[setting:blockheaderborderleft]]' => '0',
            '[[setting:blockheaderborderright]]' => '0',
            '[[setting:blockheaderborderbottom]]' => '0',
            '[[setting:blockmainbordertop]]' => '0',
            '[[setting:blockmainborderleft]]' => '0',
            '[[setting:blockmainborderright]]' => '0',
            '[[setting:blockmainborderbottom]]' => '0',
            '[[setting:blockheaderbordertopstyle]]' => 'dashed',
            '[[setting:blockmainbordertopstyle]]' => 'solid',
            '[[setting:blockheadertopradius]]' => '0',
            '[[setting:blockheaderbottomradius]]' => '0',
            '[[setting:blockmaintopradius]]' => '0',
            '[[setting:blockmainbottomradius]]' => '0',
            '[[setting:coursesectionbgcolor]]' => '#FFFFFF',
            '[[setting:coursesectionheaderbg]]' => '#FFFFFF',
            '[[setting:coursesectionheaderbordercolor]]' => '#F3F3F3',
            '[[setting:coursesectionheaderborderstyle]]' => 'none',
            '[[setting:coursesectionheaderborderwidth]]' => '0px',
            '[[setting:coursesectionheaderborderradiustop]]' => '0px',
            '[[setting:coursesectionheaderborderradiusbottom]]' => '0px',
            '[[setting:coursesectionborderstyle]]' => '1px',
            '[[setting:coursesectionborderwidth]]' => '1px',
            '[[setting:coursesectionbordercolor]]' => '#e8eaeb',
            '[[setting:coursesectionborderradius]]' => '0px',
            '[[setting:coursesectionactivityiconsize]]' => '24px',
            '[[setting:coursesectionactivityheadingcolour]]' => '#0066cc',
            '[[setting:coursesectionactivityborderwidth]]' => '2px',
            '[[setting:coursesectionactivityborderstyle]]' => 'dashed',
            '[[setting:coursesectionactivitybordercolor]]' => '#eeeeee',
            '[[setting:coursesectionactivityleftborderwidth]]' => '3px',
            '[[setting:coursesectionactivityassignleftbordercolor]]' => '#0066cc',
            '[[setting:coursesectionactivityassignbgcolor]]' => '#FFFFFF',
            '[[setting:coursesectionactivityforumleftbordercolor]]' => '#990099',
            '[[setting:coursesectionactivityforumbgcolor]]' => '#FFFFFF',
            '[[setting:coursesectionactivityquizleftbordercolor]]' => '#FF3333',
            '[[setting:coursesectionactivityquizbgcolor]]' => '#FFFFFF',
            '[[setting:coursesectionactivitymargintop]]' => '2px',
            '[[setting:coursesectionactivitymarginbottom]]' => '2px',
            '[[setting:tilesbordercolor]]' => '#3A454b',
            '[[setting:slidermargintop]]' => '20px',
            '[[setting:slidermarginbottom]]' => '20px',
            '[[setting:currentcolor]]' => '#d9edf7',
            '[[setting:sectionheadingcolor]]' => '#3A454b',
            '[[setting:menufontsize]]' => '14px',
            '[[setting:menufontpadding]]' => '20px',
            '[[setting:menubkcolor]]' => '#ffffff',
            '[[setting:menufontcolor]]' => '#444444',
            '[[setting:menubkhovercolor]]' => '#00B3A1',
            '[[setting:menufonthovercolor]]' => '#ffffff',
            '[[setting:menubordercolor]]' => '#00B3A1',
            '[[setting:mobilemenubkcolor]]' => '#F9F9F9',
            '[[setting:navbardropdownborderradius]]' => '0',
            '[[setting:navbardropdownhovercolor]]' => '#EEE',
            '[[setting:navbardropdowntextcolor]]' => '#007',
            '[[setting:navbardropdowntexthovercolor]]' => '#000',
            '[[setting:navbardropdowntransitiontime]]' => '0.0s',
            '[[setting:covbkcolor]]' => '#3A454b',
            '[[setting:covfontcolor]]' => '#ffffff',
            '[[setting:editonbk]]' => '#4caf50',
            '[[setting:editoffbk]]' => '#f44336',
            '[[setting:edithorizontalpadding]]' => '4px',
            '[[setting:editfont]]' => '#ffffff',
            '[[setting:sliderh3color]]' => '#ffffff',
            '[[setting:sliderh4color]]' => '#ffffff',
            '[[setting:slidersubmitbgcolor]]' => '#51666C',
            '[[setting:slidersubmitcolor]]' => '#ffffff',
            '[[setting:slider2h3color]]' => '#000000',
            '[[setting:slider2h4color]]' => '#000000',
            '[[setting:slider2h3bgcolor]]' => '#000000',
            '[[setting:slider2h4bgcolor]]' => '#ffffff',
            '[[setting:slideroption2color]]' => '#51666C',
            '[[setting:slideroption2submitcolor]]' => '#ffffff',
            '[[setting:slideroption2a]]' => '#51666C',
            '[[setting:socialsize]]' => '37px',
            '[[setting:mobile]]' => '22',
            '[[setting:socialpaddingside]]' => 16,
            '[[setting:socialpaddingtop]]' => '0%',
            '[[setting:fontheadercolor]]' => '#333333',
            '[[setting:fontweight]]' => '400',
            '[[setting:fontheaderweight]]' => '400',
            '[[setting:fonttitleweight]]' => '400',
            '[[setting:fonttitlesize]]' => '48px',
            '[[setting:fonttitlecolor]]' => '#ffffff',
            '[[setting:searchboxpadding]]' => '0 0 0 0',
            '[[setting:enablesavecanceloverlay]]' => true,
            '[[setting:headermainrowminheight]]' => '72px',
            '[[setting:emoticonsize]]' => '16px',
            '[[setting:fullscreenwidth]]' => '98%',
            '[[setting:coursetitlemaxwidth]]' => '20',
            '[[setting:responsiveheader]]' => 'd-none d-lg-flex',
            '[[setting:responsivesocial]]' => 'd-none d-lg-block',
            '[[setting:responsivesocialsize]]' => '34px',
            '[[setting:responsivelogo]]' => 'd-none d-lg-inline-block',
            '[[setting:responsivesectionnav]]' => '1',
            '[[setting:responsiveticker]]' => 'd-none d-lg-block',
            '[[setting:responsivebreadcrumb]]' => 'd-none d-md-flex',
            '[[setting:responsiveslider]]' => 'd-none d-lg-block',
            '[[setting:responsivepagefooter]]' => 'd-none d-lg-block',
            '[[setting:hidefootersocial]]' => 1,
            '[[setting:enableavailablecourses]]' => 'inherit',
            '[[setting:enableticker]]' => true,
            '[[setting:enabletickermy]]' => true,
            '[[setting:tickerheaderbackgroundcolour]]' => '#00796b',
            '[[setting:tickerheadertextcolour]]' => '#eee',
            '[[setting:tickerconstainerbackgroundcolour]]' => '#009688',
            '[[setting:tickerconstainertextcolour]]' => '#eee',
            '[[setting:onetopicactivetabbackgroundcolor]]' => '#d9edf7',
            '[[setting:onetopicactivetabtextcolor]]' => '#000000',
            '[[setting:fontblockheaderweight]]' => '400',
            '[[setting:fontblockheadersize]]' => '22px',
            '[[setting:fontblockheadercolor]]' => '#3A454b',
            '[[setting:blockiconsheadersize]]' => '20px',
            '[[setting:alertcolorinfo]]' => '#3a87ad',
            '[[setting:alertbackgroundcolorinfo]]' => '#d9edf7',
            '[[setting:alertbordercolorinfo]]' => '#bce8f1',
            '[[setting:alertcolorsuccess]]' => '#468847',
            '[[setting:alertbackgroundcolorsuccess]]' => '#dff0d8',
            '[[setting:alertbordercolorsuccess]]' => '#d6e9c6',
            '[[setting:alertcolorwarning]]' => '#8a6d3b',
            '[[setting:alertbackgroundcolorwarning]]' => '#fcf8e3',
            '[[setting:alertbordercolorwarning]]' => '#fbeed5',
            '[[setting:forumheaderbackgroundcolor]]' => '#ffffff',
            '[[setting:forumbodybackgroundcolor]]' => '#ffffff',
            '[[setting:introboxbackgroundcolor]]' => '#ffffff',
            '[[setting:tabbedlayoutdashboardcolorselected]]' => '#06c',
            '[[setting:tabbedlayoutdashboardcolorunselected]]' => '#eee',
            '[[setting:tabbedlayoutcoursepagetabcolorselected]]' => '#06c',
            '[[setting:tabbedlayoutcoursepagetabcolorunselected]]' => '#eee',
            '[[setting:frontpagenumbertiles]]' => '4',
            '[[setting:gdprbutton]]' => 1,
            '[[setting:infoiconcolor]]' => '#5bc0de',
            '[[setting:dangericoncolor]]' => '#d9534f',
            '[[setting:loginheader]]' => 0,
            '[[setting:loginfooter]]' => 0,
            '[[setting:printpageorientation]]' => 'landscape',
            '[[setting:printbodyfontsize]]' => '11pt',
            '[[setting:printmargin]]' => '2cm 1cm 2cm 2cm',
            '[[setting:printlineheight]]' => '1.2',
        ];

        // Get all the defined settings for the theme and replace defaults.
        foreach ($theme->settings as $key => $val) {
            if (((!empty($val)) || (strlen($val) > 0)) && (array_key_exists('[[setting:' . $key . ']]', $defaults))) {
                $defaults['[[setting:' . $key . ']]'] = $val;
            }
        }

        // Font name defaults.  This allows you to change the 'default' here if you wish.
        $fontdefaults = [
            '[[setting:fontname]]' => 'sans-serif',
            '[[setting:fontheadername]]' => 'sans-serif',
            '[[setting:fonttitlename]]' => 'sans-serif',
        ];
        $fontsettingnames = [
            'fontname',
            'fontheadername',
            'fonttitlename',
        ];
        foreach ($fontsettingnames as $fontsettingname) {
            if ((!empty($theme->settings->$fontsettingname)) && ($theme->settings->$fontsettingname != 'default')) {
                $fontdefaults['[[setting:' . $fontsettingname . ']]'] = $theme->settings->$fontsettingname;
            }
        }
        // Replace the CSS with values from the $fontdefaults array.
        $scss = strtr($scss, $fontdefaults);

        $homebkg = '';
        if (!empty($theme->settings->homebk)) {
            $homebkg = $theme->setting_file_url('homebk', 'homebk');
            $homebkg = 'background-image: url("' . $homebkg . '");';
        }
        $defaults['[[setting:homebkg]]'] = $homebkg;

        if (is_object($localtoolbox)) {
            // DEPRECATED.  Here to allow initial update of local_adaptable.
            $theme->settings->headerbkcolor2 = $theme->settings->headermainrowbkcolour;
            $retr = $localtoolbox->login_style($theme);
            $defaults['[[setting:loginbgimage]]'] = $retr->loginbgimage;
            $defaults['[[setting:loginbgstyle]]'] = $retr->loginbgstyle;
            $defaults['[[setting:loginbgopacity]]'] = $retr->loginbgopacity;
        } else {
            $defaults['[[setting:loginbgimage]]'] = '';
            $defaults['[[setting:loginbgstyle]]'] = '';
            $defaults['[[setting:loginbgopacity]]'] = '';
        }

        $socialpaddingsidehalf = '16';
        if (!empty($theme->settings->socialpaddingside)) {
            $socialpaddingsidehalf = '' . $theme->settings->socialpaddingside / 2;
        }
        $defaults['[[setting:socialpaddingsidehalf]]'] = $socialpaddingsidehalf;

        // Add in rgba colours.
        $defaults['[[setting:fontcolorrgba]]'] = self::hex2rgba('#333333', 0.25);
        $defaults['[[setting:regionmaincolorrgba]]'] = self::hex2rgba('#ffffff', 0.75);
        $defaults['[[setting:linkcolorrgba]]'] = self::hex2rgba('#51666C', 0.75);
        $defaults['[[setting:linkhoverrgba]]'] = self::hex2rgba('#009688', 0.75);

        // The navbardisplaymenuarrow setting.
        $defaults['[[setting:navbardisplaymenuarrow]]'] = (empty($theme->settings->navbardisplaymenuarrow)) ? 'content: none;' : '';

        // Replace the CSS with values from the $defaults array.
        $scss = strtr($scss, $defaults);
        if (empty($theme->settings->tilesshowallcontacts) || $theme->settings->tilesshowallcontacts == false) {
            $scss = self::set_tilesshowallcontacts($scss, false);
        } else {
            $scss = self::set_tilesshowallcontacts($scss, true);
        }

        return $scss;
    }

    /**
     * Set display of course contacts on front page tiles
     * @param string $css
     * @param string $display
     * @return $string
     */
    private static function set_tilesshowallcontacts($css, $display) {
        $tag = '[[setting:tilesshowallcontacts]]';
        if ($display) {
            $replacement = 'block';
        } else {
            $replacement = 'none';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    /**
     * Returns version file number(s) for the theme and local plugin.
     *
     * @return array Theme and local versions.
     */
    public static function get_file_versions() {
        $theme = \core_component::get_all_plugins_list('theme');
        $local = \core_component::get_all_plugins_list('local');

        $themeversion = null;
        $localversion = null;

        if (!empty($theme['adaptable'])) {
            $themeversion = self::get_version_file_info($theme['adaptable']);
        }
        if (!empty($local['adaptable'])) {
            $localversion = self::get_version_file_info($local['adaptable']);
        }

        return ['theme' => $themeversion, 'local' => $localversion];
    }

    /**
     * Returns version and release in the version.php file at the supplied storage location.
     *
     * @param $fullplug Plugin location on storage.
     * @return array Version and release or null if not there.
     */
    private static function get_version_file_info($fullplug) {
        $plugin = new stdClass();
        $plugin->release = null;
        $plugin->version = null;
        include($fullplug . '/version.php');

        return ['version' => $plugin->version, 'release' => $plugin->release];
    }

    /**
     * Get the user preference for the zoom (show / hide block) function.
     *
     * @return string User preference.
     */
    public static function get_zoom() {
        return get_user_preferences('theme_adaptable_zoom', '');
    }

    /**
     * Set user preferences for zoom (show / hide block) function.
     */
    public static function initialise_zoom() {
        global $USER;
        $USER->adaptable_user_pref['theme_adaptable_zoom'] = PARAM_TEXT;
    }

    /**
     * Set the user preference for full screen
     */
    public static function initialise_full() {
        if (self::get_setting('enablezoom')) {
            global $USER;
            $USER->adaptable_user_pref['theme_adaptable_full'] = PARAM_TEXT;
        }
    }

    /**
     * Get the user preference for the zoom function.
     */
    public static function get_full() {
        $fullpref = '';
        if ((isloggedin()) && (self::get_setting('enablezoom'))) {
            $fullpref = get_user_preferences('theme_adaptable_full', '');
        }

        if (empty($fullpref)) { // Zoom disabled, not logged in or user not chosen preference.
            $defaultzoom = self::get_setting('defaultzoom');
            if (empty($defaultzoom)) {
                $defaultzoom = 'normal';
            }
            if ($defaultzoom == 'normal') {
                $fullpref = 'nofull';
            } else {
                $fullpref = 'fullin';
            }
        }

        return $fullpref;
    }

    /**
     * Get top level categories.
     *
     * @return array category ids
     */
    public static function get_top_level_categories() {
        $categoryids = [];
        $categories = \core_course_category::get(0)->get_children(); // Parent = 0 i.e. top-level categories only.

        foreach ($categories as $category) {
            $categoryids[$category->id] = $category->name;
        }

        return $categoryids;
    }

    /**
     * Get top level categories with sub-categories.
     *
     * @return array category list
     */
    public static function get_top_categories_with_children() {
        static $catlist = null;
        static $dbcatlist = null;

        if (empty($catlist)) {
            global $DB;
            $dbcatlist = $DB->get_records('course_categories', null, 'sortorder', 'id, name, depth, path');
            $catlist = [];

            foreach ($dbcatlist as $category) {
                if ($category->depth > 1) {
                    $path = preg_split('|/|', $category->path, -1, PREG_SPLIT_NO_EMPTY);
                    $top = $path[0];
                    if (empty($catlist[$top])) {
                        $catlist[$top] = ['name' => $dbcatlist[$top]->name, 'children' => []];
                    }
                    unset($path[0]);
                    foreach ($path as $id) {
                        if (!array_key_exists($id, $catlist[$top]['children'])) {
                            $catlist[$top]['children'][$id] = $category->name;
                        }
                    }
                } else if (empty($catlist[$category->id])) {
                    $catlist[$category->id] = ['name' => $category->name, 'children' => []];
                }
            }
        }

        return $catlist;
    }

    /**
     * Compile properties.
     *
     * @param string $pluginfrankenstyle Plugin frankenstle.
     * @param array $theirprops Properties received.
     *
     * @return array properties
     */
    public static function compile_properties($pluginfrankenstyle, $theirprops = null) {
        global $CFG, $DB;

        $ourprops = [];
        $themeprops = $DB->get_records('config_plugins', ['plugin' => $pluginfrankenstyle]);

        if ($theirprops) {
            // In a format where we can update our props from their props.
            // Need id and value pairs in order to be able to update existing records using the id.
            $data = new stdClass();
            $data->id = 0;
            $data->value = $CFG->version;
            $ourprops['moodle_version'] = $data;
            // Convert 'version' to 'plugin_version' and put second.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $data = new stdClass();
                    $data->id = $themeprop->id;
                    $data->value = $themeprop->value;
                    $ourprops['plugin_version'] = $data;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }
            // Put 'feature_version' third.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'feature_version') {
                    $data = new stdClass();
                    $data->id = $themeprop->id;
                    $data->value = $themeprop->value;
                    $ourprops['feature_version'] = $data;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }
            foreach ($themeprops as $themeprop) {
                $data = new stdClass();
                $data->id = $themeprop->id;
                $data->value = $themeprop->value;
                $ourprops[$themeprop->name] = $data;
            }
        } else {
            $ourprops['moodle_version'] = $CFG->version;
            // Put the plugin version next so that it will be second in the table.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $ourprops['plugin_version'] = $themeprop->value;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }
            // Put the feature version next so that it will be third in the table.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'feature_version') {
                    $ourprops['feature_version'] = $themeprop->value;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }

            foreach ($themeprops as $themeprop) {
                $ourprops[$themeprop->name] = $themeprop->value;
            }
        }

        // File property processing.
        $ourfileprops = self::FILEPROPERTYNAMES;

        // If receiving properties then we need to take into account the slides being set, not what we currently have.
        // Using 'theme_adaptable_pluginfile' in lib.php as a reference.
        if ($theirprops) {
            $slidercount = $theirprops['slidercount'];
        } else {
            $slidercount = self::get_setting('slidercount');
        }

        // Slide show.
        for ($propslide = 1; $propslide <= $slidercount; $propslide++) {
            $ourfileprops[] = 'p' . $propslide;
        }

        // Category.
        $customheaderids = explode(',', get_config('theme_adaptable', 'categoryhavecustomheader'));
        foreach ($customheaderids as $customheaderid) {
            $ourfileprops[] = 'categoryheaderbgimage' . $customheaderid;
            $ourfileprops[] = 'categoryheaderlogo' . $customheaderid;
        }

        if (array_key_exists('propertyfiles', $ourprops)) {
            unset($ourprops['propertyfiles']); // Prevent props in props.
        }

        if (array_key_exists('getprops', $ourprops)) {
            unset($ourprops['getprops']); // Prevent property information in props.
        }
        if (array_key_exists('putprops', $ourprops)) {
            unset($ourprops['putprops']); // Prevent property report in props.
        }
        if (array_key_exists('fileputprops', $ourprops)) {
            unset($ourprops['fileputprops']); // Prevent property file in props.
        }

        $properties = [self::PROPS => $ourprops, self::FILEPROPNAMES => $ourfileprops];

        return $properties;
    }

    /**
     * Get properties.
     *
     * @param string $pluginfrankenstyle Plugin frankenstyle.
     * @param bool $encodefiles.
     *
     * @return array Properties.
     */
    public static function get_properties(string $pluginfrankenstyle, bool $encodefiles = false) {
        $props = self::compile_properties($pluginfrankenstyle);

        if ($encodefiles) {
            $fileprops = $props[self::FILEPROPNAMES];

            foreach ($fileprops as $fileprop) {
                $name = $pluginfrankenstyle . '/' . $fileprop;
                // Remove any number to get a common string.
                $title = get_string(preg_replace('/[0-9]+/', '', $fileprop), $pluginfrankenstyle);
                $description = $title;
                $setting = new \theme_adaptable\admin_setting_configstoredfiles(
                    $name,
                    $title,
                    $description,
                    $fileprop,
                    null
                );
                $encoded = $setting->base64encode();

                $props[self::PROPS][$fileprop] = $encoded;
            }
        }

        return $props;
    }

    /**
     * Store properties.
     *
     * @param string $pluginname Plugin name.
     * @param string $pluginfrankenstyle Plugin frankenstle.
     * @param array $props Properties.
     * @return string Result.
     */
    public static function put_properties($pluginname, $pluginfrankenstyle, $props) {
        global $DB;

        // Get the current properties as a reference and for theme version information.
        $currentprops = self::compile_properties($pluginfrankenstyle, $props);

        // Build the report.
        // Report Moodle version.
        $report = get_string('putpropertyreport', $pluginfrankenstyle) . PHP_EOL;
        $report .= get_string(
            'putpropertyproperties',
            $pluginfrankenstyle,
            [
                'name' => 'Moodle',
                'version' => $props['moodle_version'],
            ]
        ) . PHP_EOL;
        unset($props['moodle_version']);
        $report .= get_string(
            'putpropertyour',
            $pluginfrankenstyle,
            [
                'name' => 'Moodle',
                'version' => $currentprops[self::PROPS]['moodle_version']->value,
            ]
        ) . PHP_EOL;
        unset($currentprops[self::PROPS]['moodle_version']);

        // Report plugin version.
        $pluginversionkey = 'plugin_version';
        if (array_key_exists('theme_version', $props)) {
            // Old properties.
            $pluginversionkey = 'theme_version';
        }
        $report .= get_string(
            'putpropertyproperties',
            $pluginfrankenstyle,
            [
                'name' => $pluginname,
                'version' => $props[$pluginversionkey],
            ]
        ) . PHP_EOL;
        unset($props[$pluginversionkey]);
        $report .= get_string(
            'putpropertyour',
            $pluginfrankenstyle,
            [
                'name' => $pluginname,
                'version' => $currentprops[self::PROPS]['plugin_version']->value,
            ]
        ) . PHP_EOL;
        unset($currentprops[self::PROPS][$pluginversionkey]);

        // Report feature version if there.
        if (array_key_exists('feature_version', $props)) {
            $report .= get_string(
                'putpropertyfeatureversion',
                $pluginfrankenstyle,
                [
                    'name' => $pluginname,
                    'version' => $props['feature_version'],
                ]
            ) . PHP_EOL;
            $propsfeatureversion = $currentprops[self::PROPS]['feature_version']->value;
            unset($props['feature_version']);
        } else {
            // Does not exist, so check for all possible changes.
            $propsfeatureversion = 0;
        }
        $report .= get_string(
            'putpropertyourfeatureversion',
            $pluginfrankenstyle,
            [
                'name' => $pluginname,
                'version' => $currentprops[self::PROPS]['feature_version']->value,
            ]
        ) . PHP_EOL . PHP_EOL;
        unset($currentprops[self::PROPS]['feature_version']);

        // Pre-process files - using 'theme_adaptable_pluginfile' in lib.php as a reference.
        $filestoreport = '';
        $fileschanged = '';
        $preprocessfilesettings = $currentprops[self::FILEPROPNAMES];

        // Process the file properties.
        foreach ($preprocessfilesettings as $preprocessfilesetting) {
            self::put_prop_file_preprocess($pluginfrankenstyle, $preprocessfilesetting, $props, $filestoreport, $fileschanged);
            unset($currentprops[self::PROPS][$preprocessfilesetting]);
        }

        if ($fileschanged) {
            $report .= get_string('putpropertiesreportfileschanged', $pluginfrankenstyle) . PHP_EOL . $fileschanged . PHP_EOL;
        }

        if ($filestoreport) {
            $report .= get_string('putpropertiesreportfiles', $pluginfrankenstyle) . PHP_EOL . $filestoreport . PHP_EOL;
        }

        // Need to ignore and report on any unknown settings.
        $report .= get_string('putpropertiessettingsreport', $pluginfrankenstyle) . PHP_EOL;

        // Process any settings updates first.
        $updated = '';
        $updates = self::process_settings_name_updates($props, $pluginfrankenstyle, $propsfeatureversion);
        foreach ($updates as $update) {
            $updated .= $update . PHP_EOL;
        }

        $changed = '';
        $unchanged = '';
        $added = '';
        $ignored = '';
        $settinglog = '';
        foreach ($props as $propkey => $propvalue) {
            $settinglog = '\'' . $propkey . '\' ' .
                get_string('putpropertiesvalue', $pluginfrankenstyle) . ' \'' . $propvalue . '\'';
            if (array_key_exists($propkey, $currentprops[self::PROPS])) {
                if ($propvalue != $currentprops[self::PROPS][$propkey]->value) {
                    $settinglog .= ' ' . get_string('putpropertiesfrom', $pluginfrankenstyle) . ' \'' .
                    $currentprops[self::PROPS][$propkey]->value . '\'';
                    $changed .= $settinglog . '.' . PHP_EOL;
                    $DB->update_record(
                        'config_plugins',
                        ['id' => $currentprops[self::PROPS][$propkey]->id, 'value' => $propvalue],
                        true
                    );
                } else {
                    $unchanged .= $settinglog . '.' . PHP_EOL;
                }
            } else if (self::to_add_property($propkey)) {
                // Properties that have an index and don't already exist.
                $DB->insert_record('config_plugins', [
                    'plugin' => $pluginfrankenstyle, 'name' => $propkey, 'value' => $propvalue, ], true);
                $added .= $settinglog . '.' . PHP_EOL;
            } else {
                $ignored .= $settinglog . '.' . PHP_EOL;
            }
        }

        if (!empty($updated)) {
            $report .= get_string('putpropertiesupdated', $pluginfrankenstyle) . PHP_EOL . $updated . PHP_EOL;
        }
        if (!empty($changed)) {
            $report .= get_string('putpropertieschanged', $pluginfrankenstyle) . PHP_EOL . $changed . PHP_EOL;
        }
        if (!empty($added)) {
            $report .= get_string('putpropertiesadded', $pluginfrankenstyle) . PHP_EOL . $added . PHP_EOL;
        }
        if (!empty($unchanged)) {
            $report .= get_string('putpropertiesunchanged', $pluginfrankenstyle) . PHP_EOL . $unchanged . PHP_EOL;
        }
        if (!empty($ignored)) {
            $report .= get_string('putpropertiesignored', $pluginfrankenstyle) . PHP_EOL . $ignored . PHP_EOL;
        }

        return $report;
    }

    /**
     * Property to add
     *
     * @param int $propkey

     * @return array matches
     */
    protected static function to_add_property($propkey) {
        static $matches = '(' .
             // Slider ....
            '^p[1-9][0-9]?url$|' .
            '^p[1-9][0-9]?cap$|' .
            '^sliderh3color$|' .
            '^sliderh4color$|' .
            '^slidersubmitcolor$|' .
            '^slidersubmitbgcolor$|' .
            '^slider2h3color$|' .
            '^slider2h3bgcolor$|' .
            '^slider2h4color$|' .
            '^slider2h4bgcolor$|' .
            '^slideroption2submitcolor$|' .
            '^slideroption2color$|' .
            '^slideroption2a$|' .
            // Alerts....
            '^enablealert[1-9][0-9]?$|' .
            '^alertkey[1-9][0-9]?$|' .
            '^alerttext[1-9][0-9]?$|' .
            '^alerttype[1-9][0-9]?$|' .
            '^alertaccess[1-9][0-9]?$|' .
            '^alertprofilefield[1-9][0-9]?$|' .
            // Analytics....
            '^analyticstext[1-9][0-9]?$|' .
            '^analyticsprofilefield[1-9][0-9]?$|' .
            // Header menu....
            '^newmenu[1-9][0-9]?title$|' .
            '^newmenu[1-9][0-9]?$|' .
            '^newmenu[1-9][0-9]?requirelogin$|' .
            '^newmenu[1-9][0-9]?field$|' .
            // Marketing blocks....
            '^market[1-9][0-9]?$|' .
            '^marketlayoutrow[1-9][0-9]?$|' .
            // Navbar menu....
            '^toolsmenu[1-9][0-9]?title$|' .
            '^toolsmenu[1-9][0-9]?$|' .
            '^toolsmenu[1-9][0-9]?icon$|' .
            // Ticker text....
            '^tickertext[1-9][0-9]?$|' .
            '^tickertext[1-9][0-9]?profilefield$' .
            ')';

        return (preg_match($matches, $propkey) === 1);
    }

    /**
     * Pre process properties file.
     *
     * @param string $pluginfrankenstyle
     * @param int $key
     * @param array $props
     * @param string $filestoreport
     * @param string $fileschanged
     */
    private static function put_prop_file_preprocess($pluginfrankenstyle, $key, &$props, &$filestoreport, &$fileschanged) {
        if (!empty($props[$key])) {
            if ($props[$key][0] == '{') { // Is a JSON encoded file(s).
                $name = $pluginfrankenstyle . '/' . $key;
                // Remove any number to get a common string.
                $title = get_string(preg_replace('/[0-9]+/', '', $key), $pluginfrankenstyle);
                $description = $title;
                $setting = new \theme_adaptable\admin_setting_configstoredfiles(
                    $name,
                    $title,
                    $description,
                    $key,
                    null
                );
                $changed = $setting->base64decode($props[$key]);
                if (!empty($changed[\theme_adaptable\admin_setting_configstoredfiles::REMOVEDFILES])) {
                    foreach ($changed[\theme_adaptable\admin_setting_configstoredfiles::REMOVEDFILES] as $removedfilename) {
                        $fileschanged .= get_string(
                            'propertyfileremoved',
                            $pluginfrankenstyle,
                            ['filename' => $removedfilename, 'settingname' => $key]
                        ) . PHP_EOL;
                    }
                }
                if (!empty($changed[\theme_adaptable\admin_setting_configstoredfiles::ADDEDFILES])) {
                    foreach ($changed[\theme_adaptable\admin_setting_configstoredfiles::ADDEDFILES] as $addedfilename) {
                        $fileschanged .= get_string(
                            'propertyfileadded',
                            $pluginfrankenstyle,
                            ['filename' => $addedfilename, 'settingname' => $key]
                        ) . PHP_EOL;
                    }
                }
                if (!empty($changed[\theme_adaptable\admin_setting_configstoredfiles::ERROR])) {
                    $filestoreport .= $changed[\theme_adaptable\admin_setting_configstoredfiles::ERROR] . PHP_EOL;
                }
            } else {
                $filestoreport .= '\'' . $key . '\' ' . get_string('putpropertiesvalue', $pluginfrankenstyle) . ' \'' .
                    \core_text::substr($props[$key], 1) . '\'.' . PHP_EOL;
            }
        }
        unset($props[$key]);
    }

    /**
     * Process updates to settings based upon feature version.
     * Note: Does not cope with file props!
     *
     * @param array $props Reference to the properties from the properties import.
     * @param string $pluginfrankenstyle Frankenstyle name of the plugin.
     * @param int $propsfeatureversion Feature version before upgrade / value in properties.
     *
     * @return array Of changes as localised strings.
     */
    public static function process_settings_name_updates(&$props, $pluginfrankenstyle, $propsfeatureversion) {
        $upgrading = (empty($props));
        $changes = [];
        $changed = [];

        // From and to = change, only from = remove and 'to' only will use setting default value.
        if ($propsfeatureversion < 2025080200) {
            // Changes in 2025080200.
            $change = new stdClass();
            $change->from = 'topmenufontsize';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursepageheaderhidesitetitle';
            $change->to = 'coursepageheaderhidetitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'enableheading';
            $change->to = 'enablecoursetitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'enablezoomshowtext';
            $change->to = 'navbardisplaytitles';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsivecoursetitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsivesitetitle';
            $change->to = 'responsiveheadertitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'jssection';
            $change->to = 'customjs';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'jssectionrestricted';
            $change->to = 'customjsrestricted';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'pageheaderheight';
            $change->to = 'headermainrowminheight';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'dividingline';
            $change->to = 'headertoprowdividingline';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'dividingline2';
            $change->to = 'footerdividingline';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headerbkcolor';
            $change->to = 'headertoprowbkcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headertextcolor';
            $change->to = 'headertoprowtextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headerbkcolor2';
            $change->to = 'headermainrowbkcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headertextcolor2';
            $change->to = 'headermainrowtextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsiveheader';
            $change->to = 'responsiveheader';
            $change->convert = function ($value) {
                return str_replace('block', 'flex', $value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'maincolor';
            $change->to = 'maincolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'fontcolor';
            $change->to = 'fontcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'regionmaincolor';
            $change->to = 'regionmaincolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'regionmaintextcolor';
            $change->to = 'regionmaintextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'linkcolor';
            $change->to = 'linkcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'dimmedtextcolor';
            $change->to = 'dimmedtextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursepageblocksliderenabled';
            $change->to = 'coursepageblockinfoenabled';
            $changes[] = $change;
        }

        if ((!empty($changes)) && ($upgrading)) {
            $props = self::compile_properties($pluginfrankenstyle)[self::PROPS];
        }

        foreach ($changes as $change) {
            if (array_key_exists($change->from, $props)) {
                // Make the change.
                if (!empty($change->to)) {
                    // Replacement.
                    if (!array_key_exists($change->to, $props)) {
                        // Replacement not defined, make new to be the old value.
                        if ($upgrading) {
                            set_config($change->to, $props[$change->from], $pluginfrankenstyle);
                        } else {
                            // Set in properties as version will have this new setting and in effect
                            // the property file is updating it, possibly.
                            $props[$change->to] = $props[$change->from];
                        }
                        $changed[] = get_string(
                            'settingschangechanged',
                            $pluginfrankenstyle,
                            [
                                'from' => $change->from,
                                'to' => $change->to,
                                'value' => $props[$change->from],
                            ]
                        );
                    } else if (($change->from == $change->to) && (!empty($change->convert))) {
                        // Existing variable conversion.
                        $fromvalue = $props[$change->from];
                        $tovalue = ($change->convert)($fromvalue);
                        if ($fromvalue == $tovalue) {
                            // No actual change!
                            continue;
                        }
                        if ($upgrading) {
                            set_config($change->to, $tovalue, $pluginfrankenstyle);
                        } else {
                            // Set in properties as version will have this new setting and in effect
                            // the property file is updating it, possibly.
                            $props[$change->to] = $tovalue;
                        }
                        $changed[] = get_string(
                            'settingschangevalue',
                            $pluginfrankenstyle,
                            [
                                'from' => $change->from,
                                'valuefrom' => $fromvalue,
                                'valueto' => $tovalue,
                            ]
                        );
                    } else {
                        // Else replacement already defined, just remove old setting as new has superceded it.
                        $changed[] = get_string(
                            'settingschangealreadydefined',
                            $pluginfrankenstyle,
                            [
                                'from' => $change->from,
                                'to' => $change->to,
                                'fromvalue' => $props[$change->from],
                                'tovalue' => $props[$change->to],
                            ]
                        );
                    }
                    if (!$upgrading) {
                        if (!((!empty($change->to)) && ($change->from == $change->to))) {
                            // Remove from properties so not shown as ignored, rather that it is reported in
                            // 'changed / alreadydefined'.
                            unset($props[$change->from]);
                        } // Else is a change of value;
                    }
                } else if ($upgrading) { // Else deletion.
                    $changed[] = get_string(
                        'settingschangedeleted',
                        $pluginfrankenstyle,
                        [
                            'from' => $change->from,
                            'value' => $props[$change->from],
                        ]
                    );
                }
                if ($upgrading) {
                    if (!((!empty($change->to)) && ($change->from == $change->to))) {
                        // Remove from, being the old.
                        unset_config($change->from, $pluginfrankenstyle);
                    } // Else is a change of value;
                } // Else as no longer exists, then will be reported as 'Ignored'.
            } // Else change does not appear in the properties file when importing or the database when upgrading.
        }

        return $changed;
    }

    /**
     * States if the Kaltura plugin is installed.
     * Ref: https://moodle.org/plugins/view.php?id=447
     *
     * @return boolean true or false.
     */
    public static function kalturaplugininstalled() {
        global $CFG;

        static $paths = [
            'local/kalturamediagallery',
            'local/mymedia',
        ];

        $hascount = 0;
        foreach ($paths as $path) {
            if (file_exists($CFG->dirroot . '/' . $path)) {
                $hascount++;
            }
        }

        return (count($paths) == $hascount);
    }

    /**
     * Get the FontAwesome markup.
     *
     * @param string $theicon Icon name or setting name containing the icon name.
     * @param array $options - Array of named options:
     *    'attributes' => Array - optional attributes to add.
     *    'classes' => Array - optional extra classes to add.
     *    'content' => String - optional content.
     *    'modulerenderer' => core_renderer - If a core_renderer instance, get icon for module where the module name is in
     *         $theicon.  Required for module name fallback.
     *    'settingicondefault' => String - Indicates that the icon name is a setting and this is the
     *         default icon to use if the setting value is empty.
     *    'settingthemename' => String - Indicates the setting theme name to use.  If not stated then
     *         will be 'adaptable'.  Use lower case, the bit after the underscore in the frankenstyle name.
     *    'title' => String - optional title.
     * @param string $content - Optional content.
     *
     * @return string markup or empty string if no icon specified.
     */
    public static function getfontawesomemarkup($theicon, $options = []) {
        $markup = '';
        if (!empty($theicon)) {
            // Function 'extract' (https://www.php.net/manual/en/function.extract.php) is forbidden!
            // So have our own similar functionality!
            if (!empty($options['settingicondefault'])) { // Setting not icon.
                $settingthemename = (empty($options['settingthemename'])) ? '' : $options['settingthemename'];
                $theicon = self::get_setting($theicon, false, $settingthemename, $options['settingicondefault']);
            }
            $theicon = trim($theicon);

            if (!empty($options['modulerenderer'])) { // Module not icon.
                $modname = $theicon;
                $theicon = self::get_module_fa($theicon);
                if (empty($theicon)) {
                    // Return the rendered pix_icon 'monologo'.
                    return $options['modulerenderer']->pix_icon(
                        'monologo',
                        get_string('pluginname', 'mod_' . $modname),
                        $modname
                    );
                }
            }

            $attributes = (empty($options['attributes'])) ? [] : $options['attributes'];
            $classes = (empty($options['classes'])) ? [] : $options['classes'];
            $content = (empty($options['content'])) ? '' : $options['content'];

            if (mb_strpos($theicon, ' ') === false) { // No spaces, so find.
                // Decide what to do if shims are enabled or not.
                $fav = self::get_setting('fav');
                $faiv = self::get_setting('faiv');
                $hasprefix = (mb_strpos($theicon, 'fa') !== false);
                if ((!empty($fav)) && (empty($faiv))) {
                    $toolbox = self::get_instance();
                    $classes[] = $toolbox->get_fa6_from_fa4($theicon, $hasprefix);
                } else {
                    $iconprefix = 'fa ';
                    if (!$hasprefix) {
                        $iconprefix .= 'fa-';
                    }
                    $classes[] = $iconprefix . $theicon;
                }
            } else {
                // Spaces so full icon specified.
                $classes[] = $theicon;
            }
            $attributes['aria-hidden'] = 'true';
            $classes[] = 'afaicon';
            $classes[] = 'fa-fw';
            $attributes['class'] = implode(' ', $classes);
            if (!empty($title)) {
                $attributes['title'] = $title;
                $content .= html_writer::tag('span', $title, ['class' => 'visually-hidden']);
            }
            $markup = html_writer::tag('i', $content, $attributes);
        }
        return $markup;
    }

    /**
     * Get the Font Awesome icon classes from the module name.
     *
     * @param string $modulename The module name.
     *
     * @return string Icon css classes or empty if not found.
     */
    protected static function get_module_fa($modulename) {
        $icon = '';

        switch ($modulename) {
            case 'assign':
                $icon = 'fa-solid fa-file-pen';
                break;
            case 'choice':
                $icon = 'fa-solid fa-arrows-split-up-and-left';
                break;
            case 'data':
                $icon = 'fa-solid fa-database';
                break;
            case 'feedback':
                $icon = 'fa-regular fa-comment-dots';
                break;
            case 'forum':
                $icon = 'fa-solid fa-people-group';
                break;
            case 'glossary':
                $icon = 'fa-solid fa-box-archive';
                break;
            case 'imscp':
                $icon = 'fa-solid fa-box';
                break;
            case 'lesson':
                $icon = 'fa-solid fa-chalkboard-user';
                break;
            case 'page':
                $icon = 'fa-solid fa-file-lines';
                break;
            case 'quiz':
                $icon = 'fa-solid fa-person-circle-question';
                break;
            case 'wiki':
                $icon = 'fa-solid fa-circle-nodes';
                break;
            case 'workshop':
                $icon = 'fa-solid fa-people-arrows';
                break;
        }

        return $icon;
    }

    /**
     * Gets the Font Awesome 6 version of the version 4 icon.
     *
     * @param string $icon The icon.
     * @param boolean $hasprefix Has the 'fa' prefix.
     *
     * @return string Icon CSS classes.
     */
    public function get_fa6_from_fa4($icon, $hasprefix = false) {
        $icontofind = ($hasprefix) ? $icon : 'fa-' . $icon;

        // Ref: fa-v4-shims.js.
        /* Node JS Code:
            shims.forEach(function(value, index, array) {
                output = '            \'fa-' + value[0] + '\' => \'';
                if (value[1] == null) {
                    output += 'fas';
                } else {
                    output += value[1];
                }
                output = output + ' fa-';
                if (value[2] == null) {
                    output += value[0];
                } else {
                    output += value[2];
                }
                output += '\',';
                console.log(output);
            });
        */
        static $icons = [
            'fa-glass' => 'fas fa-martini-glass-empty',
            'fa-envelope-o' => 'far fa-envelope',
            'fa-star-o' => 'far fa-star',
            'fa-remove' => 'fas fa-xmark',
            'fa-close' => 'fas fa-xmark',
            'fa-gear' => 'fas fa-gear',
            'fa-trash-o' => 'far fa-trash-can',
            'fa-home' => 'fas fa-house',
            'fa-file-o' => 'far fa-file',
            'fa-clock-o' => 'far fa-clock',
            'fa-arrow-circle-o-down' => 'far fa-circle-down',
            'fa-arrow-circle-o-up' => 'far fa-circle-up',
            'fa-play-circle-o' => 'far fa-circle-play',
            'fa-repeat' => 'fas fa-arrow-rotate-right',
            'fa-rotate-right' => 'fas fa-arrow-rotate-right',
            'fa-refresh' => 'fas fa-arrows-rotate',
            'fa-list-alt' => 'far fa-rectangle-list',
            'fa-dedent' => 'fas fa-outdent',
            'fa-video-camera' => 'fas fa-video',
            'fa-picture-o' => 'far fa-image',
            'fa-photo' => 'far fa-image',
            'fa-image' => 'far fa-image',
            'fa-map-marker' => 'fas fa-location-dot',
            'fa-pencil-square-o' => 'far fa-pen-to-square',
            'fa-edit' => 'far fa-pen-to-square',
            'fa-share-square-o' => 'fas fa-share-from-square',
            'fa-check-square-o' => 'far fa-square-check',
            'fa-arrows' => 'fas fa-up-down-left-right',
            'fa-times-circle-o' => 'far fa-circle-xmark',
            'fa-check-circle-o' => 'far fa-circle-check',
            'fa-mail-forward' => 'fas fa-share',
            'fa-expand' => 'fas fa-up-right-and-down-left-from-center',
            'fa-compress' => 'fas fa-down-left-and-up-right-to-center',
            'fa-eye' => 'far fa-eye',
            'fa-eye-slash' => 'far fa-eye-slash',
            'fa-warning' => 'fas fa-triangle-exclamation',
            'fa-calendar' => 'fas fa-calendar-days',
            'fa-arrows-v' => 'fas fa-up-down',
            'fa-arrows-h' => 'fas fa-left-right',
            'fa-bar-chart' => 'fas fa-chart-column',
            'fa-bar-chart-o' => 'fas fa-chart-column',
            'fa-twitter-square' => 'fab fa-twitter-square',
            'fa-facebook-square' => 'fab fa-facebook-square',
            'fa-gears' => 'fas fa-gears',
            'fa-thumbs-o-up' => 'far fa-thumbs-up',
            'fa-thumbs-o-down' => 'far fa-thumbs-down',
            'fa-heart-o' => 'far fa-heart',
            'fa-sign-out' => 'fas fa-right-from-bracket',
            'fa-linkedin-square' => 'fab fa-linkedin',
            'fa-thumb-tack' => 'fas fa-thumbtack',
            'fa-external-link' => 'fas fa-up-right-from-square',
            'fa-sign-in' => 'fas fa-right-to-bracket',
            'fa-github-square' => 'fab fa-github-square',
            'fa-lemon-o' => 'far fa-lemon',
            'fa-square-o' => 'far fa-square',
            'fa-bookmark-o' => 'far fa-bookmark',
            'fa-twitter' => 'fab fa-twitter',
            'fa-facebook' => 'fab fa-facebook-f',
            'fa-facebook-f' => 'fab fa-facebook-f',
            'fa-github' => 'fab fa-github',
            'fa-credit-card' => 'far fa-credit-card',
            'fa-feed' => 'fas fa-rss',
            'fa-hdd-o' => 'far fa-hard-drive',
            'fa-hand-o-right' => 'far fa-hand-point-right',
            'fa-hand-o-left' => 'far fa-hand-point-left',
            'fa-hand-o-up' => 'far fa-hand-point-up',
            'fa-hand-o-down' => 'far fa-hand-point-down',
            'fa-globe' => 'fas fa-earth-americas',
            'fa-tasks' => 'fas fa-bars-progress',
            'fa-arrows-alt' => 'fas fa-maximize',
            'fa-group' => 'fas fa-users',
            'fa-chain' => 'fas fa-link',
            'fa-cut' => 'fas fa-scissors',
            'fa-files-o' => 'far fa-copy',
            'fa-floppy-o' => 'far fa-floppy-disk',
            'fa-save' => 'far fa-floppy-disk',
            'fa-navicon' => 'fas fa-bars',
            'fa-reorder' => 'fas fa-bars',
            'fa-magic' => 'fas fa-wand-magic-sparkles',
            'fa-pinterest' => 'fab fa-pinterest',
            'fa-pinterest-square' => 'fab fa-pinterest-square',
            'fa-google-plus-square' => 'fab fa-google-plus-square',
            'fa-google-plus' => 'fab fa-google-plus-g',
            'fa-money' => 'fas fa-money-bill-1',
            'fa-unsorted' => 'fas fa-sort',
            'fa-sort-desc' => 'fas fa-sort-down',
            'fa-sort-asc' => 'fas fa-sort-up',
            'fa-linkedin' => 'fab fa-linkedin-in',
            'fa-rotate-left' => 'fas fa-arrow-rotate-left',
            'fa-legal' => 'fas fa-gavel',
            'fa-tachometer' => 'fas fa-gauge',
            'fa-dashboard' => 'fas fa-gauge',
            'fa-comment-o' => 'far fa-comment',
            'fa-comments-o' => 'far fa-comments',
            'fa-flash' => 'fas fa-bolt',
            'fa-clipboard' => 'fas fa-paste',
            'fa-lightbulb-o' => 'far fa-lightbulb',
            'fa-exchange' => 'fas fa-right-left',
            'fa-cloud-download' => 'fas fa-cloud-arrow-down',
            'fa-cloud-upload' => 'fas fa-cloud-arrow-up',
            'fa-bell-o' => 'far fa-bell',
            'fa-cutlery' => 'fas fa-utensils',
            'fa-file-text-o' => 'far fa-file-lines',
            'fa-building-o' => 'far fa-building',
            'fa-hospital-o' => 'far fa-hospital',
            'fa-tablet' => 'fas fa-tablet-screen-button',
            'fa-mobile' => 'fas fa-mobile-screen-button',
            'fa-mobile-phone' => 'fas fa-mobile-screen-button',
            'fa-circle-o' => 'far fa-circle',
            'fa-mail-reply' => 'fas fa-reply',
            'fa-github-alt' => 'fab fa-github-alt',
            'fa-folder-o' => 'far fa-folder',
            'fa-folder-open-o' => 'far fa-folder-open',
            'fa-smile-o' => 'far fa-face-smile',
            'fa-frown-o' => 'far fa-face-frown',
            'fa-meh-o' => 'far fa-face-meh',
            'fa-keyboard-o' => 'far fa-keyboard',
            'fa-flag-o' => 'far fa-flag',
            'fa-mail-reply-all' => 'fas fa-reply-all',
            'fa-star-half-o' => 'far fa-star-half-stroke',
            'fa-star-half-empty' => 'far fa-star-half-stroke',
            'fa-star-half-full' => 'far fa-star-half-stroke',
            'fa-code-fork' => 'fas fa-code-branch',
            'fa-chain-broken' => 'fas fa-link-slash',
            'fa-unlink' => 'fas fa-link-slash',
            'fa-calendar-o' => 'far fa-calendar',
            'fa-maxcdn' => 'fab fa-maxcdn',
            'fa-html5' => 'fab fa-html5',
            'fa-css3' => 'fab fa-css3',
            'fa-unlock-alt' => 'fas fa-unlock',
            'fa-minus-square-o' => 'far fa-square-minus',
            'fa-level-up' => 'fas fa-turn-up',
            'fa-level-down' => 'fas fa-turn-down',
            'fa-pencil-square' => 'fas fa-square-pen',
            'fa-external-link-square' => 'fas fa-square-up-right',
            'fa-compass' => 'far fa-compass',
            'fa-caret-square-o-down' => 'far fa-square-caret-down',
            'fa-toggle-down' => 'far fa-square-caret-down',
            'fa-caret-square-o-up' => 'far fa-square-caret-up',
            'fa-toggle-up' => 'far fa-square-caret-up',
            'fa-caret-square-o-right' => 'far fa-square-caret-right',
            'fa-toggle-right' => 'far fa-square-caret-right',
            'fa-eur' => 'fas fa-euro-sign',
            'fa-euro' => 'fas fa-euro-sign',
            'fa-gbp' => 'fas fa-sterling-sign',
            'fa-usd' => 'fas fa-dollar-sign',
            'fa-dollar' => 'fas fa-dollar-sign',
            'fa-inr' => 'fas fa-indian-rupee-sign',
            'fa-rupee' => 'fas fa-indian-rupee-sign',
            'fa-jpy' => 'fas fa-yen-sign',
            'fa-cny' => 'fas fa-yen-sign',
            'fa-rmb' => 'fas fa-yen-sign',
            'fa-yen' => 'fas fa-yen-sign',
            'fa-rub' => 'fas fa-ruble-sign',
            'fa-ruble' => 'fas fa-ruble-sign',
            'fa-rouble' => 'fas fa-ruble-sign',
            'fa-krw' => 'fas fa-won-sign',
            'fa-won' => 'fas fa-won-sign',
            'fa-btc' => 'fab fa-btc',
            'fa-bitcoin' => 'fab fa-btc',
            'fa-file-text' => 'fas fa-file-lines',
            'fa-sort-alpha-asc' => 'fas fa-arrow-down-a-z',
            'fa-sort-alpha-desc' => 'fas fa-arrow-down-z-a',
            'fa-sort-amount-asc' => 'fas fa-arrow-down-short-wide',
            'fa-sort-amount-desc' => 'fas fa-arrow-down-wide-short',
            'fa-sort-numeric-asc' => 'fas fa-arrow-down-1-9',
            'fa-sort-numeric-desc' => 'fas fa-arrow-down-9-1',
            'fa-youtube-square' => 'fab fa-youtube-square',
            'fa-youtube' => 'fab fa-youtube',
            'fa-xing' => 'fab fa-xing',
            'fa-xing-square' => 'fab fa-xing-square',
            'fa-youtube-play' => 'fab fa-youtube',
            'fa-dropbox' => 'fab fa-dropbox',
            'fa-stack-overflow' => 'fab fa-stack-overflow',
            'fa-instagram' => 'fab fa-instagram',
            'fa-flickr' => 'fab fa-flickr',
            'fa-adn' => 'fab fa-adn',
            'fa-bitbucket' => 'fab fa-bitbucket',
            'fa-bitbucket-square' => 'fab fa-bitbucket',
            'fa-tumblr' => 'fab fa-tumblr',
            'fa-tumblr-square' => 'fab fa-tumblr-square',
            'fa-long-arrow-down' => 'fas fa-down-long',
            'fa-long-arrow-up' => 'fas fa-up-long',
            'fa-long-arrow-left' => 'fas fa-left-long',
            'fa-long-arrow-right' => 'fas fa-right-long',
            'fa-apple' => 'fab fa-apple',
            'fa-windows' => 'fab fa-windows',
            'fa-android' => 'fab fa-android',
            'fa-linux' => 'fab fa-linux',
            'fa-dribbble' => 'fab fa-dribbble',
            'fa-skype' => 'fab fa-skype',
            'fa-foursquare' => 'fab fa-foursquare',
            'fa-trello' => 'fab fa-trello',
            'fa-gratipay' => 'fab fa-gratipay',
            'fa-gittip' => 'fab fa-gratipay',
            'fa-sun-o' => 'far fa-sun',
            'fa-moon-o' => 'far fa-moon',
            'fa-vk' => 'fab fa-vk',
            'fa-weibo' => 'fab fa-weibo',
            'fa-renren' => 'fab fa-renren',
            'fa-pagelines' => 'fab fa-pagelines',
            'fa-stack-exchange' => 'fab fa-stack-exchange',
            'fa-arrow-circle-o-right' => 'far fa-circle-right',
            'fa-arrow-circle-o-left' => 'far fa-circle-left',
            'fa-caret-square-o-left' => 'far fa-square-caret-left',
            'fa-toggle-left' => 'far fa-square-caret-left',
            'fa-dot-circle-o' => 'far fa-circle-dot',
            'fa-vimeo-square' => 'fab fa-vimeo-square',
            'fa-try' => 'fas fa-turkish-lira-sign',
            'fa-turkish-lira' => 'fas fa-turkish-lira-sign',
            'fa-plus-square-o' => 'far fa-square-plus',
            'fa-slack' => 'fab fa-slack',
            'fa-wordpress' => 'fab fa-wordpress',
            'fa-openid' => 'fab fa-openid',
            'fa-institution' => 'fas fa-bank',
            'fa-bank' => 'fas fa-bank',
            'fa-mortar-board' => 'fas fa-graduation-cap',
            'fa-yahoo' => 'fab fa-yahoo',
            'fa-google' => 'fab fa-google',
            'fa-reddit' => 'fab fa-reddit',
            'fa-reddit-square' => 'fab fa-reddit-square',
            'fa-stumbleupon-circle' => 'fab fa-stumbleupon-circle',
            'fa-stumbleupon' => 'fab fa-stumbleupon',
            'fa-delicious' => 'fab fa-delicious',
            'fa-digg' => 'fab fa-digg',
            'fa-pied-piper-pp' => 'fab fa-pied-piper-pp',
            'fa-pied-piper-alt' => 'fab fa-pied-piper-alt',
            'fa-drupal' => 'fab fa-drupal',
            'fa-joomla' => 'fab fa-joomla',
            'fa-behance' => 'fab fa-behance',
            'fa-behance-square' => 'fab fa-behance-square',
            'fa-steam' => 'fab fa-steam',
            'fa-steam-square' => 'fab fa-steam-square',
            'fa-automobile' => 'fas fa-car',
            'fa-cab' => 'fas fa-taxi',
            'fa-spotify' => 'fab fa-spotify',
            'fa-deviantart' => 'fab fa-deviantart',
            'fa-soundcloud' => 'fab fa-soundcloud',
            'fa-file-pdf-o' => 'far fa-file-pdf',
            'fa-file-word-o' => 'far fa-file-word',
            'fa-file-excel-o' => 'far fa-file-excel',
            'fa-file-powerpoint-o' => 'far fa-file-powerpoint',
            'fa-file-image-o' => 'far fa-file-image',
            'fa-file-photo-o' => 'far fa-file-image',
            'fa-file-picture-o' => 'far fa-file-image',
            'fa-file-archive-o' => 'far fa-file-zipper',
            'fa-file-zip-o' => 'far fa-file-zipper',
            'fa-file-audio-o' => 'far fa-file-audio',
            'fa-file-sound-o' => 'far fa-file-audio',
            'fa-file-video-o' => 'far fa-file-video',
            'fa-file-movie-o' => 'far fa-file-video',
            'fa-file-code-o' => 'far fa-file-code',
            'fa-vine' => 'fab fa-vine',
            'fa-codepen' => 'fab fa-codepen',
            'fa-jsfiddle' => 'fab fa-jsfiddle',
            'fa-life-bouy' => 'fas fa-life-ring',
            'fa-life-buoy' => 'fas fa-life-ring',
            'fa-life-saver' => 'fas fa-life-ring',
            'fa-support' => 'fas fa-life-ring',
            'fa-circle-o-notch' => 'fas fa-circle-notch',
            'fa-rebel' => 'fab fa-rebel',
            'fa-ra' => 'fab fa-rebel',
            'fa-resistance' => 'fab fa-rebel',
            'fa-empire' => 'fab fa-empire',
            'fa-ge' => 'fab fa-empire',
            'fa-git-square' => 'fab fa-git-square',
            'fa-git' => 'fab fa-git',
            'fa-hacker-news' => 'fab fa-hacker-news',
            'fa-y-combinator-square' => 'fab fa-hacker-news',
            'fa-yc-square' => 'fab fa-hacker-news',
            'fa-tencent-weibo' => 'fab fa-tencent-weibo',
            'fa-qq' => 'fab fa-qq',
            'fa-weixin' => 'fab fa-weixin',
            'fa-wechat' => 'fab fa-weixin',
            'fa-send' => 'fas fa-paper-plane',
            'fa-paper-plane-o' => 'far fa-paper-plane',
            'fa-send-o' => 'far fa-paper-plane',
            'fa-circle-thin' => 'far fa-circle',
            'fa-header' => 'fas fa-heading',
            'fa-futbol-o' => 'far fa-futbol',
            'fa-soccer-ball-o' => 'far fa-futbol',
            'fa-slideshare' => 'fab fa-slideshare',
            'fa-twitch' => 'fab fa-twitch',
            'fa-yelp' => 'fab fa-yelp',
            'fa-newspaper-o' => 'far fa-newspaper',
            'fa-paypal' => 'fab fa-paypal',
            'fa-google-wallet' => 'fab fa-google-wallet',
            'fa-cc-visa' => 'fab fa-cc-visa',
            'fa-cc-mastercard' => 'fab fa-cc-mastercard',
            'fa-cc-discover' => 'fab fa-cc-discover',
            'fa-cc-amex' => 'fab fa-cc-amex',
            'fa-cc-paypal' => 'fab fa-cc-paypal',
            'fa-cc-stripe' => 'fab fa-cc-stripe',
            'fa-bell-slash-o' => 'far fa-bell-slash',
            'fa-trash' => 'fas fa-trash-can',
            'fa-copyright' => 'far fa-copyright',
            'fa-eyedropper' => 'fas fa-eye-dropper',
            'fa-area-chart' => 'fas fa-chart-area',
            'fa-pie-chart' => 'fas fa-chart-pie',
            'fa-line-chart' => 'fas fa-chart-line',
            'fa-lastfm' => 'fab fa-lastfm',
            'fa-lastfm-square' => 'fab fa-lastfm-square',
            'fa-ioxhost' => 'fab fa-ioxhost',
            'fa-angellist' => 'fab fa-angellist',
            'fa-cc' => 'far fa-closed-captioning',
            'fa-ils' => 'fas fa-shekel-sign',
            'fa-shekel' => 'fas fa-shekel-sign',
            'fa-sheqel' => 'fas fa-shekel-sign',
            'fa-buysellads' => 'fab fa-buysellads',
            'fa-connectdevelop' => 'fab fa-connectdevelop',
            'fa-dashcube' => 'fab fa-dashcube',
            'fa-forumbee' => 'fab fa-forumbee',
            'fa-leanpub' => 'fab fa-leanpub',
            'fa-sellsy' => 'fab fa-sellsy',
            'fa-shirtsinbulk' => 'fab fa-shirtsinbulk',
            'fa-simplybuilt' => 'fab fa-simplybuilt',
            'fa-skyatlas' => 'fab fa-skyatlas',
            'fa-diamond' => 'far fa-gem',
            'fa-transgender' => 'fas fa-mars-and-venus',
            'fa-intersex' => 'fas fa-mars-and-venus',
            'fa-transgender-alt' => 'fas fa-transgender',
            'fa-facebook-official' => 'fab fa-facebook',
            'fa-pinterest-p' => 'fab fa-pinterest-p',
            'fa-whatsapp' => 'fab fa-whatsapp',
            'fa-hotel' => 'fas fa-bed',
            'fa-viacoin' => 'fab fa-viacoin',
            'fa-medium' => 'fab fa-medium',
            'fa-y-combinator' => 'fab fa-y-combinator',
            'fa-yc' => 'fab fa-y-combinator',
            'fa-optin-monster' => 'fab fa-optin-monster',
            'fa-opencart' => 'fab fa-opencart',
            'fa-expeditedssl' => 'fab fa-expeditedssl',
            'fa-battery-4' => 'fas fa-battery-full',
            'fa-battery' => 'fas fa-battery-full',
            'fa-battery-3' => 'fas fa-battery-three-quarters',
            'fa-battery-2' => 'fas fa-battery-half',
            'fa-battery-1' => 'fas fa-battery-quarter',
            'fa-battery-0' => 'fas fa-battery-empty',
            'fa-object-group' => 'far fa-object-group',
            'fa-object-ungroup' => 'far fa-object-ungroup',
            'fa-sticky-note-o' => 'far fa-note-sticky',
            'fa-cc-jcb' => 'fab fa-cc-jcb',
            'fa-cc-diners-club' => 'fab fa-cc-diners-club',
            'fa-clone' => 'far fa-clone',
            'fa-hourglass-o' => 'fas fa-hourglass-empty',
            'fa-hourglass-1' => 'fas fa-hourglass-start',
            'fa-hourglass-half' => 'fas fa-hourglass',
            'fa-hourglass-2' => 'fas fa-hourglass',
            'fa-hourglass-3' => 'fas fa-hourglass-end',
            'fa-hand-rock-o' => 'far fa-hand-back-fist',
            'fa-hand-grab-o' => 'far fa-hand-back-fist',
            'fa-hand-paper-o' => 'far fa-hand',
            'fa-hand-stop-o' => 'far fa-hand',
            'fa-hand-scissors-o' => 'far fa-hand-scissors',
            'fa-hand-lizard-o' => 'far fa-hand-lizard',
            'fa-hand-spock-o' => 'far fa-hand-spock',
            'fa-hand-pointer-o' => 'far fa-hand-pointer',
            'fa-hand-peace-o' => 'far fa-hand-peace',
            'fa-registered' => 'far fa-registered',
            'fa-creative-commons' => 'fab fa-creative-commons',
            'fa-gg' => 'fab fa-gg',
            'fa-gg-circle' => 'fab fa-gg-circle',
            'fa-odnoklassniki' => 'fab fa-odnoklassniki',
            'fa-odnoklassniki-square' => 'fab fa-odnoklassniki-square',
            'fa-get-pocket' => 'fab fa-get-pocket',
            'fa-wikipedia-w' => 'fab fa-wikipedia-w',
            'fa-safari' => 'fab fa-safari',
            'fa-chrome' => 'fab fa-chrome',
            'fa-firefox' => 'fab fa-firefox',
            'fa-opera' => 'fab fa-opera',
            'fa-internet-explorer' => 'fab fa-internet-explorer',
            'fa-television' => 'fas fa-tv',
            'fa-contao' => 'fab fa-contao',
            'fa-500px' => 'fab fa-500px',
            'fa-amazon' => 'fab fa-amazon',
            'fa-calendar-plus-o' => 'far fa-calendar-plus',
            'fa-calendar-minus-o' => 'far fa-calendar-minus',
            'fa-calendar-times-o' => 'far fa-calendar-xmark',
            'fa-calendar-check-o' => 'far fa-calendar-check',
            'fa-map-o' => 'far fa-map',
            'fa-commenting' => 'fas fa-comment-dots',
            'fa-commenting-o' => 'far fa-comment-dots',
            'fa-houzz' => 'fab fa-houzz',
            'fa-vimeo' => 'fab fa-vimeo-v',
            'fa-black-tie' => 'fab fa-black-tie',
            'fa-fonticons' => 'fab fa-fonticons',
            'fa-reddit-alien' => 'fab fa-reddit-alien',
            'fa-edge' => 'fab fa-edge',
            'fa-credit-card-alt' => 'fas fa-credit-card',
            'fa-codiepie' => 'fab fa-codiepie',
            'fa-modx' => 'fab fa-modx',
            'fa-fort-awesome' => 'fab fa-fort-awesome',
            'fa-usb' => 'fab fa-usb',
            'fa-product-hunt' => 'fab fa-product-hunt',
            'fa-mixcloud' => 'fab fa-mixcloud',
            'fa-scribd' => 'fab fa-scribd',
            'fa-pause-circle-o' => 'far fa-circle-pause',
            'fa-stop-circle-o' => 'far fa-circle-stop',
            'fa-bluetooth' => 'fab fa-bluetooth',
            'fa-bluetooth-b' => 'fab fa-bluetooth-b',
            'fa-gitlab' => 'fab fa-gitlab',
            'fa-wpbeginner' => 'fab fa-wpbeginner',
            'fa-wpforms' => 'fab fa-wpforms',
            'fa-envira' => 'fab fa-envira',
            'fa-wheelchair-alt' => 'fab fa-accessible-icon',
            'fa-question-circle-o' => 'far fa-circle-question',
            'fa-volume-control-phone' => 'fas fa-phone-volume',
            'fa-asl-interpreting' => 'fas fa-hands-asl-interpreting',
            'fa-deafness' => 'fas fa-ear-deaf',
            'fa-hard-of-hearing' => 'fas fa-ear-deaf',
            'fa-glide' => 'fab fa-glide',
            'fa-glide-g' => 'fab fa-glide-g',
            'fa-signing' => 'fas fa-hands',
            'fa-viadeo' => 'fab fa-viadeo',
            'fa-viadeo-square' => 'fab fa-viadeo-square',
            'fa-snapchat' => 'fab fa-snapchat',
            'fa-snapchat-ghost' => 'fab fa-snapchat',
            'fa-snapchat-square' => 'fab fa-snapchat-square',
            'fa-pied-piper' => 'fab fa-pied-piper',
            'fa-first-order' => 'fab fa-first-order',
            'fa-yoast' => 'fab fa-yoast',
            'fa-themeisle' => 'fab fa-themeisle',
            'fa-google-plus-official' => 'fab fa-google-plus',
            'fa-google-plus-circle' => 'fab fa-google-plus',
            'fa-font-awesome' => 'fab fa-font-awesome',
            'fa-fa' => 'fab fa-font-awesome',
            'fa-handshake-o' => 'far fa-handshake',
            'fa-envelope-open-o' => 'far fa-envelope-open',
            'fa-linode' => 'fab fa-linode',
            'fa-address-book-o' => 'far fa-address-book',
            'fa-vcard' => 'fas fa-address-card',
            'fa-address-card-o' => 'far fa-address-card',
            'fa-vcard-o' => 'far fa-address-card',
            'fa-user-circle-o' => 'far fa-circle-user',
            'fa-user-o' => 'far fa-user',
            'fa-id-badge' => 'far fa-id-badge',
            'fa-drivers-license' => 'fas fa-id-card',
            'fa-id-card-o' => 'far fa-id-card',
            'fa-drivers-license-o' => 'far fa-id-card',
            'fa-quora' => 'fab fa-quora',
            'fa-free-code-camp' => 'fab fa-free-code-camp',
            'fa-telegram' => 'fab fa-telegram',
            'fa-thermometer-4' => 'fas fa-temperature-full',
            'fa-thermometer' => 'fas fa-temperature-full',
            'fa-thermometer-3' => 'fas fa-temperature-three-quarters',
            'fa-thermometer-2' => 'fas fa-temperature-half',
            'fa-thermometer-1' => 'fas fa-temperature-quarter',
            'fa-thermometer-0' => 'fas fa-temperature-empty',
            'fa-bathtub' => 'fas fa-bath',
            'fa-s15' => 'fas fa-bath',
            'fa-window-maximize' => 'far fa-window-maximize',
            'fa-window-restore' => 'far fa-window-restore',
            'fa-times-rectangle' => 'fas fa-rectangle-xmark',
            'fa-window-close-o' => 'far fa-rectangle-xmark',
            'fa-times-rectangle-o' => 'far fa-rectangle-xmark',
            'fa-bandcamp' => 'fab fa-bandcamp',
            'fa-grav' => 'fab fa-grav',
            'fa-etsy' => 'fab fa-etsy',
            'fa-imdb' => 'fab fa-imdb',
            'fa-ravelry' => 'fab fa-ravelry',
            'fa-eercast' => 'fab fa-sellcast',
            'fa-snowflake-o' => 'far fa-snowflake',
            'fa-superpowers' => 'fab fa-superpowers',
            'fa-wpexplorer' => 'fab fa-wpexplorer',
            'fa-meetup' => 'fab fa-meetup',
        ];

        if (isset($icons[$icontofind])) {
            return $icons[$icontofind];
        } else {
            // Guess.
            return 'fas ' . $icontofind;
        }
    }

    /**
     * Returns the RGB for the given hex.
     *
     * @param string $hex
     * @return array
     */
    public static function hex2rgb($hex) {
        // From: http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/.
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = ['r' => $r, 'g' => $g, 'b' => $b];
        return $rgb; // Returns the rgb as an array.
    }

    /**
     * Returns the RGBA for the given hex and alpha.
     *
     * @param string $hex
     * @param string $alpha
     * @return string
     */
    public static function hex2rgba($hex, $alpha) {
        $rgba = self::hex2rgb($hex);
        $rgba[] = $alpha;
        return 'rgba(' . implode(", ", $rgba) . ')'; // Returns the rgba values separated by commas.
    }

    /**
     * Gets the overridden template if the setting for that template has been enabled and set.
     *
     * @param string $templatename
     * @return string or false if not overridden.
     */
    public static function get_template_override($templatename) {
        $template = false;

        $overridetemplates = get_config('theme_adaptable', 'templatessel');
        if ($overridetemplates) {
            $overridetemplates = explode(',', $overridetemplates);

            if (in_array($templatename, $overridetemplates)) {
                global $PAGE;

                $overridetemplatesetting = str_replace('/', '_', $templatename);
                $setting = 'activatetemplateoverride_' . $overridetemplatesetting;

                if (!empty($PAGE->theme->settings->$setting)) {
                    $setting = 'overriddentemplate_' . $overridetemplatesetting;

                    if (!empty($PAGE->theme->settings->$setting)) {
                        $template = $PAGE->theme->settings->$setting;
                    }
                }
            }
        }

        return $template;
    }


    /**
     * Renderers the overridden template if the setting for that template has been enabled and set.
     *
     * @param string $templatename
     * @param array|stdClass $data Context containing data for the template.
     * @return string or false if not overridden.
     */
    public static function apply_template_override($templatename, $data) {
        $output = false;

        $template = self::get_template_override($templatename);
        if (!empty($template)) {
            global $PAGE;
            $renderer = $PAGE->get_renderer('theme_adaptable', 'mustache');

            /* Pass in the setting value as our Mustache engine uses the Mustache_Loader_StringLoader
               instead of effectively the Mustache_Loader_FilesystemLoader and that just returns the
               'name' as passed in.  The engine then calls 'loadSource' from 'loadTemplate' which can
               have 'Mustache_Source' as an input, being the mustache template source itself. */
            $output = $renderer->render_from_template($template, $data);
        }

        return $output;
    }

    /**
     * Admin setting layout builder to build the setting layout and reduce code duplication.
     *
     * @param admin_settingpage $settingpage
     * @param string $adminsettingname
     * @param int $totalrows
     * @param array $admindefaults
     * @param array $adminchoices
     *
     * @return array of the imgblder and totalblocks.
     */
    public static function admin_settings_layout_builder(
        $settingpage,
        $adminsettingname,
        $totalrows,
        $admindefaults,
        $adminchoices
    ) {
        global $OUTPUT;

        $totalblocks = 0;
        $imgblder = '<div class="img-fluid">';
        $themesettings = self::get_settings();

        for ($i = 1; $i <= $totalrows; $i++) {
            $name = 'theme_adaptable/' . $adminsettingname . $i;
            $title = get_string($adminsettingname, 'theme_adaptable');
            $description = get_string($adminsettingname . 'desc', 'theme_adaptable');
            $default = $admindefaults[$i - 1];
            $setting = new \admin_setting_configselect($name, $title, $description, $default, $adminchoices);
            $settingpage->add($setting);

            $settingname = $adminsettingname . $i;

            if (!isset($themesettings->$settingname)) {
                $themesettings->$settingname = '0-0-0-0';
            }

            if ($themesettings->$settingname != '0-0-0-0') {
                $imgurl = $OUTPUT->image_url('layout-builder/' . $themesettings->$settingname, 'theme_adaptable');
                $imgblder .= '<img src="' . $imgurl . '" class="mb-1 img-fluid">';
            }

            $vals = explode('-', $themesettings->$settingname);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $totalblocks++;
                }
            }
        }
        $imgblder .= '</div>';

        return ['imgblder' => $imgblder, 'totalblocks' => $totalblocks];
    }
}
