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
// Adaptable Bootstrap opt in functions JS file
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
define(['jquery', 'theme_adaptable/util', 'core/log'], function($, AdaptableUtil, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable Bootstrap AMD opt in functions');

    return {
        init: function(data) {
            $(document).ready(function($) {

                var body = $("body");
                // Get the navbar, if present.
                var navbar = document.getElementById("main-navbar");

                if (data.stickynavbar) {
                    /* New way to handle sticky navbar requirement.
                       Simply taken from https://www.w3schools.com/howto/howto_js_navbar_sticky.asp. */

                    const screenmd = 992;
                    const screensm = 768;
                    var windowWidth = $(window).width();
                    var currentWindowSize;
                    if (windowWidth < screensm) {
                        currentWindowSize = 1;
                    } else if (windowWidth < screenmd) {
                        currentWindowSize = 2;
                    } else {
                        currentWindowSize = 3;
                    }

                    // Container.
                    var header = document.getElementById("adaptable-page-header-wrapper");
                    var aboveHeader = document.getElementById("header1");
                    if (!aboveHeader) {
                        aboveHeader = document.getElementById("header2");
                    }

                    // Drawers.
                    var courseIndex = document.getElementById("theme_adaptable-drawers-courseindex");
                    var sidePost = document.getElementById("theme_adaptable-drawers-sidepost");
                    var drawerTogglers = document.getElementsByClassName("drawer-toggler");

                    // Page.
                    var page = document.getElementById("page");

                    // Adjustments.
                    var pageScrollTop = page.scrollTop;
                    var currentPageScrollTop = pageScrollTop;
                    var headerHeight = 0;
                    var headerNoNavbar = 0;
                    var navbarHeight = 0;
                    if (navbar !== null) {
                        navbarHeight = navbar.getBoundingClientRect().height;
                    }
                    var aboveHeaderHeight = aboveHeader.getBoundingClientRect().height;

                    var drawerPaddingTop = 0;
                    var newDrawerPaddingTop = 0;
                    var pageMarginTop = 0;
                    var newPageMarginTop = 0;
                    var headerTop = 0;
                    var newHeaderTop = 0;

                    var isFixed = 0;
                    /* Ok, here's an odd one... desktops need to use the 'inner' variables and mobiles the 'outer'
                       to be accurate! But... I've (GB) found that the jQuery height and width functions adapt and
                       report close to correct values regardless of device, so use them instead without complicated
                       device detection here!  Update: postion:fixed does not work on mobiles at the moment so won't
                       be for such, left comment for future info. */

                    /* Top navbar stickyness.
                    As per above comments, some issues noted with using CSS position: fixed, but these seem to mostly be constrained
                    to older browsers (inc. mobile browsers). May need to revisit!
                    https://caniuse.com/#feat=css-fixed */
                    if (windowWidth < screenmd) {
                        header.classList.remove("sticky");
                        body.addClass("page-header-margin");
                        isFixed = 1;
                    } else {
                        header.classList.add("sticky");
                        body.removeClass("page-header-margin");
                    }

                    $(window).resize(function() {
                        windowWidth = $(window).width();
                        if (windowWidth < screensm) {
                            if (currentWindowSize != 1) {
                                makeNavbarSticky(true);
                                currentWindowSize = 1;
                            }
                        } else if (windowWidth < screenmd) {
                            if (currentWindowSize != 2) {
                                makeNavbarSticky(true);
                                currentWindowSize = 2;
                            }
                        } else {
                            if (currentWindowSize != 3) {
                                currentWindowSize = 3;
                            }
                            // At screenmd and above, window width changes can change the height of the header.
                            makeNavbarSticky(true);
                        }
                        if (windowWidth < screenmd) {
                            if (isFixed === 0) {
                                header.classList.remove("sticky");
                                body.addClass("page-header-margin");
                                isFixed = 1;
                            }
                        } else {
                            if (isFixed === 1) {
                                header.classList.add("sticky");
                                body.removeClass("page-header-margin");
                                isFixed = 0;
                            }
                        }
                    });

                    var makeNavbarSticky = function(update = false) {
                        pageScrollTop = page.scrollTop;

                        if (windowWidth < screenmd) {
                            if ((!update) && (currentPageScrollTop == aboveHeaderHeight) && (pageScrollTop >= aboveHeaderHeight)) {
                                return;
                            }
                            pageScrollTop = aboveHeaderHeight;
                            newHeaderTop = 0;
                            newPageMarginTop = 0;
                        } else {
                            if ((!update) && (currentPageScrollTop == headerNoNavbar) && (pageScrollTop >= headerNoNavbar)) {
                                return;
                            }
                            if (update) {
                                // Just changed from <= screenmd.
                                headerHeight = header.getBoundingClientRect().height;
                                if (navbar !== null) {
                                    navbarHeight = navbar.getBoundingClientRect().height;
                                } // Else will never change from 0 at init!
                                headerNoNavbar = headerHeight - navbarHeight;
                            }
                            if (pageScrollTop > headerNoNavbar) {
                                pageScrollTop = headerNoNavbar;
                            }
                            newHeaderTop = -pageScrollTop;
                            newPageMarginTop = headerHeight - pageScrollTop;
                        }
                        currentPageScrollTop = pageScrollTop;

                        if ((update) || (newHeaderTop != headerTop)) {
                            header.style.top = newHeaderTop + 'px';
                            headerTop = newHeaderTop;
                        }
                        if ((update) || (newPageMarginTop != pageMarginTop)) {
                            page.style.marginTop = newPageMarginTop + 'px';
                            pageMarginTop = newPageMarginTop;
                        }

                        if ((courseIndex) || (sidePost)) {
                            if (windowWidth < screenmd) {
                                newDrawerPaddingTop = 0;
                            } else {
                                newDrawerPaddingTop = headerHeight - pageScrollTop;
                            }
                            if ((update) || (newDrawerPaddingTop != drawerPaddingTop)) {
                                drawerPaddingTop = newDrawerPaddingTop;
                                if (courseIndex) {
                                    courseIndex.style.paddingTop = drawerPaddingTop + 'px';
                                }
                                if (sidePost) {
                                    sidePost.style.paddingTop = drawerPaddingTop + 'px';
                                }
                                if ((courseIndex) || (sidePost)) {
                                    if (windowWidth < screenmd) {
                                        for (let dt = 0; dt < drawerTogglers.length; dt++) {
                                            drawerTogglers[dt].style.top = null;
                                        }
                                    } else {
                                        for (let dt = 0; dt < drawerTogglers.length; dt++) {
                                            drawerTogglers[dt].style.top = (drawerPaddingTop + 22) + 'px';
                                        }
                                    }
                                }
                            }
                        }
                    };
                    makeNavbarSticky(true);

                    // When the user scrolls the page, execute makeNavbarSticky().
                    page.onscroll = function() {makeNavbarSticky();};
                }

                $('.moodlewidth').click(function() {
                    if (body.hasClass('fullin') ) {
                        body.removeClass('fullin');
                        AdaptableUtil.setUserPreference('theme_adaptable_full', 'nofull');
                    } else {
                        body.addClass('fullin');
                        AdaptableUtil.setUserPreference('theme_adaptable_full', 'fullin');
                    }
                });

                $('#openoverlaymenu').click(function() {
                    $('#conditionalmenu').toggleClass('open');
                });
                $('#overlaymenuclose').click(function() {
                    $('#conditionalmenu').toggleClass('open');
                });

                // Bootstrap sub-menu functionality.
                // See: https://bootstrapthemes.co/demo/resource/bootstrap-4-multi-dropdown-hover-navbar/.

                $( '.dropdown-menu a.dropdown-toggle' ).on( 'click', function () {
                    var $el = $(this);
                    var $parent = $(this).offsetParent( ".dropdown-menu" );
                    if ( !$(this).next().hasClass('show')) {
                        $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                    }
                    var $subMenu = $( this ).next(".dropdown-menu");
                    $subMenu.toggleClass('show');

                    $(this).parent("li").toggleClass('show');

                    $(this).parents('li.nav-item.dropdown.show').on( 'hidden.bs.dropdown', function () {
                        $('.dropdown-menu .show').removeClass("show");
                    });

                     if (!$parent.parent().hasClass( 'navbar-nav')) {
                        $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4 });
                    }

                    return false;
                } );

            });

            /* Conditional javascript to resolve anchor link clicking issue with sticky navbar.
               in old bootstrap version. Re: issue #919.
               Original issue / solution discussion here: https://github.com/twbs/bootstrap/issues/1768. */
            if (data.stickynavbar) {
                var shiftWindow = function() {scrollBy(0, -50);};
                if (location.hash) {
                    shiftWindow();
                }
                window.addEventListener("hashchange", shiftWindow);
            }
        }
    };
});
/* jshint ignore:end */
