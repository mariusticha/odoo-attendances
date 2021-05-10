<?php

namespace Main;

use Carbon\Carbon;
use Services\Misc;
use Services\PersonalData;

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
    private $period = [
        'begin' => '',
        'end' => '',
    ];

    /**
     * 
     *  public functions
     *
     */
    public function __construct()
    {
        $this->json = @file_get_contents('personal_data.json');

        Misc::nl();
        Misc::my_print("####  odoo attendances  ####", 2, false, 'success');
    }

    public function execute(): void
    {

        $this->chapter_one();

        $this->chapter_two();
    }

    private function chapter_one(): void
    {
        // init helpers
        $personal = new PersonalData();

        if ($this->json === false) {

            // welcome
            Misc::my_print("hey there, welcome to the odoo attendances filler 🎉");

            Misc::my_print("## chapter 1 - personal data ##", 2, false, 'info');

            Misc::my_print("first of all, let's set up your personal data.");
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

            Misc::my_print("## chapter 1 - personal data ##", 2, false, 'info');

            // check personal
            $personal->check($this->personal_data);
        }
    }

    private function chapter_two(): void
    {
        Misc::my_print("## chapter 2 - time span ##", 2, false, 'info');

        Misc::my_print("first, let's define the period of time for which you want to enter the attendances");

        $input_begin = Misc::my_read("what's the first day you want to fill, e.g. 2020-01-01? ");
        $input_end = Misc::my_read(".. and what's the last day you want to fill, e.g. 2020-01-31? ");
        
        while ($input_begin > $input_end) {
            Misc::my_print("❗ there was an error in your dates ❗", 2, false, 'warning');
            Misc::my_print("hint: please note that the first day must be before the last day");
            Misc::my_print("let's try again..");
            $input_begin = Misc::my_read("what's the first day you want to fill, e.g. 2020-01-01? ");
            $input_end = Misc::my_read(".. and what's the last day you want to fill, e.g. 2020-01-31? ");
        }

        try {
            $this->period['begin'] =  Carbon::createFromFormat('Y-m-d', $input_begin);
            $this->period['end'] =  Carbon::createFromFormat('Y-m-d', $input_end);
        } catch (\Throwable $th) {
            exit('error');
        }
        
        Misc::my_print("all good. let's move on..");
    }
}
