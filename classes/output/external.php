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
 * @copyright  2023 G J Barnard.  Based upon work by Damyon Wiese and G Thomas.
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output;

use external_function_parameters;
use core\external\output\icon_system\load_fontawesome_map;

/**
 * Adaptable theme.
 */
class external extends \external_api {
    /**
     * Returns description of load_icon_map() parameters.
     *
     * @return external_function_parameters
     */
    public static function load_fontawesome_icon_map_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Return a mapping of icon names to icons.
     *
     * @return array the mapping
     */
    public static function load_fontawesome_icon_map() {
        $instance = \core\output\icon_system::instance('\\theme_adaptable\\output\\icon_system_fontawesome');

        $map = $instance->get_icon_name_map();
        $result = [];

        foreach ($map as $from => $to) {
            [$component, $pix] = explode(':', $from);
            $one = [];
            $one['component'] = $component;
            $one['pix'] = $pix;
            $one['to'] = $to;
            $result[] = $one;
        }
        return $result;
    }

    /**
     * Returns description of load_icon_map() result value.
     *
     * @return external_description
     */
    public static function load_fontawesome_icon_map_returns() {
        return load_fontawesome_map::execute_returns();
    }
}
