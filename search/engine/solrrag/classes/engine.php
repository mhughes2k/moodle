<?php

namespace search_solrrag;

use search_solrrag\document;
use search_solrrag\schema;

class engine extends \search_solr\engine {
    public function __construct(bool $alternateconfiguration = false)
    {
//        debugging("Solrrag construct");
        parent::__construct($alternateconfiguration);
//        var_dump($this->config);
    }

    public function is_server_ready() {

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
     * @see \search_solr\engine
     * @param $document
     * @return void
     * @throws \core_search\engine_exception
     */
    protected function process_document_files($document) {
        if (!$this->file_indexing_enabled()) {
            return;
        }
        // AI Retrieval support.
        // Ideally we'd be using a Moodle AI provider to tell us which LLM to use for generating embeddings, and
        // then simply calling the API and get some results back...but we don't have that yet.
        // So we'll fudge this for the moment and leverage an OpenAI Web Service API via a simple HTTP request.

        // Maximum rows to process at a time.
        $rows = 500;

        // Get the attached files.
        $files = $document->get_files();

        // If this isn't a new document, we need to check the exiting indexed files.
        if (!$document->get_is_new()) {
            // We do this progressively, so we can handle lots of files cleanly.
            list($numfound, $indexedfiles) = $this->get_indexed_files($document, 0, $rows);
            $count = 0;
            $idstodelete = array();

            do {
                // Go through each indexed file. We want to not index any stored and unchanged ones, delete any missing ones.
                foreach ($indexedfiles as $indexedfile) {
                    $fileid = $indexedfile->solr_fileid;

                    if (isset($files[$fileid])) {
                        // Check for changes that would mean we need to re-index the file. If so, just leave in $files.
                        // Filelib does not guarantee time modified is updated, so we will check important values.
                        if ($indexedfile->modified != $files[$fileid]->get_timemodified()) {
                            continue;
                        }
                        if (strcmp($indexedfile->title, $files[$fileid]->get_filename()) !== 0) {
                            continue;
                        }
                        if ($indexedfile->solr_filecontenthash != $files[$fileid]->get_contenthash()) {
                            continue;
                        }
                        if ($indexedfile->solr_fileindexstatus == document::INDEXED_FILE_FALSE &&
                            $this->file_is_indexable($files[$fileid])) {
                            // This means that the last time we indexed this file, filtering blocked it.
                            // Current settings say it is indexable, so we will allow it to be indexed.
                            continue;
                        }

                        // If the file is already indexed, we can just remove it from the files array and skip it.
                        unset($files[$fileid]);
                    } else {
                        // This means we have found a file that is no longer attached, so we need to delete from the index.
                        // We do it later, since this is progressive, and it could reorder results.
                        $idstodelete[] = $indexedfile->id;
                    }
                }
                $count += $rows;

                if ($count < $numfound) {
                    // If we haven't hit the total count yet, fetch the next batch.
                    list($numfound, $indexedfiles) = $this->get_indexed_files($document, $count, $rows);
                }

            } while ($count < $numfound);

            // Delete files that are no longer attached.
            foreach ($idstodelete as $id) {
                // We directly delete the item using the client, as the engine delete_by_id won't work on file docs.
                $this->get_search_client()->deleteById($id);
            }
        }

        // Now we can actually index all the remaining files.
        foreach ($files as $file) {
            $this->add_stored_file($document, $file);
        }
    }
}