<?php

namespace Chapters;

use Carbon\CarbonPeriod;
use Services\Period;

class Four
{
    private $personal_data = [];
    private $working_period = [];
    private $excluded_period = [];
    private $default_timesheet = 'timesheet';

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
        my_print("## chapter 4 - filling time sheet ##", 2, false, 'info');

        // get working days in period
        $example = italic("(default: {$this->default_timesheet}.xlsx)");
        $timesheet = my_read("how do you want to name your timesheet $example");
        if ($timesheet === '') {
            $timesheet = $this->default_timesheet;
        }

        dd(
            $this->personal_data,
            $this->working_period,
            $this->excluded_period,
        );
    }
}
