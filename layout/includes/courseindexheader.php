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
 * Course Index header.
 *
 * @package    theme_adaptable
 * @copyright  2022 G J Barnard (http://moodle.org/user/profile.php?id=442195)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/lib.php');

user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
} else {
    $courseindexopen = false;
}

$courseindex = core_course_drawer();

if (!$courseindex) {
    $courseindexopen = false;
}

$templatecontext = [
    'courseindexopen' => $courseindexopen,
    'courseindex' => $courseindex,
    'left' => $left
];

$courseindexmarkup = $OUTPUT->render_from_template('theme_adaptable/courseindex', $templatecontext);
$courseindextogglemarkup = $OUTPUT->render_from_template('theme_adaptable/courseindextoggle', $templatecontext);
