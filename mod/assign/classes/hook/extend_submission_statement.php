<?php
namespace mod_assign\hook;
require_once($CFG->libdir . '/formslib.php');
use core\hook\described_hook;
use assign;
use MoodleQuickForm;
use stdClass;
/**
 * Allows plugins to extend submission statements.
 */
class extend_submission_statement implements described_hook {

    public function __construct(
        public readonly assign $assign,
        public readonly MoodleQuickForm $mform,
        public readonly stdClass $data
    ) {

    }

    public static function get_hook_description(): string {
        return 'Hook dispatched to allow plugins to extend the submission statement';
    }

    public static function get_hook_tags(): array {
        return ['mod_assign'];
    }
}
