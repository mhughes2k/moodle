<?php
// This file is part of Moodle - http://moodle.org
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

namespace assignfeedback_comments\event;
use \mod_assign\event\base;

/**
 * Event indicates that a user performed an update of grades via offline worksheet.
 */
class feedbackcomment_saved extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * @param \assign $assign
     * @param int $gradeid ID of the grade feedback comment was saved against.
     * @param int $relateduserid ID of the user comment was saved against.
     * @return feedbackcomment_saved
     * @throws \coding_exception
     */
    public static function create_from_assign(\assign $assign, $gradeid, $relateduserid) {
        $data = [
            'context' => $assign->get_context(),
            'objectid' => $assign->get_instance()->id,
            'relateduserid' => $relateduserid,
            'other' => [
                'gradeid' => $gradeid
            ]
        ];
        self::$preventcreatecall = false;
        /** @var feedbackcomment_saved $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_assign($assign);
        return $event;
    }
    /**
     * Returns description of the event.
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has saved feedback comment for the user id '$this->relateduserid' " .
        "for the assignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns localised event name.
     * @return \lang_string|string
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('eventfeedbackcommentsaved', 'assignfeedback_comments');
    }

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'assign';
    }
}
