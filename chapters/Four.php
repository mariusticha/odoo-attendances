<?php

namespace Chapters;

use Services\Period;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Four
{
    private array $personal_data = [];
    private array $working_period = [];
    private array $excluded_period = [];
    private string $default_timesheet;
    private float $default_break = 0.5;
    private ?Spreadsheet $spreadsheet = null;
    private ?Worksheet $sheet = null;

    public function __construct(array $personal_data, array $periods)
    {
        $this->personal_data = $personal_data;
        $this->working_period = $periods['working_period'];
        $this->excluded_period = $periods['excluded_period'];

        $startDate = $periods['working_period']['start'];

        $endDate = $periods['working_period']['end'];

        $this->default_timesheet = "odoo-{$startDate}-{$endDate}";

        // init spreadsheet
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->sheet->setCellValue('A1', 'Employee');
        $this->sheet->setCellValue('B1', 'Check In');
        $this->sheet->setCellValue('C1', 'Break');
        $this->sheet->setCellValue('D1', 'Check Out');
        $this->sheet->setCellValue('E1', 'Netto Hours');
        $this->sheet->setCellValue('F1', 'Worked Hours');
    }

    public function execute(): void
    {
        // welcome
        nl(2);
        my_print("## chapter 4 - filling time sheet ##", 2, false, 'info');

        $name = $this->getTimeSheetName();

        $period = $this->getPeriod();

        $this->fillExcel($period);

        $this->storeExcel($name);

        $this->printPeriod($period);
    }

    private function getTimeSheetName(): string
    {
        $example = italic("(default: {$this->default_timesheet}.xlsx)");
        $timesheet = my_read("how do you want to name your timesheet? $example");

        return $timesheet === ''
            ? $this->default_timesheet
            : $timesheet;
    }

    private function getPeriod(): array
    {
        $working_period = Period::get_carbon_period($this->working_period);

        $period = [];

        foreach ($working_period as $day) {

            // if day is part of excluded period, continue and check next day
            if (array_key_exists(
                $day->format(Period::FORMAT_SHOW),
                $this->excluded_period
            )) {

                continue;
            }

            $begin = $day->setTime(8, rand(0, 59), rand(0, 59));
            $end = (clone $begin)
                ->addSeconds($this->getDailyWorkDuranceInSeconds());

            $period[] = [
                'name' => $this->personal_data['name'],
                'begin' => $day->format("Y-m-d H:i:s"),
                'break' => $this->default_break,
                'end' => $end->format("Y-m-d H:i:s"),
            ];
        }

        return $period;
    }

    private function getDailyWorkDuranceInSeconds(): int
    {
        // calc hours per day from contract
        $working_hours_per_day = $this->personal_data['contract']['value'] / 5;

        // min and max
        $min = (($working_hours_per_day + $this->default_break) * 3600) - 200;
        $max = $min + 600;

        return rand($min, $max);
    }

    private function fillExcel($period): void
    {
        $row = 2;

        foreach ($period as $day) {

            $this->sheet->setCellValue("A$row", $day['name']);
            $this->sheet->setCellValue("B$row", $day['begin']);
            $this->sheet->setCellValue("C$row", $day['break']);
            $this->sheet->setCellValue("D$row", $day['end']);

            $row += 1;
        }
    }

    private function storeExcel($name): void
    {
        (new Xlsx($this->spreadsheet))->save("{$name}.xlsx");
    }

    private function printPeriod($period): void
    {
        foreach ($period as $day) {
            my_print("{$day['begin']} - {$day['end']}", 1, false, 'warning');
        }
    }
}
