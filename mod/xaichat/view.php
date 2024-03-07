<?php
define("NO_OUTPUT_BUFFERING", true);
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_xaichat.
 *
 * @package     mod_xaichat
 * @copyright   2024 Michael Hughes <michaelhughes@strath.ac.uk>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
use mod_xaichat\aichatform;

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$x = optional_param('x', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('xaichat', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('xaichat', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('xaichat', array('id' => $x), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('xaichat', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$PAGE->set_url('/mod/xaichat/view.php', ['id' => $cm->id]);

$modulecontext = context_module::instance($cm->id);

$aicontextkey = "mod_xaichat:context:{$cm->id}:{$USER->id}";
if (!isset($_SESSION[$aicontextkey])) {
    $_SESSION[$aicontextkey] = [
        'messages'=>[]
    ];
}
//$aicontext = $_SESSION[$aicontextkey];

if (!($aiprovider = \core\ai\api::get_provider($moduleinstance->aiproviderid))){
    throw new moodle_exception("noaiproviderfound", 'xaichat');
}

$event = \mod_xaichat\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('xaichat', $moduleinstance);
$event->trigger();

$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);
echo $OUTPUT->header();

$chatform = new aichatform();
if ($data = $chatform->get_data()) {
    if (isset($data->restartbutton)) {
        redirect(new \moodle_url('/mod/xaichat/view.php', array('id' => $cm->id)));
    }
    $stepnow = 0;
    $totalsteps = 4;
    $aiclient = new \core\ai\AIClient($aiprovider);

    $progress = new \progress_bar();
    $progress->create();
    if (empty($_SESSION[$aicontextkey]['messages'])) {
        // If the user has not made any prompts yet, we need to prime the interaction with
        // a bunch of system and context specific prompts to constrain behaviour.
        $totalsteps++;
        $progress->update(1, $totalsteps,'Processing System Prompts');
    }
    $progress->update(1, $totalsteps,'Looking for relevant context');
//    $vector = $aiclient->embed_query($data->userprompt);
    $search = \core_search\manager::instance(true, true);
//    $search->similarity_search($settings);
    // Perform "R" from RAG, finding documents from within the context that are similar to the user's prompt.
    // Add the retrieved documents to the context for this chat by generating some system messages with the content
    // returned

    // Add the user's new prompt to the messages.
    $progress->update(2, $totalsteps,'Attaching user prompt');
    $_SESSION[$aicontextkey]['messages'][] = (object)[
        "role" => "user",
        "content" => $data->userprompt
    ];
    // Pass the whole context over the AI to summarise.
    $progress->update(3, $totalsteps, 'Waiting for response');
    $airesults = $aiclient->chat($_SESSION[$aicontextkey]['messages']);
    $_SESSION[$aicontextkey]['messages'] = array_merge($_SESSION[$aicontextkey]['messages'],$airesults);
    $progress->update(4, $totalsteps, 'Got Response');

    // We stash the data in the session temporarily (should go into an activity-user store in database) but this
    // is fast and dirty, and then we do a redirect so that we don't double up the request if the user hit's
    // refresh.
    redirect(new \moodle_url('/mod/xaichat/view.php', ['id' => $cm->id]));
} else if ($chatform->is_cancelled()) {
    $_SESSION[$aicontextkey] = [
        'messages'=>[]
    ];
} else {
    // Clear session on first view of form.

    $toform = [
        'id' => $id,
        'aiproviderid' => $moduleinstance->aiproviderid,
        'aicontext' => $_SESSION[$aicontextkey],
    ];
    // Initialise;
    $chatform->set_data($toform);
}
$userpic = $OUTPUT->render(new \user_picture($USER)). fullname($USER);
$aipic = $aiprovider->get('name');

$displaymessages = [];
foreach ($_SESSION[$aicontextkey]['messages'] as $message) {
    $displaymessages[] = [
        "role" => $message->role == "user" ? $userpic : \html_writer::tag("strong", $aipic),
        "content" => format_text($message->content, FORMAT_MARKDOWN)
    ];
}
$tcontext = [
    "userpic" => new user_picture($USER),
    "messages" => $displaymessages
];

echo $OUTPUT->render_from_template("mod_xaichat/conversation", $tcontext);

$chatform->display();

//echo \html_writer::tag('pre', print_r($displaymessages,1));
//echo \html_writer::tag('pre', print_r($_SESSION[$aicontextkey]['messages'],1));

echo $OUTPUT->footer();
