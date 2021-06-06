<?php

namespace Main;

use Chapters\One;
use Chapters\Two;

class Main
{
    /**
     *
     * variables
     *
     */
    private $json;
    private $personal_data = null;

    /**
     * 
     *  public functions
     *
     */
    public function __construct()
    {
        $this->json = @file_get_contents('personal_data.json');
    
        if ($this->json) {
            
            $this->personal_data = json_decode($this->json, true);
            $this->abort_on_invalid_json();
        }
    }
    
    public function execute(): void
    {
        $this->intro();
        (new One($this->personal_data))->execute();
        (new Two())->execute();
    }

    private function intro(): void
    {
        // welcome screen
        my_print("####  odoo attendances  ####", 2, false, 'success');
        if ($this->json) {

            $name = $this->personal_data['first_name'];
            my_print("hey $name, nice to have you back 🎉");
        } else {

            my_print("hey there, welcome to the odoo attendances filler 🎉");
        }
    }

    private function abort_on_invalid_json()
    {
        if (
            !isset($this->personal_data['name']) ||
            !isset($this->personal_data['contract'])
        ) {
            my_print("  there was an error parsing your personal data", 1);
            my_print("  we've cleared your personal data storage", 1);
            my_print("  please try again", 1);
            `rm personal_data.json`;
            exit();
        }
    }
}
