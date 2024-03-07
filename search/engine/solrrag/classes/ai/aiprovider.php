<?php
// We're mocking a core Moodle "AI" Subsystem a la Oauth 2

namespace core\ai;
use \core\persistent;

class AIProvider extends persistent {
// Ultimately this would extend a persistent.


    protected static function define_properties()
    {
        return [
            'name' => [
                'type' => PARAM_TEXT
            ],
            'apikey' =>[
                'type' => PARAM_ALPHANUMEXT
            ],
            'allowembeddings' => [
                'type' => PARAM_BOOL
            ],
            'allowquery' => [
                'type' => PARAM_BOOL
            ],
            'baseurl' => [
                'type' => PARAM_URL
            ],
            'embeddings' => [
                'type' => PARAM_URL
            ],
            'embeddingmodel' => [
                'type' => PARAM_ALPHANUMEXT
            ],
            'completions' => [
                'type' => PARAM_URL
            ],
            'completionmodel' => [
                'type' => PARAM_ALPHANUMEXT
            ]
        ];
    }

    public function use_for_embeddings(): bool {
        return $this->get('allowembeddings');
    }

    public function use_for_query():bool {
        return $this->get('allowquery');
    }
    public function get_usage($type) {
        return "-";
        $key = [
            '$type',
            $this->get('id'),
            $this->get('apikey'),
        ];
        $current = get_config('ai', $key);
        return $current;
    }
    public function increment_prompt_usage($change) {
        return;
        $key = [
            'prompttokens',
            $this->get('id'),
            $this->get('apikey'),
        ];
        $key = implode("_", $key);
        $current = get_config('ai', $key);
        $new = $current + $change;
        set_config($key, $new, 'ai');
    }
    public function increment_completion_tokens($change) {
        return;
        $key = [
            'completiontokens',
            $this->get('id'),
            $this->get('apikey'),
        ];
        $key = implode("_", $key);
        $current = get_config('ai', $key);
        $new = $current + $change;
        set_config($key, $new, 'ai');
    }
    public function increment_total_tokens($change) {
        return;
        $key = [
            'totaltokens',
            $this->get('id'),
            $this->get('apikey'),
        ];
        $key = implode("_", $key);
        $current = get_config('ai', $key);
        $new = $current + $change;
        set_config($key, $new, 'ai');
    }

    //public function
    // TODO token counting.
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
        global $_ENV;
        $records = [];
        $fake = new static(0, (object) [
            'id' => 1,
            'name' => "Fake Open AI Provider",
            'allowembeddings' => true,
            'allowquery' => true,
            'baseurl' => 'https://api.openai.com/v1/',
            'embeddings' => 'embeddings',
            'embeddingmodel' => 'text-embedding-3-small',
            'completions' => 'chat/completions',
            'completionmodel' => 'gpt-4-turbo-preview',
            'apikey'=> $_ENV['OPENAIKEY']
        ]);
        array_push($records, $fake);
        return $records;
    }

}
