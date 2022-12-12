/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable Bootstrap AMD opt in functions');

    return {
        init: function(data) {
            $(document).ready(function($) {

                // Get the navbar, if present.
                var navbar = document.getElementById("main-navbar");

                if (data.stickynavbar && navbar !== null) {
                    /* New way to handle sticky navbar requirement.
                      Simply taken from https://www.w3schools.com/howto/howto_js_navbar_sticky.asp. */

                    // Initial sticky position.
                    var sticky = navbar.offsetTop;

                    // Container.
                    var header = document.getElementById("adaptable-page-header-wrapper");

                    /* Add the sticky class to the navbar when you reach its scroll position.
                       Remove "sticky" when you leave the scroll position. */
                    var makeNavbarSticky = function() {
                        if (sticky > 0) {
                            if (window.pageYOffset >= sticky) {
                                if (isSticky === false) {
                                    navbar.classList.add("adaptable-navbar-sticky");
                                    header.style.paddingTop = navbar.offsetHeight + 'px';
                                    isSticky = true;
                                }
                            } else {
                                if (isSticky === true) {
                                    navbar.classList.remove("adaptable-navbar-sticky");
                                    header.style.paddingTop = '0px';
                                    isSticky = false;
                                }
                            }
                        }
                    };

                    // Adjust sticky if 0 when window resizes.
                    var checkSticky = function() {
                        if (sticky === 0) {
                            sticky = navbar.offsetTop;
                            isSticky = (window.pageYOffset < sticky);
                            // Check if we are already down the page because of an anchor etc.
                            makeNavbarSticky();
                        }
                    };

                    // When the user scrolls the page, execute makeNavbarSticky().
                    window.onscroll = function() {makeNavbarSticky();};

                    // When the page changes size, check the sticky.
                    window.onresize = function() {checkSticky();};

                    // Changed?
                    var isSticky = (window.pageYOffset < sticky); // Initial inverse logic to cause first check to work.

                    // Check if we are already down the page because of an anchor etc.
                    makeNavbarSticky();
                }

                var screenmd = 979;

                var isFixed = 0;
                /* Ok, here's an odd one... desktops need to use the 'inner' variables and mobiles the 'outer' to be accurate!
                But... I've (GB) found that the jQuery height and width functions adapt and report close to correct values
                regardless of device, so use them instead without complicated device detection here!
                Update: postion:fixed does not work on mobiles at the moment so won't be for such, left comment for future info. */

                /* Top navbar stickyness.
                   As per above comments, some issues noted with using CSS position: fixed, but these seem to mostly be constrained
                   to older browsers (inc. mobile browsers). May need to revisit!
                   https://caniuse.com/#feat=css-fixed */
                var stickything = $(".stickything");
                var body = $("body");
                if ($(window).width() <= screenmd) {
                    stickything.addClass("fixed-top");
                    body.addClass("page-header-margin");
                    isFixed = 1;
                } else {
                    stickything.removeClass("fixed-top");
                    body.removeClass("page-header-margin");
                }

                /* If you want these classes to toggle when a desktop user shrinks the browser width to
                   an xs width - or from xs to larger. */
                $(window).resize(function() {
                    if ($(window).width() <= screenmd) {
                        if (isFixed === 0) {
                            stickything.addClass("fixed-top");
                            body.addClass("page-header-margin");
                            isFixed = 1;
                        }
                    } else {
                        if (isFixed === 1) {
                            stickything.removeClass("fixed-top");
                            body.removeClass("page-header-margin");
                            isFixed = 0;
                        }
                    }
                });

                var showsidebaricon = $("#showsidebaricon");
                if (showsidebaricon.length) {
                    // Using 'css' and not 'offset' function as latter seems unreliable on mobiles as changes the value!
                    showsidebaricon.css({ top: ($(window).height() / 2) + 'px'});
                }

                $(window).resize(function() {
                    if ($(window).width() > screenmd) {
                        var navDrawer = $("#nav-drawer");
                        if (navDrawer.length) {
                            if (!navDrawer.hasClass("closed")) {
                                navDrawer.addClass("closed");
                                navDrawer.attr("aria-hidden", "true");
                                $("#drawer").attr("aria-expanded", "false");
                                var side = $('#drawer').attr('data-side');
                                body.removeClass("drawer-open-" + side);
                            }
                        }
                    }
                    if (showsidebaricon.length) {
                        showsidebaricon.css({ top: ($(window).height() / 2) + 'px'});
                    }
                });

                $('.moodlewidth').click(function() {
                    if ($('#page').hasClass('fullin') ) {
                        $('#page').removeClass('fullin');
                        M.util.set_user_preference('theme_adaptable_full', 'nofull');
                    } else {
                        $('#page').addClass('fullin');
                        M.util.set_user_preference('theme_adaptable_full', 'fullin');
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
