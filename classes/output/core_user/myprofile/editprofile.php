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

defined('MOODLE_INTERNAL') || die;

/**
 * myprofile editprofile.
 *
 * @package    theme_adaptable
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @copyright Copyright (c) 2017 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editprofile {

    /**
     * Generate form.
     *
     * @return array
     */
    public static function generate_form() {
        global $CFG, $DB, $PAGE, $USER;

        $userid = optional_param('id', 0, PARAM_INT);
        $userid = $userid ? $userid : $USER->id;
        $user = \core_user::get_user($userid);

        $courseid = optional_param('course', SITEID, PARAM_INT); // Course id (defaults to Site).
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        // User interests.
        $user->interests = \core_tag_tag::get_item_tags_array('core', 'user', $userid);

        require_once($CFG->dirroot.'/lib/formslib.php');
        $usercontext = \context_user::instance($user->id);
        $editoroptions = array(
            'maxfiles'   => EDITOR_UNLIMITED_FILES,
            'maxbytes'   => $CFG->maxbytes,
            'trusttext'  => false,
            'forcehttps' => false,
            'context'    => $usercontext
        );
        $user = file_prepare_standard_editor($user, 'description', $editoroptions, $usercontext, 'user', 'profile', 0);

        // Prepare filemanager draft area.
        $draftitemid = 0;
        $filemanagercontext = $editoroptions['context'];
        $filemanageroptions = array(
            'maxbytes'       => $CFG->maxbytes,
            'subdirs'        => 0,
            'maxfiles'       => 1,
            'accepted_types' => 'web_image');
        \file_prepare_draft_area($draftitemid, $filemanagercontext->id, 'user', 'newicon', 0, $filemanageroptions);
        $user->imagefile = $draftitemid;

        $editprofileform = new editprofile_form(
            new \moodle_url(
                $PAGE->url,
                array(
                    'id' => $user->id,
                    'course' => $course->id,
                    'aep' => 'aep'
                )
            ),
            array(
                'editoroptions' => $editoroptions,
                'filemanageroptions' => $filemanageroptions,
                'user' => $user)
            );

        return array(
            'form' => $editprofileform,
            'user' => $user,
            'course' => $course,
            'editoroptions' => $editoroptions,
            'filemanageroptions' => $filemanageroptions,
            'usercontext' => $usercontext
            );
    }

    /**
     * Process form.
     *
     * @param bool $redirect
     * @param array $editprofile
     */
    public static function process_form($redirect = true, $editprofile = null) {
        global $CFG, $DB, $SITE, $USER;

        if (is_null($editprofile)) {
            $editprofile = self::generate_form();
        }

        $editprofileform = $editprofile['form'];
        $user = $editprofile['user'];
        $course = $editprofile['course'];
        $editoroptions = $editprofile['editoroptions'];
        $filemanageroptions = $editprofile['filemanageroptions'];
        $usercontext = $editprofile['usercontext'];

        // Deciding where to send the user back in most cases.
        if ($redirect) {
            if ($course->id != SITEID) {
                $returnurl = new \moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id));
            } else {
                $returnurl = new \moodle_url('/user/profile.php', array('id' => $user->id));
            }
        }

        if ($editprofileform->is_cancelled()) {
            if ($redirect) {
                redirect($returnurl);
            }
        } else if ($usernew = $editprofileform->get_data()) {
            $usercreated = false;
            if (empty($usernew->auth)) {
                // User editing self.
                $authplugin = get_auth_plugin($user->auth);
                unset($usernew->auth); // Can not change/remove.
            } else {
                $authplugin = get_auth_plugin($usernew->auth);
            }

            $usernew->timemodified = time();
            $createpassword = false;

            if ($usernew->id == -1) {
                unset($usernew->id);
                $createpassword = !empty($usernew->createpassword);
                unset($usernew->createpassword);
                $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions, null, 'user', 'profile', null);
                $usernew->mnethostid = $CFG->mnet_localhost_id; // Always local user.
                $usernew->confirmed  = 1;
                $usernew->timecreated = time();
                if ($authplugin->is_internal()) {
                    if ($createpassword or empty($usernew->newpassword)) {
                        $usernew->password = '';
                    } else {
                        $usernew->password = hash_internal_user_password($usernew->newpassword);
                    }
                } else {
                    $usernew->password = AUTH_PASSWORD_NOT_CACHED;
                }
                $usernew->id = user_create_user($usernew, false, false);

                if (!$authplugin->is_internal() and $authplugin->can_change_password() and !empty($usernew->newpassword)) {
                    if (!$authplugin->user_update_password($usernew, $usernew->newpassword)) {
                        // Do not stop here, we need to finish user creation.
                        debugging(get_string('cannotupdatepasswordonextauth', '', '', $usernew->auth), DEBUG_NONE);
                    }
                }
                $usercreated = true;
            } else {
                $usernew = file_postupdate_standard_editor($usernew, 'description', $editoroptions,
                                                           $usercontext, 'user', 'profile', 0);
                // Pass a true old $user here.
                if (!$authplugin->user_update($user, $usernew)) {
                    // Auth update failed.
                    print_error('cannotupdateuseronexauth', '', '', $user->auth);
                }
                user_update_user($usernew, false, false);

                // Set new password if specified.
                if (!empty($usernew->newpassword)) {
                    if ($authplugin->can_change_password()) {
                        if (!$authplugin->user_update_password($usernew, $usernew->newpassword)) {
                            print_error('cannotupdatepasswordonextauth', '', '', $usernew->auth);
                        }
                        unset_user_preference('create_password', $usernew); // Prevent cron from generating the password.

                        if (!empty($CFG->passwordchangelogout)) {
                            // We can use SID of other user safely here because they are unique,
                            // the problem here is we do not want to logout admin here when changing own password.
                            \core\session\manager::kill_user_sessions($usernew->id, session_id());
                        }
                        if (!empty($usernew->signoutofotherservices)) {
                            webservice::delete_user_ws_tokens($usernew->id);
                        }
                    }
                }

                // Force logout if user just suspended.
                if (isset($usernew->suspended) and $usernew->suspended and !$user->suspended) {
                    \core\session\manager::kill_user_sessions($user->id);
                }
            }

            $usercontext = \context_user::instance($usernew->id);

            // Update preferences.
            useredit_update_user_preference($usernew);

            // Update tags.
            if (empty($USER->newadminuser) && isset($usernew->interests)) {
                useredit_update_interests($usernew, $usernew->interests);
            }

            // Update user picture.
            if (empty($USER->newadminuser)) {
                \core_user::update_picture($usernew, $filemanageroptions);
            }

            // Update mail bounces.
            useredit_update_bounces($user, $usernew);

            // Update forum track preference.
            useredit_update_trackforums($user, $usernew);

            // Save custom profile fields data.
            profile_save_data($usernew);

            // Reload from db.
            $usernew = $DB->get_record('user', array('id' => $usernew->id));

            if ($createpassword) {
                setnew_password_and_mail($usernew);
                unset_user_preference('create_password', $usernew);
                set_user_preference('auth_forcepasswordchange', 1, $usernew);
            }

            // Trigger update/create event, after all fields are stored.
            if ($usercreated) {
                \core\event\user_created::create_from_userid($usernew->id)->trigger();
            } else {
                \core\event\user_updated::create_from_userid($usernew->id)->trigger();
            }

            if ($user->id == $USER->id) {
                // Override old $USER session variable.
                foreach ((array)$usernew as $variable => $value) {
                    if ($variable === 'description' or $variable === 'password') {
                        // These are not set for security nad perf reasons.
                        continue;
                    }
                    $USER->$variable = $value;
                }
                // Preload custom fields.
                profile_load_custom_fields($USER);

                if (!empty($USER->newadminuser)) {
                    unset($USER->newadminuser);
                    // Apply defaults again - some of them might depend on admin user info, backup, roles, etc.
                    admin_apply_default_settings(null, false);
                    // Admin account is fully configured - set flag here in case the redirect does not work.
                    unset_config('adminsetuppending');
                    // Redirect to admin/ to continue with installation.
                    if ($redirect) {
                        self::redirect("$CFG->wwwroot/$CFG->admin/");
                    }
                } else if (empty($SITE->fullname)) {
                    // Somebody double clicked when editing admin user during install.
                    if ($redirect) {
                        self::redirect("$CFG->wwwroot/$CFG->admin/");
                    }
                } else {
                    if ($redirect) {
                        self::redirect($returnurl);
                    }
                }
            } else {
                \core\session\manager::gc(); // Remove stale sessions.
                if ($redirect) {
                    self::redirect("$CFG->wwwroot/$CFG->admin/user.php");
                }
            }
            // Never reached if redirect is true.
        }
    }

    /**
     * Redirect function.
     *
     * @param string $url
     */
    public static function redirect($url) {
        global $CFG, $OUTPUT, $PAGE;

        // Adapted from function of same name in lib/weblib.php.
        if ($url instanceof moodle_url) {
            $url = $url->out(false);
        }

        // Prevent debug errors - make sure context is properly initialised.
        if ($PAGE) {
            $PAGE->set_context(null);
            $PAGE->set_pagelayout('redirect');  // No header and footer needed.
            $PAGE->set_title(get_string('pageshouldredirect', 'moodle'));
        }

        /* Technically, HTTP/1.1 requires Location: header to contain the absolute path.
           (In practice browsers accept relative paths - but still, might as well do it properly.)
           This code turns relative into absolute. */
        if (!preg_match('|^[a-z]+:|i', $url)) {
            // Get host name http://www.wherever.com.
            $hostpart = preg_replace('|^(.*?[^:/])/.*$|', '$1', $CFG->wwwroot);
            if (preg_match('|^/|', $url)) {
                // URLs beginning with / are relative to web server root so we just add them in.
                $url = $hostpart.$url;
            } else {
                // URLs not beginning with / are relative to path of current script, so add that on.
                $url = $hostpart.preg_replace('|\?.*$|', '', me()).'/../'.$url;
            }
            // Replace all ..s.
            while (true) {
                $newurl = preg_replace('|/(?!\.\.)[^/]*/\.\./|', '/', $url);
                if ($newurl == $url) {
                    break;
                }
                $url = $newurl;
            }
        }

        /* Sanitise url - we can not rely on moodle_url or our URL cleaning
           because they do not support all valid external URLs. */
        $url = preg_replace('/[\x00-\x1F\x7F]/', '', $url);
        $url = str_replace('"', '%22', $url);
        $encodedurl = preg_replace("/\&(?![a-zA-Z0-9#]{1,8};)/", "&amp;", $url);
        $encodedurl = preg_replace('/^.*href="([^"]*)".*$/', "\\1", clean_text('<a href="'.$encodedurl.'" />', FORMAT_HTML));
        $url = str_replace('&amp;', '&', $encodedurl);

        /* Make sure the session is closed properly, this prevents problems in IIS
           and also some potential PHP shutdown issues. */
        \core\session\manager::write_close();

        if (!headers_sent()) {
            // 302 might not work for POST requests, 303 is ignored by obsolete clients.
            @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
            @header('Location: '.$url);
            echo \bootstrap_renderer::plain_redirect_message($encodedurl);
            exit;
        }

        // Include a redirect message, even with a HTTP redirect, because that is recommended practice.
        if ($PAGE) {
            $CFG->docroot = false; // To prevent the link to moodle docs from being displayed on redirect page.
            echo $OUTPUT->adaptable_redirect($encodedurl);
            exit;
        } else {
            echo \bootstrap_renderer::early_redirect_message($encodedurl, '', '0');
            exit;
        }
    }
}
