<?php

require 'vendor/autoload.php';
// require 'month_and_days.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Employee');
$sheet->setCellValue('B1', 'Check In');
$sheet->setCellValue('C1', 'Break');
$sheet->setCellValue('D1', 'Check Out');
$sheet->setCellValue('E1', 'Netto Hours');
$sheet->setCellValue('F1', 'Worked Hours');


$months = [];

for ($i=0; $i < 12; $i++) { 
    $months[] = $i+1;
}

function inputs($months, $intervall)
{
    $year_min = 1900;
    $year_max = 2100;

    // month start
    $year = readLine("$intervall year [$year_min,$year_max]: ");
    if ($year<$year_min || $year>$year_max) {
        exit("error - wrong year input \n");
    }

    // month start
    $month = readLine("$intervall month [1,12]: ");
    if (!in_array($month, $months)) {
        exit("error - wrong month input \n");
    }

    // evaluate max day
    $max_day = in_array($month, [2, 4, 6, 9, 11]) ? $month == 2 ? 28 : 30 : 31;
    for ($i = 0; $i < $max_day; $i++) {
        $days[] = $i + 1;
    }

    // day start
    $day = readLine("$intervall day [1,$max_day]: ");

    if (!in_array($day, $days)) {
        exit("error - wrong day input \n");
    }

    return [
        'year' => $year,
        'month' => $month,
        'day' => $day
    ];
}

// name
$name = readLine("your name: ");
$start_input = inputs($months, 'start');
$end_input = inputs($months, 'end');

// debugs
// $start_input = [
//     'year' => 2020,
//     'month' => 5,
//     'day' => 14,
// ];
// $end_input = [
//     'year' => 2020,
//     'month' => 5,
//     'day' => 31,
// ];

$excludeDays = [];
$i=1;
echo "note: weekends are excluded automatically.\n";
$excludeDaysYesNo = readLine("do you want to exclude days like holiday or sick leaves (y/n): ");
while ($excludeDaysYesNo == 'y') {
    $inputToExclude = inputs($months, "$i. exclusion: ");
    $dayToExclude = new DateTime();
    $dayToExclude->setDate($inputToExclude['year'], $inputToExclude['month'], $inputToExclude['day']);
    $excludeDays[] = $dayToExclude->format("Y-m-d");
    $excludeDaysYesNo = readLine("do you want to exclude another day (y/n): ");
    $i=$i+1;
}

$begin = new DateTime();
$begin->setDate($start_input['year'], $start_input['month'], $start_input['day']);

$end = new DateTime();
$end->setDate($end_input['year'], $end_input['month'], $end_input['day']+1);

if ($begin >= $end) {
    exit("error - wrong start/end input \n");
}

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

$row = 2;

foreach ($period as $dt) {

    // exclude weekends
    if($dt->format('N') >= 6) {
        continue;
    }

    // exclude personal excludes
    if(in_array($dt->format("Y-m-d"), $excludeDays)) {
        continue;
    }

    $begin_work = clone $dt;
    $begin_work->setTime(rand(7,9), rand(1,59), rand(1,59));

    $working = rand(30200, 31000);

    $end_work = clone $begin_work;
    $end_work->add(new DateInterval('PT' . $working . 'S'));

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


exit();