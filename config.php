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
 * Config.
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

// The plugin internal name.
$THEME->name = 'adaptable';

// The frontpage regions.
$frontlayoutregions = [
    'side-post',
    'hidden',
];

if (!empty(get_config('theme_adaptable', 'frontpageblocksenabled'))) {
    $themesettings = new stdClass();
    $settingnameprefix = 'blocklayoutlayoutrow';
    $totalrows = 5;
    for ($row = 1; $row <= $totalrows; $row++) {
        $settingname = $settingnameprefix . $row;
        $themesettings->$settingname = get_config('theme_adaptable', $settingname);
    }

    $helper = \theme_adaptable\toolbox::admin_settings_layout_helper(
        $settingnameprefix,
        $totalrows,
        $themesettings,
        'frnt-market-'
    );

    foreach ($helper['rows'] as $row) {
        foreach (array_keys($row) as $blockregion) {
            $frontlayoutregions[] = $blockregion;
        }
    }
}

if (get_config('theme_adaptable', 'informationblocksenabled')) {
    $frontlayoutregions[] = 'information';
}

// The dashboard regions.
$dashboardlayoutregions = [
    'side-post',
    'hidden',
];

if (!empty(get_config('theme_adaptable', 'dashblocksenabled'))) {
    $themesettings = new stdClass();
    $settingnameprefix = 'dashblocklayoutlayoutrow';
    $totalrows = 5;
    for ($row = 1; $row <= $totalrows; $row++) {
        $settingname = $settingnameprefix . $row;
        $themesettings->$settingname = get_config('theme_adaptable', $settingname);
    }

    $helper = \theme_adaptable\toolbox::admin_settings_layout_helper(
        $settingnameprefix,
        $totalrows,
        $themesettings,
        'dash-blocks-'
    );

    foreach ($helper['rows'] as $row) {
        foreach (array_keys($row) as $blockregion) {
            $dashboardlayoutregions[] = $blockregion;
        }
    }
}

if (get_config('theme_adaptable', 'tabbedlayoutdashboard')) {
    $dashboardlayoutregions[] = 'my-tab-one-a';
    $dashboardlayoutregions[] = 'my-tab-two-a';
    $frontlayoutregions[] = 'my-tab-one-a';
    $frontlayoutregions[] = 'my-tab-two-a';
}

// The course page regions.
$courselayoutregions = [
    'side-post',
    'hidden',
];

if (get_config('theme_adaptable', 'coursepageblockactivitybottomenabled')) {
    $courselayoutregions[] = 'course-section-a';
    $frontlayoutregions[] = 'course-section-a';
}

if (get_config('theme_adaptable', 'coursepageblockinfoenabled')) {
    $courselayoutregions[] = 'news-slider-a';
    $frontlayoutregions[] = 'news-slider-a';
}

if (get_config('theme_adaptable', 'coursepageblocksenabled')) {
    $courselayoutregions = array_merge(
        $courselayoutregions,
        [
            'course-top-a',
            'course-top-b',
            'course-top-c',
            'course-top-d',
            'course-bottom-a',
            'course-bottom-b',
            'course-bottom-c',
            'course-bottom-d',
        ]
    );
}

if (get_config('theme_adaptable', 'tabbedlayoutcoursepage')) {
    $courselayoutregions[] = 'course-tab-one-a';
    $courselayoutregions[] = 'course-tab-one-b';
    $frontlayoutregions[] = 'course-tab-one-a';
    $frontlayoutregions[] = 'course-tab-one-b';
}

$standardregions = ['side-post'];

// The theme HTML DOCTYPE.
$THEME->doctype = 'html5';

// Theme parent.
$THEME->parents = ['boost'];

// Styles.
$THEME->sheets = [
    'custom',
];

$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = [];
$THEME->editor_sheets = [];

$THEME->plugins_exclude_sheets = [
    'block' => [
        'html',
    ],
];

// Disabling block docking.
$THEME->enable_dock = false;

// Call the renderer.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Load the theme layouts.
$THEME->layouts = [
    // Most backwards compatible layout without the blocks - this is the layout used by default.
    'base' => [
        'file' => 'columns2.php',
        'regions' => [],
    ],
    // Standard layout with blocks, this is recommended for most pages with general information.
    'standard' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // Main course page.
    'course' => [
        'file' => 'course.php',
        'regions' => $courselayoutregions,
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => [
        'file' => 'incourse.php',
        'regions' => array_merge($standardregions, ['course-section-a']),
        'defaultregion' => 'side-post',
    ],
    // The site home page.
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => $frontlayoutregions,
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    // Server administration scripts.
    'admin' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // My courses page.
    'mycourses' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    // My dashboard page.
    'mydashboard' => [
        'file' => 'dashboard.php',
        'regions' => $dashboardlayoutregions,
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    // My public page.
    'mypublic' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // Login page.
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true, 'nonavbar' => true],
    ],
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true],
    ],
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nocoursefooter' => true],
    ],
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    /* Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
       This must not have any blocks, and it is good idea if it does not have links to
       other places - for example there should not be a home link in the footer... */
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true, 'nocoursefooter' => true, 'nocourseheader' => true],
    ],
    // Should display the content and basic headers only.
    'print' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false],
    ],
    // The pagelayout used when a redirection is occuring.
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // The pagelayout used for reports.
    'report' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // The pagelayout used for safebrowser and securewindow.
    'secure' => [
        'file' => 'secure.php',
        'regions' => array_merge($standardregions, ['course-section-a']),
        'defaultregion' => 'side-post',
        'options' => ['nofooter' => true, 'nonavbar' => true],
    ],
];

// Select the opposite sidebar when switch to RTL.
$THEME->blockrtlmanipulations = [
    'side-pre' => 'side-post',
    'side-post' => 'side-pre',
];

$THEME->prescsscallback = 'theme_adaptable_pre_scss';
$THEME->scss = function (theme_config $theme) {
    return theme_adaptable_get_main_scss_content($theme);
};

$THEME->csspostprocess = 'theme_adaptable_process_css';
$THEME->haseditswitch = false;
$THEME->usescourseindex = true;
$THEME->iconsystem = '\\theme_adaptable\\output\\icon_system_fontawesome';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
