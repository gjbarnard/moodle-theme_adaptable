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
// @copyright  2019 G J Barnard
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable AMD');

    return {
        init: function() {
            $(document).ready(function($) {

                log.debug('Adaptable AMD init');

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