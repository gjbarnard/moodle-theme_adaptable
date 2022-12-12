/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {
    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable AMD Zoom in');

    return {
        init: function() {
            $(document).ready(function($) {
                log.debug('Adaptable AMD Zoom in init');

                var zoomInIcon = $('#zoominicon');
                if (zoomInIcon.length) {
                    var sidePost = $('#block-region-side-post');
                    var zoomInFaIcon = $('#zoominicon i.fa');
                    var body = $('body');
                    var zoomLeft = false;
                    if (zoomInIcon.hasClass('left')) {
                        zoomLeft = true;
                    }
                    var hidestring = zoomInIcon.data('hidetitle');
                    var showstring = zoomInIcon.data('showtitle');
                    var showhideblocksdesc = $('.showhideblocksdesc');

                    if (typeof sidePost != 'undefined') {
                        zoomInIcon.click(function() {
                            if (body.hasClass('zoomin') ) { // Blocks not shown.
                                body.removeClass('zoomin');
                                if (zoomLeft) {
                                    zoomInFaIcon.removeClass('fa-indent');
                                    zoomInFaIcon.addClass('fa-outdent');
                                } else {
                                    zoomInFaIcon.removeClass('fa-outdent');
                                    zoomInFaIcon.addClass('fa-indent');
                                }
                                M.util.set_user_preference('theme_adaptable_zoom', 'nozoom');
                                zoomInIcon.prop('title', hidestring);
                                if (showhideblocksdesc.length) {
                                    showhideblocksdesc.text(hidestring);
                                }
                            } else {
                                body.addClass('zoomin');
                                if (zoomLeft) {
                                    zoomInFaIcon.removeClass('fa-outdent');
                                    zoomInFaIcon.addClass('fa-indent');
                                } else {
                                    zoomInFaIcon.removeClass('fa-indent');
                                    zoomInFaIcon.addClass('fa-outdent');
                                }
                                M.util.set_user_preference('theme_adaptable_zoom', 'zoomin');
                                zoomInIcon.prop('title', showstring);
                                if (showhideblocksdesc.length) {
                                    showhideblocksdesc.text(showstring);
                                }
                            }
                        });
                    }
                }
            });
        }
    };
});
/* jshint ignore:end */
