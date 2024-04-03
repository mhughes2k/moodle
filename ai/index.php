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

$renderer = $PAGE->get_renderer('core', 'ai');

$action = optional_param('action', '', PARAM_ALPHAEXT);
// We're using pid as "id" is used to specify contextids.
$providerid = optional_param('pid', '', PARAM_RAW);
$incontextid = optional_param('contextid', null, PARAM_RAW);

$context = !is_null($incontextid) ? \context::instance_by_id($incontextid) : null;

if (empty($context)) {
    $strheading = get_string(get_string('pluginname', 'ai'));
} else {
    $strheading = get_string('aiprovidersin', 'ai', $context->get_context_name());
}
$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);

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
    $mform = new \core_ai\form\openaiapiprovider(null, [
        'persistent' => $provider,
        'type' => required_param('type', PARAM_RAW),
        'contextid' => $incontextid
    ]);
}


if ($mform && $mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/oauth2/issuers.php'));
} else if ($action == api::ACTION_EDIT_PROVIDER) {
    // Handle edit.
    if ($mform->is_cancelled()) {
        echo 'cancelled';
    }
    if ($mform->is_submitted()) {
        echo 'submitted';
    }
    echo 'validated '. (int)$mform->is_validated();

    if ($data = $mform->get_data()) {
        var_dump($data);
        if (!empty($data->id)) {
            core_ai\api::update_provider($data);
        } else {
            core_ai\api::create_provider($data);
        }
        exit();
    } else {
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
} else if ($action == api::ACTION_REMOVE_PROVIDER) {
    // Handle remove.
} else {
    // Display list of providers.
    $indexpage = new \core_ai\output\index_page(
        api::get_providers($incontextid)
    );
    echo $OUTPUT->header();

    echo $renderer->render_index_page($indexpage);
    echo $OUTPUT->footer();
}
