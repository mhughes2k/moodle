<?php

namespace core_ai\form;
use core_ai\api;
class openaiapiprovider extends \core\form\persistent{
    /** @var string $persistentclass */
    protected static $persistentclass = 'core_ai\\aiprovider';

    protected static $fieldstoremove = [
        'type',
        'submitbutton',
        'action'
    ];

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

        $displaylist = [
            ""  => get_string('anywhere', 'ai'),
            "-1" => get_string('anyusercourse', 'ai')
        ];
        $displaylist =
            $displaylist +
            \core_course_category::make_categories_list('moodle/ai:selectcategory')
        ;
        
        $mform->addElement('autocomplete', 'categoryid', get_string('scopecoursecategory','ai'), $displaylist);
        $mform->addHelpButton('categoryid', 'scopecoursecategory', 'ai');
        $mform->setDefault('categoryid', null); // a null category is technical "whole" site

        $coursedisplaylist = \get_courses("all", "shortname");
        $coursedisplaylist = array_map(function($course) {
            return $course->shortname;
        }, $coursedisplaylist);
        $coursedisplaylist = ["" => "No Restriction"] + $coursedisplaylist;
        $mform->addElement('autocomplete', 'courseid', get_string('course'), $coursedisplaylist);
        $mform->addHelpButton('courseid', 'scopecourse', 'ai');
        $mform->setDefault('courseid', null); // a null category is technical "whole" site
//        $mform->disabledIf('courseid', 'categoryid', 'neq', "");

        $mform->addElement('hidden', 'contextid', );
        $mform->setType('contextid', PARAM_RAW);

        $mform->addElement('hidden', 'onlyenrolledcourses', );
        $mform->setType('onlyenrolledcourses', PARAM_RAW);

        $mform->addElement('hidden', 'enabled', true);
        $mform->setType('enabled', PARAM_ALPHA);
        
        $mform->addElement('hidden', 'type', $this->_customdata['type']);
        $mform->setType('type', PARAM_ALPHANUM);

        $mform->addElement('hidden', 'action', api::ACTION_EDIT_PROVIDER);
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'id', $provider->get('id'));
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons(true, get_string('savechanges', 'ai'));
    }

    protected function filter_data_for_persistent($data) {
        if (!empty($data->categoryid)) {
            $data->onlyenrolledcourses = false;
            if ($data->categoryid >0) {
                $data->contextid = \core_course_category::get($data->categoryid)->get_context()->id;
            } else{
                $data->contextid = $data->categoryid;
                if ($data->contextid == -1) {
                    $data->onlyenrolledcourses = true;
                }
            }
        } else if (!empty($data->courseid)) {
            $data->contextid = \core\context\course::instance($data->courseid)->id;
        }
        return (object) array_diff_key((array) $data, array_flip((array) static::$foreignfields));
    }
    function extra_validation($data, $files, array &$errors) {
        parent::extra_validation($data, $files, $errors);
        if(!empty($data->categoryid) && !empty($data->courseid)) {
            // $data->category is not allowed to be set.
            $errors['courseid'] = "Course constraint cannot be set whilst a category one is set";
        }
        var_dump($errors);
        return $errors;
    }
}
