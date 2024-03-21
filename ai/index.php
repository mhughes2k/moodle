<?php

/**
 * A lot of this is based on the Oauth2 issuers.php file.
 */
require_once("../config.php");


require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

use core_ai\api;

$PAGE->set_url('/ai/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout("admin");

$strheading = get_string('aiprovider', 'ai');
$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);

$renderer = $PAGE->get_renderer('core', 'ai');

$action = optional_param('action', '', PARAM_ALPHAEXT);
// We're using pid as "id" is used to specify contextids.
$providerid = optional_param('pid', '', PARAM_RAW);

$provider = null;
$mform = null;

if ($providerid) {
    $provider = core\ai\api::get_provider($providerid);
    if (!$provider) {
        throw new moodle_exception('invaliddata');
    }
}

if ($action == api::ACTION_EDIT_PROVIDER) {
    if ($provider) {
        // Edit
    } else {
        // Create new
    }
    $mform = new \core\ai\form\provider(null, ['persistent' => $provider]);
}


if ($mform && $mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/oauth2/issuers.php'));
} else if ($action == api::ACTION_EDIT_PROVIDER) {
    // Handle edit.
    if ($data = $mform->get_data()) {

    } else {
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
} else if ($action == api::ACTION_REMOVE_PROVIDER) {
    // Handle remove.
} else {
    // Display list of providers.
    $providers = api::get_providers();
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'ai'));
    echo $renderer->providers_table($providers);
    echo $OUTPUT->footer();
}
