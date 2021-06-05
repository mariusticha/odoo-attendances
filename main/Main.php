<?php

namespace Main;

use Chapters\One;
use Chapters\Two;
use Services\Misc;

class Main
{
    /**
     *
     * variables
     *
     */
    private $json;

    /**
     * 
     *  public functions
     *
     */
    public function __construct()
    {
        // inits
        $this->json = @file_get_contents('personal_data.json');

        // welcome screen
        Misc::nl();
        Misc::my_print("####  odoo attendances  ####", 2, false, 'success');
    }

    public function execute(): void
    {
        (new One($this->json))->execute();
        (new Two($this->json))->execute();
    }
}
