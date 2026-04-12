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
 * Define unit tests for the settings toolbox class.
 *
 * @package    theme_adaptable
 * @copyright  &copy; 2026 G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

/**
 * Toolbox unit tests for the Adaptable theme.
 * @group theme_adaptable
 * @copyright Copyright (c) 2017 Manoj Solanki (Coventry University)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
final class settings_toolbox_test extends \advanced_testcase {
    /**
     * Set up.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        set_config('theme', 'adaptable');
    }

    /**
     * Percentages test.
     * @covers \theme_adaptable\settings_toolbox::percentages
     */
    public function test_percentages(): void {
        $percentages = settings_toolbox::percentages(95, 100, 1, '%');
        $count = (!empty($percentages)) ? count($percentages) : 0;

        $this->assertTrue(
            ($count == 6),
            'Incorrect number of percentages: ' . $count . ', should be 6.'
        );
        if (!empty($percentages)) {
            $this->assertArrayHasKey('95%', $percentages);
            $this->assertArrayHasKey('100%', $percentages);
            $this->assertContains('95%', $percentages);
            $this->assertContains('100%', $percentages);
        }
    }

    /**
     * Units test.
     * @covers \theme_adaptable\settings_toolbox::percentages
     */
    public function test_units(): void {
        // Ref: http://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit.
        // and http://php.net/manual/en/reflectionmethod.invoke.php.
        $reflectionmethod = new \ReflectionMethod('\theme_adaptable\settings_toolbox', 'units');
        $reflectionmethod->setAccessible(true);

        $pixels = $reflectionmethod->invoke(null, 0, 20, 1, 'px');
        $count = (!empty($pixels)) ? count($pixels) : 0;

        $this->assertTrue(
            ($count == 21),
            'Incorrect number of pixels: ' . $count . ', should be 21.'
        );
        if (!empty($pixels)) {
            $this->assertArrayHasKey('0px', $pixels);
            $this->assertArrayHasKey('12px', $pixels);
            $this->assertArrayHasKey('20px', $pixels);
            $this->assertContains('0px', $pixels);
            $this->assertContains('14px', $pixels);
            $this->assertContains('20px', $pixels);
        }

        $percentages = $reflectionmethod->invoke(null, 90, 95, 0.5, '%');
        $count = (!empty($percentages)) ? count($percentages) : 0;

        $this->assertTrue(
            ($count == 11),
            'Incorrect number of percentages: ' . $count . ', should be 11.'
        );
        if (!empty($percentages)) {
            $this->assertArrayHasKey('90%', $percentages);
            $this->assertArrayHasKey('92.5%', $percentages);
            $this->assertArrayHasKey('95%', $percentages);
            $this->assertContains('90%', $percentages);
            $this->assertContains('92.5%', $percentages);
            $this->assertContains('95%', $percentages);
        }
    }
}
