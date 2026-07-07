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

namespace core\exception;

/**
 * Tests for moodle_exception.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle_exception_test extends \advanced_testcase {
    public function test_get_error_code(): void {
        $exception = new moodle_exception('authnotexisting', 'moodle');
        $this->assertEquals('authnotexisting', $exception->errorcode);
        $this->assertEquals(get_string('authnotexisting', 'error'), $exception->getMessage());
    }

    public function test_previous(): void {
        $previous = new \Exception('Previous exception');
        $exception = new moodle_exception('authnotexisting', 'moodle', '', null, '', $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
