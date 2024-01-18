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
 * No header
 *
 * @package    theme_adaptable
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

$bodyclasses = [];
$bodyclasses[] = 'theme_adaptable';
$bodyclasses[] = 'two-column';
$standardscreenwidthclass = 'standard';
if (!empty($PAGE->theme->settings->standardscreenwidth)) {
    $bodyclasses[] = $PAGE->theme->settings->standardscreenwidth;
} else {
    $bodyclasses[] = 'standard';
}

theme_adaptable_initialise_full();
$bodyclasses[] = theme_adaptable_get_full();

// Include header.
require_once(dirname(__FILE__) . '/head.php');
?>

<body <?php echo $OUTPUT->body_attributes($bodyclasses); ?>>

<?php
echo $OUTPUT->standard_top_of_body_html();
?>

<div id="page-wrapper">
    <div id="page" class="container-fluid">

    <?php
    // Display alerts.
    echo $OUTPUT->get_alert_messages();
