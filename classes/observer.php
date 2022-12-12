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
 * Event observers
 *
 * @package   theme_adaptable
 * @copyright 2021 G J Barnard.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers supported by this theme.
 */
class theme_adaptable_observer {

    /**
     * Observer for the role_allow_view_updated event.
     */
    public static function role_allow_view_updated() {
        /* Subsitute for a 'role created' event that does not exist in core!
           But this seems to happen when a role is created.  See 'create_role'
           in lib/accesslib.php. */
        \theme_adaptable\activity::invalidatestudentrolescache();
        \theme_adaptable\activity::invalidatemodulecountcache();
        \theme_adaptable\activity::invalidatestudentscache();
    }

    /**
     * Observer for the role_updated event.
     */
    public static function role_updated() {
        \theme_adaptable\activity::invalidatestudentrolescache();
        \theme_adaptable\activity::invalidatemodulecountcache();
        \theme_adaptable\activity::invalidatestudentscache();
    }

    /**
     * Observer for the role_deleted event.
     */
    public static function role_deleted() {
        \theme_adaptable\activity::invalidatestudentrolescache();
        \theme_adaptable\activity::invalidatemodulecountcache();
        \theme_adaptable\activity::invalidatestudentscache();
    }

    /**
     * Observer for the user_enrolment_created event.
     *
     * @param \core\event\user_enrolment_created $event
     */
    public static function user_enrolment_created(\core\event\user_enrolment_created $event) {
        \theme_adaptable\activity::userenrolmentcreated($event->relateduserid, $event->courseid);
    }

    /**
     * Observer for the user_enrolment_updated event.
     *
     * @param \core\event\user_enrolment_updated $event
     */
    public static function user_enrolment_updated(\core\event\user_enrolment_updated $event) {
        \theme_adaptable\activity::userenrolmentupdated($event->relateduserid, $event->courseid);
    }

    /**
     * Observer for the user_enrolment_deleted event.
     *
     * @param \core\event\user_enrolment_deleted $event
     */
    public static function user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        \theme_adaptable\activity::userenrolmentdeleted($event->relateduserid, $event->courseid);
    }

    /**
     * Observer for the course_module_created event.
     *
     * @param \core\event\course_module_created $event
     */
    public static function course_module_created(\core\event\course_module_created $event) {
        \theme_adaptable\activity::modulecreated($event->objectid, $event->courseid);
    }

    /**
     * Observer for the course_module_updated event.
     *
     * @param \core\event\course_module_updated $event
     */
    public static function course_module_updated(\core\event\course_module_updated $event) {
        \theme_adaptable\activity::moduleupdated($event->objectid, $event->courseid);
    }

    /**
     * Observer for the course_module_deleted event.
     *
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        \theme_adaptable\activity::moduledeleted($event->objectid, $event->courseid);
    }
}
