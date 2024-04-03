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
$string['aiprovidersin'] = 'AI Providers in {$a}';
$string['addprovider'] = 'Add AI Provider';
$string['anyusercourse'] = 'Any course user is enrolled in';
$string['anywhere'] = 'Anywhere in site';
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
$string['disable'] = 'Disable';
$string['embedding'] = 'Embedding';
$string['embedding_help'] = 'Embedding allows the AI to generate vector representations of text.';
$string['aiproviderfeatures'] = '';
$string['aiproviderfeatures_desc'] = 'This plugin needs the following AI features';

// providers
$string['newprovider'] = '{$a} Based Provider';

// Provider instance form
$string['providername'] = "Name";
$string['providername_help'] = "Name";
$string['baseurl'] = 'Base URL';
$string['baseurl_help'] = 'Base URL';
$string['apikey'] = 'API Key';
$string['apikey_help'] = 'API KEY ';

$string['features'] = 'Features';
$string['allowchat'] = 'Allow Chat';
$string['allowchat_help'] = 'Allow this provider to provide chat completion.';
$string['completionspath'] = 'Completions path';
$string['completionspath_help'] = 'Completions path';
$string['completionmodel'] = 'Completion Model';
$string['completionmodel_help'] = 'Completion Model';
$string['allowembeddings'] = 'Allow Emebddings';
$string['allowembeddings_help'] = 'Allow this provider to be used to create embeddings.';
$string['embeddingspath'] = 'Embeddings path';
$string['embeddingspath_help'] = 'Embeddings path';
$string['embeddingmodel'] = 'Embedding Model';
$string['embeddingmodel_help'] = 'Embedding Model';

$string['scopecoursecategory'] = 'Category';
$string['scopecoursecategory_help'] = 'Limit AI scope to courses and sub-categories.

This can be limited to work only against the user\'s enrolled courses.

Users must hold the `moodle/ai:selectcategory` capability on a category to choose it.';
$string['scopecourse'] = 'Course(s)';
$string['scopecourse_help'] = 'Limit AI scope to specific courses.

Not available if a category scope constraint has been chosen.

Users must hold the `moodle/ai:selectcourse` capability on a course to choose it.';

$string['constraints'] = 'Constraints';
$string['savechanges'] = 'Save changes';
$string['aisettings'] = 'AI Provider Settings';
