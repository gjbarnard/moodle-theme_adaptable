//
// This file is part of Adaptable theme for moodle
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
//
//
// Adaptable main JS file
//
// @package    theme_adaptable
// @copyright  2015-2019 Jeremy Hopkins (Coventry University)
// @copyright  2018-2019 Manoj Solanki (Coventry University)
// @copyright  2019 G J Barnard
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/* jshint ignore:start */
define(["jquery", "core/log"], function($, log) {

    "use strict"; // ... jshint ;_;.
    return {
        init: function(currentpage, tabpersistencetime) {

            $(document).ready(function($) {
                if (currentpage == 'coursepage') {
                    var hasStorage = ("sessionStorage" in window && window.sessionStorage);
                    var now, expiration;
                    var currentUrl = document.location.toString();

                    if ( (hasStorage) && (currentUrl.indexOf('course/view.php?id=') != -1) ) {

                        var tabValues = JSON.parse(sessionStorage.getItem('tabValues')) || {};
                        var tabTimestamp = JSON.parse(sessionStorage.getItem('tabTimestamp'));
                        var $radiobuttons = $("#coursetabcontainer :radio");

                        // Check timestamp for session.
                        if (tabTimestamp) {
                            // calculate expiration time for content,
                            // to force periodic refresh after 30 minutes
                            now = new Date();
                            expiration = new Date(tabTimestamp);
                            expiration.setMinutes(expiration.getMinutes() + parseInt(tabpersistencetime));
                            if (now.getTime() > expiration.getTime()) {
                                log.debug('Expired');
                                sessionStorage.removeItem('tabTimestamp');
                                sessionStorage.removeItem('tabValues');
                                tabValues = {};
                            }

                            // Reset timestamp anyway as user is still active.
                            sessionStorage.setItem("tabTimestamp", JSON.stringify(new Date()));
                        } else {
                            sessionStorage.setItem("tabTimestamp", JSON.stringify(new Date()));
                            log.debug('Setting timestamp');
                        }

                        var params = (new URL(document.location)).searchParams;
                        var courseid = params.get("id");

                        $radiobuttons.on("change", function() {

                            $radiobuttons.each(function(){
                                if (this.checked) {
                                    tabValues[courseid] = this.id;
                                }
                            });
                            sessionStorage.setItem("tabValues", JSON.stringify(tabValues));
                        });

                        var tabhasbeenset = false;
                        $.each(tabValues, function(key, value) {
                            if (key == courseid) {
                                $("#" + value).prop('checked', true);
                                tabhasbeenset = true;
                            }
                        });
                        if (tabhasbeenset == false) {
                            $("input:radio[name=tabs]:first").attr('checked', true);
                        }

                        $('label.coursetab').show();
                    }

                }
            });
        }
    };
});
/* jshint ignore:end */
