<?php
//namespace core_ai\output;

use core_ai\api;

//use \html_table;
//use \html_table_cell;
//use \html_table_row;
//use html_writer;
//use moodle_url;
use core_ai\output\index_page;
class core_ai_renderer extends \plugin_renderer_base {

    public function render_index_page(index_page $indexpage) {
        $data = $indexpage->export_for_template($this);
        return parent::render_from_template('core_ai/index_page', $data);
    }

}
