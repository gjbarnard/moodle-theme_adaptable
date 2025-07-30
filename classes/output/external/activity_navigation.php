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
 * @copyright  2025 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later..
 */

namespace theme_adaptable\output\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * Class for returning the rendered activity navigation, based on:
 * Class for exporting a course state - get_state.
 *
 * @package    theme_adaptable
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @copyright  2025 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_navigation extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'moduleid' => new external_value(PARAM_INT, 'module id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Return the activity navigation for the given course.
     *
     * @param int $moduleid The module id.
     * @return string Markup.
     */
    public static function execute(int $moduleid): string {
        global $PAGE;

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'moduleid' => $moduleid,
        ]);
        $moduleid = $params['moduleid'];

        // Sets up the $PAGE global with the course id.
        self::validate_context(\context_module::instance($moduleid));

        $renderer = $PAGE->get_renderer('theme_adaptable', 'core');

        return json_encode($renderer->create_activity_navigation());
    }

    /**
     * Webservice returns.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_RAW, 'Activity navigation markup');
    }
}
