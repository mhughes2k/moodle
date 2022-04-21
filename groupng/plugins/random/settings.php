<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_heading('groupng_random_settings', '', get_string('pluginname_desc', 'groupng_random')));
    $settings->add(new admin_setting_configselect('groupng_random/test',
        get_string('test', ), '', null, ["a","B"]));

}