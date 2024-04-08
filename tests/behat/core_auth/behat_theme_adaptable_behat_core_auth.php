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
 * Basic authentication steps definitions.
 *
 * @package    theme_adaptable
 * @category   test
 * @copyright  &copy; 2020 G J Barnard.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../../auth/tests/behat/behat_auth.php');

/**
 * Log out step definition.
 *
 * @package    theme_adaptable
 * @category   test
 * @copyright  &copy; 2020 G J Barnard.
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class behat_theme_adaptable_behat_core_auth extends behat_auth {
    /**
     * Logs out of the system.
     */
    public function i_log_out() {

        // Wait for page to be loaded.
        $this->wait_for_pending_js();

        // Click on logout link in user menu on the navbar.
        $this->execute('behat_general::i_click_on', ['#usermenu', 'css_element']);
        $this->execute('behat_general::i_click_on_in_the', [get_string('logout'), 'link', '#usermenu-dropdown', "css_element"]);
    }
}
