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
 * myprofile edit profile.
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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}
require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/user/editlib.php');

/**
 * Class editprofile_form.
 *
 * @package    theme_adaptable
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @copyright Copyright (c) 2017 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editprofile_form extends \moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $COURSE;

        $mform = $this->_form;
        $editoroptions = null;
        $filemanageroptions = null;

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for user_edit_form');
        }
        $editoroptions = $this->_customdata['editoroptions'];
        $filemanageroptions = $this->_customdata['filemanageroptions'];
        $user = $this->_customdata['user'];
        $userid = $user->id;

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', \core_user::get_property_type('id'));
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        // Print the required moodle fields first.
        $mform->addElement('static', 'moodle', '<h3>'.get_string('general').'</h3>');

        // Fields.
        $this->editprofile_definition($mform, $editoroptions, $filemanageroptions, $user);

        if ($userid == -1) {
            $btnstring = get_string('createuser');  // Should never happen, but leave as an indicator.
        } else {
            $btnstring = get_string('updatemyprofile');
        }

        $this->add_action_buttons(true, $btnstring);

        $this->set_data($user);
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {
        global $DB, $OUTPUT, $USER;

        $mform = $this->_form;

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }

        if ($userid = $mform->getElementValue('id')) {
            $user = $DB->get_record('user', array('id' => $userid));
        } else {
            $user = false;
        }

        // Require password for new users.
        if ($userid > 0) {
            if ($mform->elementExists('createpassword')) {
                $mform->removeElement('createpassword');
            }
        }

        if ($user and is_mnet_remote_user($user)) {
            // Only local accounts can be suspended.
            if ($mform->elementExists('suspended')) {
                $mform->removeElement('suspended');
            }
        }
        if ($user and ($user->id == $USER->id or is_siteadmin($user))) {
            // Prevent self and admin mess ups.
            if ($mform->elementExists('suspended')) {
                $mform->hardFreeze('suspended');
            }
        }

        // Print picture.
        if (empty($USER->newadminuser)) {
            if ($user) {
                $context = \context_user::instance($user->id, MUST_EXIST);
                $fs = get_file_storage();
                $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png')
                || $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));

                if (!empty($user->picture) && $hasuploadedpicture) {
                    $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size' => 64));
                } else {
                    $imagevalue = get_string('none');
                }
            } else {
                $imagevalue = get_string('none');
            }

            $imageelement = $mform->getElement('currentpicture');
            $imageelement->setValue($imagevalue);

            if ($user && $mform->elementExists('deletepicture') && !$hasuploadedpicture) {
                $mform->removeElement('deletepicture');
            }
        }

        // Next the customisable profile fields.
        profile_definition_after_data($mform, $userid);
    }

    /**
     * Validate the form data.
     * @param array $usernew
     * @param array $files
     * @return array|bool
     */
    public function validation($usernew, $files) {
        $usernew = (object)$usernew;

        $err = array();
        // Next the customisable profile fields.
        $err += profile_validation($usernew, $files);

        if (count($err) == 0) {
            return true;
        } else {
            return $err;
        }
    }

     /**
      * Powerful function that is used by edit and editadvanced to add common form elements/rules/etc.
      *
      * @param moodleform $mform
      * @param array $editoroptions
      * @param array $filemanageroptions
      * @param stdClass $user
      */
    public function editprofile_definition(&$mform, $editoroptions, $filemanageroptions, $user) {
        global $CFG, $USER;

        if ($user->id > 0) {
            useredit_load_preferences($user, false);
        }

        $mform->addElement('editor', 'description_editor', get_string('userdescription'),
                           'class="adaptablemyeditprofile"', $editoroptions);
        $mform->setType('description_editor', PARAM_RAW);
        $mform->addHelpButton('description_editor', 'userdescription');

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="54" class="adaptablemyeditprofile"');
        $mform->setType('city', PARAM_TEXT);
        if (!empty($CFG->defaultcity)) {
            $mform->setDefault('city', $CFG->defaultcity);
        }

        if (\core_tag_tag::is_enabled('core', 'user') and empty($USER->newadminuser)) {
            $mform->addElement('static', 'moodle_interests', '<h3>'.get_string('interests').'</h3>');
            $mform->addElement('tags', 'interests', get_string('interestslist'),
                array('itemtype' => 'user', 'component' => 'core'));
            $mform->addHelpButton('interests', 'interestslist');
        }

        if (empty($USER->newadminuser)) {
            $mform->addElement('static', 'moodle_picture', '<h3>'.get_string('pictureofuser').'</h3>');

            if (!empty($CFG->enablegravatar)) {
                $mform->addElement('html', \html_writer::tag('p', get_string('gravatarenabled')));
            }

            $mform->addElement('static', 'currentpicture', get_string('currentpicture'));

            $mform->addElement('checkbox', 'deletepicture', get_string('deletepicture'));
            $mform->setDefault('deletepicture', 0);

            $mform->addElement('filemanager', 'imagefile', get_string('newpicture'),
                               'class="adaptablemyeditprofile"', $filemanageroptions);
            $mform->addHelpButton('imagefile', 'newpicture');

            $mform->addElement('text', 'imagealt', get_string('imagealt'),
                               'maxlength="100" size="54" class="adaptablemyeditprofile"');
            $mform->setType('imagealt', PARAM_TEXT);
        }
    }

}
