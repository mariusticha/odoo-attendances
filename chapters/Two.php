<?php

namespace Chapters;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Services\LPLib_Feiertage_Connector;
use Services\Period;

class Two
{
    private $working_period = [
        'start' => '',
        'end' => '',
    ];
    private $excluded_period = [];

    public function __construct()
    {
        $this->feiertageApi = LPLib_Feiertage_Connector::getInstance();
    }

    public function execute(): array
    {
        // welcome
        nl(2);
        my_print("## chapter 2 - time period ##", 2, false, 'info');

        // get whole period
        my_print("first, let's define the time period you want to fill in... ");

        $this->working_period = Period::get_period([
            'start' => '2021-05-03',
            'end' => '2021-05-31',
        ]);

        // exclude days without work by default
        $this->exclude_weekends_and_public_holiday();
        my_print(
            "great! please note, that we've removed weekends and public holidays by default ",
            2,
            false,
            'success'
        );
        Period::show_period(
            $this->working_period,
            $this->excluded_period,
            "do you want to see the period selected so far?",
        );

        // exclude holidays
        my_print(
            "alright. let's move on and exclude the days you were not at work...",
            2,
            false,
            'success'
        );

        $this->exclude_holidays();
        Period::show_period(
            $this->working_period,
            $this->excluded_period,
            'thanks! do you want to see the updated period?'
        );

        return [
            'working_period' => $this->working_period,
            'excluded_period' => $this->excluded_period,
        ];
    }

    private function exclude_weekends_and_public_holiday(): void
    {
        $period = Period::get_carbon_period($this->working_period);

        foreach ($period as $date) {

            if ($date->isWeekend()) {

                $this->excluded_period[$date->format(Period::FORMAT_SHOW)] = 'weekend';
                continue;
            }

            if ($feiertag = $this->feiertageApi->isFeiertagInLand(
                $date,
                LPLib_Feiertage_Connector::LAND_BRANDENBURG
            )) {

                $this->excluded_period[$date->format(Period::FORMAT_SHOW)] = "public holiday: $feiertag";
                continue;
            }
        }
    }

    private function exclude_holidays(): void
    {
        $example = italic('(e.g. sick leaves, holidays, etc)');
        // pick holiday type
        $holiday = my_switch(
            "do you want to exclude any days? $example",
            [
                [
                    'value' => 1,
                    'text' => 'yes, a period of days',
                ],
                [
                    'value' => 2,
                    'text' => 'yes, a single but whole day',
                ],
                // [
                //     'value' => 3,
                //     'text' => 'yes, a half day',
                // ],
                [
                    'value' => 3,
                    'text' => 'no',
                ],
            ],
            0,
        );

        while ($holiday['value'] != 3) {
            // exclude holidays by type
            $excluded = match ($holiday['value']) {
                1 => $this->exlude_period_as_holidays(),
                2 => $this->exclude_full_day_as_holidays(),
                3 => $this->exclude_half_day_as_holidays(),
            };

            my_print(
                "thanks. we've removed these days: ",
                2,
                false,
                'success'
            );
            Period::show_exclusion($excluded);

            // another exclude?
            $holiday = my_switch(
                "do you want to exclude more days?",
                [
                    [
                        'value' => 1,
                        'text' => 'yes, a period of days',
                    ],
                    [
                        'value' => 2,
                        'text' => 'yes, a single but whole day',
                    ],
                    // [
                    //     'value' => 3,
                    //     'text' => 'yes, a half day',
                    // ],
                    [
                        'value' => 3,
                        'text' => 'no',
                    ],
                ],
                2,
            );
        }

        my_print(
            "great, we have excluded all of your holidays from working days",
            2,
            false,
            'success'
        );
    }

    private function exlude_period_as_holidays(): array
    {
        $excluded = [];

        $period_input = Period::get_period([
            'start' => '2021-05-15',
            'end' => '2021-05-20',
        ]);

        $period = CarbonPeriod::create(
            $period_input['start'],
            $period_input['end']
        );
        foreach ($period as $date) {

            // only add if not has already been a weekend or public holiday
            if (!array_key_exists($date->format(Period::FORMAT_SHOW), $this->excluded_period)) {

                $this->excluded_period[$date->format(Period::FORMAT_SHOW)] = 'holiday';
            }

            $excluded[] = $date->format(Period::FORMAT_SHOW);
        }

        return $excluded;
    }

    private function exclude_full_day_as_holidays(): array
    {
        $excluded_to_store = Period::get_single_date('debug:single');

        $this->excluded_period[$excluded_to_store] = 'holiday';

        return [$excluded_to_store];
    }

    private function exclude_half_day_as_holidays(): void
    {
    }
}
