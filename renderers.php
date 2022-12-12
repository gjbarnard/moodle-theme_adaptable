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
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Load libraries.
require_once($CFG->dirroot.'/course/format/topics/renderer.php');
require_once($CFG->dirroot.'/course/format/weeks/renderer.php');

use \theme_adaptable\traits\single_section_page;

/**
 * Class for implementing topics format rendering.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @copyright 2017 Gareth J Barnard
 *
 */
class theme_adaptable_format_topics_renderer extends format_topics_renderer {
    use single_section_page;
}

/**
 * Class for implementing weeks format rendering.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @copyright 2017 Gareth J Barnard
 *
 */
class theme_adaptable_format_weeks_renderer extends format_weeks_renderer {
    use single_section_page;
}

/******************************************************************************************
 * @copyright 2017 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @copyright 2017 Gareth J Barnard
 *
 * Grid format renderer for the Adaptable theme.
 */

// Check if GRID is installed before trying to override it.
if (file_exists("$CFG->dirroot/course/format/grid/renderer.php")) {
    include_once($CFG->dirroot."/course/format/grid/renderer.php");

    /**
     * Class for implementing grid format rendering.
     * @copyright 2017 Gareth J Barnard
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
     *
     */
    class theme_adaptable_format_grid_renderer extends format_grid_renderer {
        use single_section_page;

        /**
         * Generate the html for the 'Jump to' menu on a single section page.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections The course_sections entries from the DB.
         * @param bool $displaysection the current displayed section number.
         *
         * @return string HTML to output.
         */
        protected function section_nav_selection($course, $sections, $displaysection) {
            $settings = $this->courseformat->get_settings();
            if (!$this->section0attop) {
                $section = 0;
            } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                $section = 0;
            } else {
                $section = 1;
            }
            return $this->section_nav_selection_content($course, $sections, $displaysection, $section);
        }

        /**
         * Generate next/previous section links for navigation.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections The course_sections entries from the DB.
         * @param int $sectionno The section number in the coruse which is being displayed.
         * @return array associative array with previous and next section link.
         */
        public function get_nav_links($course, $sections, $sectionno) {
            $settings = $this->courseformat->get_settings();
            if (!$this->section0attop) {
                $buffer = -1;
            } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                $buffer = -1;
            } else {
                $buffer = 0;
            }
            return $this->get_nav_links_content($course, $sections, $sectionno, $buffer);
        }

        /**
         * Output the html for a single section page.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections (argument not used).
         * @param array $mods (argument not used).
         * @param array $modnames (argument not used).
         * @param array $modnamesused (argument not used).
         * @param int $displaysection The section number in the course which is being displayed.
         */
        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            $settings = $this->courseformat->get_settings();
            if (!$this->section0attop) {
                $section0attop = 0;
            } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                $section0attop = 0;
            } else {
                $section0attop = 1;
            }
            $this->print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection,
                $section0attop);
        }
    }
}

// Check if Flexible is installed before trying to override it.
if (file_exists("$CFG->dirroot/course/format/flexible/renderer.php")) {
    include_once($CFG->dirroot."/course/format/flexible/renderer.php");

    /**
     * @copyright 2019 Gareth J Barnard
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
     *
     * Flexible format renderer for the Adaptable theme.
     */
    class theme_adaptable_format_flexible_renderer extends format_flexible_renderer {
        use single_section_page;

        /**
         * Generate the html for the 'Jump to' menu on a single section page.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections The course_sections entries from the DB.
         * @param bool $displaysection the current displayed section number.
         *
         * @return string HTML to output.
         */
        protected function section_nav_selection($course, $sections, $displaysection) {
            if ($this->settings['section0attop'] == 2) { // One is 'Top' and two is 'Grid'.
                $section = 0;
            } else {
                $section = 1;
            }
            return $this->section_nav_selection_content($course, $sections, $displaysection, $section);
        }

        /**
         * Generate next/previous section links for navigation.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections The course_sections entries from the DB.
         * @param int $sectionno The section number in the coruse which is being displayed.
         * @return array associative array with previous and next section link.
         */
        public function get_nav_links($course, $sections, $sectionno) {
            if ($this->settings['section0attop'] == 2) { // One is 'Top' and two is 'Grid'.
                $buffer = -1;
            } else {
                $buffer = 0;
            }
            return $this->get_nav_links_content($course, $sections, $sectionno, $buffer);
        }

        /**
         * Output the html for a single section page.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections (argument not used).
         * @param array $mods (argument not used).
         * @param array $modnames (argument not used).
         * @param array $modnamesused (argument not used).
         * @param int $displaysection The section number in the course which is being displayed.
         */
        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            $this->print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection,
                false);
        }
    }
}

/******************************************************************************************
 * @copyright 2020 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 *
 * Collapsed Topics format renderer for the Adaptable theme.
 */

// Check if Collapsed Topics is installed before trying to override it.
if (file_exists("$CFG->dirroot/course/format/topcoll/renderer.php")) {
    include_once($CFG->dirroot."/course/format/topcoll/renderer.php");

    /**
     * Constructor
     *
     * @copyright 2020 Gareth J Barnard
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
     */
    class theme_adaptable_format_topcoll_renderer extends format_topcoll_renderer {
        /**
         * Constructor method.
         *
         * @param moodle_page $page
         * @param string $target one of rendering target constants.
         */
        public function __construct(moodle_page $page, $target) {
            parent::__construct($page, $target);
            $this->courserenderer = $this->page->get_renderer('theme_adaptable', 'topcoll_course');
        }
    }
}
