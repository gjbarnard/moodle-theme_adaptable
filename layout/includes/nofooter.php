<?php
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

/**
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2021 G J Barnard (http://moodle.org/user/profile.php?id=442195)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

echo $OUTPUT->standard_after_main_region_html();
echo '</div>'; // End #page.
echo '</div>'; // End #page-wrapper.
echo $OUTPUT->standard_end_of_body_html();
echo $PAGE->theme->settings->jssection;
echo $OUTPUT->get_all_tracking_methods(); ?>
<script type="text/javascript">
    M.util.js_pending('theme_boost/loader');
        require(['theme_boost/loader'], function() {
        M.util.js_complete('theme_boost/loader');
    });
</script>
</body>
</html>
<?php
