<?php
// We're mocking a core Moodle "AI" Subsystem a la Oauth 2

namespace core\ai;

class api {

    public static function get_all_providers() {
        return array_values(AIProvider::get_records());
    }
}