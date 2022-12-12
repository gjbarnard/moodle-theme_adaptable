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
 * Head
 *
 * @package   theme_adaptable
 * @copyright 2020 G J Barnard (http://moodle.org/user/profile.php?id=442195)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

// HTML head.
echo $OUTPUT->standard_head_html();
$siteurl = new moodle_url('');
?>
    <!-- CSS print media -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Twitter Card data -->
    <meta name="twitter:card" value="summary">
    <meta name="twitter:site" value="<?php echo $SITE->fullname; ?>" />
    <meta name="twitter:title" value="<?php echo $OUTPUT->page_title(); ?>" />

    <!-- Open Graph data -->
    <meta property="og:title" content="<?php echo $OUTPUT->page_title(); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $siteurl->out(); ?>" />
    <meta name="og:site_name" value="<?php echo $SITE->fullname; ?>" />

    <!-- Chrome, Firefox OS and Opera on Android topbar color -->
    <meta name="theme-color" content="<?php echo $PAGE->theme->settings->maincolor; ?>" />

    <!-- Windows Phone topbar color -->
    <meta name="msapplication-navbutton-color" content="<?php echo $PAGE->theme->settings->maincolor; ?>" />

    <!-- iOS Safari topbar color -->
    <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo $PAGE->theme->settings->maincolor; ?>" />

    <?php
    if (!empty($PAGE->theme->settings->googlefonts)) {
        $fontssubset = '';

        $fontsettings = array('fontname', 'fontheadername', 'fonttitlename');
        $fontstoload = array();
        foreach ($fontsettings as $fontsetting) {
            switch ($PAGE->theme->settings->$fontsetting) {
                case 'sans-serif':
                break;
                default:
                    // Google font name.
                    $fontname = str_replace(" ", "+", $PAGE->theme->settings->$fontsetting);
                    if (!in_array($fontname, $fontstoload)) {
                        $fontstoload[] = $fontname;
                    }
                break;
            }
        }

        if (!empty($fontstoload)) {
            // Get the Google Font weights.
            $fontweight = ':'.$PAGE->theme->settings->fontweight.','.$PAGE->theme->settings->fontweight.'i';
            $fontheaderweight = ':'.$PAGE->theme->settings->fontheaderweight.','.$PAGE->theme->settings->fontheaderweight.'i';
            $fonttitleweight = ':'.$PAGE->theme->settings->fonttitleweight.','.$PAGE->theme->settings->fonttitleweight.'i';

            // Get the Google fonts subset.
            if (!empty($PAGE->theme->settings->fontsubset)) {
                $fontssubset = '&subset='.$PAGE->theme->settings->fontsubset;
            }

            // Load Google fonts.
            echo '<!-- Load Google Fonts -->';
            foreach ($fontstoload as $googlefontname) {
                echo '<link href="https://fonts.googleapis.com/css?family=';
                echo $googlefontname.$fontweight.$fontssubset;
                echo '" rel="stylesheet" type="text/css">';
            }
        }
    }
    ?>
</head>
<?php
