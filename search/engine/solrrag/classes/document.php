<?php
namespace search_solrrag;

class document extends \search_solr\document {
    protected static $enginefields = array(
        'solr_filegroupingid' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => true
        ),
        'solr_fileid' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => true
        ),
        'solr_filecontenthash' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => true
        ),
        // Stores the status of file indexing.
        'solr_fileindexstatus' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        // Field to index, but not store, file contents.
        'solr_filecontent' => array(
            'type' => 'text',
            'stored' => false,
            'indexed' => true,
            'mainquery' => true
        ),
        'solr_vector' => [
            'type' => 'knn_vector_10',
            'stored' => true,
            'indexed' => true

        ]
    );

    /**
     * Export the data for the given file in relation to this document.
     *
     * @param \stored_file $file The stored file we are talking about.
     * @return array
     */
    public function export_file_for_engine($file) {
        $data = $this->export_for_engine();

        // Content is index in the main document.
        unset($data['content']);
        unset($data['description1']);
        unset($data['description2']);

        // Going to append the fileid to give it a unique id.
        $data['id'] = $data['id'].'-solrfile'.$file->get_id();
        $data['type'] = \core_search\manager::TYPE_FILE;
        $data['solr_fileid'] = $file->get_id();
        $data['solr_filecontenthash'] = $file->get_contenthash();
        $data['solr_fileindexstatus'] = self::INDEXED_FILE_TRUE;
        $data['solr_vector'] = null;
        $data['title'] = $file->get_filename();
        $data['modified'] = self::format_time_for_engine($file->get_timemodified());

        return $data;
    }

    /**
     * Returns the "content" of the documents for embedding.
     * This may use some sort of external system.
     * @return void
     */
    public function fetch_document_contents() {

    }
}