<?php

namespace Chapters;

use Services\Misc;
use Services\PersonalData;

class One
{
    public function __construct($json)
    {
        $this->json = $json;
    }

    public function execute(): array
    {
        // init helpers
        $personal = new PersonalData();

        if ($this->json === false) {

            // welcome
            Misc::my_print("hey there, welcome to the odoo attendances filler 🎉");
            Misc::my_print("## chapter 1 - personal data ##", 2, false, 'info');
            Misc::my_print("first of all, let's set up your personal data.");

            return $personal->set_up_personal_data();
        } else {

            // parse data
            $this->personal_data = json_decode($this->json, true);

            $this->abort_on_invalid_inputs();

            // welcome
            Misc::my_print("hey {$this->personal_data['name']}, nice to have you back 🎉");
            Misc::my_print("## chapter 1 - personal data ##", 2, false, 'info');

            // check personal
            return $personal->check($this->personal_data);
        }
    }

    private function abort_on_invalid_inputs()
    {
        if (
            !isset($this->personal_data['name']) ||
            !isset($this->personal_data['contract'])
        ) {
            Misc::my_print("  there was an error parsing your personal data", 1);
            Misc::my_print("  we've cleared your personal data storage", 1);
            Misc::my_print("  please try again", 1);
            `rm personal_data.json`;
            exit();
        }
    }
}
