<?php

namespace mod_xaichat;

//defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class aichatform extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden','id', 'id');
//        $mform->setConstant('id', );
        $mform->setType('id', PARAM_INT);
        $mform->addElement('textarea', 'userprompt', 'User Prompt');

        $this->add_action_buttons(true,'Send');
//        $buttons = [
//            $mform->createElement('submit','submitbutton','Send'),
//            $mform->createElement('cancel','submitbutton','Cancel'),
//            $mform->createElement('submit','restartbutton','Restart')
//        ];
//        $mform->addGroup($buttons, 'actionbuttons', ' ', array(' '), false);

    }
}
