<?php
// We're mocking a core Moodle "AI" Subsystem a la Oauth 2
namespace core_ai;

use core\persistent;
use core_course_category;

class AIProvider extends persistent {
// Ultimately this would extend a persistent.

    const CONTEXT_ALL_MY_COURSES = -1;
    const TABLE = "aiprovider";

    protected static function define_properties()
    {
        return [
            'name' => [
                'type' => PARAM_TEXT
            ],
            'enabled' => [
                'type' => PARAM_BOOL
            ],
            'apikey' =>[
                'type' => PARAM_ALPHANUMEXT
            ],
            'allowembeddings' => [
                'type' => PARAM_BOOL
            ],
            'allowchat' => [
                'type' => PARAM_BOOL
            ],
            'baseurl' => [
                'type' => PARAM_URL
            ],
            'embeddingsurl' => [
                'type' => PARAM_URL
            ],
            'embeddingmodel' => [
                'type' => PARAM_ALPHANUMEXT
            ],
            'completionsurl' => [
                'type' => PARAM_URL
            ],
            'completionmodel' => [
                'type' => PARAM_ALPHANUMEXT
            ],
            // What context is this provider attached to. 
            // If null, it's a global provider.
            // If -1 its limited to user's own courses.
            'contextid' => [
                'type' => PARAM_INT
            ],
            // If true, only courses that the user is enrolled in will be searched.
            'onlyenrolledcourses' => [
                'type' => PARAM_BOOL
            ],
        ];
    }


    /**
     * Work out the context path from the site to this AI Provider's context
     * @return void
     */
    public function get_context_path() {
        $context = \context::instance_by_id($this->get('contextid'));
        var_dump($context);
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

    /**
     * Returns appropriate search settings based on 
     * provider configuration.
     */
    public function get_settings() {
        // `userquery` and `vector` will be filled at run time.
        $settings = [
            'userquery'=> null,
            'vector' => null,
            // `similarity` is a boolean that determines if the query should use vector similarity search.
            'similarity' => true,            
            'areaids' => [],
            // `excludeareaids` is an array of areaids that should be excluded from the search.
            'excludeareaids'=> ["core_user-user"],  // <-- This may be should be in control of the AI Provider.
            'courseids' => [],   // This of course IDs that result should be limited to.
        ];
        return $settings;
    }

    /**
     * Gets user specific settings.
     * 
     * This takes on some of the function that the manager code did.
     */
    public function get_settings_for_user($user) {
        $usersettings =  $this->get_settings();

        // This is basically manager::build_limitcourseids().
        $mycourseids = enrol_get_my_courses(array('id', 'cacherev'), 'id', 0, [], false);
        $onlyenrolledcourses = $this->get('onlyenrolledcourses');
        $courseids = [];
        if ($this->get('contextid') == self::CONTEXT_ALL_MY_COURSES) {
            $courseids  = array_keys($mycourseids);
        } else {
            $context = \context::instance_by_id($this->get('contextid'));
            if ($context->contextlevel == CONTEXT_COURSE) {
                // Check that the specific course is also in the user's list of courses.
                $courseids = array_intersect([$context->instanceid], $mycourseids);
            } else if ($context->contextlevel == CONTEXT_COURSECAT) {
                // CourseIDs will be all courses in the category, 
                // optionally that the user is enrolled in
                $category = core_course_category::get($context->instanceid);
                $categorycourseids = $category->get_courses([
                    'recursive'=>true,
                    'idonly' => true
                ]);
            } else if ($context->contextlevel == CONTEXT_SYSTEM) {
                // No restrictions anywhere.
            }
        }
        $usersettings['courseids'] = $courseids;
        
        return $usersettings;
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
    public static function get_records($filters = [], $sort = '', $order = 'ASC', $skip = 0, $limit = 0) {
        global $_ENV;
        $records = parent::get_records($filters, $sort, $order, $skip, $limit);
//        $records = [];
//        $fake = new static(0, (object) [
//            'id' => 1,
//            'name' => "Open AI Provider (hardcoded)",
//            'enabled' => true,
//            'allowembeddings' => true,
//            'allowchat' => true,
//            'baseurl' => 'https://api.openai.com/v1/',
//            'embeddings' => 'embeddings',
//            'embeddingmodel' => 'text-embedding-3-small',
//            'completions' => 'chat/completions',
//            'completionmodel' => 'gpt-4-turbo-preview',
//            'apikey'=> $_ENV['OPENAIKEY'],
//            'contextid' => \context_system::instance()->id,
//            //null,  // Global AI Provider
//            'onlyenrolledcourses' => true
//        ]);
//        array_push($records, $fake);
//        $fake = new static(0, (object) [
//            'id' => 2,
//            'name' => "Ollama AI Provider (hard coded)",
//            'enabled' => true,
//            'allowembeddings' => true,
//            'allowchat' => true,
//            'baseurl' => 'http://127.0.0.1:11434/api/',
//            'embeddings' => 'embeddings',
//            'embeddingmodel' => '',
//            'completions' => 'chat',
//            'completionmodel' => 'llama2',
//            'contextid' => null,  // Global AI Provider
//            'onlyenrolledcourses' => true
//        ]);
//        array_push($records, $fake);
/*
        $fake = new static(0, (object) [
            'id' => 3,
            'name' => "Ollama AI Provider (hard coded) Misc Category only",
            'enabled' => true,
            'allowembeddings' => true,
            'allowchat' => true,
            'baseurl' => 'http://127.0.0.1:11434/api/',
            'embeddings' => 'embeddings',
            'embeddingmodel' => '',
            'completions' => 'chat',
            'completionmodel' => 'llama2',
            'contextid' => \context_system::instance()->id,
            // 111,  // Global AI Provider
            'onlyenrolledcourses' => true,
        ]);
        array_push($records, $fake);
*/
        $targetcontextid = $filters['contextid'] ?? null;
        $targetcontext = null;
        if (is_null($targetcontextid)) {
            unset($filters['contextid']); // Because we need special handling.
        } else {
            $targetcontext = \context::instance_by_id($targetcontextid);
        }
        $records = array_filter($records, function($record) use ($filters, $targetcontext) {
            $result = true;
            foreach($filters as $key => $value) {
                if ($key == "contextid") {
                    $providercontextid = $record->get('contextid');
                    if ($providercontextid == self::CONTEXT_ALL_MY_COURSES) {
                        // More problematic.
                        $result = $result & true;
                    } else if ($providercontextid == null) {
                        // System provider so always matches.
                        $result = $result & true;
                    } else {
                        $providercontext = \context::instance_by_id(
                            $providercontextid
                        );
                        $ischild = $targetcontext->is_child_of($providercontext, true);
                        debugging("IS child ". (int)$ischild, DEBUG_DEVELOPER);
                        $result = $result & $ischild;
                    }
                }else {
//                    debugging('Filtering on '.$key. "' = {$value}", DEBUG_DEVELOPER);
                    if ($record->get($key) != $value) {
                        return false;
                    }
                }
            }
            return $result;
        });

        return $records;
    }

}
