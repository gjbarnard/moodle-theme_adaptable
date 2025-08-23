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
 * Lib
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

use core\output\theme_config;
use core\url;
use theme_adaptable\toolbox;

/**
 * Gets the pre SCSS for the theme.
 *
 * @param theme_config $theme The theme configuration object.
 * @return string SCSS.
 */
function theme_adaptable_pre_scss($theme) {
    return toolbox::pre_scss($theme);
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string SCSS.
 */
function theme_adaptable_get_main_scss_content($theme) {
    return toolbox::get_main_scss_content($theme);
}

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_adaptable_process_css($css, $theme) {
    if (!empty(toolbox::get_setting('fav'))) {
        // Change references to 6 to 7.
        $css = str_replace('Font Awesome 6 Free";', 'Font Awesome 7 Free";', $css);
    }

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_adaptable_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_adaptable_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Serves the H5P Custom CSS.
 *
 * @param string $filename The filename.
 * @param theme_config $theme The theme config object.
 */
function theme_adaptable_serve_hvp_css($filename, $theme) {
    global $CFG, $PAGE;
    require_once($CFG->dirroot . '/lib/configonlylib.php'); // For 'min_enable_zlib_compression' function.

    $PAGE->set_context(context_system::instance());
    $themename = $theme->name;

    $content = \theme_adaptable\toolbox::get_setting('hvpcustomcss');
    $md5content = md5($content);
    $md5stored = get_config('theme_' . $themename, 'hvpccssmd5');
    if ((empty($md5stored)) || ($md5stored != $md5content)) {
        // Content changed, so the last modified time needs to change.
        set_config('hvpccssmd5', $md5content, 'theme_' . $themename);
        $lastmodified = time();
        set_config('hvpccsslm', $lastmodified, 'theme_' . $themename);
    } else {
        $lastmodified = get_config('theme_' . $themename, 'hvpccsslm');
        if (empty($lastmodified)) {
            $lastmodified = time();
        }
    }

    // Sixty days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('HTTP/1.1 200 OK');

    header('Etag: "' . $md5content . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . strlen($content));
    }

    echo $content;

    die;
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 */
function theme_adaptable_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_adaptable_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('adaptable');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        // By default, theme files must be cache-able by both browsers and proxies.  From 'More' theme.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'customjsfiles') {
            return $theme->setting_file_serve('customjsfiles', $args, $forcedownload, $options);
        } else if ($filearea === 'homebk') {
            return $theme->setting_file_serve('homebk', $args, $forcedownload, $options);
        } else if ($filearea === 'frontpagerendererdefaultimage') {
            return $theme->setting_file_serve('frontpagerendererdefaultimage', $args, $forcedownload, $options);
        } else if ($filearea === 'headerbgimage') {
            return $theme->setting_file_serve('headerbgimage', $args, $forcedownload, $options);
        } else if ($filearea === 'hvp') {
            theme_adaptable_serve_hvp_css($args[1], $theme);
        } else if ($filearea === 'loginbgimage') {
            return $theme->setting_file_serve('loginbgimage', $args, $forcedownload, $options);
        } else if (preg_match("/^p[1-9][0-9]?$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^categoryheaderbgimage[1-9][0-9]*$/", $filearea)) { // Link: http://regexpal.com/ useful.
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^categoryheaderlogo[1-9][0-9]*$/", $filearea)) { // Link: http://regexpal.com/ useful.
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'adaptablemarkettingimages') {
            return $theme->setting_file_serve('adaptablemarkettingimages', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Initialize page
 * @param moodle_page $page
 */
function theme_adaptable_page_init(moodle_page $page) {
    global $CFG;

    if (
        (isloggedin()) && (\theme_adaptable\toolbox::get_setting('enableaccesstool')) &&
        (file_exists($CFG->dirroot . "/local/accessibilitytool/lib.php"))
    ) {
        require_once($CFG->dirroot . "/local/accessibilitytool/lib.php");
        local_accessibilitytool_page_init($page);
    }
}

/**
 * Extend the course navigation.
 *
 * Ref: MDL-69249.
 *
 * @param navigation_node $coursenode The navigation node.
 * @param stdClass $course The course.
 * @param context_course $coursecontext The course context.
 */
function theme_adaptable_extend_navigation_course($coursenode, $course, $coursecontext) {
    global $PAGE;

    if (($PAGE->theme->name == 'adaptable') && ($PAGE->user_allowed_editing())) {
        // Add the turn on/off settings.
        if ($PAGE->pagetype == 'grade-report-grader-index') {
            $editurl = clone($PAGE->url);
            $editurl->param('plugin', 'grader');

            // From /grade/report/grader/index.php ish.
            $edit = optional_param('edit', -1, PARAM_BOOL); // Sticky editing mode.
            if (($edit != - 1) && (has_capability('moodle/grade:edit', $coursecontext))) {
                $editing = $edit;
            } else {
                $editing = 0;
            }
            /* Note: The 'single_button' will still use the Moodle core strings because of the
               way /grade/report/grader/index.php is written. */
            if ($editing) {
                $editstring = get_string('turngradereditingoff', 'theme_adaptable');
            } else {
                $editstring = get_string('turngradereditingon', 'theme_adaptable');
            }
        } else {
            if ($PAGE->url->compare(new url('/course/view.php'), URL_MATCH_BASE)) {
                // We are on the course page, retain the current page params e.g. section.
                $editurl = clone($PAGE->url);
            } else {
                // Edit on the main course page.
                $editurl = new url(
                    '/course/view.php',
                    ['id' => $course->id, 'return' => $PAGE->url->out_as_local_url(false)]
                );
            }
            $editing = $PAGE->user_is_editing();
            if ($editing) {
                $editstring = get_string('turneditingoff');
            } else {
                $editstring = get_string('turneditingon');
            }
        }
        $editurl->param('sesskey', sesskey());

        if ($editing) {
            $editurl->param('edit', '0');
        } else {
            $editurl->param('edit', '1');
        }

        $childnode = navigation_node::create(
            $editstring,
            $editurl,
            navigation_node::TYPE_SETTING,
            null,
            'turneditingonoff',
            new pix_icon('i/edit', '')
        );
        $keylist = $coursenode->get_children_key_list();
        if (!empty($keylist)) {
            if (count($keylist) > 1) {
                $beforekey = $keylist[1];
            } else {
                $beforekey = $keylist[0];
            }
        } else {
            $beforekey = null;
        }
        $coursenode->add_node($childnode, $beforekey);
    }
}
