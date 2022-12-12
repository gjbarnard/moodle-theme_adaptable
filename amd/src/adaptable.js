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
// @copyright  2015-2019 Fernando Acedo (3-bits.com)
// @copyright  2018-2019 Manoj Solanki (Coventry University)
//
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable AMD');

    return {
        init: function() {
            $(document).ready(function($) {

                log.debug('Adaptable AMD init');

                /* Dismiss Alerts
                   Bootstrap will close the alert because it spots the data-dismiss="alert" attribute
                   Here we also handle the alert close event. We have added two custom tags data-alertindex
                   and data-alertkey. e.g Alert1  has alertindex1. The alertkey value identifies the
                   alert content, since Alert1 (2 and 3) will be reused. We use a YUI function to set
                   the user preference for the key to the last dismissed key for the alertindex.
                   alertkey undismissable is a special case for "loginas" alert which shouldn't really
                   be permanently dismissed.
                   Justin 2015/12/05. */

                $('.close').click(function() {
                    var alertindex = $(this).data('alertindex');
                    var alertkey = $(this).data('alertkey');
                    if (alertkey != 'undismissable' && alertkey != 'undefined' && alertkey) {
                        M.util.set_user_preference('theme_adaptable_alertkey' + alertindex, alertkey);
                    }
                });

                // Breadcrumb.
                $(".breadcrumb li:not(:last-child) span").not('.separator').addClass('');
                $(".breadcrumb li:last-child").addClass("lastli");

                // Edit button keep position.  Needs session storage!
                try {
                    $('.context-header-settings-menu .dropdown-menu .dropdown-item a[href*="edit"], #editingbutton a')
                        .click(function(event) {
                        event.preventDefault();

                        var to = $(window).scrollTop();
                        sessionStorage.setItem('scrollTo', to);
                        var url = $(this).prop('href');
                        window.location.replace(url);

                        return false;
                    });
                    var scrollTo = sessionStorage.getItem('scrollTo');
                    if (scrollTo !== null) {
                        window.scrollTo(0, scrollTo);
                        sessionStorage.removeItem('scrollTo');
                    }
                } catch(e) {
                    log.debug('Adaptable: Session storage exception: ' + e.name);
                }

                // Scroll to top.
                var offset = 50;
                var duration = 500;
                var bttOn;
                if ($(window).scrollTop() > offset) {
                    bttOn = false;
                } else {
                    bttOn = true;
                }
                var scrollCheck = function() {
                    if ($(window).scrollTop() > offset) {
                        if (bttOn == false) {
                            bttOn = true;
                            $('#back-to-top').fadeIn(duration);
                        }
                    } else {
                        if (bttOn == true) {
                            bttOn = false;
                            $('#back-to-top').fadeOut(duration);
                        }
                    }
                };
                scrollCheck();
                $(window).scroll(function () {
                    scrollCheck();
                });

                $('#back-to-top').click(function(event) {
                    event.preventDefault();
                    $('html, body').animate({scrollTop: 0}, duration);
                    return false;
                });

                // Anchor.
                if (window.location.hash) {
                    if ($("body").hasClass("pagelayout-course")) {
                        var anchorTop = $(window.location.hash).offset().top;
                        $('html, body').animate({scrollTop: anchorTop - 102}, duration);
                    }
                }
            });
        }
    };
});
/* jshint ignore:end */