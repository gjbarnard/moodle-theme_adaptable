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
 * Activity navigation main component.
 *
 * @module     theme_adaptable/core_course/activity_navigation
 * @class      theme_adaptable/core_course/activity_navigation
 * @copyright  2025 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import ajax from 'core/ajax';
import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import log from 'core/log';
import Notification from 'core/notification';
import Templates from 'core/templates';

export default class Component extends BaseComponent {

    static singleton = false;

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'theme_adaptable_activity_navigation';
    }

    /**
     * Static method to create a component instance from the mustache template.
     *
     * @param {element|string} target the DOM main element or its ID
     *
     * @return {Component}
     */
    static init(target) {
        if (this.singleton) {
            log.debug("Adaptable activity navigation Inited");
        } else {
            log.debug("Adaptable activity navigation Init");
            this.singleton = true;
            return new this({
                element: document.getElementById(target),
                reactive: getCurrentCourseEditor(),
            });
        }
    }

    getWatchers() {
        return [
            {watch: `cm.completionstate:updated`, handler: this._completionstateUpdated },
        ];
    }

    async _completionstateUpdated({element}) {
        log.debug("Adaptable activity navigation _completionstateUpdated element: " + JSON.stringify(element));

        const request = {
            methodname: 'theme_adaptable_activity_navigation',
            args: {
                moduleid: element.id
            }
        };

        ajax.call([request])[0]
            .then((jsoncontextstring) => {
                log.debug("Adaptable activity navigation update: " + jsoncontextstring);
                if (jsoncontextstring !== "") {
                    const context = JSON.parse(jsoncontextstring);
                    Templates.renderForPromise('theme_adaptable/core_course/activity_navigation', context)
                        .then(({ html, js }) => {
                            Templates.replaceNodeContents('#adaptable-activity-navigation', html, js);
                        })
                        .catch(error => Notification.exception(error));
                } else {
                    Notification.addNotification({
                        message: "Adaptable activity navigation update navigation not generated.",
                        type: 'error',
                    });
                }
            })
            .catch(error => {
                Notification.addNotification({
                    message: error.message,
                    type: 'error',
                });
            });
    }
}
