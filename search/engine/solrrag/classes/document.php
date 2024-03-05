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
}