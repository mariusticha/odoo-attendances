<?php

require_once('services/Misc.php');
require_once('services/PersonalData.php');

class Main
{
    /**
     *  
     * variables
     *
     */
    private $json;
    private $personal_data = [
        'name' => '',
        'contract' => '',
    ];

    /**
     * 
     *  public functions
     *
     */
    public function __construct()
    {
        $this->json = @file_get_contents('personal_data.json');
    }

    public function execute()
    {
        // init helpers
        $personal = new PersonalData();

        if ($this->json === false) {

            // welcome
            Misc::my_print("hey there, welcome to the odoo attendances filler 🎉");

            Misc::my_print("first, let's set up your personal data.");
            $personal->set_up_personal_data();
        } else {

            // parse data
            $this->personal_data = json_decode($this->json, true);
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

            // welcome
            Misc::my_print("hey {$this->personal_data['name']}, nice to have you back 🎉");

            // check personal
            $personal->check($this->personal_data);
        }
    }


   
}
