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
 * Database upgrade.
 *
 * @package    theme_adaptable
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * Upgrade.
 *
 * @param int   $oldversion Is this an old version
 * @return bool Success.
 */
function xmldb_theme_adaptable_upgrade($oldversion = 0) {
    global $OUTPUT;

    if ($oldversion < 2020073101) {
        if (get_config('theme_adaptable', 'fontname') == 'default') {
            set_config('fontname', 'sans-serif', 'theme_adaptable');
        }
        if (get_config('theme_adaptable', 'fontheadername') == 'default') {
            set_config('fontheadername', 'sans-serif', 'theme_adaptable');
        }
        if (get_config('theme_adaptable', 'fonttitlename') == 'default') {
            set_config('fonttitlename', 'sans-serif', 'theme_adaptable');
        }

        upgrade_plugin_savepoint(true, 2020073101, 'theme', 'adaptable');
    }

    if ($oldversion < 2020073107) {
        $settings = get_config('theme_adaptable');
        foreach ($settings as $settingname => $settingvalue) {
            $settingvalue = trim($settingvalue);
            $changedsettingvalue = preg_replace('/^0px|\b0px/', '0', $settingvalue);
            if ((!is_null($changedsettingvalue)) && ($changedsettingvalue != $settingvalue)) {
                // Not null and replacement(s) have happened.
                set_config($settingname, $changedsettingvalue, 'theme_adaptable');
            }
        }

        upgrade_plugin_savepoint(true, 2020073107, 'theme', 'adaptable');
    }

    if ($oldversion < 2024032801) {
        $value = get_config('theme_adaptable', 'menuhovercolor');
        if (!empty($value)) {
            set_config('menubkhovercolor', $value, 'theme_adaptable');
            // Prevent replacement when upgrade has already happened in a version for an older Moodle!
            unset_config('menuhovercolor', 'theme_adaptable');
        }

        upgrade_plugin_savepoint(true, 2024032801, 'theme', 'adaptable');
    }

    if ($oldversion < 2024032803) {
        $value = get_config('theme_adaptable', 'buttonfocuscolor');
        if (!empty($value)) {
            set_config('inputbuttonfocuscolour', $value, 'theme_adaptable');
            // Prevent replacement when upgrade has already happened in a version for an older Moodle!
            unset_config('buttonfocuscolor', 'theme_adaptable');
        }

        $value = get_config('theme_adaptable', 'inputbuttonfocuscolouropacity');
        if (!empty($value)) {
            set_config('inputbuttonfocuscolouropacity', $value, 'theme_adaptable');
            // Prevent replacement when upgrade has already happened in a version for an older Moodle!
            unset_config('inputbuttonfocuscolouropacity', 'theme_adaptable');
        }

        upgrade_plugin_savepoint(true, 2024032803, 'theme', 'adaptable');
    }

    if ($oldversion < 2024100502) {
        $value = get_config('theme_adaptable', 'navbardisplaysubmenuarrow');
        if (!empty($value)) {
            set_config('navbardisplaymenuarrow', $value, 'theme_adaptable');
            // Prevent replacement when upgrade has already happened in a version for an older Moodle!
            unset_config('navbardisplaysubmenuarrow', 'theme_adaptable');
        }

        upgrade_plugin_savepoint(true, 2024100502, 'theme', 'adaptable');
    }

    if ($oldversion < 2024100503) {
        $value = get_config('theme_adaptable', 'msgbadgecolor');
        if (!empty($value)) {
            set_config('notbadgebackgroundcolour', $value, 'theme_adaptable');
            // Prevent replacement when upgrade has already happened in a version for an older Moodle!
            unset_config('msgbadgecolor', 'theme_adaptable');
        }

        upgrade_plugin_savepoint(true, 2024100503, 'theme', 'adaptable');
    }

    // Check / report on versions.
    $versions = \theme_adaptable\toolbox::get_file_versions();
    $themeversion = $versions['theme'];
    if ($themeversion['version'] != null) {
        echo $OUTPUT->notification(
            $themeversion['release'] . ' - ' . $themeversion['version'],
            'info',
            false,
            'Adaptable theme release and version',
            'i/circleinfo'
        );

        if (!empty($versions['local'])) {
            $localversion = $versions['local'];
            if ($localversion['version'] != null) {
                echo $OUTPUT->notification(
                    $localversion['release'] . ' - ' . $localversion['version'],
                    'info',
                    false,
                    'Adaptable local release and version',
                    'i/circleinfo'
                );
                if ($themeversion['version'] != $localversion['version']) {
                    echo $OUTPUT->notification(
                        'Adaptable theme and local versions must match!' .
                        '  Rectify by updating local_adaptable otherwise errors may occur.',
                        'warning',
                        false,
                        'Version issue',
                        'i/warning'
                    );
                }
            }
        }

        // Method check.
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            if (method_exists($localtoolbox, 'supported_methods')) {
                // Method check.
                $methods = ['get_custom_js', 'userfav_menu_items'];
                $unsupportedmethods = $localtoolbox->supported_methods($methods, $themeversion['version']);
                if (!empty($unsupportedmethods)) {
                    echo $OUTPUT->notification(
                        '',
                        'warning',
                        false,
                        $unsupportedmethods,
                        'i/warning'
                    );
                }
            }
        }
    }

    // Feature version to keep track of changes across versions for major releases of Moodle.
    $currentfeatureversion = get_config('theme_adaptable', 'feature_version');
    $props = [];
    $changed = \theme_adaptable\toolbox::process_settings_name_updates($props, 'theme_adaptable', $currentfeatureversion);
    if (!empty($changed)) {
        $title = get_string('settingschangenotificationtitle', 'theme_adaptable');
        foreach ($changed as $change) {
            echo $OUTPUT->notification($change, 'info', false, $title, 'i/circleinfo');
        }
    }

    // Feature version for this version.
    set_config('feature_version', 2025080200, 'theme_adaptable');

    if ($oldversion < 2025092500) {
        upgrade_plugin_savepoint(true, 2025092500, 'theme', 'adaptable');
    }

    // Automatic 'Purge all caches'....
    purge_all_caches();

    return true;
}
