<?php

namespace core_ai\external;
use core\exception\coding_exception;
use core_ai\manager;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
class test_connection extends external_api {

    public static function execute_parameters(): \core_external\external_function_parameters {
        return new \core_external\external_function_parameters([
            'providertype' => new \core_external\external_value(
                PARAM_ALPHANUMEXT,
                'The provider type',
                VALUE_REQUIRED,
            ),
        ]);
    }

    public static function execute_returns(): \core_external\external_function_parameters {
        return new \core_external\external_function_parameters([
            'status' => new \core_external\external_value(
                PARAM_BOOL,
                'The status of the provider connection',
            ),
            'message' => new external_value(
                PARAM_TEXT,
                'The message from the provider'
            ),
        ]);
    }

    public static function execute(
        string $providername
    ): array {
        global $USER;
        // Security check first of all, if failed everything is invalid anyway.
        $context = \context_system::instance();
        self::validate_context($context);
//        if (!is_primary_admin($USER)) {
//            require_capability('moodle/ai:testconnection', $context);
//        }

        [
            'providertype' => $providername,
        ] = self::validate_parameters(self::execute_parameters(), [
            'providertype' => $providername,
        ]);
        // Append "aiprovider_" if providername doesn't start with it.
        if (strpos($providername, 'aiprovider_') !== 0) {
            $providername = "aiprovider_{$providername}";
        }
        $providerclassname = \core_ai\manager::get_ai_plugin_classname($providername);
        $plugin = new $providerclassname();
        $status = false;
        $message = get_string('statusfailed');
        try {
            $status = $plugin->test_status();
            if (is_bool($status)) {
                if ($status) {
                    $message = get_string('statusok');
                }
            } else if (is_string($status)) {
                $message = $status;
                $status = false;
            }
        }
        catch (coding_exception $e) {
            $status = false;
            $message= $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
        ];
    }
}
