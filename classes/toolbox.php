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
 * @copyright  &copy; 2018 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_adaptable;

defined('MOODLE_INTERNAL') || die;

/**
 *
 * Class definition for toolbox.
 *
 * @package    theme_adaptable
 * @copyright  &copy; 2018 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toolbox {

    /**
     * Gets the setting moodle_url for the given setting if it exists and set.
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
    public static function get_setting_moodle_url($setting, $theconfig = null) {
        $settingurl = null;

        if (empty($theconfig)) {
            $theconfig = \theme_config::load('adaptable');
        }
        if ($theconfig != null) {
            $thesetting = $theconfig->settings->$setting;
            if (!empty($thesetting)) {
                global $CFG;
                $itemid = \theme_get_revision();
                $syscontext = \context_system::instance();

                $settingurl = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                              "/$syscontext->id/theme_$theconfig->name/$setting/$itemid".$thesetting);
            }
        }
        return $settingurl;
    }

    /**
     * Finds the given setting in the theme using the get_config core function for when the
     * theme_config object has not been created.
     *
     * @param string $setting Setting name.
     * @param themename $themename null(default of 'adaptable' used)|theme name.
     *
     * @return any false|value of setting.
     */
    public static function get_config_setting($setting, $themename = null) {
        if (empty($themename)) {
            $themename = 'adaptable';
        }
        return \get_config('theme_'.$themename, $setting);
    }


    /**
     * Get top level categories.
     *
     * @return array category ids
     */
    public static function get_top_level_categories() {
        $categoryids = array();
        $categories = \core_course_category::get(0)->get_children(); // Parent = 0 i.e. top-level categories only.

        foreach ($categories as $category) {
            $categoryids[$category->id] = $category->name;
        }

        return $categoryids;
    }

    /**
     * Get the current top level category.
     *
     * @return int category id
     */
    public static function get_current_top_level_catetgory() {
        global $PAGE;
        $catid = false;

        if (is_array($PAGE->categories)) {
            $catids = array_keys($PAGE->categories);
            if (!empty($catids)) {
                // The last entry in the array is the top level category.
                $catid = $catids[(count($catids) - 1)];
            }
        } else if (!empty($PAGE->course->category)) {
            $catid = $PAGE->course->category;
            // See if the course category is a top level one.
            if (!array_key_exists($catid, self::get_top_level_categories())) {
                $catid = false;
            }
        }

        return $catid;
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
            $catlist = array();

            foreach ($dbcatlist as $category) {
                if ($category->depth > 1 ) {
                    $path = preg_split('|/|', $category->path, -1, PREG_SPLIT_NO_EMPTY);
                    $top = $path[0];
                    if (empty($catlist[$top])) {
                        $catlist[$top] = array('name' => $dbcatlist[$top]->name, 'children' => array());
                    }
                    unset($path[0]);
                    foreach ($path as $id) {
                        if (!array_key_exists($id, $catlist[$top]['children'])) {
                            $catlist[$top]['children'][$id] = $category->name;
                        }
                    }
                } else if (empty($catlist[$category->id])) {
                    $catlist[$category->id] = array('name' => $category->name, 'children' => array());
                }
            }
        }

        return $catlist;
    }

    /**
     * Compile properties.
     *
     * @param string $themename Theme name
     * @param bool $array Is this an array (confusing variable name)
     *
     * @return array properties
     */
    public static function compile_properties($themename, $array = true) {
        global $CFG, $DB;

        $props = array();
        $themeprops = $DB->get_records('config_plugins', array('plugin' => 'theme_'.$themename));

        if ($array) {
            $props['moodle_version'] = $CFG->version;
            // Put the theme version next so that it will be at the top of the table.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $props['theme_version'] = $themeprop->value;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }

            foreach ($themeprops as $themeprop) {
                $props[$themeprop->name] = $themeprop->value;
            }
        } else {
            $data = new \stdClass();
            $data->id = 0;
            $data->value = $CFG->version;
            $props['moodle_version'] = $data;
            // Convert 'version' to 'theme_version'.
            foreach ($themeprops as $themeprop) {
                if ($themeprop->name == 'version') {
                    $data = new \stdClass();
                    $data->id = $themeprop->id;
                    $data->name = 'theme_version';
                    $data->value = $themeprop->value;
                    $props['theme_version'] = $data;
                    unset($themeprops[$themeprop->id]);
                    break;
                }
            }
            foreach ($themeprops as $themeprop) {
                $data = new \stdClass();
                $data->id = $themeprop->id;
                $data->value = $themeprop->value;
                $props[$themeprop->name] = $data;
            }
        }

        return $props;
    }

    /**
     * Store properties.
     *
     * @param string $themename Theme name
     * @param string $props Properties
     * @return string
     */
    public static function put_properties($themename, $props) {
        global $DB;

        // Get the current properties as a reference and for theme version information.
        $currentprops = self::compile_properties($themename, false);

        // Build the report.
        $report = get_string('putpropertyreport', 'theme_adaptable').PHP_EOL;
        $report .= get_string('putpropertyproperties', 'theme_adaptable').' \'Moodle\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$props['moodle_version'].'.'.PHP_EOL;
        unset($props['moodle_version']);
        $report .= get_string('putpropertyour', 'theme_adaptable').' \'Moodle\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$currentprops['moodle_version']->value.'.'.PHP_EOL;
        unset($currentprops['moodle_version']);
        $report .= get_string('putpropertyproperties', 'theme_adaptable').' \''.ucfirst($themename).'\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$props['theme_version'].'.'.PHP_EOL;
        unset($props['theme_version']);
        $report .= get_string('putpropertyour', 'theme_adaptable').' \''.ucfirst($themename).'\' '.
            get_string('putpropertyversion', 'theme_adaptable').' '.$currentprops['theme_version']->value.'.'.PHP_EOL.PHP_EOL;
        unset($currentprops['theme_version']);

        // Pre-process files - using 'theme_adaptable_pluginfile' in lib.php as a reference.
        $filestoreport = '';
        $preprocessfilesettings = array('logo', 'favicon', 'homebk', 'pagebackground', 'frontpagerendererdefaultimage',
            'headerbgimage', 'loginbgimage', 'adaptablemarkettingimages');

        // Slide show.
        for ($propslide = 1; $propslide <= $props['slidercount']; $propslide++) {
            $preprocessfilesettings[] = 'p'.$propslide;
        }

        // Category.
        $customheaderids = explode(',', get_config('theme_adaptable', 'categoryhavecustomheader'));
        foreach ($customheaderids as $customheaderid) {
            $preprocessfilesettings[] = 'categoryheaderbgimage'.$customheaderid;
            $preprocessfilesettings[] = 'categoryheaderlogo'.$customheaderid;
        }

        // Process the file properties.
        foreach ($preprocessfilesettings as $preprocessfilesetting) {
            self::put_prop_file_preprocess($preprocessfilesetting, $props, $filestoreport);
            unset($currentprops[$preprocessfilesetting]);
        }

        if ($filestoreport) {
            $report .= get_string('putpropertiesreportfiles', 'theme_adaptable').PHP_EOL.$filestoreport.PHP_EOL;
        }

        // Need to ignore and report on any unknown settings.
        $report .= get_string('putpropertiessettingsreport', 'theme_adaptable').PHP_EOL;
        $changed = '';
        $unchanged = '';
        $added = '';
        $ignored = '';
        $settinglog = '';
        foreach ($props as $propkey => $propvalue) {
            $settinglog = '\''.$propkey.'\' '.get_string('putpropertiesvalue', 'theme_adaptable').' \''.$propvalue.'\'';
            if (array_key_exists($propkey, $currentprops)) {
                if ($propvalue != $currentprops[$propkey]->value) {
                    $settinglog .= ' '.get_string('putpropertiesfrom', 'theme_adaptable').' \''.$currentprops[$propkey]->value.'\'';
                    $changed .= $settinglog.'.'.PHP_EOL;
                    $DB->update_record('config_plugins', array('id' => $currentprops[$propkey]->id, 'value' => $propvalue), true);
                } else {
                    $unchanged .= $settinglog.'.'.PHP_EOL;
                }
            } else if (self::to_add_property($propkey)) {
                // Properties that have an index and don't already exist.
                $DB->insert_record('config_plugins', array(
                    'plugin' => 'theme_'.$themename, 'name' => $propkey, 'value' => $propvalue), true);
                $added .= $settinglog.'.'.PHP_EOL;
            } else {
                $ignored .= $settinglog.'.'.PHP_EOL;
            }
        }

        if (!empty($changed)) {
            $report .= get_string('putpropertieschanged', 'theme_adaptable').PHP_EOL.$changed.PHP_EOL;
        }
        if (!empty($added)) {
            $report .= get_string('putpropertiesadded', 'theme_adaptable').PHP_EOL.$added.PHP_EOL;
        }
        if (!empty($unchanged)) {
            $report .= get_string('putpropertiesunchanged', 'theme_adaptable').PHP_EOL.$unchanged.PHP_EOL;
        }
        if (!empty($ignored)) {
            $report .= get_string('putpropertiesignored', 'theme_adaptable').PHP_EOL.$ignored.PHP_EOL;
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
        static $matches = '('.
             // Slider ....
            '^p[1-9][0-9]?url$|'.
            '^p[1-9][0-9]?cap$|'.
            '^sliderh3color$|'.
            '^sliderh4color$|'.
            '^slidersubmitcolor$|'.
            '^slidersubmitbgcolor$|'.
            '^slider2h3color$|'.
            '^slider2h3bgcolor$|'.
            '^slider2h4color$|'.
            '^slider2h4bgcolor$|'.
            '^slideroption2submitcolor$|'.
            '^slideroption2color$|'.
            '^slideroption2a$|'.
            // Alerts....
            '^enablealert[1-9][0-9]?$|'.
            '^alertkey[1-9][0-9]?$|'.
            '^alerttext[1-9][0-9]?$|'.
            '^alerttype[1-9][0-9]?$|'.
            '^alertaccess[1-9][0-9]?$|'.
            '^alertprofilefield[1-9][0-9]?$|'.
            // Analytics....
            '^analyticstext[1-9][0-9]?$|'.
            '^analyticsprofilefield[1-9][0-9]?$|'.
            // Header menu....
            '^newmenu[1-9][0-9]?title$|'.
            '^newmenu[1-9][0-9]?$|'.
            '^newmenu[1-9][0-9]?requirelogin$|'.
            '^newmenu[1-9][0-9]?field$|'.
            // Marketing blocks....
            '^market[1-9][0-9]?$|'.
            '^marketlayoutrow[1-9][0-9]?$|'.
            // Navbar menu....
            '^toolsmenu[1-9][0-9]?title$|'.
            '^toolsmenu[1-9][0-9]?$|'.
            // Ticker text....
            '^tickertext[1-9][0-9]?$|'.
            '^tickertext[1-9][0-9]?profilefield$'.
            ')';

        return (preg_match($matches, $propkey) === 1);
    }

    /**
     * Pre process properties file.
     *
     * @param int $key
     * @param array $props
     * @param string $filestoreport
     *
     */
    private static function put_prop_file_preprocess($key, &$props, &$filestoreport) {
        if (!empty($props[$key])) {
            $filestoreport .= '\''.$key.'\' '.get_string('putpropertiesvalue', 'theme_adaptable').' \''.
                \core_text::substr($props[$key], 1).'\'.'.PHP_EOL;
        }
        unset($props[$key]);
    }

    /**
     * States if the Kaltura plugin is installed.
     * Ref: https://moodle.org/plugins/view.php?id=447
     *
     * @return boolean true or false.
     */
    public static function kalturaplugininstalled() {
        global $CFG;

        static $paths = array(
            'local/kalturamediagallery',
            'local/mymedia'
        );

        $hascount = 0;
        foreach ($paths as $path) {
            if (file_exists($CFG->dirroot.'/'.$path)) {
                $hascount++;
            }
        }

        return (count($paths) == $hascount);
    }

    /**
     * Gets the Font Awesome markup for the given icon.
     *
     * @param string $theicon
     * @param array $classes - Optional extra classes to add.
     * @param array $attributes - Optional attributes to add.
     * @param string $content - Optional content.
     *
     * @return string markup or empty string if no icon specified.
     */
    public static function getfontawesomemarkup($theicon, $classes = array(), $attributes = array(), $content = '') {
        $icon = '';
        if (!empty($theicon)) {
            $classes[] = 'fa fa-'.$theicon;
            $attributes['aria-hidden'] = 'true';
            $attributes['class'] = implode(' ', $classes);
            $icon = \html_writer::tag('i', $content, $attributes);
        }
        return $icon;
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
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array('r' => $r, 'g' => $g, 'b' => $b);
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
        return 'rgba('.implode(", ", $rgba).')'; // Returns the rgba values separated by commas.
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
                $setting = 'activatetemplateoverride_'.$overridetemplatesetting;

                if (!empty($PAGE->theme->settings->$setting)) {
                    $setting = 'overriddentemplate_'.$overridetemplatesetting;

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
     * @param array $admindefaults
     * @param array $adminchoices
     *
     * @return array of the imgblder and totalblocks.
     */
    public static function admin_settings_layout_builder($settingpage, $adminsettingname, $admindefaults, $adminchoices) {
        global $CFG, $PAGE;

        $totalblocks = 0;
        $imgpath = $CFG->wwwroot.'/theme/adaptable/pix/layout-builder/';
        $imgblder = '';
        for ($i = 1; $i <= 5; $i++) {
            $name = 'theme_adaptable/'.$adminsettingname.$i;
            $title = get_string($adminsettingname, 'theme_adaptable');
            $description = get_string($adminsettingname.'desc', 'theme_adaptable');
            $default = $admindefaults[$i - 1];
            $setting = new \admin_setting_configselect($name, $title, $description, $default, $adminchoices);
            $settingpage->add($setting);

            $settingname = $adminsettingname.$i;

            if (!isset($PAGE->theme->settings->$settingname)) {
                $PAGE->theme->settings->$settingname = '0-0-0-0';
            }

            if ($PAGE->theme->settings->$settingname != '0-0-0-0') {
                $imgblder .= '<img src="'.$imgpath.$PAGE->theme->settings->$settingname.'.png'.'" style="padding-top: 5px">';
            }

            $vals = explode('-', $PAGE->theme->settings->$settingname);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $totalblocks++;
                }
            }
        }

        return array('imgblder' => $imgblder, 'totalblocks' => $totalblocks);
    }
}
