<?php

/**
 * Group NG Manipulation Script
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$groupng   = required_param('groupng', PARAM_PLUGIN);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/groupng.php');
$PAGE->set_context(context_system::instance());

require_admin();
require_sesskey();

$enabled = groupng_get_plugins(true);
$all     = groupng_get_plugins(false);

$return = new moodle_url('/admin/settings.php', array('section'=>'managegroupng'));

switch ($action) {
    case 'disable':
        $class = \core_plugin_manager::resolve_plugininfo_class('groupng');
        $class::enable_plugin($groupng, false);
        break;

    case 'enable':
        if (!isset($all[$groupng])) {
            break;
        }
        $class = \core_plugin_manager::resolve_plugininfo_class('groupng');
        $class::enable_plugin($groupng, true);
        break;

    case 'up':
        if (!isset($enabled[$groupng])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled = array_flip($enabled);
        $current = $enabled[$groupng];
        if ($current == 0) {
            break; //already at the top
        }
        $enabled = array_flip($enabled);
        $enabled[$current] = $enabled[$current - 1];
        $enabled[$current - 1] = $groupng;
        set_config('enrol_plugins_enabled', implode(',', $enabled));
        break;

    case 'down':
        if (!isset($enabled[$groupng])) {
            break;
        }
        $enabled = array_keys($enabled);
        $enabled = array_flip($enabled);
        $current = $enabled[$enrol];
        if ($current == count($enabled) - 1) {
            break; //already at the end
        }
        $enabled = array_flip($enabled);
        $enabled[$current] = $enabled[$current + 1];
        $enabled[$current + 1] = $groupng;
        set_config('groupng_plugins_enabled', implode(',', $enabled));
        break;

    case 'migrate':
        if (get_string_manager()->string_exists('pluginname', 'groupng_'.$groupng)) {
            $strplugin = get_string('pluginname', 'groupng_'.$groupng);
        } else {
            $strplugin = $groupng;
        }

        $PAGE->set_title($strplugin);
        echo $OUTPUT->header();

        // This may take a long time.
        core_php_time_limit::raise();

        // Disable plugin to prevent concurrent cron execution.
        unset($enabled[$enrol]);
        set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));

        echo $OUTPUT->heading(get_string('uninstallmigrating', 'groupng', 'groupng_'.$groupng));

//        require_once("$CFG->dirroot/groupng/manual/locallib.php");
//        enrol_manual_migrate_plugin_enrolments($enrol);

        echo $OUTPUT->notification(get_string('success'), 'notifysuccess');

        if (!$return = core_plugin_manager::instance()->get_uninstall_url('groupng'.$groupng, 'manage')) {
            $return = new moodle_url('/admin/plugins.php');
        }
        echo $OUTPUT->continue_button($return);
        echo $OUTPUT->footer();
        exit;
}


redirect($return);