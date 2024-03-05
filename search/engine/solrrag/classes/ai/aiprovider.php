<?php
// We're mocking a core Moodle "AI" Subsystem a la Oauth 2

namespace core\ai;


class AIProvider extends persistent {
// Ultimately this would extend a persistent.
    protected static function define_properties()
    {
        return [
            'name' => [
                'type' => PARAM_TEXT
            ]
        ];
    }

    /**
     * We're overriding this whilst we don't have a real DB table.
     * @param $filters
     * @param $sort
     * @param $order
     * @param $skip
     * @param $limit
     * @return array
     */
    public static function get_records($filters = array(), $sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        $records = [];
        $fake = new static(0, (object) [
            'name' => "Fake AI Provider"
        ]);
        array_push($records, $fake);
        return $records;
    }
}