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

//
// Slider
//
// @module     theme_adaptable/slider
// @copyright  2024 G J Barnard.
// @author     G J Barnard -
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//

import $ from 'jquery';
import { flexslider } from 'theme_adaptable/flexslider';
import log from 'core/log';

/**
 * Initialise this module.
 */
export const init = () => {

    const sliderInit = () => {
        flexslider();

        // Slider.
        if ($('#main-slider').length) {
            $('#main-slider').flexslider({
                // See flexslider.js for explanations.
                namespace: "flex-",
                selector: ".slides > li",
                animation: "slide",
                easing: "swing",
                direction: "horizontal",
                reverse: false,
                animationLoop: true,
                smoothHeight: false,
                startAt: 0,
                slideshow: true,
                slideshowSpeed: 7000,
                animationSpeed: 600,
                initDelay: 0,
                randomize: false,

                // Usability features.
                pauseOnAction: true,
                pauseOnHover: false,
                useCSS: true,
                touch: true,
                video: false,

                // Primary Controls.
                controlNav: true,
                directionNav: true,
                prevText: "Previous",
                nextText: "Next",

                // Secondary Navigation.
                keyboard: true,
                multipleKeyboard: false,
                mousewheel: false,
                pausePlay: false,
                pauseText: 'Pause',
                playText: 'Play',

                // Special properties.
                controlsContainer: "",
                manualControls: "",
                sync: "",
                asNavFor: "",

                // Callback API
                start: function(slider) {
                    log.debug("Adaptable ES6 Slider resize start");
                    slider.resize();
                },
            });

            if (('#theme_adaptable-drawers-sidepost').length) {
                $('#theme_adaptable-drawers-sidepost').on('webkitTransitionEnd msTransitionEnd transitionend', function() {
                    var slider = $('#main-slider').data('flexslider');
                    log.debug("Adaptable ES6 Slider sidepost drawer resize");
                    slider.resize();
                });
            }
        }
    };

    if (document.readyState !== 'loading') {
        log.debug("Adaptable ES6 Slider JS DOM content already loaded");
        sliderInit();
    } else {
        log.debug("Adaptable ES6 Slider JS DOM content not loaded");
        document.addEventListener('DOMContentLoaded', function () {
            log.debug("Adaptable ES6 Slider JS DOM content loaded");
            sliderInit();
        });
    }
};
