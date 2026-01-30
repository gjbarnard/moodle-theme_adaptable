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
 * Adaptable's config HTML editor
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015 Fernando Acedo (3-bits.com)
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

use context_system;
use context_user;
use core\url;
use stdClass;

defined('MOODLE_INTERNAL') || die;

// Require libs.
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Class to configure html editor for admin settings allowing use of repositories.
 *
 * Special thanks to Iban Cardona i Subiela (http://icsbcn.blogspot.com.es/2015/03/use-image-repository-in-theme-settings.html)
 * This post laid the ground work for some of the code featured in this file.
 */
class admin_setting_confightmleditor extends \admin_setting_configtext {
    /** @var int number of rows */
    private $rows;

    /** @var int number of columns */
    private $cols;

    /** @var string filearea - filearea within Moodle repository API */
    private $filearea;

    /** @var int itemid - Item id */
    private $itemid;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param string $filearea
     * @param int $itemid The item id.
     * @param mixed $paramtype
     * @param int $cols
     * @param int $rows
     */
    public function __construct(
        $name,
        $visiblename,
        $description,
        $defaultsetting,
        $filearea,
        $itemid = 0,
        $paramtype = PARAM_RAW,
        $cols = '60',
        $rows = '8'
    ) {
        $this->rows = $rows;
        $this->cols = $cols;
        $this->filearea = $filearea;
        $this->nosave = (during_initial_install() || CLI_SCRIPT);
        $this->itemid = $itemid;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype);
        editors_head_setup();
    }

    /**
     * Set the item id.
     *
     * @param int itemid.
     */
    public function setitemid($itemid) {
        $this->itemid = $itemid;
    }

    /**
     * Gets the file area options.
     *
     * @param $ctx
     * @return array
     */
    private static function get_options($ctx) {
        $default = [];
        $default['noclean'] = false;
        $default['context'] = $ctx;
        $default['maxbytes'] = 0;
        $default['maxfiles'] = -1;
        $default['forcehttps'] = false;
        $default['subdirs'] = false;
        $default['changeformat'] = 0;
        $default['areamaxbytes'] = FILE_AREA_MAX_BYTES_UNLIMITED;
        $default['return_types'] = (FILE_INTERNAL | FILE_EXTERNAL);

        return $default;
    }

    /**
     * Returns an XHTML string for the editor
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query = '') {
        if (PHPUNIT_TEST) {
            $userid = 2;  // Admin user.
        } else {
            global $USER;
            $userid = $USER->id;
        }

        $default = $this->get_defaultsetting();

        $defaultinfo = $default;
        if (!is_null($default) && $default !== '') {
            $defaultinfo = "\n" . $default;
        }

        $ctx = context_user::instance($userid);
        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $options = self::get_options($ctx);
        $draftitemid = file_get_unused_draft_itemid();

        if (!empty($data)) {
            $data = self::file_rewrite_setting_urls($data, $this->filearea, $this->itemid);
        }

        $fpoptions = [];
        $args = new stdClass();

        // Need these three to filter repositories list.
        $args->accepted_types = ['web_image'];
        $args->return_types = $options['return_types'];
        $args->context = $ctx;
        $args->env = 'filepicker';

        // Advimage plugin.
        $imageoptions = initialise_filepicker($args);
        $imageoptions->context = $ctx;
        $imageoptions->client_id = uniqid();
        $imageoptions->maxbytes = $options['maxbytes'];
        $imageoptions->areamaxbytes = $options['areamaxbytes'];
        $imageoptions->env = 'editor';
        $imageoptions->itemid = $draftitemid;

        // Moodlemedia plugin.
        $args->accepted_types = ['video', 'audio'];
        $mediaoptions = initialise_filepicker($args);
        $mediaoptions->context = $ctx;
        $mediaoptions->client_id = uniqid();
        $mediaoptions->maxbytes = $options['maxbytes'];
        $mediaoptions->areamaxbytes = $options['areamaxbytes'];
        $mediaoptions->env = 'editor';
        $mediaoptions->itemid = $draftitemid;

        // Advlink plugin.
        $args->accepted_types = '*';
        $linkoptions = initialise_filepicker($args);
        $linkoptions->context = $ctx;
        $linkoptions->client_id = uniqid();
        $linkoptions->maxbytes = $options['maxbytes'];
        $linkoptions->areamaxbytes = $options['areamaxbytes'];
        $linkoptions->env = 'editor';
        $linkoptions->itemid = $draftitemid;

        $fpoptions['image'] = $imageoptions;
        $fpoptions['media'] = $mediaoptions;
        $fpoptions['link'] = $linkoptions;

        $editor->set_text($data);
        $editor->use_editor($this->get_id(), $options, $fpoptions);

        return format_admin_setting(
            $this,
            $this->visiblename,
            '<div class="form-textarea"><textarea rows="' . $this->rows . '" cols="' . $this->cols . '" id="' .
            $this->get_id() . '" name="' . $this->get_full_name() . '"spellcheck="true">' . s($data) .
            '</textarea></div><input value="' . $draftitemid . '" name="' . $this->get_full_name() .
            '_draftitemid" type="hidden" />',
            $this->description,
            true,
            '',
            $defaultinfo,
            $query
        );
    }

    /**
     * Writes the setting to the database.
     *
     * @param mixed $data
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     * @throws file_exception
     * @throws stored_file_creation_exception
     */
    public function write_setting($data) {
        global $CFG, $USER;

        // ... $data is a string.
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        $draftitemidname = sprintf('%s_draftitemid', $this->get_full_name());
        if (PHPUNIT_TEST || !isset($_REQUEST[$draftitemidname])) {
            $draftitemid = 0;
        } else {
            $draftitemid = $_REQUEST[$draftitemidname];
        }

        // Based upon file_save_draft_area_files().
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'theme_adaptable' : $this->plugin;
        $systemcontext = context_system::instance();
        $usercontext = context_user::instance($USER->id);

        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);
        $existingfiles = $fs->get_area_files($systemcontext->id, $component, $this->filearea, $this->itemid, 'id', false);

        // Excluding directories.
        if (count($draftfiles) > 0 || count($existingfiles) > 0) {
            $newhashes = [];
            $filecount = 0;

            $options = self::get_options($systemcontext);

            foreach ($draftfiles as $file) {
                if (!$options['subdirs'] && $file->get_filepath() !== '/') {
                    continue;
                }
                $filecount++;

                $newhash = $fs->get_pathname_hash(
                    $systemcontext->id,
                    $component,
                    $this->filearea,
                    $this->itemid,
                    $file->get_filepath(),
                    $file->get_filename()
                );
                $newhashes[$newhash] = $file;
            }

            // After this cycle the array $newhashes will only contain the files that need to be added.
            foreach ($existingfiles as $existingfile) {
                if (!$options['subdirs'] && $existingfile->get_filepath() !== '/') {
                    $existingfile->delete();
                    // Deleted, so don't need to check if not needed.
                    $existinghash = $existingfile->get_pathnamehash();
                    unset($existingfiles[$existinghash]);
                    continue;
                }

                $existinghash = $existingfile->get_pathnamehash();

                if (isset($newhashes[$existinghash])) {
                    // Draft matches an existing file, is it identical content wise?
                    $draftcontenthash = $newhashes[$existinghash]->get_contenthash();
                    $existingcontenthash = $existingfile->get_contenthash();
                    if ($draftcontenthash == $existingcontenthash) {
                        // Same, so remove draft.
                        unset($newhashes[$existinghash]);
                    } else {
                        // Different, so delete existing and allow the new one to replace it.
                        $existingfile->delete();
                        // Deleted, so don't need to check if not needed.
                        unset($existingfiles[$existinghash]);
                    }
                }
            }

            // The array $newhashes will only contain new / replacement files.
            foreach ($newhashes as $file) {
                $filerecord = [
                    'contextid' => $systemcontext->id,
                    'component' => $component,
                    'filearea' => $this->filearea,
                    'itemid' => $this->itemid,
                    'timemodified' => time(),
                ];
                if ($source = @unserialize($file->get_source() ?? '')) {
                    // Field files.source for draftarea files contains serialised object with source and original information.
                    // We only store the source part of it for non-draft file area.
                    $filerecord['source'] = $source->source;
                }

                if ($file->is_external_file()) {
                    $repoid = $file->get_repository_id();
                    if (!empty($repoid)) {
                        $repo = repository::get_repository_by_id($repoid, $systemcontext);
                        if (!empty($options)) {
                            $repo->options = $options;
                        }
                        $filerecord['repositoryid'] = $repoid;
                        // This hook gives the repo a place to do some house cleaning, and update the $reference before it's saved
                        // to the file store. E.g. transfer ownership of the file to a system account etc.
                        $reference = $repo->reference_file_selected(
                            $file->get_reference(),
                            $systemcontext,
                            $component,
                            $filearea,
                            $itemid
                        );

                        $filerecord['reference'] = $reference;
                    }
                }

                $fs->create_file_from_storedfile($filerecord, $file);
            }

            // For draftfile to '@@PLUGINFILE@@' use file_rewrite_urls_to_pluginfile() to cope with %2F.
            $data = file_rewrite_urls_to_pluginfile($data, $draftitemid);

            // At this point we have the new files on both the data and file storage, but the could be existing
            // files that are not needed any more.  So two things here to get the '@@PLUGINFILE@@' prefix for storage
            // and so we can match against what is stored.
            $options['reverse'] = true;
            $data = file_rewrite_pluginfile_urls(
                $data,
                'pluginfile.php',
                $systemcontext->id,
                $component,
                $this->filearea,
                $this->itemid,
                $options
            );

            // Strip theme revision if any.
            $data = preg_replace_callback(
                '/@@PLUGINFILE@@\/(.*?)\/(.*?)"/',
                function ($matches) {
                    return '@@PLUGINFILE@@/' . $matches[2] . '"';
                },
                $data
            );

            // Get the list of filenames, ignoring any parameters.
            $areafilesmatches = [];
            preg_match_all(
                '/@@PLUGINFILE@@\/(.*?)(?:\?.*)?"/',
                $data,
                $areafilesmatches
            );

            $datafilenames = [];
            if (!empty($areafilesmatches[1])) {
                $datafilenames = $areafilesmatches[1];
            }

            foreach ($existingfiles as $existingfile) {
                if (!$existingfile->is_directory()) {
                    $existingfilename = $existingfile->get_filename();
                    if (!in_array($existingfilename, $datafilenames)) {
                        // Not in the data, so delete!
                        $existingfile->delete();
                    }
                }
            }
        }

        if (!strpos($data, '@@PLUGINFILE@@')) {
            if (trim(html_to_text($data)) === '') {
                $data = '';
            }
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Rewrite the setting URL's so that they can be served.
     *
     * @param string $data The setting data.
     * @param string $filearea The file area.
     * @param int $itemid The item id.
     * @param string $component The component.
     *
     * @return string Processed data.
     */
    public static function file_rewrite_setting_urls(
        $data,
        $filearea = 'adaptablemarkettingimages',
        $itemid = 0,
        $component = 'theme_adaptable'
    ) {
        $context = context_system::instance();
        $options = self::get_options($context);

        $data = preg_replace_callback(
            '/@@PLUGINFILE@@\/(.*?)"/',
            function ($matches) {
                // Add theme revision.
                return '@@PLUGINFILE@@/' . theme_get_revision() . '/' . $matches[1] . '"';
            },
            $data
        );

        return file_rewrite_pluginfile_urls(
            $data,
            'pluginfile.php',
            $context->id,
            $component,
            $filearea,
            $itemid,
            $options
        );
    }

    /**
     * Gets the array of filenames stored in the given setting data.
     *
     * @param string $data The setting data.
     * @param string $filearea The file area that they are stored in.
     * @param int $itemid The item id.
     * @param string $component The component.
     *
     * @return array Updated data and array of filenames if any.
     */
    public static function get_filenames($data, $filearea, $itemid = 0, $component = 'theme_adaptable') {
        $filenames = [];
        $contextsystem = context_system::instance();
        $options = self::get_options($contextsystem);
        $options['reverse'] = true;

        // Ensure that all files have the '@@PLUGINFILE@@' prefix for matching.
        $data = file_rewrite_pluginfile_urls(
            $data,
            'pluginfile.php',
            $contextsystem->id,
            $component,
            $filearea,
            $itemid,
            $options
        );

        $areafilesmatches = [];
        preg_match_all(
            '/@@PLUGINFILE@@\/(.*?)"/',
            $data,
            $areafilesmatches
        );

        if (!empty($areafilesmatches[1])) {
            $filenames = $areafilesmatches[1];
        }

        return ['data' => $data, 'filenames' => $filenames];
    }

    /**
     * Move any contained files from one area to another for the given setting value.
     *
     * @param string $data The setting data.
     * @param string $areafrom The from file area.
     * @param string $areato The to file area.
     * @param int $itemid The item id.
     * @param string $component The component.
     *
     * @return string Updated setting data.
     */
    public static function area_move($data, $areafrom, $areato, $itemid = 0, $component = 'theme_adaptable') {
        $fromareafilenames = self::get_filenames($data, $areafrom, $component);
        if (!empty($fromareafilenames['filenames'])) {
            $fs = get_file_storage();
            $contextsystemid = context_system::instance()->id;
            $areafromfiles = $fs->get_area_files($contextsystemid, $component, $areafrom, false, 'id', false);
            foreach ($areafromfiles as $fromfile) {
                $fromfilename = $fromfile->get_filename();
                if (in_array($fromfilename, $fromareafilenames['filenames'])) {
                    $filerecord = [
                        'contextid' => $contextsystemid,
                        'component' => $component,
                        'filearea' => $areato,
                        'filename' => $fromfilename,
                        'filepath' => '/',
                        'itemid' => $itemid,
                        'timemodified' => time(),
                    ];
                    if (
                        !$fs->get_file(
                            $filerecord['contextid'],
                            $filerecord['component'],
                            $filerecord['filearea'],
                            $filerecord['itemid'],
                            $filerecord['filepath'],
                            $filerecord['filename']
                        )
                    ) {
                        // To file does not already exist.
                        $newfile = $fs->create_file_from_storedfile($filerecord, $fromfile);
                        if (!is_null($newfile)) {
                            // Not null so safe to delete old.
                            $fromfile->delete();
                        }
                    }
                }
            }
        }

        return $fromareafilenames['data'];
    }

    /**
     * Delete the setting.
     *
     * @return array Changes.
     */
    public function delete() {
        $changed = [
            toolbox::REMOVEDFILES => [],
            toolbox::DELETEDFILEDATA => '',
        ];

        $systemcontextid = context_system::instance()->id;
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'theme_adaptable' : $this->plugin;

        $files = $fs->get_area_files(
            $systemcontextid,
            $component,
            $this->filearea,
            $this->itemid,
            'sortorder,filepath,filename',
            false
        );
        foreach ($files as $file) {
            $changed[toolbox::REMOVEDFILES][] = $file->get_filename();
            $file->delete();
        }

        $changed[toolbox::DELETEDFILEDATA] = $this->get_setting();

        unset_config($this->name, $component);

        return $changed;
    }

    /**
     * Base 64 encode.
     *
     * @return string Setting JSON with files base64 encoded.
     */
    public function base64encode() {
        $component = is_null($this->plugin) ? 'theme_adaptable' : $this->plugin;
        $syscontext = context_system::instance();

        $data = $this->get_setting();

        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $syscontext->id,
            $component,
            $this->filearea,
            $this->itemid,
            'sortorder,filepath,filename',
            false
        );

        $settingfiles = [];
        foreach ($files as $file) {
            $filecontent = $file->get_content();
            $base64enc = base64_encode($filecontent);

            $settingarrfile = [
                'filepath' => $file->get_filepath(),
                'filename' => $file->get_filename(),
                'author' => $file->get_author(),
                'license' => $file->get_license(),
                'timecreated' => $file->get_timecreated(),
                'timemodified' => $file->get_timemodified(),
                'mimetype' => $file->get_mimetype(),
                'content' => $base64enc,
            ];
            $settingfilejson = json_encode($settingarrfile, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
            $settingfileent = htmlentities($settingfilejson, ENT_COMPAT);
            $settingfiles[] = $settingfileent;
        }

        $settingfilesjson = json_encode($settingfiles, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $settingarr = [$this->name => $data, $this->filearea => $settingfilesjson];
        $settingarrjson = json_encode($settingarr, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

        return $settingarrjson;
    }

    /**
     * Base 64 decode.
     *
     * @param string $settingarrjson JSON of setting.
     *
     * @return array Changes.
     */
    public function base64decode($settingarrjson) {
        global $USER;

        $changed = [
            toolbox::ADDEDFILES => [],
            toolbox::REMOVEDFILES => [],
            toolbox::REPLACEDFILES => [],
            toolbox::UNCHANGEDFILES => [],
            toolbox::CHANGEDFILEDATA => '',
            toolbox::UNCHANGEDFILEDATA => '',
            toolbox::ERROR => '',
        ];

        // Decode the array containing the data and the files.
        $settingarrjsondec = json_decode($settingarrjson, true);

        // Data.
        $settingarrdata = $settingarrjsondec[$this->name];
        $validated = $this->validate($settingarrdata);
        if ($validated !== true) {
            $changed[toolbox::ERROR] = $validated;
            return $changed;
        }

        // Data okay, so files.
        $component = is_null($this->plugin) ? 'theme_adaptable' : $this->plugin;
        $syscontextid = context_system::instance()->id;
        $usercontextid = context_user::instance($USER->id)->id;

        $settingarrfilesdec = $settingarrjsondec[$this->filearea];

        $settingsfilesjsondec = json_decode($settingarrfilesdec, true);

        $fs = get_file_storage();
        $draftfiles = [];
        foreach ($settingsfilesjsondec as $settingsfilejsondec) {
            $settingfileentdec = html_entity_decode($settingsfilejsondec, ENT_COMPAT);
            $settingfilejsondec = json_decode($settingfileentdec, true);

            $base64dec = base64_decode($settingfilejsondec['content']);
            $filerecord = [
                'contextid' => $usercontextid,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => file_get_unused_draft_itemid(),
                'filepath' => '/',
                'filename' => $settingfilejsondec['filename'],
                // Don't use userid as could be different!
                'author' => $settingfilejsondec['author'],
                'license' => $settingfilejsondec['license'],
                'timecreated' => time(),
                'timemodified' => time(),
                'mimetype' => $settingfilejsondec['mimetype'],
            ];
            $draftfile = $fs->create_file_from_string($filerecord, $base64dec); // Draft.
            $draftfilehash = $fs->get_pathname_hash(
                $syscontextid,
                $component,
                $this->filearea,
                $this->itemid,
                $draftfile->get_filepath(),
                $draftfile->get_filename()
            );
            $draftfiles[$draftfilehash] = $draftfile;
        }

        // Got this far with all the draft files created...
        $replacementfilenames = [];
        $existingfiles = $fs->get_area_files(
            $syscontextid,
            $component,
            $this->filearea,
            $this->itemid,
            'sortorder,filepath,filename',
            false
        );

        foreach ($existingfiles as $existingfile) {
            if (!$existingfile->is_directory()) {
                $existinghash = $existingfile->get_pathnamehash();

                if (isset($draftfiles[$existinghash])) {
                    // Draft matches an existing file, is it identical content wise?
                    $draftcontenthash = $draftfiles[$existinghash]->get_contenthash();
                    $existingcontenthash = $existingfile->get_contenthash();
                    if ($draftcontenthash == $existingcontenthash) {
                        // Same, so remove draft.
                        $changed[toolbox::UNCHANGEDFILES][] = $draftfiles[$existinghash]->get_filename();
                        $draftfiles[$existinghash]->delete(); // Draft not needed.
                        unset($draftfiles[$existinghash]);
                    } else {
                        // Different, so delete existing and allow the new one to replace it.
                        $replacementfilenames[] = $existingfile->get_filename();
                        $existingfile->delete();
                        // Deleted.
                        unset($existingfiles[$existinghash]);
                    }
                } else {
                    // Existing file not in the setting.
                    $changed[toolbox::REMOVEDFILES][] = $existingfile->get_filename();
                    $existingfile->delete();
                    // Deleted.
                    unset($existingfiles[$existinghash]);
                }
            }
        }

        foreach ($draftfiles as $draftfile) {
            $filerecord = [
                'contextid' => $syscontextid,
                'component' => $component,
                'filearea' => $this->filearea,
                'itemid' => $this->itemid,
                'filepath' => '/',
                'filename' => $draftfile->get_filename(),
                'author' => $draftfile->get_author(),
                'license' => $draftfile->get_license(),
                'timecreated' => $draftfile->get_timecreated(),
                'timemodified' => $draftfile->get_timemodified(),
                'mimetype' => $draftfile->get_mimetype(),
            ];
            $settingfile = $fs->create_file_from_storedfile($filerecord, $draftfile); // New / replacement.

            $settingfilename = $settingfile->get_filename();
            if (in_array($settingfilename, $replacementfilenames)) {
                // Replacement.
                $changed[toolbox::REPLACEDFILES][] = $settingfilename;
            } else {
                // Added.
                $changed[toolbox::ADDEDFILES][] = $settingfilename;
            }

            $draftfile->delete(); // Finished with draft.
        }

        // All good?
        $currentdata = $this->get_setting();
        $result = ($this->config_write($this->name, $settingarrdata) ? '' : get_string('errorsetting', 'admin'));
        if (!empty($result)) {
            $changed[toolbox::ERROR] = $result;
        } else {
            if ($currentdata == $settingarrdata) {
                $changed[toolbox::UNCHANGEDFILEDATA] = $currentdata;
            } else {
                $changed[toolbox::CHANGEDFILEDATA] = [$currentdata, $settingarrdata];
            }
            $callbackfunction = $this->updatedcallback;
            if (!empty($callbackfunction) && is_callable($callbackfunction)) {
                $callbackfunction($this->get_full_name());
            }
        }

        return $changed;
    }
}
