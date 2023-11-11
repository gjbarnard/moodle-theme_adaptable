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
// Adaptable Template Preview JS file
//
// @package    theme_adaptable
// @copyright  2020 G J Barnard
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict"; // jshint ;_;

    log.debug('Adaptable Template Preview AMD');

    return {
        init: function() {
            $(document).ready(function() {
                log.debug('Adaptable Template Preview AMD init');
            });
        }
    };
});
/* jshint ignore:end */
