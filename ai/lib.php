<?php

use core_ai\api;

/**
 * Extend categories with option to define an AI Provider for all enclosed
 * subcategories and courses.
 * @param $categorynode
 * @param $catcontext
 * @return void
 */
function extend_navigation_category_settings($categorynode, $catcontext) {
    if (has_capability('moodle/ai:addprovider', $catcontext)) {
        $categorynode->add(
            get_string('addprovider', 'ai'),
            new moodle_url(
                '/ai/index.php',
                [
                    'id' => $catcontext->id,
                    'action' => api::ACTION_ADD_PROVIDER
                ]),
            navigation_node::TYPE_SETTING
        );
    }
}

/**
 * Extend Course Navigation with option to create AI providers within a
 * course.
 * @param $coursenode
 * @param $coursecontext
 * @return void
 * @throws coding_exception
 * @throws moodle_exception
 */
function extend_navigation_course_settings($coursenode, $coursecontext) {
    if (has_capability('moodle/addprovider', $coursecontext)) {
        $coursenode->add(
            get_string('addaiprovider', 'core_ai'),
            new moodle_url(
                '/ai/index.php',
                [
                    'id' => $coursecontext->id,
                    'action' => api::ACTION_ADD_PROVIDER
                ]),
            navigation_node::TYPE_SETTING
        );
    }
}
