<?php

namespace Main;

use Chapters\One;
use Chapters\Two;
use Chapters\Three;
use Chapters\Four;


class Main
{
    /**
     *
     * variables
     *
     */
    private $json;
    private $personal_data = null;
    private $periods;
    private $debug = true;

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
        // intro
        $this->intro();

        // chapter getpersonaldata
        if (!$this->debug) {

            $this->personal_data = (new One($this->personal_data))->execute();
        } else {

            my_print("## chapter 1 - debugged ##", 2, false, 'info');
        }

        // chapter two
        if (!$this->debug) {

            $this->periods = (new Two())->execute();
        } else {

            $this->periods = [
                'working_period' => [
                    'start' => '2021-05-03',
                    'end' => '2021-05-31',
                ],
                'excluded_period' => [
                    'Saturday, 2021-05-08' => 'weekend',
                    'Sunday, 2021-05-09' => 'weekend',
                    'Thursday, 2021-05-13' => 'public holiday: Christi Himmelfahrt',
                    'Saturday, 2021-05-15' => 'weekend',
                    'Sunday, 2021-05-16' => 'weekend',
                    'Saturday, 2021-05-22' => 'weekend',
                    'Sunday, 2021-05-23' => 'weekend',
                    'Monday, 2021-05-24' => 'public holiday: Pfingstmontag',
                    'Saturday, 2021-05-29' => 'weekend',
                    'Sunday, 2021-05-30' => 'weekend',
                    'Monday, 2021-05-17' => 'holiday',
                    'Tuesday, 2021-05-18' => 'holiday',
                    'Wednesday, 2021-05-19' => 'holiday',
                    'Thursday, 2021-05-20' => 'holiday',
                ],
            ];
            my_print("## chapter 2 - debugged ##", 2, false, 'info');
        }
        // chapter three
        if (!$this->debug) {

            (new Three($this->personal_data, $this->periods))->execute();
        } else {

            my_print("## chapter 3 - debugged ##", 0, false, 'info');
        }

        // chapter four
        (new Four($this->personal_data, $this->periods))->execute();

        // bye
        my_print("thanks and bye", 2, false, 'success');
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
