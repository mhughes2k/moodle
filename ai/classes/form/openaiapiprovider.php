<?php

namespace core_ai\form;

class openaiapiprovider extends \core\form\persistent{
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
        $mform->addElement('text','baseurl', get_string('baseurl', 'ai'));
        $mform->setType('baseurl', PARAM_URL);
        $mform->addElement('text', 'apikey', get_string('apikey', 'ai'));
        $mform->addRule('apikey', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('apikey', 'apikey', 'ai');

        $mform->addElement('header', 'features', get_string('features', 'ai'));

        $mform->addElement('advcheckbox', 'allowchat', get_string('allowchat', 'ai'));
        $mform->addHelpButton('allowchat', 'allowchat', 'ai');
        $mform->addElement('text','completions', get_string('completionspath', 'ai'));
        $mform->addElement('text','completionmodel', get_string('completionmodel', 'ai'));
        $mform->disabledIf('completions', 'allowchat', 'notchecked');
        $mform->disabledIf('completionmodel', 'allowchat', 'notchecked');

        $mform->addElement('advcheckbox', 'allowembeddings', get_string('allowembeddings', 'ai'));
        $mform->addHelpButton('allowembeddings', 'allowembeddings', 'ai');
        $mform->addElement('text','embeddings', get_string('embeddingspath', 'ai'));
        $mform->addElement('text','embeddingmodel', get_string('embeddingmodel', 'ai'));
        $mform->disabledIf('embeddings', 'allowembeddings', 'notchecked');
        $mform->disabledIf('embeddingmodel', 'allowembeddings', 'notchecked');

        $mform->addElement('header','constraints', get_string('constraints', 'ai'));
        $mform->addElement('checkbox', 'systemwide', get_string('allowsystemwide', 'ai'));
        $displaylist = \core_course_category::make_categories_list('moodle/course:changecategory');
//        if (!isset($displaylist[$course->category])) {
//            //always keep current
//            $displaylist[$course->category] = core_course_category::get($course->category, MUST_EXIST, true)
//                ->get_formatted_name();
//        }
        $mform->addElement('autocomplete', 'contextid', get_string('coursecategory'), $displaylist);
//        $mform->addRule('contextid', null, 'required', null, 'client');
        $mform->addHelpButton('contextid', 'coursecategory');
        $mform->disabledIf('contextid', 'systemwide', 'checked');

        $this->add_action_buttons(true, get_string('savechanges', 'ai'));
    }
}
