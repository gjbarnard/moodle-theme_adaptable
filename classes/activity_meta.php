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
 * Activity-related meta data.
 *
 * This defines the activity_meta class that is used to store information such as submission status,
 * due dates etc.
 *
 * @package   theme_adaptable
 * @copyright 2018 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace theme_adaptable;

defined('MOODLE_INTERNAL') || die();

/**
 * Activity meta data.
 *
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_meta {

    // Strings.
    /**
     * @var string $submitstrkey - language string key.
     */
    public $submitstrkey = '';

    // Teacher meta data.
    /**
     * @var bool $isteacher - true if meta data is intended for teacher.
     */
    public $isteacher = false;

    /**
     * @var bool $submissionnotrequired - true if a submission is not required.
     */
    public $submissionnotrequired = false;

    /**
     * @var bool $grade - has the submission been graded.
     */
    public $grade = false;

    /**
     * @var int $numsubmissions - number of submissions.
     */
    public $numsubmissions = 0;

    /**
     * @var int $numrequiregrading - number of submissions requiring grading.
     */
    public $numrequiregrading = 0;

    /**
     * @var int $numparticipants - number of participants.
     */
    public $numparticipants = 0;
}
