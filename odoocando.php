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
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Services\LPLib_Feiertage_Connector;
use Main\Main;

$main = new Main();
$main->execute();

exit('exit');

/**
 * 
 * 
 *         _     _       _          __  __
 *        | |   | |     | |        / _|/ _|
 *    ___ | | __| |  ___| |_ _   _| |_| |_
 *   / _ \| |/ _` | / __| __| | | |  _|  _|
 *  | (_) | | (_| | \__ \ |_| |_| | | | |
 *   \___/|_|\__,_| |___/\__|\__,_|_| |_|
 *  
 *
 */

/**
 * 
 *  init spreadsheet
 * 
 */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Employee');
$sheet->setCellValue('B1', 'Check In');
$sheet->setCellValue('C1', 'Break');
$sheet->setCellValue('D1', 'Check Out');
$sheet->setCellValue('E1', 'Netto Hours');
$sheet->setCellValue('F1', 'Worked Hours');

const MONTHS = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
];

/**
 * 
 *  ---- user inputs ----
 * 
 */
function inputs($intervall)
{
    // setup connector

    $year_min = 1900;
    $year_max = 2100;

    // month start
    $year = readline("$intervall year [$year_min,$year_max]: ");
    if ($year < $year_min || $year > $year_max) {
        exit("error - wrong year input \n");
    }

    // month start
    $month = readline("$intervall month [1,12]: ");
    if (!in_array($month, MONTHS)) {
        exit("error - wrong month input \n");
    }

    // evaluate max day
    $max_day = in_array($month, [2, 4, 6, 9, 11]) ? ($month == 2 ? 28 : 30) : 31;
    for ($i = 0; $i < $max_day; $i++) {
        $days[] = $i + 1;
    }

    // day start
    $day = readline("$intervall day [1,$max_day]: ");

    if (!in_array($day, $days)) {
        exit("error - wrong day input \n");
    }

    return [
        'year' => $year,
        'month' => $month,
        'day' => $day
    ];
}


/**
 * 
 *  ---- command line arguments ----
 * 
 */

// if ($argc != 5) {
//     echo "wrong input\n\n";
//     echo "usage:\n";
//     echo "\tphp odoocando.php {first name} {last name} {startDate} {endDate}\n";
//     echo "formats:\n";
//     echo "\tfirst name: capitalized\n";
//     echo "\tlast name: capitalized\n";
//     echo "\tstartDate: yyyy-mm-dd\n";
//     echo "\tendDate: yyyy-mm-dd\n";
//     echo "example:\n";
//     echo "\tphp odoocando.php Paul_Hammer 2019-01-01 2020-12-31\n\n";
//     exit("please try again\n\n");
// }

// // read command line arguments
// try {

//     // fist name
//     if (isset($argv[1])) {
//         $name = $argv[1];
//     }

//     // last name
//     if (isset($argv[2])) {
//         $name .= ' ' . $argv[2];
//     }

//     // start
//     if (isset($argv[3])) {
//         $start = $argv[3];

//         [$startYear, $startMonth, $startDay] = explode("-", $start);

//         $start_input = [
//             'year' => $startYear,
//             'month' => $startMonth,
//             'day' => $startDay
//         ];
//     }

//     // end
//     if (isset($argv[4])) {
//         $end = $argv[4];

//         [$endYear, $endMonth, $endDay] = explode("-", $end);

//         $end_input = [
//             'year' => $endYear,
//             'month' => $endMonth,
//             'day' => $endDay
//         ];
//     }
// } catch (Exception $e) {
//     /* ignoring */
// }

$start_input = null;
$end_input = null;
$name = null;




if ($start_input == null) $start_input = inputs(MONTHS, 'start');
if ($end_input == null) $end_input = inputs(MONTHS, 'end');
if ($name == null) $name = readline("your name: ");


$excludeDays = [];
$i = 1;
echo "note: weekends are excluded automatically.\n";
$excludeDaysYesNo = readline("do you want to exclude days like holiday or sick leaves (y/N): ");
while ($excludeDaysYesNo == 'y') {
    $singleDay = readline("do you want to exclude a single day (y/N)? ");
    switch ($singleDay) {
        case 'y':
            [$year, $month, $day] = explode('-', readline("date to exclude (yyyy-mm-dd): "));
            $dayToExclude = new DateTime();
            $dayToExclude->setDate($year, $month, $day);
            $excludeDays[] = $dayToExclude->format("Y-m-d");
            $i = $i + 1;
            break;

        default:
            [$year, $month, $day] = explode('-', readline("begin date of exclusion (yyyy-mm-dd): "));
            $beginExclude = new DateTime();
            $beginExclude->setDate($year, $month, $day);
            [$year, $month, $day] = explode('-', readline("end date of exclusion (yyyy-mm-dd): "));
            $endExclude = new DateTime();
            $endExclude->setDate($year, $month, $day);

            $interval = DateInterval::createFromDateString('1 day');
            $exclusion = new DatePeriod($beginExclude, $interval, $endExclude);
            foreach ($exclusion as $date) {
                $excludeDays[] = $date->format("Y-m-d");
            }
            $i = $i + 1;
            break;
    }

    echo "\n";
    echo "the following days are marked for exclusion: \n";
    foreach ($excludeDays as $key => $value) {
        echo "\t$value\n";
    }
    echo "\n";
    $excludeDaysYesNo = readline("do you want to add another exclusion (y/N): ");
}

$begin = new DateTime();
$begin->setDate($start_input['year'], $start_input['month'], $start_input['day']);

$end = new DateTime();
$end->setDate($end_input['year'], $end_input['month'], $end_input['day'] + 1);

if ($begin >= $end) {
    exit("error - wrong start/end input \n");
}

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

$row = 2;
$connector = LPLib_Feiertage_Connector::getInstance();

foreach ($period as $dt) {

    // exclude weekends
    if ($dt->format('N') >= 6) {
        continue;
    }

    // exclude personal excludes
    if (in_array($dt->format("Y-m-d"), $excludeDays)) {
        continue;
    }

    $possibleVacation = $dt->format("Y-m-d");
    if ($connector->isFeiertagInLand($possibleVacation, LPLib_Feiertage_Connector::LAND_BRANDENBURG)) {
        echo "\n\nskipped vacation at $possibleVacation\n\n";
        continue;
    }
    $begin_work = clone $dt;
    // $begin_work->setTime(8, rand(0, 59), rand(0, 59));

    $working = rand(30200, 31000);

    $end_work = clone $begin_work;
    // $end_work->add(new DateInterval('PT' . $working . 'S'));

    $sheet->setCellValue("A$row", $name);
    $sheet->setCellValue("B$row", $begin_work->format("Y-m-d H:i:s"));
    $sheet->setCellValue("C$row", 0.5);
    $sheet->setCellValue("D$row", $end_work->format("Y-m-d H:i:s"));
    // $sheet->setCellValue("E$row", 'Netto Hours');
    // $sheet->setCellValue("F$row", 'Worked Hours');


    echo $begin_work->format("l Y-m-d H:i:s\n");
    echo $end_work->format("l Y-m-d H:i:s\n");
    echo $end_work->diff($begin_work)->format("%h:%i working\n");

    $row += 1;
}



$writer = new Xlsx($spreadsheet);
$writer->save('timesheet.xlsx');

echo "\n";
echo "***********************************\n";
echo "*                                 *\n";
echo "*     saved as timesheet.xlsx     *\n";
echo "*                                 *\n";
echo "***********************************\n";

exit();
