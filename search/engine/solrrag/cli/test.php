<?php
define("CLI_SCRIPT", true);
require_once("../../../../config.php");

$search = $search = \core_search\manager::instance(true, true);

$engine = $search->get_engine();

/**
 * \core\ai\AIProvider
 */
$provider = core\ai\api::get_provider(1);

$doccontent = file_get_contents($CFG->dirroot . "/search/engine/solrrag/tests/testdoc.txt");
if (file_exists($CFG->dirroot . "/search/engine/solrrag/tests/testdoc_vector.txt")) {
    $vector = file_get_contents($CFG->dirroot . "/search/engine/solrrag/tests/testdoc_vector.txt");
} else {
    $client = new \core\ai\AIClient($provider);
    $vector = $client->embed_query($doccontent);
    file_put_contents($CFG->dirroot . "/search/engine/solrrag/tests/testdoc_vector.txt", $vector);
}
$doc = [
    'id' => 'testdoc',
    'solr_vector_1356' => $vector,
    'title' => "this is a test document"
];

$document = new \search_solrrag\document("1", "mod_xaichat", "files");
$document->set('title', 'test document');
$document->set('solr_vector_1536', $vector);
$document->set('content',$doccontent);
$document->set('contextid', context_system::instance()->id);
$document->set('courseid', SITEID);
$document->set('owneruserid', $USER->id);
$document->set('modified', time());
$engine->add_document($document);

