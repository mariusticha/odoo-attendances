<?php

namespace Chapters;

use Services\PersonalData;

class One
{
    public ?array $personal_data;

    public function __construct(?array $personal_data)
    {
        $this->personal_data = $personal_data;
    }

    public function execute(): array
    {
        // init helpers
        $personal = new PersonalData();

        // welcome
        nl(2);
        my_print("## chapter 1 - personal data ##", 2, false, 'info');

        if ($this->personal_data) {

            // check personal
            return $personal->check($this->personal_data);
        } else {

            return $personal->set_up_personal_data();
        }
    }
}
