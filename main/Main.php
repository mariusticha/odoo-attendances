<?php

namespace Main;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Services\LPLib_Feiertage_Connector;
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
    private $whole_period = [
        'start' => '',
        'end' => '',
        'period' => null,
        'holidays' => [],
    ];

    /**
     * 
     *  public functions
     *
     */
    public function __construct()
    {
        $this->json = @file_get_contents('personal_data.json');
        $this->feiertageApi = LPLib_Feiertage_Connector::getInstance();

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

        $this->period = $this->get_period();
        Misc::my_print("we've removed weekends and national holidays by default.");

        $this->show_period();

        Misc::my_print("alright. let's move on and exclude your holidays or sick leaves.");

        $this->handle_holidays();
        var_export(get_object_vars($this));
    }

    private function get_period(): array
    {
        $dates = $this->get_start_and_end_date();

        $period = CarbonPeriod::create($dates['start'], $dates['end']);

        return [
            'start' => $dates['start'],
            'end' => $dates['end'],
            'carbon' => $period,
        ];
    }

    private function handle_holidays(): void
    {
        $holiday = Misc::my_switch("do you want to exclude any days, e.g. sick leaves, holidays, etc?", [
            [
                'value' => 1,
                'text' => 'yes, a period of days',
            ],
            [
                'value' => 2,
                'text' => 'yes, a single but whole day',
            ],
            [
                'value' => 3,
                'text' => 'yes, a half day',
            ],
            [
                'value' => 0,
                'text' => 'no',
            ],
        ]);

        while ($holiday['value']) {
            match ($holiday['value']) {
                1 => $this->exlude_period(),
                2 => $this->exclude_full_day(),
                3 => $this->exclude_half_day(),
            };

            $holiday = Misc::my_switch("do you want to exclude more days?", [
                [
                    'value' => 1,
                    'text' => 'yes, a period of days',
                ],
                [
                    'value' => 2,
                    'text' => 'yes, a single but whole day',
                ],
                [
                    'value' => 3,
                    'text' => 'yes, a half day',
                ],
                [
                    'value' => 0,
                    'text' => 'no',
                ],
            ]);
        }
    }

    private function get_start_and_end_date(): array
    {
        $input_start = Misc::my_read("what's the first day you want to fill, e.g. 2020-01-01? ");
        $input_end = Misc::my_read(".. and what's the last day you want to fill, e.g. 2020-01-31? ");

        return [
            'start' => Carbon::createFromFormat('Y-m-d', '2021-05-01'),
            'end' => Carbon::createFromFormat('Y-m-d', '2021-05-31'),
        ];

        while ($result = $this->are_invalid_date_inputs($input_start, $input_end)) {
            Misc::my_print("❗ there was an error in your dates ❗", 2, false, 'warning');
            Misc::my_print("hint: $result");
            Misc::my_print("let's try again..");
            $input_start = Misc::my_read("what's the first day you want to fill, e.g. 2020-01-01? ");
            $input_end = Misc::my_read(".. and what's the last day you want to fill, e.g. 2020-01-31? ");
        }

        $start = Carbon::createFromFormat('Y-m-d', $input_start);
        $end = Carbon::createFromFormat('Y-m-d', $input_end);

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private function are_invalid_date_inputs(string $start, string $end): string
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
        return '';
    }

    private function print_date(Object $date): void
    {
        if ($date->isWeekend()) {
            Misc::my_print(
                $date->format('l, Y-m-d') . " (weekend)",
                1,
                true,
                'error'
            );

            return;
        }

        if ($feiertag = $this->feiertageApi->isFeiertagInLand(
            $date,
            LPLib_Feiertage_Connector::LAND_BRANDENBURG
        )) {
            Misc::my_print(
                $date->format('l, Y-m-d') . " ($feiertag)",
                1,
                true,
                'error'
            );

            return;
        }

        Misc::my_print($date->format('l, Y-m-d'), 1, true, 'info');
    }

    private function show_period(): void
    {
        $show_current_period = Misc::my_read("do you want to see the period selected so far? (Y/n) ");

        if ($show_current_period != 'n') {
            foreach ($this->period['carbon'] as $date) {

                $this->print_date($date);
            }
            Misc::nl();
        }
    }

    private function exlude_period(): void
    {
    }

    private function exclude_full_day(): void
    {
    }

    private function exclude_half_day(): void
    {
    }
}
