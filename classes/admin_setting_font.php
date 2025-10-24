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
 * @author     G J Barnard -
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

defined('MOODLE_INTERNAL') || die;

// Require admin library.
require_once($CFG->libdir . '/adminlib.php');

/**
 * Adaptable admin_setting_font
 */
class admin_setting_font extends \admin_setting_configtext {
    /**
     * @var string Regex string to test against valid font values / multipliers with optoinal spaces.
     */
    private static $regex = '/([0-9]*|[0-9]*[.][0-9]*)[[:space:]]*?(px|%|rem)/';

    /**
     * @var int Base font size in pixels.
     */
    private static $basefontsize = 16;

    /**
     * @var bool Allow multipler values.
     */
    protected $multiplier;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones
     *                     in config_plugins.
     * @param string $visiblename string localised name.
     * @param string $description string localised info.
     * @param string $defaultsetting mixed Value.
     * @param bool $multiplier Allow multiplier values.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $multiplier = true) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_TEXT, 5);
        $this->multiplier = $multiplier;
    }

    /**
     * Write the data.
     *
     * @param string $data Data
     *
     * @return mixed true if alright, string if error found.
     */
    public function write_setting($data) {
        $validated = $this->validate($data);
        if ($validated['result'] !== true) {
            return $validated['value'];
        }

        return ($this->config_write($this->name, $validated['value']) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage.
     *
     * @param string $data Data
     *
     * @return array bool 'result' false then 'value' contains error message, if true then 'value' contains converted font size.
     */
    public function validate($data) {
        $converted = self::validate_and_convert($data);

        if (empty($converted)) {
            // Failed to convert.
            if (is_numeric($data)) {
                $result = ['result' => false, 'value' => get_string('fontsizemultiplererror', 'theme_adaptable', $data)];
            } else {
                $result = ['result' => false, 'value' => get_string('fontsizeuniterror', 'theme_adaptable', $data)];
            }
        } else {
            if ((!$this->multiplier) && (is_numeric($converted))) {
                // Multipliers not allowed!
                $result = ['result' => false, 'value' => get_string('fontsizeuniterror', 'theme_adaptable', $data)];
            } else {
                // Store what is valid so the user is not confused by the conversion.
                $result = ['result' => true, 'value' => $data];
            }
        }

        return $result;
    }

    /**
     * Validate and convert.
     *
     * @param string $fontsize Font size.
     *
     * @return mixed false if invalid font size otherwise converted rem font size or pure number multipler (no post 'rem').
     */
    public static function validate_and_convert($fontsize) {
        $result = false;
        $matches = [];
        $regresult = preg_match(self::$regex, $fontsize, $matches);

        if (!empty($regresult)) {
            if (count($matches) == 3) { // The input string, number and units of the first match.
                switch ($matches[2]) { // Units.
                    case 'rem':
                        // No conversion, but strip any tailing characters.
                        $result = $matches[1] . $matches[2];
                        break;
                    case 'px':
                        $pixels = $matches[1];
                        if (($pixels >= 5) && ($pixels <= 200)) { // Sensible.
                            $rem = $matches[1] / self::$basefontsize;
                            $result = $rem . 'rem';
                        }
                        break;
                    case '%':
                        $percent = $matches[1];
                        if (($percent >= 85) && ($percent <= 110)) { // Sensible.
                            $rem = $percent / 100;
                            $result = $rem . 'rem';
                        }
                        break;
                }
            }
        } else if (is_numeric($fontsize)) {
            // A multiplier.
            if (($fontsize >= 0.5) && ($fontsize <= 5)) { // Sensible.
                $result = $fontsize;
            }
        }

        return $result;
    }
}
