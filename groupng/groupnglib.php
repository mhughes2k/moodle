<?php
// Based on enrollib.php.

define('GROUPNG_INSTANCE_ENABLED', 0);
define('GROUPNG_INSTANCE_DISABLED', 1);

function groupng_get_plugins($enabled) {
    global $CFG;

    $result = [];

    if ($enabled) {
        $enabled = explode(',', $CFG->groupng_plugins_enabled);
        $plugins = [];
        foreach ($enabled as $plugin) {
            $plugins[$plugin] = "{$CFG->dirroot}/groupng/plugins/$plugin";
        }
    } else {
        $plugins = core_component::get_plugin_list('groupng');
        ksort($plugins);
    }

    foreach ($plugins as $plugin=>$location) {
        $class = "groupng_{$plugin}_plugin";
        if (!class_exists($class)) {
            $liblocation = "$location/lib.php";
            if (!file_exists($liblocation)) {
                continue;
            }
            include_once($liblocation);
            if (!class_exists($class)) {
                continue;
            }
        }

        $result[$plugin] = new $class();
    }

    return $result;
}
/**
 * Returns instance of groupng plugin
 * @param  string $name name of groupng plugin ('manual', 'guest', ...)
 * @return groupng_plugin
 */
function groupng_get_plugin($name) {
    global $CFG;

    $name = clean_param($name, PARAM_PLUGIN);

    if (empty($name)) {
        // ignore malformed or missing plugin names completely
        return null;
    }

    $location = "$CFG->dirroot/groupng/plugins/$name";

    $class = "groupng_{$name}_plugin";
    if (!class_exists($class)) {
        if (!file_exists("$location/lib.php")) {
            return null;
        }
        include_once("$location/lib.php");
        if (!class_exists($class)) {
            return null;
        }
    }

    return new $class();
}

function groupng_get_instances($courseid, $enabled) {
    global $DB, $CFG;

    if (!$enabled) {
        return $DB->get_records('groupng', array('courseid'=>$courseid), 'sortorder,id');
    }

    $result = $DB->get_records('groupng', array('courseid'=>$courseid, 'status'=>GROUPNG_INSTANCE_ENABLED), 'sortorder,id');

    $enabled = explode(',', $CFG->groupng_enabled_plugins);
    foreach ($result as $key=>$instance) {
        if (!in_array($instance->enrol, $enabled)) {
            unset($result[$key]);
            continue;
        }
        if (!file_exists("$CFG->dirroot/groupng/plugins/$instance->enrol/lib.php")) {
            // broken plugin
            unset($result[$key]);
            continue;
        }
    }
}
/**
 * Checks if a given plugin is in the list of enabled enrolment plugins.
 *
 * @param string $enrol Enrolment plugin name
 * @return boolean Whether the plugin is enabled
 */
function groupng_is_enabled($enrol) {
    global $CFG;

    if (empty($CFG->groupng_plugins_enabled)) {
        return false;
    }
    return in_array($enrol, explode(',', $CFG->groupng_plugins_enabled));
}

abstract class groupng_plugin {
    /**
     * Returns name of this enrol plugin
     * @return string
     */
    public function get_name() {
        // second word in class is always enrol name, sorry, no fancy plugin names with _
        $words = explode('_', get_class($this));
        return $words[1];
    }
}