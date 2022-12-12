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
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace theme_adaptable\output\core;

defined('MOODLE_INTERNAL') || die();

/******************************************************************************************
 *
 * Overridden Core Course Renderer for Adaptable theme
 *
 * @package    theme_adaptable
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @copyright 2015 Moodlerooms Inc. (http://www.moodlerooms.com) (activity further information functionality)
 * @copyright 2017 Manoj Solanki (Coventry University)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

use html_writer;
use moodle_url;
use coursecat_helper;
use lang_string;
use core_course_list_element;
use stdClass;

/**
 * Course renderer implementation.
 *
 * @package   theme_adaptable
 * @copyright 2017 Manoj Solanki (Coventry University)
 * @copyright Copyright (c) 2016 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {

    /**
     * Build the HTML for the module chooser javascript popup
     *
     * @param array $modules A set of modules as returned form
     * @see get_module_metadata
     * @param object $course The course that will be displayed
     * @return string The composed HTML for the module
     */
    public function course_modchooser($modules, $course) {
        if (!$this->page->requires->should_create_one_time_item_now('core_course_modchooser')) {
            return '';
        }
        $modchooser = new \theme_adaptable\output\core_course\output\modchooser($course, $modules);
        return $this->render($modchooser);
    }

    /**
     * Render course tiles in the fron page
     *
     * @param coursecat_helper $chelper
     * @param string $course
     * @param string $additionalclasses
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        $type = theme_adaptable_get_setting('frontpagerenderer');
        if ($type == 3 || $this->output->body_id() != 'page-site-index') {
            return parent::coursecat_coursebox($chelper, $course, $additionalclasses = '');
        }

        $additionalcss = '';

        if ($type == 2) {
            $additionalcss = 'hover';
        }

        if ($type == 4) {
            $additionalcss = 'hover covtiles';
        }

        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }

        $showcourses = $chelper->get_show_courses();

        if ($showcourses <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }

        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $content = '';
        $classes = trim($additionalclasses);

        if ($showcourses < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // Number of tiles per row: 12=1 tile / 6=2 tiles / 4 (default)=3 tiles / 3=4 tiles / 2=6 tiles.
        $spanclass = $this->page->theme->settings->frontpagenumbertiles;

        // Display course tiles depending the number per row.
        $content .= html_writer::start_tag('div',
              array('class' => 'col-xs-12 col-sm-'.$spanclass.' panel panel-default coursebox '.$additionalcss));

        // Add the course name.
        $coursename = $chelper->get_course_formatted_name($course);
        if (($type == 1) || ($showcourses < self::COURSECAT_SHOW_COURSES_EXPANDED)) {
            $content .= html_writer::start_tag('div', array('class' => 'panel-heading'));
            $content .= html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                $coursename, array('class' => $course->visible ? '' : 'dimmed', 'title' => $coursename));
        }

        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        if ($showcourses < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $arrow = html_writer::tag('span', '', array('class' => 'fa fp-chevron ml-1'));
            $content .= html_writer::link('#coursecollapse' . $course->id , '' . $arrow,
                array('class' => 'fpcombocollapse collapsed', 'data-toggle' => 'collapse',
                      'data-parent' => '#frontpage-category-combo'));
        }

        if ($type == 1) {
            $content .= $this->coursecat_coursebox_enrolmenticons($course);
        }

        if (($type == 1) || ($showcourses < self::COURSECAT_SHOW_COURSES_EXPANDED)) {
            $content .= html_writer::end_tag('div'); // End .panel-heading.
        }

        if ($showcourses < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $content .= html_writer::start_tag('div', array('id' => 'coursecollapse' . $course->id,
                'class' => 'panel-collapse collapse'));
        }

        $content .= html_writer::start_tag('div', array('class' => 'panel-body clearfix'));

        // This gets the course image or files.
        $content .= $this->coursecat_coursebox_content($chelper, $course, $type);

        if ($showcourses >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $icondirection = 'left';
            if ('ltr' === get_string('thisdirection', 'langconfig')) {
                $icondirection = 'right';
            }
            $arrow = html_writer::tag('span', '', array('class' => 'fa fa-chevron-'.$icondirection));
            $btn = html_writer::tag('span', get_string('course', 'theme_adaptable') . ' ' .
                    $arrow, array('class' => 'get_stringlink'));

            if (($type != 4) || (empty($this->page->theme->settings->covhidebutton))) {
                $content .= html_writer::link(new moodle_url('/course/view.php',
                    array('id' => $course->id)), $btn, array('class' => " coursebtn submit btn btn-info btn-sm"));
            }
        }

        $content .= html_writer::end_tag('div'); // End .panel-body.

        if ($showcourses < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $content .= html_writer::end_tag('div'); // End .collapse.
        }

        $content .= html_writer::end_tag('div'); // End .panel.

        return $content;
    }

    /**
     * Returns enrolment icons
     *
     * @param string $course
     * @return string
     */
    protected function coursecat_coursebox_enrolmenticons($course) {
        $content = '';
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pixicon) {
                $content .= $this->render($pixicon);
            }
            $content .= html_writer::end_tag('div'); // Enrolmenticons.
        }
        return $content;
    }

    /**
     * Returns course box content for categories
     *
     * Type - 1 = No Overlay.
     * Type - 2 = Overlay.
     * Type - 3 = Moodle default.
     * Type - 4 = Coventry tiles.
     *
     * @param coursecat_helper $chelper
     * @param string $course
     * @param int $type = 3
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course, $type = 3) {
        global $CFG;

        if ($course instanceof stdClass) {
            $course = new \core_course_list_element($course);
        }
        if ($type == 3 || $this->output->body_id() != 'page-site-index') {
            return parent::coursecat_coursebox_content($chelper, $course);
        }
        $content = '';

        // Display course overview files.
        $contentimages = '';
        $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                if ($type == 1) {
                    $contentimages .= html_writer::start_tag('div', array('class' => 'courseimage'));
                    $link = new moodle_url('/course/view.php', array('id' => $course->id));
                    $contentimages .= html_writer::link($link, html_writer::empty_tag('img', array('src' => $url)));
                    $contentimages .= html_writer::end_tag('div');
                } else {
                    $cimboxattr = array(
                        'class' => 'cimbox',
                        'style' => 'background-image: url(\''.$url.'\');'
                    );
                    if ($type == 4) {
                        $cimtag = 'a';
                        $cimboxattr['href'] = new moodle_url('/course/view.php', array('id' => $course->id));
                    } else {
                        $cimtag = 'div';
                    }
                    $contentimages .= html_writer::tag($cimtag, '', $cimboxattr);
                }
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        if (strlen($contentimages) == 0 && (($type == 2) || ($type == 4))) {
            // Default image.
            $cimboxattr = array('class' => 'cimbox');
            $url = $this->page->theme->setting_file_url('frontpagerendererdefaultimage', 'frontpagerendererdefaultimage');
            if (!empty($url)) {
                $cimboxattr['style'] = 'background-image: url(\''.$url.'\');';
            }
            if ($type == 2) {
                $cimtag = 'div';
            } else { // Type is 4.
                $cimboxattr['href'] = new moodle_url('/course/view.php', array('id' => $course->id));
                $cimtag = 'a';
            }
            $contentimages .= html_writer::tag($cimtag, '', $cimboxattr);
        }
        $content .= $contentimages.$contentfiles;

        if (($type == 2) || ($type == 4)) {
            $content .= $this->coursecat_coursebox_enrolmenticons($course);
            $content .= html_writer::start_tag('div', array(
                'class' => 'coursebox-content'
                )
            );
            $coursename = $chelper->get_course_formatted_name($course);
            $content .= html_writer::start_tag('a', array('href' => new moodle_url('/course/view.php', array('id' => $course->id))));
            $content .= html_writer::tag('h3', $coursename, array('class' => $course->visible ? '' : 'dimmed'));
            $content .= html_writer::end_tag('a');
        }
        $content .= html_writer::start_tag('div', array('class' => 'summary'));
        // Display course summary.
        if ($course->has_summary()) {
            $summs = $chelper->get_course_formatted_summary($course, array('overflowdiv' => false, 'noclean' => true,
                    'para' => false));
            $summs = strip_tags($summs);
            $truncsum = mb_strimwidth($summs, 0, 70, "...", 'utf-8');
            $content .= html_writer::tag('span', $truncsum, array('title' => $summs));
        }
        $coursecontacts = theme_adaptable_get_setting('tilesshowcontacts');
        if ($coursecontacts) {
            $coursecontacttitle = theme_adaptable_get_setting('tilescontactstitle');
            // Display course contacts. See ::get_course_contacts().
            if ($course->has_course_contacts()) {
                $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                    $cct = ($coursecontacttitle ? $coursecontact['rolename'].': ' : html_writer::tag('i', '&nbsp;',
                        array('class' => 'fa fa-graduation-cap')));
                    $name = html_writer::link(new moodle_url('/user/view.php',
                        array('id' => $userid, 'course' => $course->id)),
                        $cct.$coursecontact['username']);
                    $content .= html_writer::tag('li', $name);
                }
                $content .= html_writer::end_tag('ul'); // Teachers.
            }
        }
        $content .= html_writer::end_tag('div'); // Summary.

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = core_course_category::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                        $content .= html_writer::end_tag('div'); // Coursecat.
            }
        }
        if (($type == 2) || ($type == 4)) {
            $content .= html_writer::end_tag('div');
            // End coursebox-content.
        }

        $content .= html_writer::tag('div', '', array('class' => 'boxfooter')); // Coursecat.

        return $content;
    }

    /**
     * Frontpage course list
     *
     * @return string
     */
    public function frontpage_my_courses() {
        global $CFG, $DB;
        $output = '';
        if (!isloggedin() or isguestuser()) {
            return '';
        }
        // Calls a core renderer method (render_mycourses) to get list of a user's current courses that they are enrolled on.
        $sortedcourses = $this->render_mycourses();

        if (!empty($sortedcourses) || !empty($rcourses) || !empty($rhosts)) {
            $chelper = new coursecat_helper();
            if (count($sortedcourses) > $CFG->frontpagecourselimit) {
                // There are more enrolled courses than we can display, display link to 'My courses'.
                $totalcount = count($sortedcourses);
                $courses = array_slice($sortedcourses, 0, $CFG->frontpagecourselimit, true);
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/my/'),
                        'viewmoretext' => new lang_string('mycourses')
                ));
            } else {
                // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/course/index.php'),
                        'viewmoretext' => new lang_string('fulllistofcourses')
                ));
                $totalcount = $DB->count_records('course') - 1;
            }
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_attributes(
                array('class' => 'frontpage-course-list-enrolled'));
            $output .= $this->coursecat_courses($chelper, $sortedcourses, $totalcount);

            if (!empty($rcourses)) {
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rcourses as $course) {
                    $output .= $this->frontpage_remote_course($course);
                }
                $output .= html_writer::end_tag('div');
            } else if (!empty($rhosts)) {
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rhosts as $host) {
                    $output .= $this->frontpage_remote_host($host);
                }
                $output .= html_writer::end_tag('div');
            }
        }
        return $output;
    }

    // New methods added for activity styling below.  Adapted from snap theme by Moodleroooms.
    /**
     * Renders the activity navigation.
     *
     * Defer to template.
     *
     * @param \core_course\output\activity_navigation $page
     * @return string html for the page
     */
    public function render_activity_navigation(\core_course\output\activity_navigation $page) {
        $data = $page->export_for_template($this->output);

        /* Add in extra data for our own overridden activity_navigation template.
           So manipulating the 'classes' and 'text' properties in 'action_link' and 'classes' in 'urlselect'. */
        if (!empty($data->prevlink)) {
            $data->prevlink->classes = 'previous_activity prevnext'; // Override the button!

            $icon = html_writer::tag('i', '', array('class' => 'fa fa-angle-double-left'));
            $previouslink = html_writer::tag('span', $icon, array('class' => 'nav_icon'));
            $activityname = html_writer::tag('span', get_string('previousactivity', 'theme_adaptable'),
                array('class' => 'nav_guide')).'<br>';
            $activityname .= substr($data->prevlink->text, strpos($data->prevlink->text, ' ') + 1);
            $previouslink .= html_writer::tag('span', $activityname, array('class' => 'text'));
            $data->prevlink->text = $previouslink;
        }

        if (!empty($data->nextlink)) {
            $data->nextlink->classes = 'next_activity prevnext'; // Override the button!

            $activityname = html_writer::tag('span', get_string('nextactivity', 'theme_adaptable'),
                array('class' => 'nav_guide')).'<br>';
            $activityname .= substr($data->nextlink->text, 0, strrpos($data->nextlink->text, ' '));
            $nextlink = html_writer::tag('span', $activityname, array('class' => 'text'));
            $icon = html_writer::tag('i', '', array('class' => 'fa fa-angle-double-right'));
            $nextlink .= html_writer::tag('span', $icon, array('class' => 'nav_icon'));
            $data->nextlink->text = $nextlink;
        }

        if (!empty($data->activitylist)) {
            $data->activitylist->classes = 'jumpmenu';
        }

        return $this->output->render_from_template('core_course/activity_navigation', $data);
    }
}
