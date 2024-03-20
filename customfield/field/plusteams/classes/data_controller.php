<?php
namespace customfield_plusteams;

defined('MOODLE_INTERNAL') || die;

class data_controller extends \core_customfield\data_controller {
    public function datafield(): string {
        return 'stringvalue';
    }

    public function get_default_value() {
        $defaultvalue = $this->get_field()->get_configdata_property('defaultvalue');
        if ('' . $defaultvalue !== '') {
            $key = array_search($defaultvalue, $this->get_field()->get_options());
            if ($key !== false) {
                return $key;
            }
        }
        return 0;
    }

    public function instance_form_definition(\MoodleQuickForm $mform) {
//        $field = $this->get_field();
//        $config = $field->get('configdata');
//        $options = $field->get_options();
//        $formattedoptions = array();
//        $context = $this->get_field()->get_handler()->get_configuration_context();
//        local_plusteams_linkform($mform);
    }
    public function instance_form_validation(array $data, array $files) : array {

    }
    public function export_value() {
        $value = $this->get_value();

        if ($this->is_empty($value)) {
            return null;
        }

        $options = $this->get_field()->get_options();
        if (array_key_exists($value, $options)) {
            return format_string($options[$value], true,
                ['context' => $this->get_field()->get_handler()->get_configuration_context()]);
        }

        return null;
    }
}