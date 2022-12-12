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
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Include header.
require_once(dirname(__FILE__) . '/includes/header.php');

// Set layout.
$left = $PAGE->theme->settings->blockside;
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$regions = theme_adaptable_grid($left, $hassidepost);

$dashblocksposition = (!empty($PAGE->theme->settings->dashblocksposition)) ? $PAGE->theme->settings->dashblocksposition : 'abovecontent';

$dashblocklayoutlayoutrow = '';
if (!empty($PAGE->theme->settings->dashblocksenabled)) {
    $dashblocklayoutlayoutrow = '<div id="frontblockregion" class="row">';
    $dashblocklayoutlayoutrow .= $OUTPUT->get_block_regions('dashblocklayoutlayoutrow');
    $dashblocklayoutlayoutrow .= '</div>';
}
?>

<div class="container outercont">
    <?php
    if ( (!empty($PAGE->theme->settings->dashblocksenabled)) &&
         (empty($PAGE->theme->settings->tabbedlayoutdashboard)) && ($dashblocksposition == 'abovecontent') ) {
        echo $dashblocklayoutlayoutrow;
    } ?>
    <div id="page-content" class="row<?php echo $regions['direction'];?>">
        <?php
        if (!empty($PAGE->theme->settings->tabbedlayoutdashboard)) {

            $showtabs = array (0 => true, 1 => true, 2 => true);
            // Get any custom user profile field restriction for tab 1 and 2. (e.g. showtab1=false).
            require_once($CFG->dirroot.'/user/profile/lib.php');
            require_once($CFG->dirroot.'/user/lib.php');
            profile_load_data($USER);

            if (!empty($PAGE->theme->settings->tabbedlayoutdashboardtab1condition)) {
                $fields = explode('=', $PAGE->theme->settings->tabbedlayoutdashboardtab1condition);
                $ftype = $fields[0];
                $setvalue = $fields[1];

                // Get user profile field (if it exists).
                $ftype = "profile_field_$ftype";
                if (isset($USER->$ftype)) {
                    if ($USER->$ftype != $setvalue) {
                        // Condition is true, so don't show this tab.
                        $showtabs[1] = false;
                    }
                }
            }

            if (!empty($PAGE->theme->settings->tabbedlayoutdashboardtab2condition)) {
                $fields = explode('=', $PAGE->theme->settings->tabbedlayoutdashboardtab2condition);
                $ftype = $fields[0];
                $setvalue = $fields[1];

                // Get user profile field (if it exists).
                $ftype = "profile_field_$ftype";
                if (isset($USER->$ftype)) {
                    if ($USER->$ftype != $setvalue) {
                        // Condition is true, so don't show this tab.
                        $showtabs[2] = false;
                    }
                }
            }

            $taborder = explode ('-', $PAGE->theme->settings->tabbedlayoutdashboard);
            $count = 0;
            echo '<div id="region-main-box" class="' . $regions['content'] . '">';
            echo '<section id="region-main">';

            echo '<main id="dashboardtabcontainer" class="tabcontentcontainer">';

            foreach ($taborder as $tabnumber) {
                if ((!empty($showtabs[$tabnumber])) && ($showtabs[$tabnumber] == true)) {
                    // Tab 0 is the original content tab.
                    if ($tabnumber == 0) {
                        $tabname = 'dashboard-tab-content';
                        $tablabel = get_string('tabbedlayouttablabeldashboard', 'theme_adaptable');
                    } else {
                            $tabname = 'dashboard-tab' . $tabnumber;
                            $tablabel = get_string('tabbedlayouttablabeldashboard' . $tabnumber, 'theme_adaptable');
                    }

                    echo '<input id="' . $tabname . '" type="radio" name="tabs" class="dashboardtab" ' .
                        ($count == 0 ? ' checked ' : '') . '>' .
                        '<label for="' . $tabname . '" class="dashboardtab">' . $tablabel .'</label>';
                        $count++;
                }
            }

            // Basic array used by appropriately named blocks below (e.g. course-tab-one).  All this is due to the re-use of
            // existing functionality and non-use of numbers in block region names.
            $wordtonumber = array (1 => 'one', 2 => 'two');
            foreach ($taborder as $tabnumber) {
                if ($tabnumber == 0) {
                    echo '<section id="adaptable-dashboard-tab-content" class="adaptable-tab-section tab-panel">';

                    if ( (!empty($PAGE->theme->settings->dashblocksenabled)) && ($dashblocksposition == 'abovecontent') ) {
                        echo $dashblocklayoutlayoutrow;
                    }
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
                    if ( (!empty($PAGE->theme->settings->dashblocksenabled)) && ($dashblocksposition == 'belowcontent') ) {
                        echo $dashblocklayoutlayoutrow;
                    }

                    echo '</section>';
                } else {
                    if ($showtabs[$tabnumber] == true) {
                        echo '<section id="adaptable-dashboard-tab-' . $tabnumber . '" class="adaptable-tab-section tab-panel">';
                        echo $OUTPUT->get_block_regions('customrowsetting', 'my-tab-' . $wordtonumber[$tabnumber] .
                            '-', '12-0-0-0');
                        echo '</section>';
                    }
                }
            }

            echo '</main>';
            echo '</section>';
            echo '</div>';
            if ($hassidepost) {
                echo $OUTPUT->blocks('side-post', $regions['blocks'].' d-print-none ');
            }
        } else { ?>
        <div id="region-main-box" class="<?php echo $regions['content'];?>">
            <section id="region-main">
            <?php
                echo $OUTPUT->course_content_header();
                echo $OUTPUT->main_content();
                echo $OUTPUT->course_content_footer();
            ?>
            </section>
        </div>

            <?php
            if ($hassidepost) {
                echo $OUTPUT->blocks('side-post', $regions['blocks'].' d-print-none ');
            }
        }
    ?>

</div>

<?php
if ( (!empty($PAGE->theme->settings->dashblocksenabled)) && (empty($PAGE->theme->settings->tabbedlayoutdashboard))
        && ($dashblocksposition == 'belowcontent') ) {
    echo $dashblocklayoutlayoutrow;
}
?>

<?php
if (is_siteadmin()) {
?>
    <div class="hidden-blocks">
        <div class="row">
            <h3><?php echo get_string('frnt-footer', 'theme_adaptable') ?></h3>
            <?php
            echo $OUTPUT->blocks('frnt-footer', 'col-10');
            ?>
        </div>
    </div>
    <?php
}
?>
</div>

<?php
// Include footer.
require_once(dirname(__FILE__) . '/includes/footer.php');
