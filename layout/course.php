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
 *  Course-specific layout page
 *
 *  Includes course block region checking and formatting.
 *
 * @package    theme_adaptable
 * @copyright  2017 Manoj Solanki (Coventry University)
 * @author     2019 G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Include header.
require_once(dirname(__FILE__) . '/includes/header.php');

$left = $PAGE->theme->settings->blockside;

// If page is Grader report, override blockside setting to align left.
if (($PAGE->pagetype == "grade-report-grader-index") ||
    ($PAGE->bodyid == "page-grade-report-grader-index")) {
    $left = true;
}

$movesidebartofooter = !empty(($PAGE->theme->settings->coursepagesidebarinfooterenabled)) ? true : false;
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

// Definition of block regions for top and bottom.  These are used in potentially retrieving
// any missing block regions (due to layout changes that may hide blocks).
$blocksarray = array (
        array('settingsname'        => 'coursepageblocklayoutlayouttoprow',
                'classnamebeginswith' => 'course-top-'),
        array('settingsname'        => 'coursepageblocklayoutlayoutbottomrow',
                'classnamebeginswith' => 'course-bottom-')
);

if ($movesidebartofooter) {
    // Side post in the footer so don't adjust the layout but pretend it is not there.
    $regions = theme_adaptable_grid($left, false);
} else {
    $regions = theme_adaptable_grid($left, $hassidepost);
}
?>

<div class="container outercont">
    <?php
        echo $OUTPUT->page_navbar();
    ?>
    <div id="page-content" class="row<?php echo $regions['direction'];?>">
        <?php
        // If course page, display course top block region.
        if (!empty($PAGE->theme->settings->coursepageblocksenabled)) {
            echo '<div id="frontblockregion" class="container">';
            echo '<div class="row">';
            echo $OUTPUT->get_block_regions('coursepageblocklayoutlayouttoprow', 'course-top-');
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div id="region-main-box" class="<?php echo $regions['content'];?>">
            <section id="region-main">
            <?php
            if (!empty($PAGE->theme->settings->tabbedlayoutcoursepage)) {
                // Use Adaptable tabbed layout.
                $currentpage = theme_adaptable_get_current_page();

                $taborder = explode ('-', $PAGE->theme->settings->tabbedlayoutcoursepage);
                $count = 0;

                echo '<main id="coursetabcontainer" class="tabcontentcontainer">';

                $sectionid = optional_param('sectionid', 0, PARAM_INT);
                $section = optional_param('section', 0, PARAM_INT);
                if ((!empty($PAGE->theme->settings->tabbedlayoutcoursepagelink)) &&
                    (($sectionid) || ($section))) {
                    global $COURSE;
                    $courseurl = new moodle_url('/course/view.php', array('id' => $COURSE->id));
                    echo '<div class="linktab"><a href="'.$courseurl->out(true).'"><i class="fa fa-th-large"></i></a></div>';
                }

                foreach ($taborder as $tabnumber) {
                    if ($tabnumber == 0) {
                        $tabname = 'tab-content';
                        $tablabel = get_string('tabbedlayouttablabelcourse', 'theme_adaptable');
                    } else {
                        $tabname = 'tab' . $tabnumber;
                        $tablabel = get_string('tabbedlayouttablabelcourse' . $tabnumber, 'theme_adaptable');
                    }

                    $checkedstatus = '';

                    if (($count == 0 && $currentpage == 'coursepage') ||
                        ($currentpage != 'coursepage' && $tabnumber == 0)) {
                            $checkedstatus = 'checked';
                    }

                    $extrastyles = '';

                    if ($currentpage == 'coursepage') {
                        $extrastyles = ' style="display: none"';
                    }

                    echo  '<input id="' . $tabname . '" type="radio" name="tabs" class="coursetab" ' .
                    $checkedstatus . ' >' .
                    '<label for="' . $tabname . '" class="coursetab" ' . $extrastyles . '>' . $tablabel .'</label>';

                    $count++;
                }

                /* Basic array used by appropriately named blocks below (e.g. course-tab-one).  All this is to re-use existing
                   functionality and the non-use of numbers in block region names. */
                $wordtonumber = array (1 => 'one', 2 => 'two');

                foreach ($taborder as $tabnumber) {
                    if ($tabnumber == 0) {
                        echo '<section id="adaptable-course-tab-content" class="adaptable-tab-section tab-panel">';

                        echo $OUTPUT->get_course_alerts();
                        if (!empty($PAGE->theme->settings->coursepageblocksliderenabled) ) {
                            echo $OUTPUT->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
                        }

                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();

                        echo '</section>';
                    } else {
                        echo '<section id="adaptable-course-tab-' . $tabnumber . '" class="adaptable-tab-section tab-panel">';

                        echo $OUTPUT->get_block_regions('customrowsetting', 'course-tab-' . $wordtonumber[$tabnumber] . '-',
                        '12-0-0-0');
                        echo '</section>';
                    }
                }
                echo '</main>';
            } else {
                echo $OUTPUT->get_course_alerts();
                if (!empty($PAGE->theme->settings->coursepageblocksliderenabled) ) {
                    echo $OUTPUT->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
                }
                echo $OUTPUT->course_content_header();
                echo $OUTPUT->main_content();
                echo $OUTPUT->course_content_footer();
            }
            ?>
<?php
/* Check here if sidebar is configured to be in footer as we want to include
   the sidebar information in the main content. */

if ($movesidebartofooter == false) { ?>
        </section>
    </div>
<?php }

/* Check if the block regions are disabled in settings.  If it is and there were any blocks
   assigned to those regions, they would obviously not display.  This will allow to override
   the call to get_missing_block_regions to just display them all. */

$displayall = false;

if (empty($PAGE->theme->settings->coursepageblocksenabled)) {
    $displayall = true;
}

if ($movesidebartofooter == false) {
    if ($hassidepost) {
        echo $OUTPUT->blocks('side-post', $regions['blocks'].' d-print-none ');
    }

    /* Get any missing blocks from changing layout settings.  E.g. From 4-4-4-4 to 6-6-0-0, to recover
       what was in the last 2 spans that are now 0. */
    echo $OUTPUT->get_missing_block_regions($blocksarray, 'col-12', $displayall);
}

// If course page, display course bottom block region.
if (!empty($PAGE->theme->settings->coursepageblocksenabled)) {
    echo '<div id="frontblockregion" class="container">';
    echo '<div class="row">';
    echo $OUTPUT->get_block_regions('coursepageblocklayoutlayoutbottomrow', 'course-bottom-');
    echo '</div>';
    echo '</div>';
}

if ($movesidebartofooter) {
    if ($hassidepost) {
        echo $OUTPUT->blocks('side-post', ' col-12 d-print-none ');
    }

    /* Get any missing blocks from changing layout settings.  E.g. From 4-4-4-4 to 6-6-0-0, to recover
       what was in the last 2 spans that are now 0. */
    echo $OUTPUT->get_missing_block_regions($blocksarray, array(), $displayall);
}

if ($movesidebartofooter) { ?>
        </section>
    </div>
<?php } ?>
    </div>
</div>

<?php
// Include footer.
require_once(dirname(__FILE__) . '/includes/footer.php');

if (!empty($PAGE->theme->settings->tabbedlayoutcoursepagetabpersistencetime)) {
    $tabbedlayoutcoursepagetabpersistencetime = $PAGE->theme->settings->tabbedlayoutcoursepagetabpersistencetime;
} else {
    $tabbedlayoutcoursepagetabpersistencetime = 30;
}
if (!empty($PAGE->theme->settings->tabbedlayoutcoursepage)) {
    $PAGE->requires->js_call_amd('theme_adaptable/utils', 'init', array('currentpage' => $currentpage,
    'tabpersistencetime' => $tabbedlayoutcoursepagetabpersistencetime));

    echo '<noscript><style>label.coursetab { display: block !important; }</style><noscript>';
}
