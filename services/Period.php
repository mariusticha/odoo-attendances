<?php

namespace Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Chapters\Two;

class Period
{
    public const FORMAT_HINT = '(yyyy-mm-dd)';
    public const FORMAT_INPUT = 'Y-m-d';
    public const FORMAT_SHOW = 'l, Y-m-d';

    //  public functions
    public static function get_single_date($debug = null): string
    {
        $example = italic(Period::FORMAT_HINT);

        $excluded = my_read(
            "which day do you want to exclude ? $example",
        );

        if ($debug) {

            $excluded = $excluded ?: '2021-05-27';
        }

        try {

            return @Carbon::createFromFormat(Period::FORMAT_INPUT, $excluded)->format(Period::FORMAT_SHOW);
        } catch (\Throwable $th) {

            my_print(
                "your input was invalid, please use the correct format: $example",
                2,
                false,
                'warning'
            );
            return self::get_single_date('debug:single');
        }
    }

    public static function get_period(array $debug = []): array
    {
        $dates = self::get_start_and_end_date($debug);

        return [
            'start' => $dates['start'],
            'end' => $dates['end'],
        ];
    }

    // helpers
    private static function get_start_and_end_date(array $debug = []): array
    {
        $exampleStart = italic(Carbon::now()->startOfMonth()->format(Period::FORMAT_INPUT));
        $exampleEnd = italic(Carbon::now()->endOfMonth()->format(Period::FORMAT_INPUT));
        $input_start = my_read("what's the first date? $exampleStart") ?? Carbon::now()->startOfMonth()->format(Period::FORMAT_INPUT);
        $input_end = my_read(".. and what's the last date? $exampleEnd") ?? Carbon::now()->endOfMonth()->format(Period::FORMAT_INPUT);

        // ? debug
        if ($debug && !$input_start && !$input_end) {
            $input_start = $debug['start'];
            $input_end = $debug['end'];
        }
        // ? end debug

        if ($result = self::are_invalid_date_inputs($input_start, $input_end)) {
            my_print("❗ there was an error in your dates ❗", 2, false, 'warning');
            $hint = style('hint:', 'error');
            my_print("$hint $result");
            my_print("let's try again..");
            return self::get_start_and_end_date($debug);
        }

        $start = $input_start;
        $end = $input_end;

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private static function are_invalid_date_inputs(string $start, string $end): ?string
    {
        $example = italic(Period::FORMAT_HINT);

        // start or enter not available » try again
        if (!$start || !$end) {

            return style(
                'please enter a first day as well as the last day.',
                'warning'
            );
        }

        // start or enter not valid » try again
        try {
            $start = Carbon::createFromFormat(Period::FORMAT_INPUT, $start);
            $end = Carbon::createFromFormat(Period::FORMAT_INPUT, $end);
        } catch (\Throwable $th) {

            return style(
                "please enter two valid dates, format: $example",
                'warning'
            );
        }



        // start <= end » try again
        if ($start > $end) {

            return style(
                'please enter a start date that is earlier than the end date',
                'warning'
            );
        }

        // all good
        return false;
    }

    public static function get_carbon_period($period): CarbonPeriod
    {
        $start = Carbon::createFromFormat(Period::FORMAT_INPUT, $period['start']);
        $end = Carbon::createFromFormat(Period::FORMAT_INPUT, $period['end']);
        return CarbonPeriod::create($start, $end);
    }

    public static function show_period(array $period, array $excluded, string $question = ''): void
    {
        $example = italic('(Y/n)');
        $show_current_period = my_read("$question $example");

        if ($show_current_period != 'n') {
            $showing_period = Period::get_carbon_period($period);
            foreach ($showing_period as $date) {

                self::print_date($date, $excluded);
            }
            nl();
        }
    }

    public static function show_exclusion(array $period): void
    {
        foreach ($period as $date) {
            self::print_date(Carbon::createFromFormat(Period::FORMAT_SHOW, $date), null, 'warning');
        }
        nl();
    }

    private static function print_date(Object $date, ?array $excluded = null, ?string $style = null): void
    {
        $date_as_stored = $date->format(self::FORMAT_SHOW);
        if (isset($excluded[$date_as_stored])) {

            $reason = $excluded[$date_as_stored];

            match ($reason) {
                'holiday' =>  my_print(
                    $date->format(self::FORMAT_SHOW) . " ({$reason})",
                    1,
                    true,
                    'warning'
                ),
                default => my_print(
                    $date->format(self::FORMAT_SHOW) . " ({$reason})",
                    1,
                    true,
                    'error'
                ),
            };

            return;
        }

        my_print(
            $date->format(self::FORMAT_SHOW),
            1,
            true,
            $style ?: 'info'
        );
    }
}
