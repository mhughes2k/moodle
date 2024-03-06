<?php

namespace search_solrrag;

use search_solrrag\document;
use search_solrrag\schema;
// Fudge autoloading!
require_once($CFG->dirroot ."/search/engine/solrrag/classes/ai/api.php");
require_once($CFG->dirroot ."/search/engine/solrrag/classes/ai/aiprovider.php");
use \core\ai\AIProvider;
class engine extends \search_solr\engine {

    /**
     * @var AIProvider AI rovider object to use to generate embeddings.
     */
    protected ?AIProvider $embeddingprovider = null;

    public function __construct(bool $alternateconfiguration = false)
    {
        parent::__construct($alternateconfiguration);
        // AI Retrieval support.
        // Set up AI provider if it's available.
        // Ideally we'd be using a Moodle AI provider to tell us which LLM to use for generating embeddings, and
        // then simply calling the API and get some results back...but we don't have that yet.
        // So we'll fudge this for the moment and leverage an OpenAI Web Service API via a simple HTTP request.
        $aiproviderid = 1;
        $aiprovider = \core\ai\api::get_provider($aiproviderid);
        if ($aiprovider->use_for_embeddings()) {
            $this->embeddingprovider = $aiprovider;
        }
    }

    public function is_server_ready()
    {

        $configured = $this->is_server_configured();
        if ($configured !== true) {
            return $configured;
        }

        // As part of the above we have already checked that we can contact the server. For pages
        // where performance is important, we skip doing a full schema check as well.
        if ($this->should_skip_schema_check()) {
            return true;
        }

        // Update schema if required/possible.
        $schemalatest = $this->check_latest_schema();
        if ($schemalatest !== true) {
            return $schemalatest;
        }

        // Check that the schema is already set up.
        try {
            $schema = new schema($this);
            $schema->validate_setup();
        } catch (\moodle_exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Adds a file to the search engine.
     *
     * Notes about Solr and Tika indexing. We do not send the mime type, only the filename.
     * Tika has much better content type detection than Moodle, and we will have many more doc failures
     * if we try to send mime types.
     *
     * @param \search_solr\document $document
     * @param \stored_file $storedfile
     * @return void
     */
    protected function add_stored_file($document, $storedfile)
    {
        $filedoc = $document->export_file_for_engine($storedfile);
        /**
         * Should we event attempt to get vectors.
         */
        if (!is_null($this->embeddingprovider)) {
            // garnish $filedoc with the embedding vector. It would be nice if this could be done
            // via the export_file_for_engine() call above, that has no awareness of the engine.
            $embeddings = $this->embeddingprovider->embed_documents([$filedoc]);
            $filedoc['solr_vector'] = "[". implode(",", $embeddings[0]) ."]";
            print_r($filedoc);
        } else {
            // potentially warn that selected provider can't be used for
            // generating embeddings for RAG.
        }


        // Used the underlying implementation

        if (!$this->file_is_indexable($storedfile)) {
            // For files that we don't consider indexable, we will still place a reference in the search engine.
            $filedoc['solr_fileindexstatus'] = document::INDEXED_FILE_FALSE;
            $this->add_solr_document($filedoc);
            return;
        }

        $curl = $this->get_curl_object();

        $url = $this->get_connection_url('/update/extract');

        // Return results as XML.
        $url->param('wt', 'xml');

        // This will prevent solr from automatically making fields for every tika output.
        $url->param('uprefix', 'ignored_');

        // Control how content is captured. This will keep our file content clean of non-important metadata.
        $url->param('captureAttr', 'true');
        // Move the content to a field for indexing.
        $url->param('fmap.content', 'solr_filecontent');

        // These are common fields that matches the standard *_point dynamic field and causes an error.
        $url->param('fmap.media_white_point', 'ignored_mwp');
        $url->param('fmap.media_black_point', 'ignored_mbp');

        // Copy each key to the url with literal.
        // We place in a temp name then copy back to the true field, which prevents errors or Tika overwriting common field names.
        foreach ($filedoc as $key => $value) {
            // This will take any fields from tika that match our schema and discard them, so they don't overwrite ours.
            $url->param('fmap.' . $key, 'ignored_' . $key);
            // Place data in a tmp field.
            $url->param('literal.mdltmp_' . $key, $value);
            // Then move to the final field.
            $url->param('fmap.mdltmp_' . $key, $key);
        }

        // This sets the true filename for Tika.
        $url->param('resource.name', $storedfile->get_filename());

        // A giant block of code that is really just error checking around the curl request.
        try {
            $requesturl = $url->out(false);
            // We have to post the file directly in binary data (not using multipart) to avoid
            // Solr bug SOLR-15039 which can cause incorrect data when you use multipart upload.
            // Note this loads the whole file into memory; see limit in file_is_indexable().
            $result = $curl->post($url->out(false), $storedfile->get_content());

            $code = $curl->get_errno();
            $info = $curl->get_info();

            // Now error handling. It is just informational, since we aren't tracking per file/doc results.
            if ($code != 0) {
                // This means an internal cURL error occurred error is in result.
                $message = 'Curl error ' . $code . ' while indexing file with document id ' . $filedoc['id'] . ': ' . $result . '.';
                debugging($message, DEBUG_DEVELOPER);
            } else if (isset($info['http_code']) && ($info['http_code'] !== 200)) {
                // Unexpected HTTP response code.
                $message = 'Error while indexing file with document id ' . $filedoc['id'];
                // Try to get error message out of msg or title if it exists.
                if (preg_match('|<str [^>]*name="msg"[^>]*>(.*?)</str>|i', $result, $matches)) {
                    $message .= ': ' . $matches[1];
                } else if (preg_match('|<title[^>]*>([^>]*)</title>|i', $result, $matches)) {
                    $message .= ': ' . $matches[1];
                }
                // This is a common error, happening whenever a file fails to index for any reason, so we will make it quieter.
                if (CLI_SCRIPT && !PHPUNIT_TEST) {
                    mtrace($message);
                    if (debugging()) {
                        mtrace($requesturl);
                    }
                    // Suspiciion that this fails due to the file contents being PDFs.
                }
            } else {
                // Check for the expected status field.
                if (preg_match('|<int [^>]*name="status"[^>]*>(\d*)</int>|i', $result, $matches)) {
                    // Now check for the expected status of 0, if not, error.
                    if ((int)$matches[1] !== 0) {
                        $message = 'Unexpected Solr status code ' . (int)$matches[1];
                        $message .= ' while indexing file with document id ' . $filedoc['id'] . '.';
                        debugging($message, DEBUG_DEVELOPER);
                    } else {
                        // The document was successfully indexed.
                        return;
                    }
                } else {
                    // We received an unprocessable response.
                    $message = 'Unexpected Solr response while indexing file with document id ' . $filedoc['id'] . ': ';
                    $message .= strtok($result, "\n");
                    debugging($message, DEBUG_DEVELOPER);
                }
            }
        } catch (\Exception $e) {
            // There was an error, but we are not tracking per-file success, so we just continue on.
            debugging('Unknown exception while indexing file "' . $storedfile->get_filename() . '".', DEBUG_DEVELOPER);
        }

        // If we get here, the document was not indexed due to an error. So we will index just the base info without the file.
        $filedoc['solr_fileindexstatus'] = document::INDEXED_FILE_ERROR;
        $this->add_solr_document($filedoc);


        // It would have been nice to use the underlying solr code, but its too tightly integrated
        // with talking to solr.
        //return parent::add_stored_file($document, $storedfile);
    }
}