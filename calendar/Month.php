<?php

namespace Calendar;

use Carbon\Carbon;

/**
 * Month representation as 2d-Array
 * Month = [ ...Weeks ]
 */ class Month
{
    public const MONTH_WIDTH = 20;
    public const WEEKDAYS = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];
    public const PADDING = " ";
    public const EMPTY_SET = "   ";
    public const NEWLINE = "\n";

    public $name;
    public $weeks;

    /**
     * Constructor
     *
     * @param String $date
     */
    public function __construct(String $date)
    {
        $date = Carbon::parse($date);

        $name = $date->englishMonth;
        $name .= Month::PADDING . $date->year;
        $this->name = $name;

        $weeks = [];

        $weeks = array_merge($weeks, $this->addDates($date));

        $this->weeks = $weeks;
    }

    /** 
     * returns month as 2d array 
     * one row per week
     * (null values represet week days not in this month)
     * 
     * @param [type] $date
     * @return Array
     */
    private function addDates($date): array
    {
        $month = [];
        $week = [];

        $dayInWeek = $date->firstOfMonth()->isoWeekday();
        $offset = $dayInWeek - 1;

        // fill days not in current month with null
        for ($i = 0; $i < $offset; $i++) {
            array_push($week, null);
        }

        // loop over every day in this month
        for ($dayInMonth = 1; $dayInMonth <= $date->daysInMonth; $dayInMonth++) {

            array_push($week, clone $date->firstOfMonth()->addDays($dayInMonth - 1));

            // if we are at day 7 week is done
            if ($dayInWeek % 7 == 0) {
                array_push($month, $week);
                $week = [];
            }

            $dayInWeek++;
        }

        // pad week to 7 days
        array_push($month, $this->paddWeek($week));

        // padd month to 6 weeks
        $month = $this->paddMonth($month);

        return $month;
    }

    /**
     * padds end of week to 7 days in case rest of week is in another month
     *
     * @param array $week
     * @return array
     */
    private function paddWeek(array $week): array
    {
        // as long as we have less than 7 days fill with null values
        while (count($week) < 7) {
            array_push($week, null);
        }

        return $week;
    }

    /**
     * padd month to 6 weeks in case it has less than that
     *
     * @param array $month
     * @return array
     */
    private function paddMonth(array $month): array
    {
        // as long as month is below 6 weeks
        // create week with null values and push to month array
        while (count($month) < 6) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                array_push($week, null);
            }

            array_push($month, $week);
        }

        return $month;
    }
}
