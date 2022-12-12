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
 * @copyright  2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2016 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Include header.
require_once(dirname(__FILE__) . '/includes/header.php');


// If page is Grader report don't show side post.
if (($PAGE->pagetype == "grade-report-grader-index") ||
    ($PAGE->bodyid == "page-grade-report-grader-index")) {
    $left = true;
    $hassidepost = false;
} else {
    $left = $PAGE->theme->settings->blockside;
    $hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
}
$regions = theme_adaptable_grid($left, $hassidepost);
?>

<div class="container outercont">
    <?php
        echo $OUTPUT->page_navbar();
    ?>
    <div id="page-content" class="row<?php echo $regions['direction'];?>">
        <div id="region-main-box" class="<?php echo $regions['content'];?>">
            <section id="region-main">
                <?php
                echo $OUTPUT->get_course_alerts();
                echo $OUTPUT->course_content_header();
                echo $OUTPUT->activity_header();
                echo $OUTPUT->main_content();

                if ($PAGE->has_set_url()) {
                    $currenturl = $PAGE->url;
                } else {
                    $currenturl = $_SERVER["REQUEST_URI"];
                }

                // Display course page block activity bottom region if this is a mod page of type where you're viewing
                // a section, page or book (chapter).
                if (!empty($PAGE->theme->settings->coursepageblockactivitybottomenabled)) {
                    if ( stristr ($currenturl, "mod/page/view") ||
                        stristr ($currenturl, "mod/book/view") ) {
                        echo $OUTPUT->get_block_regions('customrowsetting', 'course-section-', '12-0-0-0');
                    }
                }

                echo $OUTPUT->activity_navigation();
                echo $OUTPUT->course_content_footer();
                ?>
            </section>
        </div>

        <?php
        if ($hassidepost) {
            echo $OUTPUT->blocks('side-post', $regions['blocks'].' d-print-none ');
        }
        ?>
    </div>
</div>

<?php
// Include footer.
require_once(dirname(__FILE__) . '/includes/footer.php');
