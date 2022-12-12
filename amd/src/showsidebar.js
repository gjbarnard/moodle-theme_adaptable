/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable AMD Show sidebar');

    return {
        init: function() {
            $(document).ready(function($) {
                log.debug('Adaptable AMD Show sidebar init');

                var sidePostClosed = true;
                var sidePost = $('#block-region-side-post');
                var showSideBarIcon = $('#showsidebaricon i.fa');
                var body = $('body');
                if (typeof sidePost != 'undefined') {
                    $('#showsidebaricon').click(function() {
                        if (sidePostClosed === true) {
                            sidePost.addClass('sidebarshown');
                            body.addClass('sidebarshown');
                            showSideBarIcon.removeClass('fa-angle-left');
                            showSideBarIcon.addClass('fa-angle-right');
                            sidePostClosed = false;
                        } else {
                            sidePost.removeClass('sidebarshown');
                            body.removeClass('sidebarshown');
                            showSideBarIcon.removeClass('fa-angle-right');
                            showSideBarIcon.addClass('fa-angle-left');
                            sidePostClosed = true;
                        }
                    });
                }
            });
        }
    };
});
/* jshint ignore:end */
