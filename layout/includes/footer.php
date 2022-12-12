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
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015-2017 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Load messages / notifications.
echo $OUTPUT->standard_after_main_region_html();
?>

<footer id="page-footer" class="<?php echo $PAGE->theme->settings->responsivepagefooter?>">

<?php
echo '<div id="course-footer">'.$OUTPUT->course_footer().'</div>';

if ($PAGE->theme->settings->showfooterblocks) {
    echo $OUTPUT->get_footer_blocks();
}

if ($PAGE->theme->settings->hidefootersocial == 1) {
    $socialicons = $OUTPUT->socialicons();
    if (!empty($socialicons)) {
        echo '<div class="container">';
        echo '<div class="row">';
        echo '<div class="col-12 pagination-centered socialicons">';
        echo $socialicons;
        echo '</div></div></div>';
    }
}
?>
    <div class="info container2 clearfix">
        <div class="container">
            <div class="row">
                <div class="tool_usertours-resettourcontainer"></div>
                <?php
                $footnote = $OUTPUT->get_setting('footnote', 'format_html');
                if (!empty($footnote)) {
                    if ($PAGE->theme->settings->moodledocs) {
                        $footnoteclass = 'col-md-4';
                    } else {
                        $footnoteclass = 'col-md-8';
                    }
                    $footnoteclass .= ' my-md-0 my-2';
                    echo '<div class="'.$footnoteclass.'">'.$footnote.'</div>';
                }
                if ($PAGE->theme->settings->moodledocs) {
                    echo '<div class="col-md-4 my-md-0 my-2 helplink">';
                    echo $OUTPUT->page_doc_link();
                    echo '</div>';
                }
                ?>
                <div class="col-md-4 my-md-0 my-2">
                    <?php echo $OUTPUT->standard_footer_html(); ?>
                </div>
            </div>
            <?php
            $debug = $OUTPUT->debug_footer_html();
            if (!empty($debug)) {
                echo '<div class="row"><div class="col-12 my-md-0 my-2">';
                echo $debug;
                echo '</div>';
            }
            ?>
        </div>
    </div>
</footer>

<div id="back-to-top"><i class="fa fa-angle-up "></i></div>

<?php
// If admin settings page, show template for floating save / discard buttons.
$templatecontext = [
    'topmargin'   => ($PAGE->theme->settings->stickynavbar ? '35px' : '0'),
    'savetext'    => get_string('savebuttontext', 'theme_adaptable'),
    'discardtext' => get_string('discardbuttontext', 'theme_adaptable')
];
if (strstr($PAGE->pagetype, 'admin-setting')) {
    if ($PAGE->theme->settings->enablesavecanceloverlay) {
        echo $OUTPUT->render_from_template('theme_adaptable/savediscard', $templatecontext);
    }
}
echo '</div>'; // End #page.
echo '</div>'; // End #page-wrapper.
echo $OUTPUT->standard_end_of_body_html();
echo $PAGE->theme->settings->jssection;

// Conditional javascript based on a user profile field.
if (!empty($PAGE->theme->settings->jssectionrestrictedprofilefield)) {
    // Get custom profile field setting. (e.g. faculty=fbl).
    $fields = explode('=', $PAGE->theme->settings->jssectionrestrictedprofilefield);
    $ftype = $fields[0];
    $setvalue = $fields[1];

    // Get user profile field (if it exists).
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->dirroot.'/user/lib.php');
    profile_load_data($USER);
    $ftype = "profile_field_$ftype";
    if (isset($USER->$ftype)) {
        if ($USER->$ftype == $setvalue) {
            // Match between user profile field value and value in setting.

            if (!empty($PAGE->theme->settings->jssectionrestricteddashboardonly)) {

                // If this is set to restrict to dashboard only, check if we are on dashboard page.
                if ($PAGE->has_set_url()) {
                    $url = $PAGE->url;
                } else if ($ME !== null) {
                    $url = new moodle_url(str_ireplace('/my/', '/', $ME));
                }

                // In practice, $url should always be valid.
                if ($url !== null) {
                    // Check if this is the dashboard page.
                    if (strstr ($url->raw_out(), '/my/')) {
                        echo $PAGE->theme->settings->jssectionrestricted;
                    }
                }
            } else {
                echo $PAGE->theme->settings->jssectionrestricted;
            }
        }
    }
}
echo $OUTPUT->get_all_tracking_methods();
?>
<script type="text/javascript">
    M.util.js_pending('theme_boost/loader');
        require(['theme_boost/loader'], function() {
        M.util.js_complete('theme_boost/loader');
    });
</script>
</body>
</html>
