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
 * Header
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Header heading.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_header', get_string('headersettings', 'theme_adaptable'));

    // Header layout section.
    $page->add(new admin_setting_heading(
        'theme_adaptable_headerstyle_heading',
        get_string('headerstyleheading', 'theme_adaptable'),
        format_text(get_string('headerstyleheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Adaptable header style selection.
    $name = 'theme_adaptable/headerstyle';
    $title = get_string('headerstyle', 'theme_adaptable');
    $description = get_string('headerstyledesc', 'theme_adaptable');
    $radchoices = [
        'style1' => get_string('headerstyle1', 'theme_adaptable'),
        'style2' => get_string('headerstyle2', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'style1', $radchoices);
    $page->add($setting);

    // Page header layout for header one.
    $name = 'theme_adaptable/pageheaderlayout';
    $title = get_string('pageheaderlayout', 'theme_adaptable');
    $description = get_string('pageheaderlayoutdesc', 'theme_adaptable');
    $radchoices = [
        'original' => get_string('pageheaderoriginal', 'theme_adaptable'),
        'alternative' => get_string('pageheaderalternative', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'original', $radchoices);
    $page->add($setting);

    // Page header layout for header two.
    $name = 'theme_adaptable/pageheaderlayouttwo';
    $title = get_string('pageheaderlayouttwo', 'theme_adaptable');
    $description = get_string('pageheaderlayouttwodesc', 'theme_adaptable');
    $radchoices = [
        'original' => get_string('pageheaderoriginal', 'theme_adaptable'),
        'nosearch' => get_string('pageheadernosearch', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'original', $radchoices);
    $page->add($setting);

    // Top header row background color.
    $name = 'theme_adaptable/headertoprowbkcolour';
    $title = get_string('headertoprowbkcolour', 'theme_adaptable');
    $description = get_string('headertoprowbkcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00796B', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Top header row text color.
    $name = 'theme_adaptable/headertoprowtextcolour';
    $title = get_string('headertoprowtextcolour', 'theme_adaptable');
    $description = get_string('headertoprowtextcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Top header row dividing line colour.
    $name = 'theme_adaptable/headertoprowdividingline';
    $title = get_string('headertoprowdividingline', 'theme_adaptable');
    $description = get_string('headertoprowdividinglinedesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main header row background color.
    $name = 'theme_adaptable/headermainrowbkcolour';
    $title = get_string('headermainrowbkcolour', 'theme_adaptable');
    $description = get_string('headermainrowbkcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#009688', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Main header row text color.
    $name = 'theme_adaptable/headermainrowtextcolour';
    $title = get_string('headermainrowtextcolour', 'theme_adaptable');
    $description = get_string('headermainrowtextcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header main row minimum height.
    $name = 'theme_adaptable/headermainrowminheight';
    $title = get_string('headermainrowminheight', 'theme_adaptable');
    $description = get_string('headermainrowminheightdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, '72px');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_adaptable_header',
        get_string('headersettingsheading', 'theme_adaptable'),
        format_text(get_string('headerdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Site title.
    $name = 'theme_adaptable/sitetitle';
    $title = get_string('sitetitle', 'theme_adaptable');
    $description = get_string('sitetitledesc', 'theme_adaptable');
    $radchoices = [
        'disabled' => get_string('sitetitleoff', 'theme_adaptable'),
        'default' => get_string('sitetitledefault', 'theme_adaptable'),
        'custom' => get_string('sitetitlecustom', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'default', $radchoices);
    $page->add($setting);

    // Site title text.
    $name = 'theme_adaptable/sitetitletext';
    $title = get_string('sitetitletext', 'theme_adaptable');
    $description = get_string('sitetitletextdesc', 'theme_adaptable');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $page->add($setting);

    // Display Course title.
    $name = 'theme_adaptable/enablecoursetitle';
    $title = get_string('enablecoursetitle', 'theme_adaptable');
    $description = get_string('enablecoursetitledesc', 'theme_adaptable');
    $radchoices = [
        'fullname' => get_string('coursetitlefullname', 'theme_adaptable'),
        'shortname' => get_string('coursetitleshortname', 'theme_adaptable'),
        'off' => get_string('hide'),
    ];
    $default = 'fullname';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $radchoices);
    $page->add($setting);

    // Course Title Maximum Width.
    $name = 'theme_adaptable/coursetitlemaxwidth';
    $title = get_string('coursetitlemaxwidth', 'theme_adaptable');
    $description = get_string('coursetitlemaxwidthdesc', 'theme_adaptable');
    $setting = new admin_setting_configtext($name, $title, $description, 20, PARAM_INT);
    $page->add($setting);

    // Title Font Name.
    $name = 'theme_adaptable/fonttitlename';
    $title = get_string('fonttitlename', 'theme_adaptable');
    $description = get_string('fonttitlenamedesc', 'theme_adaptable');
    $default = 'default';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $fontlist);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title Font size.
    $name = 'theme_adaptable/fonttitlesize';
    $title = get_string('fonttitlesize', 'theme_adaptable');
    $description = get_string('fonttitlesizedesc', 'theme_adaptable');
    $default = '48px';
    $setting = new admin_setting_configselect($name, $title, $description, $default, $standardfontsize);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title Font weight.
    $name = 'theme_adaptable/fonttitleweight';
    $title = get_string('fonttitleweight', 'theme_adaptable');
    $description = get_string('fonttitleweightdesc', 'theme_adaptable');
    $setting = new admin_setting_configselect($name, $title, $description, 400, $from100to900);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Title font color.
    $name = 'theme_adaptable/fonttitlecolor';
    $title = get_string('fonttitlecolor', 'theme_adaptable');
    $description = get_string('fonttitlecolordesc', 'theme_adaptable');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', null);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header image.
    $name = 'theme_adaptable/headerbgimage';
    $title = get_string('headerbgimage', 'theme_adaptable');
    $description = get_string('headerbgimagedesc', 'theme_adaptable');
    $setting = new \theme_adaptable\admin_setting_configstoredfiles(
        $name,
        $title,
        $description,
        'headerbgimage',
        ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
    );
    $page->add($setting);

    // Header image text colour.
    $name = 'theme_adaptable/headerbgimagetextcolour';
    $title = get_string('headerbgimagetextcolour', 'theme_adaptable');
    $description = get_string('headerbgimagetextcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Logo.
    $name = 'theme_adaptable/logo';
    $title = get_string('logo', 'theme_adaptable');
    $description = get_string('logodesc', 'theme_adaptable');
    $setting = new \theme_adaptable\admin_setting_configstoredfiles(
        $name,
        $title,
        $description,
        'logo',
        ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
    );
    $page->add($setting);

    // Logo description text.
    $name = 'theme_adaptable/logoalt';
    $title = get_string('logoalt', 'theme_adaptable');
    $description = get_string('logoaltdesc', 'theme_adaptable');
    $default = get_string('logo', 'theme_adaptable');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $page->add($setting);

    // Select type of login.
    $name = 'theme_adaptable/displaylogin';
    $title = get_string('displaylogin', 'theme_adaptable');
    $description = get_string('displaylogindesc', 'theme_adaptable');
    $choices = [
        'button' => get_string('displayloginbutton', 'theme_adaptable'),
        'box' => get_string('displayloginbox', 'theme_adaptable'),
        'no' => get_string('displayloginno', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'button', $choices);
    $page->add($setting);

    // Show username.
    $name = 'theme_adaptable/showusername';
    $title = get_string('showusername', 'theme_adaptable');
    $description = get_string('showusernamedesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Have mobile primary navigation.
    $name = 'theme_adaptable/mobileprimarynav';
    $title = get_string('mobileprimarynav', 'theme_adaptable');
    $description = get_string('mobileprimarynavdesc', 'theme_adaptable');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $page->add($setting);

    // Course page header title.
    $name = 'theme_adaptable/coursepageheaderhidetitle';
    $title = get_string('coursepageheaderhidetitle', 'theme_adaptable');
    $description = get_string('coursepageheaderhidetitledesc', 'theme_adaptable');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_adaptable_messnotheader',
        get_string('headermessnot', 'theme_adaptable'),
        format_text(get_string('headermessnotdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Header notifications badge colour.
    $name = 'theme_adaptable/notbadgecolour';
    $title = get_string('notbadgecolour', 'theme_adaptable');
    $description = get_string('notbadgecolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Header notifications badge background colour.
    $name = 'theme_adaptable/notbadgebackgroundcolour';
    $title = get_string('notbadgebackgroundcolour', 'theme_adaptable');
    $description = get_string('notbadgebackgroundcolourdesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#e53935', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Messages main chat window background colour.
    $name = 'theme_adaptable/messagingbackgroundcolor';
    $title = get_string('messagingbackgroundcolor', 'theme_adaptable');
    $description = get_string('messagingbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFFFFF', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Messages pop-up background color.
    $name = 'theme_adaptable/messagepopupbackground';
    $title = get_string('messagepopupbackground', 'theme_adaptable');
    $description = get_string('messagepopupbackgrounddesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#fff000', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Messages pop-up text color.
    $name = 'theme_adaptable/messagepopupcolor';
    $title = get_string('messagepopupcolor', 'theme_adaptable');
    $description = get_string('messagepopupcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#333333', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_adaptable_breadcrumbheader',
        get_string('headerbreadcrumb', 'theme_adaptable'),
        format_text(get_string('headerbreadcrumbdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Display Breadcrumb or Course title where the breadcrumb normally is.
    $name = 'theme_adaptable/breadcrumbdisplay';
    $title = get_string('breadcrumbdisplay', 'theme_adaptable');
    $description = get_string('breadcrumbdisplaydesc', 'theme_adaptable');
    $radchoices = [
        'breadcrumb' => get_string('breadcrumb', 'theme_adaptable'),
        'fullname' => get_string('coursetitlefullname', 'theme_adaptable'),
        'shortname' => get_string('coursetitleshortname', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'breadcrumb', $radchoices);
    $page->add($setting);

    // Breadcrumb home.
    $name = 'theme_adaptable/breadcrumbhome';
    $title = get_string('breadcrumbhome', 'theme_adaptable');
    $description = get_string('breadcrumbhomedesc', 'theme_adaptable');
    $radchoices = [
        'text' => get_string('breadcrumbhometext', 'theme_adaptable'),
        'icon' => get_string('breadcrumbhomeicon', 'theme_adaptable'),
        'off' => get_string('breadcrumbhomeoff', 'theme_adaptable'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'icon', $radchoices);
    $page->add($setting);

    // Breadcrumb separator.
    $name = 'theme_adaptable/breadcrumbseparator';
    $title = get_string('breadcrumbseparator', 'theme_adaptable');
    $description = get_string('breadcrumbseparatordesc', 'theme_adaptable', 'https://fontawesome.com/search?o=r&m=free');
    $setting = new admin_setting_configtext($name, $title, $description, 'angle-right');
    $page->add($setting);

    // Breadcrumb background color.
    $name = 'theme_adaptable/breadcrumb';
    $title = get_string('breadcrumbbackgroundcolor', 'theme_adaptable');
    $description = get_string('breadcrumbbackgroundcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#f5f5f5', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Breadcrumb text color.
    $name = 'theme_adaptable/breadcrumbtextcolor';
    $title = get_string('breadcrumbtextcolor', 'theme_adaptable');
    $description = get_string('breadcrumbtextcolordesc', 'theme_adaptable');
    $previewconfig = null;
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#444444', $previewconfig);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $asettings->add($page);
}
