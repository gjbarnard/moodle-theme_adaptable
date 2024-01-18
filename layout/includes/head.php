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
 * @package    theme_adaptable
 * @copyright  2020, 2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

$headcontext = new stdClass;
$headcontext->output = $OUTPUT;
$headcontext->sitefullname = $SITE->fullname;
$headcontext->pagetitle = $OUTPUT->page_title();
$siteurl = new moodle_url('');
$headcontext->siteurl = $siteurl->out();
$headcontext->maincolor = $PAGE->theme->settings->maincolor;

if (!empty($PAGE->theme->settings->googlefonts)) {
    // Select fonts used.
    $fontssubset = '';
    if (!empty($PAGE->theme->settings->fontsubset)) {
        // Get the Google fonts subset.
        $fontssubset = '&subset='.$PAGE->theme->settings->fontsubset;
    }

    if (!empty($PAGE->theme->settings->fontname)) {
        switch ($PAGE->theme->settings->fontname) {
            case 'default':
            case 'sans-serif':
                // Use 'sans-serif'.
            break;

            default:
                // Get the Google main font.
                $fontname = str_replace(" ", "+", $PAGE->theme->settings->fontname);
                $fontweight = ':'.$PAGE->theme->settings->fontweight.','.$PAGE->theme->settings->fontweight.'i';
                $headcontext->fontname = $fontname.$fontweight.$fontssubset;
            break;
        }
    }

    if (!empty($PAGE->theme->settings->fontheadername)) {
        switch ($PAGE->theme->settings->fontheadername) {
            case 'default':
            case 'sans-serif':
                // Use 'sans-serif'.
            break;

            default:
                // Get the Google header font.
                $fontheadername = str_replace(" ", "+", $PAGE->theme->settings->fontheadername);
                $fontheaderweight = ':'.$PAGE->theme->settings->fontheaderweight.','.$PAGE->theme->settings->fontheaderweight.'i';
                $headcontext->fontheadername = $fontheadername.$fontheaderweight.$fontssubset;
            break;
        }
    }

    if (!empty($PAGE->theme->settings->fonttitlename)) {
        switch ($PAGE->theme->settings->fonttitlename) {
            case 'default':
            case 'sans-serif':
                // Use 'sans-serif'.
            break;

            default:
                // Get the Google title font.
                $fonttitlename = str_replace(" ", "+", $PAGE->theme->settings->fonttitlename);
                $fonttitleweight = ':'.$PAGE->theme->settings->fonttitleweight.','.$PAGE->theme->settings->fonttitleweight.'i';
                $headcontext->fonttitlename = $fonttitlename.$fonttitleweight.$fontssubset;
            break;
        }
    }
}
echo $OUTPUT->render_from_template('theme_adaptable/head', $headcontext);
