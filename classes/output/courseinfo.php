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
 * Course info.
 *
 * @package    theme_adaptable
 * @copyright  2021 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_adaptable\output;

use renderable;
use templatable;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * The course information.
 *
 * @copyright  &copy; 2021-onwards G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class courseinfo implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output.
     * @return stdClass Data context for a mustache template.
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $PAGE;

        $data = new stdClass();

        if (\theme_adaptable\activity::activitymetaenabled() && $PAGE->user_is_editing()) {
            global $COURSE;

            $maxstudentsinfo = \theme_adaptable\activity::maxstudentsnotexceeded($COURSE->id, true);
            if ($maxstudentsinfo['maxstudents'] == 0) {
                $activityinfostring = get_string('courseadditionalmoddatastudentsinfounlimited', 'theme_adaptable',
                    $maxstudentsinfo['nostudents']);
            } else if (!$maxstudentsinfo['notexceeded']) {
                $activityinfostring = get_string('courseadditionalmoddatastudentsinfolimitednoshow', 'theme_adaptable',
                    array('students' => $maxstudentsinfo['nostudents'], 'maxstudents' => $maxstudentsinfo['maxstudents']));
            } else {
                $activityinfostring = get_string('courseadditionalmoddatastudentsinfolimitedshow', 'theme_adaptable',
                    array('students' => $maxstudentsinfo['nostudents'], 'maxstudents' => $maxstudentsinfo['maxstudents']));
            }
            $data->activityinfo = $activityinfostring;
        }

        return $data;
    }
}
