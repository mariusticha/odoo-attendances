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

// imports
require "{$_SERVER['HOME']}/projects/odoo-attendances/vendor/autoload.php";
require "{$_SERVER['HOME']}/projects/odoo-attendances/services/helpers.php";

(new \NunoMaduro\Collision\Provider)->register();

use Main\Main;

// entry point
(new Main())->execute();

exit('exit');