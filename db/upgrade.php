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
 * @copyright  2019 G Barnard
 * @author     G Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade.
 *
 * @param int   $oldversion Is this an old version
 * @return bool Success.
 */
function xmldb_theme_adaptable_upgrade($oldversion = 0) {

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

    // Automatic 'Purge all caches'....
    purge_all_caches();

    return true;
}
