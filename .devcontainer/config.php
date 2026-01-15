<?php
// Moodle configuration file for devcontainer
// This file uses environment variables for database configuration

unset($CFG);
global $CFG;
$CFG = new stdClass();

// Database configuration from environment variables
$CFG->dbtype    = getenv('MOODLE_DBTYPE') ?: 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = getenv('MOODLE_DBHOST') ?: 'mariadb';
$CFG->dbname    = getenv('MOODLE_DBNAME') ?: 'moodle';
$CFG->dbuser    = getenv('MOODLE_DBUSER') ?: 'moodle';
$CFG->dbpass    = getenv('MOODLE_DBPASSWORD') ?: 'moodle_password';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = [
    'dbpersist' => 0,
    'dbport'    => getenv('MOODLE_DBPORT') ?: '3306',
    'dbsocket'  => '',
];

// Site configuration
$CFG->wwwroot   = getenv('MOODLE_WWWROOT') ?: 'http://localhost:8080';
$CFG->dataroot  = getenv('MOODLE_DATAROOT') ?: '/var/www/moodledata';

// Determine Moodle directory
if (file_exists('/workspace/public/lib/setup.php')) {
    $CFG->dirroot = '/workspace/public';
} else {
    $CFG->dirroot = '/workspace';
}

// Admin settings
$CFG->admin = 'admin';
$CFG->lang = 'en';

// Email configuration (using Mailpit for development)
$CFG->smtphosts = 'mailpit:1025';
$CFG->smtpsecure = '';
$CFG->smtpauthtype = 'PLAIN';
$CFG->smtpuser = '';
$CFG->smtppass = '';
$CFG->noreplyaddress = 'noreply@example.com';

// Development and debugging settings
// WARNING: NOT FOR PRODUCTION!
if (getenv('MOODLE_DEBUG') === 'true') {
    @error_reporting(E_ALL | E_STRICT);
    @ini_set('display_errors', '1');
    $CFG->debug = (E_ALL | E_STRICT);
    $CFG->debugdisplay = 1;
    $CFG->debugpageinfo = 1;
    $CFG->perfdebug = 15;
    $CFG->debugstringids = 0;
} else {
    @error_reporting(E_ALL | E_STRICT);
    @ini_set('display_errors', '0');
    $CFG->debug = 0;
    $CFG->debugdisplay = 0;
}

// Performance settings
$CFG->cachejs = false;
$CFG->themedesignermode = true;
$CFG->langstringcache = false;

// PHPUnit settings
$CFG->phpunit_prefix = 'phpu_';
$CFG->phpunit_dataroot = '/var/www/phpunitdata';

// Behat settings
$CFG->behat_prefix = 'bht_';
$CFG->behat_dataroot = '/var/www/behatdata';
$CFG->behat_wwwroot = 'http://moodle:80';
$CFG->behat_faildump_path = '/var/www/behatfaildumps';

// Proxy settings (for codespaces/cloud environments)
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $CFG->wwwroot = 'https://' . $_SERVER['HTTP_X_FORWARDED_HOST'];
    $CFG->reverseproxy = true;
    $CFG->sslproxy = true;
}

// Security
$CFG->passwordsaltmain = 'changeme_dev_only_salt_' . bin2hex(random_bytes(16));

// Unicode database
$CFG->unicodedb = true;

// Session handling
$CFG->session_handler_class = '\core\session\file';
$CFG->session_file_save_path = $CFG->dataroot . '/sessions';

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
