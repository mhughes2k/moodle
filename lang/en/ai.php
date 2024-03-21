<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
* Strings for component 'ai', language 'en', branch 'MOODLE_0_STABLE'
*
* @package   core_ai
* @copyright 2024 onwards Michael Hughes {@link http://moodle.com}
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
$string['pluginname'] = 'AI Providers';
$string['aiprovider'] = 'AI Provider';
$string['addprovider'] = 'Add AI Provider';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['removeprovider'] = 'Remove AI Provider';
$string['manageproviders'] = 'Manage AI Providers';
$string['availableproviders'] = 'Available Providers';
$string['availableproviders_help'] = 'This is a list of configured AI Providers that meet the requirements of the activity.

Activity developers specify which AI features their plugin requires, and an administrator must configure
an AI Provider instance, and make it available at a context level that the plugin can access.

System level AI Providers that meet the required features will always be listed.
';
$string['chat'] = 'Chat Completion';
$string['chat_help'] = 'Chat Completion allows the AIProvider to be used to generate text.';
$string['embedding'] = 'Embedding';
$string['embedding_help'] = 'Embedding allows the AI to generate vector representations of text.';
$string['aiproviderfeatures'] = '';
$string['aiproviderfeatures_desc'] = 'This plugin needs the following AI features';
