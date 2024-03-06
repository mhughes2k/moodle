<?php
// We're mocking a core Moodle "AI" Subsystem a la Oauth 2

namespace core\ai;
use \core\persistent;

class AIProvider extends persistent {
// Ultimately this would extend a persistent.
    public function __construct($id = 0, stdClass $record = null) {
        if ($id > 0) {
            $this->raw_set('id', $id);
            $this->raw_set('name', "Fake AI Provider");
            $this->raw_set('allowembeddings', true);
            $this->raw_set('allowquery', true);
        }
    }

    protected static function define_properties()
    {
        return [
            'name' => [
                'type' => PARAM_TEXT
            ],
            'allowembeddings' => [
                'type' => PARAM_BOOL
            ],
            'allowquery' => [
                'type' => PARAM_BOOL
            ]
        ];
    }

    public function use_for_embeddings(): bool {
        return $this->get('allowembeddings');
    }

    public function use_for_query():bool {
        return $this->get('allowquery');
    }
    public function embed_documents(array $documents) {
        // Go send the documents off to a back end and then return array of each document's vectors.
        print_r($documents);
        return [
            [0.0053587136790156364,
                -0.0004999046213924885,
                0.038883671164512634,
                -0.003001077566295862,
                -0.00900818221271038]
        ];
    }

    /**
     * @param $document
     * @return array
     */
    public function embed_query($document): array {
        print_r($document);
        // Send document to back end and return the vector
        return [0.0053587136790156364,
            -0.0004999046213924885,
            0.038883671164512634,
            -0.003001077566295862,
            -0.00900818221271038
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
            'id' => 1,
            'name' => "Fake AI Provider"
        ]);
        array_push($records, $fake);
        return $records;
    }
}