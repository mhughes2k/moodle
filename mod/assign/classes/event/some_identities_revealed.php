<?php

namespace mod_assign\event;

class some_identities_revealed extends identities_revealed
{
    public function get_description()
    {
        return "The user with id '$this->userid' has revealed identities in the assignment with course module " .
            "id '$this->contextinstanceid'.";
    }

    public static function get_name() {
        return get_string('eventsomeidentitiesrevealed', 'mod_assign');
    }

    protected function get_legacy_logdata() {
        $this->set_legacy_logdata('reveal some identities', get_string('revealedselectedidentities', 'assign'));
        return parent::get_legacy_logdata();
    }

    public function set_assign(\assign $assign) {
        parent::set_assign($assign);
        $this->data['anonymous'] = 0; // Unset anonymous so that this can be seen.
    }
}