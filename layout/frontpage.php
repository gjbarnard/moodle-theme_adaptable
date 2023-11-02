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
 * Frontpage
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

defined('MOODLE_INTERNAL') || die;

$sidepostdrawer = false;
if ((\theme_adaptable\toolbox::get_setting('frontpageuserblocksenabled')) || (is_siteadmin($USER))) {
    $sidepostdrawer = true;
}

// Let's go to include first the common header file.
require_once(dirname(__FILE__) . '/includes/header.php');
// Include secondary navigation.
require_once(dirname(__FILE__) . '/includes/secondarynav.php');

if (!empty($secondarynavigation)) {
    echo $secondarynavigation;
}
if (!empty($overflow)) {
    echo $overflow;
}

echo $OUTPUT->get_news_ticker();

// Slider.
echo $OUTPUT->get_frontpage_slider();

// And let's show Infobox 1 if enabled.
if (!empty(\theme_adaptable\toolbox::get_setting('infobox'))) {
    if (!empty(\theme_adaptable\toolbox::get_setting('infoboxfullscreen'))) {
        echo '<div id="theinfo">';
    } else {
        echo '<div id="theinfo" class="container">';
    }
    echo '<div class="row">';
    echo \theme_adaptable\toolbox::get_setting('infobox', 'format_moodle');
    echo '</div>';
    echo '</div>';
}

// If Marketing Blocks are enabled then let's show them.
if (!empty(\theme_adaptable\toolbox::get_setting('frontpagemarketenabled'))) {
    echo $OUTPUT->get_marketing_blocks();
}

if (!empty(\theme_adaptable\toolbox::get_setting('frontpageblocksenabled'))) { ?>
    <div id="frontblockregion" class="container">
        <div class="row">
            <?php echo $OUTPUT->get_block_regions(); ?>
        </div>
    </div>
    <?php
}

// And finally let's show the Infobox 2 if enabled.
if (!empty(\theme_adaptable\toolbox::get_setting('infobox2'))) {
    if (!empty(\theme_adaptable\toolbox::get_setting('infoboxfullscreen'))) {
        echo '<div id="theinfo2">';
    } else {
        echo '<div id="theinfo2" class="container">';
    }
    echo '<div class="row">';
    echo \theme_adaptable\toolbox::get_setting('infobox2', 'format_moodle');
    echo '</div>';
    echo '</div>';
}

// The main content goes here.
?>
<div id="maincontainer" class="container outercont">
    <div id="page-content" class="row">
        <div id="page-navbar" class="col-12">
            <nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
        </div>

        <div id="region-main-box" class="col-12">
            <section id="region-main">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
            </section>
        </div>
    </div>

<?php
// Let's show the hidden blocks region ONLY for administrators.
if (is_siteadmin()) {
    ?>
    <div class="hidden-blocks">
        <div class="row">

        <?php
        if (!empty($PAGE->theme->settings->coursepageblocksliderenabled)) {
            echo $OUTPUT->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
        }

        if (!empty($PAGE->theme->settings->coursepageblockactivitybottomenabled)) {
            echo $OUTPUT->get_block_regions('customrowsetting', 'course-section-', '12-0-0-0');
        }

        if (!empty($PAGE->theme->settings->tabbedlayoutcoursepage)) {
            echo $OUTPUT->get_block_regions('customrowsetting', 'course-tab-one-', '12-0-0-0');
            echo $OUTPUT->get_block_regions('customrowsetting', 'course-tab-two-', '12-0-0-0');
        }

        if (!empty($PAGE->theme->settings->tabbedlayoutdashboard)) {
            echo $OUTPUT->get_block_regions('customrowsetting', 'my-tab-one-', '12-0-0-0');
            echo $OUTPUT->get_block_regions('customrowsetting', 'my-tab-two-', '12-0-0-0');
        }

        ?>

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
// And to finish, we include the common footer file.
require_once(dirname(__FILE__) . '/includes/footer.php');
