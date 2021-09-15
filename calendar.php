#!/usr/bin/env php
<?php

/**
 *                                  _         _          __  __
 *                                 | |       | |        / _|/ _|
 *    ___ _   _ _ __ _ __ ___ _ __ | |_   ___| |_ _   _| |_| |_
 *   / __| | | | '__| '__/ _ \ '_ \| __| / __| __| | | |  _|  _|
 *  | (__| |_| | |  | | |  __/ | | | |_  \__ \ |_| |_| | | | |
 *   \___|\__,_|_|  |_|  \___|_| |_|\__| |___/\__|\__,_|_| |_|
 *
 *
 */

// clear cli
system('clear');


// (new \NunoMaduro\Collision\Provider)->register();
require "{$_SERVER['HOME']}/projects/odoo-attendances/vendor/autoload.php";
require "{$_SERVER['HOME']}/projects/odoo-attendances/services/helpers.php";

use Carbon\Carbon;
use Calendar\Calendar;


        #DEBUG VACATIONS
        $vacations = [];
        for($i = 0; $i< 42; $i++) {
            array_push($vacations, Carbon::now()->addDays(3 + $i)->toDateString());
        }

// entry point
(new Calendar("2021-07-01", "2022-1-02", $vacations, 3))->print();
exit('exit');