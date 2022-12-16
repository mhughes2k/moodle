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
 * Course Copy completed event.
 *
 * @package moodlecore
 * @subpackage backup
 */
namespace core\event;

use core\event\base;
use core_competency\plan;

defined('MOODLE_INTERNAL') || die();

/**
 * Returns description of course_copy.
 */
class asynchronous_copy_completed extends base {
    const OTHER_KEY_NEWCOURSEID = 'newcourseid';
    const OTHER_KEY_SOURCECOURSEID = 'sourcecourseid';
    public static final function create_from_copy_task($sourcecourseid, $newcourse) {
        $context = \context_course::instance($newcourse->id);

        $event = static::create([
            'contextid' => $context,
            'objectid' => $newcourse->id,
            'other' => [
                self::OTHER_KEY_SOURCECOURSEID => $sourcecourseid,
                self::OTHER_KEY_NEWCOURSEID => $newcourse->id
            ]
        ]);
        return $event;
    }
    public function get_description() {
        return "Copy of course '{$this->other[self::OTHER_KEY_SOURCECOURSEID]}'. New course: '{$this->objectid}' created.";
    }
    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('asynccopycompleted', 'backup', (object)$a);
    }

    public function get_url() {
        return new \moodle_url('/course/view.php', [
            'id' => $this->objectid
        ]);
    }

    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'course';
    }
}
