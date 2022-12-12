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
 * Overridden Collapsed Topics Core Course Renderer for Adaptable theme
 *
 * @package    theme_adaptable
 * @copyright  2020 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
namespace theme_adaptable\output;

defined('MOODLE_INTERNAL') || die();

use cm_info;
use core_text;
use html_writer;

/**
 * Collapsed Topics Course renderer implementation.
 *
 * @package   theme_adaptable
 * @copyright  2020 Gareth J Barnard
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class topcoll_course_renderer extends \theme_adaptable\output\core\course_renderer {

    /**
     * Overridden. Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link.
     *
     * Note that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string.
     *
     * This method has only been overridden in order to strip -24 and similar from icon image filenames
     * to allow using of local theme icons in /pix_core/f.
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            // Nothing to be displayed to the user.
            return '';
        }

        if (!$mod->url) {
            return '';
        }

        // Run CT version of the method.
        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);
        $groupinglabel = $mod->get_grouping_label($textclasses);

        /* Render element that allows to edit activity name inline. It calls course_section_cm_name_title()
           to get the display title of the activity. */
        $tmpl = new \format_topcoll\output\course_module_name($mod, $this->page->user_is_editing(), $displayoptions);
        return $this->output->render_from_template('core/inplace_editable', $tmpl->export_for_template($this->output)).
            $groupinglabel;
    }
}
