<?php

namespace Chapters;

use Services\Period;

class Three
{
    private array $personal_data = [];
    private array $working_period = [];
    private array $excluded_period = [];
    private int $working_days = 0;
    private int $working_hours = 0;

    public function __construct(array $personal_data, array $periods)
    {
        $this->personal_data = $personal_data;
        $this->working_period = $periods['working_period'];
        $this->excluded_period = $periods['excluded_period'];
    }

    public function execute(): void
    {
        // welcome
        nl(2);
        my_print("## chapter 3 - working days & overtime ##", 2, false, 'info');

        // get working days in period
        my_print("let's see how many days you've been working during the whole period: ");
        $this->working_days = $this->get_working_days();
        my_print($this->working_days, 2, true, 'success');

        // hours per day
        my_print("... and this is the total durance in hours: ");
        $this->working_hours = $this->working_days * 8;
        my_print($this->working_hours, 2, true, 'success');

        // overtime
        // $this->add_over_time();
        my_print(italic("-- overtime not implemented --"), 2, false, 'error');
        my_print(italic("---- this feature is under construction. please pr ----"), 2, false, 'warning');
    }

    private function get_working_days(): int
    {
        // get whole period as carbon object
        $working_period = Period::get_carbon_period($this->working_period);

        $working_days = 0;
        foreach ($working_period as $day) {

            // if day is part of excluded period, continue and check next day
            if (array_key_exists($day->format(Period::FORMAT_SHOW), $this->excluded_period)) {

                continue;
            }

            // else add to working days
            $working_days += 1;
        }

        return $working_days;
    }

    private function add_over_time(): void
    {
        $example = italic('(Y/n)');
        $percentage = 5;
        $max_overtime = ($percentage / 100) * $this->working_hours;

        $over_time = my_read("do you want to add over/under time to your working hours? $example");

        if ($over_time === 'n') {

            my_print("very well-behaved, let's finish filling your timesheet...");
            return;
        } else {
            my_print("🕒 please note, that you can add a maximum of $percentage% ({$max_overtime}h) to your working hours 🕒", 2, false, 'warning');

            $options = array_map(
                function ($value) {
                    return [
                        'value' => $value,
                        'text' => abs($value) == 1 ? "$value hour" : "$value hours",
                    ];
                },
                range(-$max_overtime, $max_overtime)
            );
            my_switch("how many hours do you want to add?", $options);

            // not yet finished
            my_print(italic("-- under construction --"), 2, false, 'error');
            my_print(italic("---- this feature is not yet finished ----"), 2, false, 'warning');
            my_print(italic("-- under construction --"), 1, false, 'error');
            return;
        }
    }
}
