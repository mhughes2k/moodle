<?php

namespace core_ai\form;

class aiprovider extends \core\form\persistent{
    /** @var string $persistentclass */
    protected static $persistentclass = 'core_ai\\aiprovider';

    public function __construct($action = null, $customdata = null, $method = 'post', $target = '', $attributes = null,
                                $editable = true, array $ajaxformdata = null){
        if (array_key_exists('type', $customdata)) {
            $this->type = $customdata['type'];
        }

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }
    public function definition() {
        global $PAGE, $OUTPUT;

        $mform = $this->_form;
        $provider = $this->get_persistent();
        $mform->addElement('html','intro', 'hello');

        // Name.
        $mform->addElement('text', 'name', get_string('providername', 'ai'));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'providername', 'ai');

        // Client Secret.
        $mform->addElement('text', 'apikey', get_string('apikey', 'ai'));
        $mform->addRule('apikey', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('apikey', 'apikey', 'ai');

        $mform->addElement('header', 'features', get_string('features', 'ai'));
        $mform->addElement('advcheckbox', 'allowchat', get_string('allowchat', 'ai'));
        $mform->addHelpButton('allowchat', 'allowchat', 'ai');

        $this->add_action_buttons(true, get_string('savechanges', 'ai'));
    }
}
