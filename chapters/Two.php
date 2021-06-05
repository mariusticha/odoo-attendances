<?php

namespace Chapters;

use Carbon\CarbonPeriod;
use Services\LPLib_Feiertage_Connector;
use Services\Period;

class Two
{
    public const FORMAT_STORE = 'l, Y-m-d';
    private $working_period = [
        'start' => '',
        'end' => '',
    ];
    private $excluded_period = [];

    public function __construct()
    {
        $this->feiertageApi = LPLib_Feiertage_Connector::getInstance();
        
    }

    public function execute(): void
    {
        my_print("## chapter 2 - time span ##", 2, false, 'info');
        dd(
            'hello',
            'yo',
            1,
            ['array' => [1,2, null]],
        );
        exit;

        my_print("first, let's define the period of time for which you want to enter the attendances");

        $this->working_period = Period::get_period('debug:total');
        my_print("we've removed weekends and national holidays by default.");

        $this->exclude_weekends_and_public_holiday();
        Period::show_period($this->working_period, $this->excluded_period);

        my_print("alright. let's move on and exclude your holidays or sick leaves.");

        $this->exclude_holidays();
    }

    private function exclude_weekends_and_public_holiday(): void
    {
        $period = CarbonPeriod::create(
            $this->working_period['start'],
            $this->working_period['end']
        );
        foreach ($period as $date) {

            if ($date->isWeekend()) {

                $this->excluded_period[$date->format(self::FORMAT_STORE)] = 'weekend';
                continue;
            }

            if ($feiertag = $this->feiertageApi->isFeiertagInLand(
                $date,
                LPLib_Feiertage_Connector::LAND_BRANDENBURG
            )) {

                $this->excluded_period[$date->format(self::FORMAT_STORE)] = $feiertag;
                continue;
            }
        }
    }

    private function exclude_holidays(): void
    {
        $holiday = my_switch("do you want to exclude any days, e.g. sick leaves, holidays, etc?", [
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
                1 => $this->exlude_period_as_holidays(),
                2 => $this->exclude_full_day_as_holidays(),
                3 => $this->exclude_half_day_as_holidays(),
            };

            $holiday = my_switch("do you want to exclude more days?", [
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

    private function exlude_period_as_holidays(): void
    {
        $period = Period::get_period();


        foreach ($period['carbon'] as $date) {
            foreach ($this->excluded_period['excluded'] as $this->excluded) {
                if (!$this->excluded['date']->isSameDay($date)) {

                    $this->excluded_period['excluded'][] = [
                        'date' => $date,
                        'reason' => 'holiday',
                    ];
                }
            }
        }
    }

    private function exclude_full_day_as_holidays(): void
    {
    }

    private function exclude_half_day_as_holidays(): void
    {
    }
}
