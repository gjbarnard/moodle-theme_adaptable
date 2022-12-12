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
 * @package   theme_adaptable
 * @copyright 2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright 2015-2016 Fernando Acedo (3-bits.com)
 * @copyright 2019 G J Barnard (http://moodle.org/user/profile.php?id=442195)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Include header.
global $PAGE, $OUTPUT;

if (!empty($PAGE->theme->settings->loginheader)) {
    require_once(dirname(__FILE__) . '/includes/header.php');
} else {
    require_once(dirname(__FILE__) . '/includes/noheader.php');
}

echo '<div class="container outercont">';
    echo $OUTPUT->page_navbar();
    ?>
    <div id="page-content" class="row">
        <div id="region-main-box" class="col-12">
            <section id="region-main">
            <?php

            $logintextboxtop = $OUTPUT->get_setting('logintextboxtop', 'format_html');
            $logintextboxbottom = $OUTPUT->get_setting('logintextboxbottom', 'format_html');
            $logintextstartwrapper = '';
            $logintextendwrapper = '';
            if ((!empty($logintextboxtop)) || (!empty($logintextboxbottom))) {
                $logintextstartwrapper = '<div class="row justify-content-center"><div class="col-xl-6 col-sm-8">'.
                    '<div class="card"><div class="card-block">';
                $logintextendwrapper = '</div></div></div></div>';
            }

            if (!empty($logintextboxtop)) {
                echo $logintextstartwrapper;
                echo $logintextboxtop;
                echo $logintextendwrapper;
            }

            echo '<div class="login-wrapper"><div class="login-container">';
            echo $OUTPUT->main_content();
            echo '</div></div>';

            if (!empty($logintextboxbottom)) {
                echo '<div class="my-1 my-sm-5"></div>';
                echo $logintextstartwrapper;
                echo $logintextboxbottom;
                echo $logintextendwrapper;
            }

            ?>
            </section>
        </div>
    </div>
</div>

<?php
// Include footer.
if (!empty($PAGE->theme->settings->loginfooter)) {
    require_once(dirname(__FILE__) . '/includes/footer.php');
} else {
    require_once(dirname(__FILE__) . '/includes/nofooter.php');
}
