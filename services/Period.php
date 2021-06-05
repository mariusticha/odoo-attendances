<?php

namespace Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Chapters\Two;

class Period
{
    public function __construct()
    {
    }

    public static function get_period($debug = null): array
    {
        $dates = self::get_start_and_end_date($debug);

        // $period = CarbonPeriod::create($dates['start'], $dates['end']);

        return [
            'start' => $dates['start'],
            'end' => $dates['end'],
            // 'carbon' => $period,
            // 'holidays' => [],
        ];
    }

    private static function get_start_and_end_date($debug = null): array
    {
        $input_start = Misc::my_read("what's the first day you want to fill, e.g. 2020-01-01? ");
        $input_end = Misc::my_read(".. and what's the last day you want to fill, e.g. 2020-01-31? ");

        // ? debug 
        if ($debug == 'debug:total') {
            $input_start = '2021-05-03';
            $input_end = '2021-05-31';
        }
        if ($debug == 'debug:holiday') {
            $input_start = '2021-05-18';
            $input_end = '2021-05-20';
        }
        // ? end debug

        while ($result = self::are_invalid_date_inputs($input_start, $input_end)) {
            Misc::my_print("❗ there was an error in your dates ❗", 2, false, 'warning');
            Misc::my_print("hint: $result");
            Misc::my_print("let's try again..");
            $input_start = Misc::my_read("what's the first day you want to fill, e.g. 2020-01-01? ");
            $input_end = Misc::my_read(".. and what's the last day you want to fill, e.g. 2020-01-31? ");
        }

        // $start = Carbon::createFromFormat('Y-m-d', $input_start);
        // $end = Carbon::createFromFormat('Y-m-d', $input_end);
        $start = $input_start;
        $end = $input_end;

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private static function are_invalid_date_inputs(string $start, string $end): ?string
    {
        // start or enter not available » try again
        if (!$start || !$end) {

            return Misc::style('please enter a first day as well as the last day.');
        }

        // start or enter not valid » try again
        try {
            @Carbon::createFromFormat('Y-m-d', $start);
            @Carbon::createFromFormat('Y-m-d', $end);
        } catch (\Throwable $th) {

            return Misc::style('please enter two valid dates, format: yyyy-mm-dd');
        }

        // start <= end » try again
        if ($start > $end) {

            return Misc::style('please enter a start date that is earlier than the end date');
        }

        // all good
        return false;
    }

    public static function show_period(array $period, array $excluded): void
    {
        $show_current_period = Misc::my_read("do you want to see the period selected so far? (Y/n) ");

        if ($show_current_period != 'n') {
            $period = CarbonPeriod::create(
                $period['start'],
                $period['end']
            );
            foreach ($period as $date) {

                self::print_date($date->format(Two::FORMAT_STORE), $excluded);
            }
            Misc::nl();
        }
    }

    private static function print_date(string $date, $excluded): void
    {
        if (isset($excluded[$date])) {

            $reason = $excluded[$date];

            match ($reason) {
                'holiday' =>  Misc::my_print(
                    $date . " ({$reason})",
                    1,
                    true,
                    'warning'
                ),
                default => Misc::my_print(
                    $date . " ({$reason})",
                    1,
                    true,
                    'error'
                ),
            };

            return;
        }

        Misc::my_print(
            $date,
            1,
            true,
            'info'
        );
    }
}
