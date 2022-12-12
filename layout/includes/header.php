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
 * @copyright 2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright 2015-2019 Fernando Acedo (3-bits.com)
 * @copyright 2017-2019 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

$PAGE->set_secondary_navigation(false);

/* Check if this is a course or module page and check setting to hide site title.
   If not one of these pages, by default show it (set $hidesitetitle to false). */
if ( (strstr($PAGE->pagetype, 'course')) ||
     (strstr($PAGE->pagetype, 'mod')) && ($this->page->course->id > 1) ) {
    $hidesitetitle = !empty(($PAGE->theme->settings->coursepageheaderhidesitetitle)) ? true : false;
} else {
    $hidesitetitle = false;
}

// Screen size.
theme_adaptable_initialise_zoom();
$setzoom = theme_adaptable_get_zoom();

theme_adaptable_initialise_full();
$setfull = theme_adaptable_get_full();

$bsoptionsdata = array('data' => array());

// Main navbar.
if (isset($PAGE->theme->settings->stickynavbar) && $PAGE->theme->settings->stickynavbar == 1
    && $PAGE->pagetype != "grade-report-grader-index" && $PAGE->bodyid != "page-grade-report-grader-index") {
    $fixedheader = true;
    $bsoptionsdata['data']['stickynavbar'] = true;
} else {
    $bsoptionsdata['data']['stickynavbar'] = false;
}

// JS calls.
$PAGE->requires->js_call_amd('theme_adaptable/adaptable', 'init');
$PAGE->requires->js_call_amd('theme_adaptable/bsoptions', 'init', $bsoptionsdata);
$PAGE->requires->js_call_amd('theme_adaptable/drawer', 'init');

// Layout.
$left = (!right_to_left());  // To know if to add 'pull-right' and 'desktop-first-column' classes in the layout for LTR.

// Navbar Menu.
$shownavbar = false;
if ((isloggedin() && !isguestuser()) ||
    (!empty($PAGE->theme->settings->enablenavbarwhenloggedout)) ) {

    // Show navbar unless disabled by config.
    if (empty($PAGE->layout_options['nonavbar'])) {
        $shownavbar = true;
    }
}
// Load header background image if it exists.
$headerbg = '';
if (!empty($PAGE->theme->settings->categoryhavecustomheader)) {
    $currenttopcat = \theme_adaptable\toolbox::get_current_top_level_catetgory();
    if (!empty($currenttopcat)) {
        $categoryheaderbgimageset = 'categoryheaderbgimage'.$currenttopcat;
        if (!empty($PAGE->theme->settings->$categoryheaderbgimageset)) {
            $headerbg = ' class="headerbgimage" style="background-image: ' .
            'url(\''.$PAGE->theme->setting_file_url($categoryheaderbgimageset, $categoryheaderbgimageset).'\');"';
        }
    }
} else {
    $currenttopcat = false;
}
if ((empty($headerbg)) && (!empty($PAGE->theme->settings->headerbgimage))) {
    $headerbg = ' class="headerbgimage" style="background-image: ' .
    'url(\''.$PAGE->theme->setting_file_url('headerbgimage', 'headerbgimage').'\');"';
}

/* Choose the header style.  There styles available are:
   "style1"  (original header)
   "style2"  (2 row header).
*/

if (!empty($PAGE->theme->settings->headerstyle)) {
    $adaptableheaderstyle = $PAGE->theme->settings->headerstyle;
} else {
    $adaptableheaderstyle = "style1";
}

// Social icons class.
$showicons = $PAGE->theme->settings->blockicons;
if ($showicons == 1) {
    $showiconsclass = "showblockicons";
} else {
    $showiconsclass = " ";
}

$standardscreenwidthclass = 'standard';
if (!empty($PAGE->theme->settings->standardscreenwidth)) {
    $standardscreenwidthclass = $PAGE->theme->settings->standardscreenwidth;
}

// HTML header.
echo $OUTPUT->doctype();
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="icon" href="<?php echo $OUTPUT->favicon(); ?>" />

<?php
// Include header.
require_once(dirname(__FILE__) . '/head.php');

// If it is a mobile and the header is not hidden or it is a desktop there will be a page header.
$pageheader = 'has-page-header';

$hasheaderbg = '';
if (!empty($headerbg)) {
    $hasheaderbg = 'has-header-bg';
}

$nomobilenavigation = '';
if (!empty($PAGE->theme->settings->responsivesectionnav)) {
    $nomobilenavigation = 'nomobilenavigation';
}
?>
<body <?php echo $OUTPUT->body_attributes(array('theme_adaptable', 'two-column', $setzoom, 'header-'.$adaptableheaderstyle,
    $pageheader, $hasheaderbg, $nomobilenavigation)); ?>>

<?php
echo $OUTPUT->standard_top_of_body_html();

// Development or wrong moodle version alert.
// echo $OUTPUT->get_dev_alert();.
?>

<div id="page-wrapper">
    <div id="page" class="<?php echo "$setfull $showiconsclass $standardscreenwidthclass"; ?>">
    <?php
    echo $OUTPUT->get_alert_messages();

    $headercontext = [
        'output' => $OUTPUT
    ];

    if ((!isloggedin() || isguestuser()) && ($PAGE->pagetype != "login-index")) {
        if ($PAGE->theme->settings->displaylogin != 'no') {
            $loginformcontext = [
                'displayloginbox' => ($PAGE->theme->settings->displaylogin == 'box') ? true : false,
                'output' => $OUTPUT,
                'token' => s(\core\session\manager::get_login_token()),
                'url' => new moodle_url('/login/index.php')
            ];
            if (!$loginformcontext['displayloginbox']) {
                $authsequence = get_enabled_auth_plugins(); // Get all auths.
                if (in_array('oidc', $authsequence)) {
                    $authplugin = get_auth_plugin('oidc');
                    $oidc = $authplugin->loginpage_idp_list($this->page->url->out(false));
                    if (!empty($oidc)) {
                        $loginformcontext['hasoidc'] = true;
                        $loginformcontext['oidcdata'] = \auth_plugin_base::prepare_identity_providers_for_output($oidc, $OUTPUT);
                    }
                }
            }
            $headercontext['loginoruser'] = '<li class="nav-item">'.
                $OUTPUT->render_from_template('theme_adaptable/headerloginform', $loginformcontext).'</li>';
        } else {
            $headercontext['loginoruser'] = '';
        }
    } else {
        // Display user profile menu.
        // Only used when user is logged in and not on the secure layout.
        if ((isloggedin()) && ($PAGE->pagelayout != 'secure')) {
            // User icon.
            $userpic = $OUTPUT->user_picture($USER, array('link' => false, 'visibletoscreenreaders' => false,
                'size' => 35, 'class' => 'userpicture'));
            // User name.
            $username = format_string(fullname($USER));

            // User menu dropdown.
            if (!empty($PAGE->theme->settings->usernameposition)) {
                $usernameposition = $PAGE->theme->settings->usernameposition;
                if ($usernameposition == 'right') {
                    $usernamepositionleft = false;
                } else {
                    $usernamepositionleft = true;
                }
            } else {
                $usernamepositionleft = true;
            }

            // Set template context.
            $usermenucontext = [
                'username' => $username,
                'userpic' => $userpic,
                'showusername' => $PAGE->theme->settings->showusername,
                'usernamepositionleft' => $usernamepositionleft,
                'userprofilemenu' => $OUTPUT->user_profile_menu(),
            ];
            $usermenu = $OUTPUT->render_from_template('theme_adaptable/usermenu', $usermenucontext);
            $headercontext['loginoruser'] = '<li class="nav-item dropdown ml-3 ml-md-2 mr-2 mr-md-0">'.$usermenu.'</li>';
        } else {
            $headercontext['loginoruser'] = '';
        }
    }

    if (!$hidesitetitle) {
        $headercontext['sitelogo'] = $OUTPUT->get_logo($currenttopcat, $shownavbar);
        $headercontext['sitetitle'] = $OUTPUT->get_title($currenttopcat);
    }

    $headercontext['headerbg'] = $headerbg;
    $headercontext['shownavbar'] = $shownavbar;

    // Navbar Menu.
    if ($shownavbar) {
        $headercontext['shownavbar'] = [
            'disablecustommenu' => (!empty($PAGE->theme->settings->disablecustommenu)),
            'navigationmenu' => $OUTPUT->navigation_menu('main-navigation'),
            'navigationmenudrawer' => $OUTPUT->navigation_menu('main-navigation-drawer'),
            'output' => $OUTPUT,
            'toolsmenu' => ($PAGE->theme->settings->enabletoolsmenus)
        ];

        if ($PAGE->theme->settings->enabletoolsmenus) {
            $headercontext['shownavbar']['toolsmenudrawer'] = $OUTPUT->tools_menu('tools-menu-drawer');
        }

        $navbareditsettings = $PAGE->theme->settings->editsettingsbutton;
        $headercontext['shownavbar']['showcog'] = true;
        $showeditbuttons = false;

        if ($navbareditsettings == 'button') {
            $showeditbuttons = true;
            $headercontext['shownavbar']['showcog'] = false;
        } else if ($navbareditsettings == 'cogandbutton') {
            $showeditbuttons = true;
        }

        if ($headercontext['shownavbar']['showcog']) {
            $headercontext['shownavbar']['coursemenucontent'] = $OUTPUT->context_header_settings_menu();
            $headercontext['shownavbar']['othermenucontent'] = $OUTPUT->region_main_settings_menu();
        }

        /* Ensure to only hide the button on relevant pages.  Some pages will need the button, such as the
           dashboard page. Checking if the cog is being displayed above to figure out if it still needs to
           show (when there is no cog). Also show mod pages (e.g. Forum, Lesson) as these sometimes have
           a button for a specific purpose. */
        if (($showeditbuttons) ||
            (($headercontext['shownavbar']['showcog']) &&
            ((empty($headercontext['shownavbar']['coursemenucontent'])) &&
            (empty($headercontext['shownavbar']['othermenucontent'])))) ||
            (strstr($PAGE->pagetype, 'mod-'))) {
            $headercontext['shownavbar']['pageheadingbutton'] = $OUTPUT->page_heading_button();
        }

        if (isloggedin()) {
            if (!empty($this->page->theme->settings->enableshowhideblocks)) {
                $zoomside = ((!empty($this->page->theme->settings->blockside)) &&
                    ($this->page->theme->settings->blockside == 1)) ? 'left' : 'right';
                $hidetitle = get_string('hideblocks', 'theme_adaptable');
                $showtitle = get_string('showblocks', 'theme_adaptable');
                if ($setzoom == 'zoomin') { // Blocks not shown.
                    $zoominicontitle = $showtitle;
                    if ($zoomside == 'right') {
                        $icontype = 'outdent';
                    } else {
                        $icontype = 'indent';
                    }
                } else {
                    $zoominicontitle = $hidetitle;
                    if ($zoomside == 'right') {
                        $icontype = 'indent';
                    } else {
                        $icontype = 'outdent';
                    }
                }
                $headercontext['shownavbar']['showhideblocks'] = true;
                $headercontext['shownavbar']['showhideblockszoomside'] = $zoomside;
                $headercontext['shownavbar']['showhideblockszoominicontitle'] = $zoominicontitle;
                $headercontext['shownavbar']['showhideblockshidetitle'] = $hidetitle;
                $headercontext['shownavbar']['showhideblocksshowtitle'] = $showtitle;
                $headercontext['shownavbar']['showhideblocksicontype'] = $icontype;
                $headercontext['shownavbar']['showhideblockstext'] = ($PAGE->theme->settings->enableshowhideblockstext);

                $PAGE->requires->js_call_amd('theme_adaptable/zoomin', 'init');
            }
            if ($PAGE->theme->settings->enablezoom) {
                $headercontext['shownavbar']['enablezoom'] = true;
                $headercontext['shownavbar']['enablezoomshowtext'] = ($PAGE->theme->settings->enablezoomshowtext);
            }
        }
    }
    $headercontext['topmenus'] = $OUTPUT->get_top_menus(false);

    if ($adaptableheaderstyle == "style1") {
        $headercontext['menuslinkright'] = (!empty($PAGE->theme->settings->menuslinkright));
        $headercontext['langmenu'] = (empty($PAGE->layout_options['langmenu']) || $PAGE->layout_options['langmenu']);
        $headercontext['responsiveheader'] = $PAGE->theme->settings->responsiveheader;

        if (!empty($PAGE->theme->settings->pageheaderlayout)) {
            $headercontext['pageheaderoriginal'] = ($PAGE->theme->settings->pageheaderlayout == 'original');
        } else {
            $headercontext['pageheaderoriginal'] = true;
        }

        $headersearchandsocial = (!empty($PAGE->theme->settings->headersearchandsocial)) ? $PAGE->theme->settings->headersearchandsocial : 'none';

        // Search box and social icons.
        switch ($headersearchandsocial) {
            case 'socialheader':
                $headersocialcontext = [
                    'classes' => $PAGE->theme->settings->responsivesocial,
                    'pageheaderoriginal' => $headercontext['pageheaderoriginal'],
                    'output' => $OUTPUT
                ];
                $headercontext['searchandsocialheader'] = $OUTPUT->render_from_template('theme_adaptable/headersocial', $headersocialcontext);
            break;
            case 'searchmobilenav':
                $headercontext['searchandsocialnavbar'] = $OUTPUT->search_box();
                $headercontext['searchandsocialnavbarextra'] = ' d-md-block d-lg-none my-auto';
                $headersearchcontext = [
                    'pagelayout' => ($headercontext['pageheaderoriginal']) ? 'pagelayoutoriginal' : 'pagelayoutalternative',
                    'search' => $OUTPUT->search_box()
                ];
                $headercontext['searchandsocialheader'] = $OUTPUT->render_from_template('theme_adaptable/headersearch', $headersearchcontext);
            break;
            case 'searchheader':
                $headersearchcontext = [
                    'pagelayout' => ($headercontext['pageheaderoriginal']) ? 'pagelayoutoriginal' : 'pagelayoutalternative',
                    'search' => $OUTPUT->search_box()
                ];
                $headercontext['searchandsocialheader'] = $OUTPUT->render_from_template('theme_adaptable/headersearch', $headersearchcontext);
            break;
            case 'searchnavbar':
                $headercontext['searchandsocialnavbar'] = $OUTPUT->search_box();
            break;
            case 'searchnavbarsocialheader':
                $headercontext['searchandsocialnavbar'] = $OUTPUT->search_box();
                $headersocialcontext = [
                    'classes' => $PAGE->theme->settings->responsivesocial,
                    'pageheaderoriginal' => $headercontext['pageheaderoriginal'],
                    'output' => $OUTPUT
                ];
                $headercontext['searchandsocialheader'] = $OUTPUT->render_from_template('theme_adaptable/headersocial', $headersocialcontext);
            break;
        }

        echo $OUTPUT->render_from_template('theme_adaptable/headerstyleone', $headercontext);
    } else if ($adaptableheaderstyle == "style2") {
        if (!empty($PAGE->theme->settings->pageheaderlayouttwo)) {
            $headercontext['pageheaderoriginal'] = ($PAGE->theme->settings->pageheaderlayouttwo == 'original');
        } else {
            $headercontext['pageheaderoriginal'] = true;
        }

        if ($headercontext['pageheaderoriginal']) {
            $headercontext['navbarsearch'] = $OUTPUT->search_box();
        }

        if (empty($PAGE->layout_options['langmenu']) || $PAGE->layout_options['langmenu']) {
            $headercontext['langmenu'] = $OUTPUT->lang_menu(false);
        }

        echo $OUTPUT->render_from_template('theme_adaptable/headerstyletwo', $headercontext);
    }

    // Display News Ticker.
    echo $OUTPUT->get_news_ticker();
