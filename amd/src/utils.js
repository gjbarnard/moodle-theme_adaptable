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
