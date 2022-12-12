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
 * myprofile renderer.
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace theme_adaptable\output\core_user\myprofile;

defined('MOODLE_INTERNAL') || die;

use core_user\output\myprofile\category;
use core_user\output\myprofile\node;
use core_user\output\myprofile\tree;
use html_writer;

require_once($CFG->dirroot.'/user/lib.php');

/**
 * myprofile renderer.
 * @copyright Copyright (c) 2017 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \core_user\output\myprofile\renderer {

    /** @var Obj $user U*/
    private $user = null;

    /** @var Obj $course */
    private $course = null;

    /** @var bool $enabletabbedprofile */
    private $enabletabbedprofile = true;

    /**
     * Constructor for class.
     *
     * @param moodle_page $page
     * @param string $target
     *
     * @return Obj
     */
    public function __construct(\moodle_page $page, $target) {
        $this->enabletabbedprofile = get_config('theme_adaptable', 'enabletabbedprofile');

        if ($this->enabletabbedprofile) {
            /* We need the user id!
            From user/profile.php - technically by the time we are instantiated then the user id will have been validated. */
            global $DB, $USER;
            $userid = optional_param('id', 0, PARAM_INT);
            $userid = $userid ? $userid : $USER->id;
            $this->user = \core_user::get_user($userid);

            $courseid = optional_param('course', SITEID, PARAM_INT); // Course id (defaults to Site).
            $this->course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

            /* Using this as the function copes with hidden fields and capabilities.  For example:
             * If the you're allowed to see the description.
             *
             * This way because the DB user record from get_user can contain the description that
             * the function user_get_user_details can exclude! */
            $this->user->userdetails = user_get_user_details($this->user, $this->course);
        }

        parent::__construct($page, $target);
    }

    /**
     * Render the whole tree.
     *
     * @param tree $tree
     *
     * @return string
     */
    public function render_tree(tree $tree) {
        if (!$this->enabletabbedprofile) {
            return parent::render_tree($tree);
        }

        $categories = array();
        foreach ($tree->categories as $category) {
            $categories[$category->name] = $category;
        }

        $output = html_writer::start_tag('div', array('id' => 'adaptable_profile_tree', 'class' => 'profile_tree row'));

        $output .= html_writer::start_tag('div', array('class' => 'ucol1 col-md-4')); // Col one.

        $output .= html_writer::start_tag('div', array('class' => 'row'));
        $output .= html_writer::start_tag('div', array('class' => 'col-12 contact'));
        $contactcategory = $this->transform_contact_category($categories['contact']);
        $output .= $this->render($contactcategory);
        unset($categories['contact']);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'ucol2 col-md-8')); // Col two.
        $output .= $this->tabs($categories);
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Transform contact category.
     *
     * @param Obj $oldcontactcategory
     *
     * @return Obj
     */
    protected function transform_contact_category($oldcontactcategory) {
        $contactcategory = new category('contact', '');

        $userimage = $this->userimage();
        if (!empty($userimage)) {
            $node = new node('contact', 'userimage adaptableuserpicture', '', null, null, $userimage);
            $contactcategory->add_node($node);
        }

        if ((empty($this->user->userdetails['firstname'])) || (empty($this->user->userdetails['lastname']))) {
            $node = new node('contact', 'fullname', '', null, null,
                $this->user->userdetails['fullname']);
            $contactcategory->add_node($node);
        } else {
            $node = new node('contact', 'firstname', '', null, null,
                $this->user->userdetails['firstname']);
            $contactcategory->add_node($node);

            $node = new node('contact', 'lastname', '', null, null,
                $this->user->userdetails['lastname']);
            $contactcategory->add_node($node);
        }

        if (theme_adaptable_get_setting('enabledtabbedprofileuserpreferenceslink')) {
            $node = new node('contact', 'userpreferences', '', null, null,
                $this->userpreferences());
            $contactcategory->add_node($node);
        }

        if (theme_adaptable_get_setting('enabledtabbedprofileeditprofilelink')) {
            if (!empty($oldcontactcategory->nodes['editprofile'])) {
                $contactcategory->add_node($oldcontactcategory->nodes['editprofile']);
            }
        }

        if (!empty($this->user->userdetails['email'])) {
            $node = new node('contact', 'email', get_string('email'), null, null,
                $this->user->userdetails['email']);
            $contactcategory->add_node($node);
        }

        if (!empty($this->user->userdetails['city'])) {
            $node = new node('contact', 'city', get_string('city'), null, null,
                $this->user->userdetails['city']);
            $contactcategory->add_node($node);
        }
        if (!empty($this->user->userdetails['country'])) {
            $node = new node('contact', 'country', get_string('country'), null, null,
                $this->user->userdetails['country']);
            $contactcategory->add_node($node);
        }

        $messagebuttons = $this->message_user();
        if (!empty($messagebuttons)) {
            foreach ($messagebuttons as $button) {
                $node = new node('contact', $button['type'], '', null, null, $button['content']);
                $contactcategory->add_node($node);
            }
        }

        return $contactcategory;
    }

    /**
     * Message user.
     *
     * @return array
     */
    protected function message_user() {
        global $CFG, $USER;
        $output = array();

        // Use Moodle code just in case!
        $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;
        $user = $this->user;

        if (user_can_view_profile($user, $course)) {
            $context = \context_user::instance($user->id, IGNORE_MISSING);
            // Check to see if we can display a message button.
            if (!empty($CFG->messaging) && has_capability('moodle/site:sendmessage', $context)) {
                $userbuttons = array();
                if (($USER->id != $user->id) || ($CFG->branch > 36)) {
                    if ($CFG->branch < 37) {
                        $linkattributes = array('role' => 'button');
                    } else {
                        $linkattributes = \core_message\helper::messageuser_link_params($user->id);
                        // Change the id to us instead of copying the method.
                        $linkattributes['id'] = 'adaptable-message-user-button';
                        $this->adaptable_messageuser_requirejs();
                    }
                    $userbuttons['messages'] = array(
                        'buttontype' => 'message',
                        'title' => get_string('message', 'message'),
                        'url' => new \moodle_url('/message/index.php', array('id' => $user->id)),
                        'image' => 't/message',
                        'linkattributes' => $linkattributes,
                        'page' => $this->page
                    );
                }

                if ($USER->id != $user->id) {
                    $iscontact = \core_message\api::is_contact($USER->id, $user->id);
                    $contacttitle = $iscontact ? 'removefromyourcontacts' : 'addtoyourcontacts';
                    $contacturlaction = $iscontact ? 'removecontact' : 'addcontact';
                    $contactimage = $iscontact ? 'removecontact' : 'addcontact';
                    $userbuttons['togglecontact'] = array(
                        'buttontype' => 'togglecontact',
                        'title' => get_string($contacttitle, 'message'),
                        'url' => new \moodle_url('/message/index.php', array(
                                'user1' => $USER->id,
                                'user2' => $user->id,
                                $contacturlaction => $user->id,
                                'sesskey' => sesskey())
                        ),
                        'image' => 't/'.$contactimage,
                        'linkattributes' => \core_message\helper::togglecontact_link_params($user, $iscontact),
                        'page' => $this->page
                    );
                    \core_message\helper::togglecontact_requirejs();
                }

                $this->page->requires->string_for_js('changesmadereallygoaway', 'moodle');
                foreach ($userbuttons as $button) {
                    $image = $this->pix_icon($button['image'], $button['title'], 'moodle', array(
                        'class' => 'iconsmall',
                        'role' => 'presentation'
                    ));
                    $image .= html_writer::span($button['title'], 'header-button-title');
                    $output[] = array(
                        'type' => $button['buttontype'],
                        'content' => html_writer::link($button['url'], html_writer::tag('span', $image), $button['linkattributes'])
                    );
                }
            }
        }

        return $output;
    }

    /**
     * Requires the JS libraries for the Adaptable message user button.
     *
     * This is needed so that we have a different id to the core button that is hidden and the core JS picks up.
     * Thus the 'trigger' of the message box would only happen on the 'first' '#message-user-button' the JS sees and
     * not ours that comes afterwards.  So by having a different id we can solve this and not invoke the core JS with
     * the core button's id in the first place.
     *
     * @return void
     */
    public function adaptable_messageuser_requirejs() {
        static $done = false;
        if ($done) {
            return;
        }

        $this->page->requires->js_call_amd('core_message/message_user_button', 'send', array('#adaptable-message-user-button'));
        $done = true;
    }

    /**
     * Render a category.
     *
     * @param category $category
     *
     * @return string
     */
    public function render_category(category $category) {
        if (!$this->enabletabbedprofile) {
            return parent::render_category($category);
        }

        $nodes = $category->nodes;
        if (empty($nodes)) {
            // No nodes, nothing to render.
            return '';
        }

        $classes = $category->classes;
        if (empty($classes)) {
            $output = html_writer::start_tag('section', array('class' => 'node_category '.$category->name));
        } else {
            $output = html_writer::start_tag('section', array('class' => 'node_category '.$category->name.' '.$classes));
        }
        if ((empty($category->notitle)) && ($category->title)) {
            $output .= html_writer::tag('h3', $category->title);
        }
        $output .= html_writer::start_tag('ul');
        foreach ($nodes as $node) {
            $output .= $this->render($node);
        }
        $output .= html_writer::end_tag('ul');
        $output .= html_writer::end_tag('section');

        return $output;
    }

    /**
     * Render a node.
     *
     * @param node $node
     *
     * @return string
     */
    public function render_node(node $node) {
        if (!$this->enabletabbedprofile) {
            return parent::render_node($node);
        }
        $return = '';
        if (is_object($node->url)) {
            $header = \html_writer::link($node->url, $node->title);
        } else {
            $header = $node->title;
        }
        $icon = $node->icon;
        if (!empty($icon)) {
            $header .= $this->render($icon);
        }
        $content = $node->content;
        $classes = $node->classes;
        if (!empty($content)) {
            if ($header) {
                // There is some content to display below this make this a header.
                $return = \html_writer::tag('dt', $header);
                $return .= \html_writer::tag('dd', $content);

                $return = \html_writer::tag('dl', $return);
            } else {
                $return = \html_writer::span($content);
            }
            if ($classes) {
                $return = \html_writer::tag('li', $return, array('class' => 'contentnode '.$node->name.' '.$classes));
            } else {
                $return = \html_writer::tag('li', $return, array('class' => 'contentnode '.$node->name));
            }
        } else {
            $return = \html_writer::span($header);
            $return = \html_writer::tag('li', $return, array('class' => $classes));
        }

        return $return;
    }

    /**
     * Output user image.
     *
     * @return string
     */
    protected function userimage() {
        $output = '';

        if (!empty($this->user)) {
            $output .= $this->output->user_picture($this->user, array('size' => '1'));
        }

        return $output;
    }

    /**
     * Get user preferences.
     *
     * @return string
     */
    protected function userpreferences() {
        global $USER;

        $output = '';
        if ($USER->id == $this->user->id) {
            $output = html_writer::start_tag('li', array('class' => 'contentnode adaptableuserpreferences'));
            $output .= html_writer::link(new \moodle_url('/user/preferences.php'), get_string('preferences', 'moodle'));
            $output .= html_writer::end_tag('li');
        }

        return $output;
    }

    /**
     * About me.
     *
     * @return category Obj
     */
    protected function create_aboutme() {
        $aboutme = new category('aboutme', get_string('aboutme', 'theme_adaptable'));

        // Description.
        if (!empty($this->user->userdetails['description'])) {
            $description = $this->user->userdetails['description'];
        } else {
            $description = get_string('usernodescription', 'theme_adaptable');
        }
        $node = new node('aboutme', 'description', get_string('description'), null, null,
            $description);
        $aboutme->add_node($node);

        // Interests.
        if (!empty($this->user->userdetails['interests'])) {
            // Odd but just the way things can be!
            $interests = $this->output->tag_list(\core_tag_tag::get_item_tags('core', 'user', $this->user->id), '');
        } else {
            $interests = get_string('usernointerests', 'theme_adaptable');
        }
        $node = new node('aboutme', 'interests', get_string('interests'), null, null,
            $interests);
        $aboutme->add_node($node);

        // Optional.
        $this->optional_fields($aboutme, 'aboutme');

        return $aboutme;
    }

    /**
     * Add the optional fields to the stated category if they are populated.
     *
     * @param category $category Category object to add to.
     * @param string $categoryname Category name to add to.
     */
    protected function optional_fields(category $category, $categoryname) {
        if (!empty($this->user->userdetails['url'])) {
            $node = new node($categoryname, 'url', get_string('url'), null, null,
                $this->user->userdetails['url'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['icq'])) {
            $node = new node($categoryname, 'icq', get_string('icqnumber'), null, null,
                $this->user->userdetails['icq'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['skype'])) {
            $node = new node($categoryname, 'skype', get_string('skypeid'), null, null,
                $this->user->userdetails['skype'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['yahoo'])) {
            $node = new node($categoryname, 'yahoo', get_string('yahooid'), null, null,
                $this->user->userdetails['yahoo'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['aim'])) {
            $node = new node($categoryname, 'aim', get_string('aimid'), null, null,
                $this->user->userdetails['aim'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['msn'])) {
            $node = new node($categoryname, 'msn', get_string('msnid'), null, null,
                $this->user->userdetails['msn'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['address'])) {
            $node = new node($categoryname, 'address', get_string('address'), null, null,
                $this->user->userdetails['address'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['phone1'])) {
            $node = new node($categoryname, 'phone1', get_string('phone1'), null, null,
                $this->user->userdetails['phone1'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['phone2'])) {
            $node = new node($categoryname, 'phone2', get_string('phone2'), null, null,
                $this->user->userdetails['phone2'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['idnumber'])) {
            $node = new node($categoryname, 'idnumber', get_string('idnumber'), null, null,
                $this->user->userdetails['idnumber'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['institution'])) {
            $node = new node($categoryname, 'institution', get_string('institution'), null, null,
                $this->user->userdetails['institution'], null, 'aduseropt');
            $category->add_node($node);
        }
        if (!empty($this->user->userdetails['department'])) {
            $node = new node($categoryname, 'department', get_string('department'), null, null,
                $this->user->userdetails['department'], null, 'aduseropt');
            $category->add_node($node);
        }
    }

    /**
     * Custom user profile.
     *
     * TODO: May need to change, somehow, to use display_data(), see: userprofilefields().
     *
     * @return category Obj or null if not created.
     */
    protected function customuserprofile() {
        $category = null;

        $customcoursetitleprofilefield = get_config('theme_adaptable', 'customcoursetitle');
        $customcoursesubtitleprofilefield = get_config('theme_adaptable', 'customcoursesubtitle');

        if ((!empty($this->user->userdetails['customfields'])) &&
            ((!empty($customcoursetitleprofilefield)) || (!empty($customcoursesubtitleprofilefield)))) {
            $customcoursetitle = '';
            $customcoursesubtitle = '';
            $searcharray = array();
            foreach ($this->user->userdetails['customfields'] as $cfield) {
                $searcharray[$cfield['shortname']] = $cfield;
            }

            if (!empty($customcoursetitleprofilefield)) {
                if (array_key_exists($customcoursetitleprofilefield, $searcharray)) {
                    $customcoursetitle = $searcharray[$customcoursetitleprofilefield]['value'];
                }
            }
            if (!empty($customcoursesubtitleprofilefield)) {
                if (array_key_exists($customcoursesubtitleprofilefield, $searcharray)) {
                    $customcoursesubtitle = $searcharray[$customcoursesubtitleprofilefield]['value'];
                }
            }

            if ((!empty($customcoursetitle)) || (!empty($customcoursesubtitle))) {
                $category = new category('customuserprofile', get_string('course', 'theme_adaptable'));

                if (!empty($customcoursetitle)) {
                    $node = new node('customuserprofile', 'customcoursetitle', '', null, null, $customcoursetitle);
                    $category->add_node($node);
                }
                if (!empty($customcoursesubtitle)) {
                    $node = new node('customuserprofile', 'customcoursesubtitle', '', null, null, $customcoursesubtitle);
                    $category->add_node($node);
                }
            }
        }

        return $category;
    }

    /**
     * User profile fields.
     *
     * @return string
     */
    protected function userprofilefields() {
        $output = '';

        if (!empty($this->user->userdetails['customfields'])) {
            $customcoursetitleprofilefield = get_config('theme_adaptable', 'customcoursetitle');
            $customcoursesubtitleprofilefield = get_config('theme_adaptable', 'customcoursesubtitle');

            $customfieldscat = new category('customfields', '');
            $hasnodes = false;

            $categories = profile_get_user_fields_with_data_by_category($this->user->id);
            foreach ($categories as $categoryid => $fields) {
                foreach ($fields as $formfield) {
                    if ((!empty($customcoursetitleprofilefield)) && ($formfield->field->shortname == $customcoursetitleprofilefield)) {
                        continue;
                    }
                    if ((!empty($customcoursesubtitleprofilefield)) && ($formfield->field->shortname == $customcoursesubtitleprofilefield)) {
                        continue;
                    }
                    if ($formfield->is_visible() and !$formfield->is_empty()) {
                        $node = new node('customfields', 'custom_field_' . $formfield->field->shortname,
                            format_string($formfield->field->name), null, null, $formfield->display_data());
                        $customfieldscat->add_node($node);
                        $hasnodes = true;
                    }
                }
            }

            if ($hasnodes) {
                $output .= $this->render($customfieldscat);
            }
        }

        return $output;
    }

    /**
     * Create edit profile form.
     *
     * @return string
     */
    protected function create_editprofile() {
        $editprofile = new category('editprofile', get_string('editmyprofile'));

        $editprofileform = editprofile::generate_form();
        $node = new node('editprofile', 'editprofile', '', null, null, $editprofileform['form']->render());
        $editprofile->add_node($node);

        // Process the form.
        editprofile::process_form(true, $editprofileform);

        return $editprofile;
    }

    /**
     * Create tabs.
     *
     * @param array $categories
     *
     * @return string
     */
    protected function tabs($categories) {
        global $USER;

        static $tabcategories = array('coursedetails');

        $tabdata = new \stdClass;
        $tabdata->containerid = 'userprofiletabs';
        $tabdata->tabs = array();

        // Aboutme tab.
        $category = $this->create_aboutme();
        $aboutmetab = new \stdClass;
        $aboutmetab->name = $category->name;
        $aboutmetab->displayname = $category->title;
        $customuserprofilecat = $this->customuserprofile();
        $aboutmetab->content = '';
        if (!is_null($customuserprofilecat)) {
            $aboutmetab->content .= $this->render($customuserprofilecat);
        } else {
            $category->notitle = true;
        }
        $aboutmetab->content .= $this->render($category);
        // Custom fields on About me tab.
        $aboutmetab->content .= $this->userprofilefields();
        $tabdata->tabs[] = $aboutmetab;

        foreach ($tabcategories as $categoryname) {
            if (!empty($categories[$categoryname])) {
                $category = $categories[$categoryname];
                if ($category->name == 'coursedetails') {
                    $category->notitle = true;
                }
                $markup = $this->render($category);
                if (!empty($markup)) {
                    $tab = new \stdClass;
                    $tab->name = $category->name;
                    if ($category->name == 'coursedetails') {
                        $tab->displayname = get_string('courses', 'theme_adaptable');
                    } else {
                        $tab->displayname = $category->title;
                    }
                    $tab->content = $markup;
                    $tabdata->tabs[] = $tab;
                }
                unset($categories[$categoryname]);
            }
        }

        // More tab.
        $misccontent = html_writer::start_tag('div', array('class' => 'row'));
        foreach ($categories as $categoryname => $category) {
            $misccontent .= html_writer::start_tag('div', array('class' => 'col-12 '.$categoryname));
            $misccontent .= $this->render($category);
            $misccontent .= html_writer::end_tag('div');
        }
        $misccontent .= html_writer::end_tag('div');
        $tab = new \stdClass;
        $tab->name = 'more';
        $tab->displayname = get_string('more', 'theme_adaptable');
        $tab->content = $misccontent;
        $tabdata->tabs[] = $tab;

        if ((is_siteadmin()) ||
            (($USER->id == $this->user->id)) &&
             (has_capability('moodle/user:editownprofile', \context_user::instance($this->user->id)))) {
            // Edit profile tab.
            $category = $this->create_editprofile();
            $editprofiletab = new \stdClass;
            $editprofiletab->name = $category->name;
            $editprofiletab->displayname = $category->title;
            $category->notitle = true;
            $editprofiletab->content = $this->render($category);
            $tabdata->tabs[] = $editprofiletab;
        }
        $aboutmetab->selected = true;

        return $this->render_from_template('theme_adaptable/tabs', $tabdata);
    }
}
