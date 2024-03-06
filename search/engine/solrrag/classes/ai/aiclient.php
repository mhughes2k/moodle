<?php
namespace core\ai;
require_once($CFG->libdir.'/filelib.php');
/**
 * Base client for AI providers that uses simple http request.
 */
class AIClient extends \curl {
    private $provider;
    public function __construct(
        \core\ai\AIProvider $provider
    ) {
        $this->provider = $provider;
        $settings = [];
        parent::__construct($settings);
        $this->setHeader('Authorization: Bearer ' . $this->provider->get('apikey'));
        $this->setHeader('Content-Type: application/json');
    }

    public function get_embeddings_url(): string {
        return $this->provider->get('baseurl') . $this->provider->get('embeddings');
    }

    public function get_chat_completions_url(): string {
        return $this->provider->get('baseurl') . $this->provider->get('completions');
    }

    /**
     * @param $document
     * @return array
     */
    public function embed_query($content): array {
        // Send document to back end and return the vector
        $usedptokens = $this->provider->get_usage('prompt_tokens');
        $totaltokens = $this->provider->get_usage('total_tokens');
        mtrace("Prompt tokens: $usedptokens. Total tokens: $totaltokens");
        $params = [
            "input" => htmlentities($content), // TODO need to do some length checking here!
            "model" => $this->provider->get('embeddingmodel')
        ];
        $params = json_encode($params);
//        var_dump($this->get_embeddings_url());

        $rawresult = $this->post($this->get_embeddings_url(), $params);
//        var_dump($rawresult);
        $result = json_decode($rawresult, true);
        var_dump($result);
        $usage = $result['usage'];
        $this->provider->increment_prompt_usage($usage['prompt_tokens']);
        $this->provider->increment_total_tokens($usage['total_tokens']);
        mtrace("Used Prompt tokens: {$usage['prompt_tokens']}. Total tokens: {$usage['total_tokens']}");
        $data = $result['data'];
        foreach($data as $d) {
            if ($d['object'] == "embedding") {
                return $d['embedding'];
            }
        }
        $usedptokens = $this->provider->get_usage('prompt_tokens');
        $totaltokens = $this->provider->get_usage('total_tokens');
        mtrace("Total Used: Prompt tokens: $usedptokens. Total tokens: $totaltokens");
        return [];
    }
    public function embed_documents(array $documents) {
        // Go send the documents off to a back end and then return array of each document's vectors.
        // But for the minute generate an array of fake vectors of a specific length.
        $embeddings = [];
        foreach($documents as $doc) {
            $embeddings[] = $this->embed_query($doc);
        }
        return $embeddings;
    }
    public function fake_embed(array $documents) {
        $vectors = [];
        foreach ($documents as $document) {
            $vectors[] = $this->fake_vector(1356);
        }
        return $vectors;
    }
    public function complete($query) {


    }
    private function fake_vector($length) {
        $vector = [];
        for ($i = 0; $i < $length; $i++) {
            $vector[] = rand(0, 1);
        }
        return $vector;
    }



}
