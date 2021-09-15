<?php

namespace Calendar;

use Calendar\Month;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

/**
 * calendar representation of given timespan 
 */
class Calendar
{

    public $calendar = [];
    public $length = 0;

    // config
    public $chunkSize;
    public $vacations;

    public const PADDING = " "; // general use padding
    public const EMPTY_SET = "  "; // replaces dates
    public const NEWLINE = "\n";
    public const SPACER = "   "; // spacing between month

    private const MAX_WEEKS = 7;

    /**
     * Constructor
     *
     * @param String $startDate
     * @param String $endDate
     * @param array $vacations
     * @param Int $chunkSize
     */
    public function __construct(String $startDate, String $endDate, array $vacations = [], Int $chunkSize = 4)
    {
        // init configuration
        $this->vacations = array_map(function($date) {
            return Carbon::parse($date);
        }, $vacations);

        $this->chunkSize = $chunkSize;

        // create timespan
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);

        // loop over timespan in single months
        foreach ($period as $month) {
            array_push($this->calendar, new Month($month));
            $this->length++;
        }
    }

    /**
     * Prints Calendar representation to console
     *
     * @return void
     */
    public function print()
    {
        // chunk month as defined in config
        $chunks = array_chunk($this->calendar, $this->chunkSize);

        // loop over chunks
        foreach ($chunks as $chunk) {

            $this->printMonth($chunk);
            $this->printHeader($chunk);
            $this->printDates($chunk);
        }
    }

    /**
     * prints Month (eng) and year centered as monthly header
     *
     * @param array $months
     * @return Void
     */
    private function printMonth(array $months): Void
    {
        foreach ($months as $month) {
            $padding_l = 20 / 2 - intval(strlen($month->name) / 2);
            $padding_r = 20 - (strlen($month->name) + $padding_l);

            echo str_repeat(Calendar::PADDING, $padding_l) . $month->name . str_repeat(Calendar::PADDING, $padding_r);
            echo Calendar::SPACER;
        }
        echo Calendar::NEWLINE;
    }

    /**
     * prints weekday abbrevations
     *
     * @param array $months
     * @return Void
     */
    private function printHeader(array $months): Void
    {
        foreach ($months as $month) {
            foreach (Month::WEEKDAYS as $index => $day) {
                // adds spacing
                if ($index > 0)
                    echo Calendar::PADDING;

                my_print($day, 0);
            }
            echo Calendar::SPACER;
        }

        echo Calendar::NEWLINE;
    }

    /**
     * prints formatted dates
     *
     * @param array $months
     * @return Void
     */
    private function printDates(array $months): Void
    {

        // loop over each row(week) in Month
        for ($i = 0; $i < Calendar::MAX_WEEKS; $i++) {
            foreach ($months as $month) {
                // check if current month has enough weeks
                if (count($month->weeks) > $i) {

                    // acces indexed weeks dates directly to print multiple month per row
                    foreach ($month->weeks[$i] as $index => $weekDay) {

                        // create gap between dates
                        if ($index > 0) {
                            echo Calendar::PADDING;
                        }

                        // print out formatted date
                        if ($weekDay) {
                            $printout = $this->paddDates($weekDay);
                            $this->colorDates($weekDay, $printout);
                        } else {
                            // replace out of month date with empty set to add spacing
                            echo Calendar::EMPTY_SET;
                        }
                    }

                    // create paddign between Months
                    echo Calendar::SPACER;
                }
            }

            // line break after row is finished
            echo Calendar::NEWLINE;
        }
    }

    /**
     * handles color coding the dates
     *
     * @param Carbon $weekDay
     * @param String $printout
     * @return void
     */
    private function colorDates(Carbon $weekDay, String $printout)
    {

        $colors = [
            'weekend' => 'success',
            'vacation' => 'error',
            'default' => '',
        ];


        // date is weekend
        if ($weekDay->isWeekend()) {
            my_print($printout, 0, 0, $colors["weekend"]);
        }

        // date collides with vacation array
        else if (array_filter($this->vacations, function ($day) use ($weekDay) {
            return $day->isSameDay($weekDay);
        })) {
            my_print($printout, 0, 0, $colors["vacation"]);
        }

        // default
        else {
            my_print($printout, 0, 0, $colors["default"]);
        }
    }

    /**
     * padds dates taking only one char
     *
     * @param Carbon $weekDay
     * @return String
     */
    private function paddDates(Carbon $weekDay): String
    {
        // prepare output
        $output = "";

        // if weekday is smaller than 10 add padding
        if ($weekDay->day < 10) {
            $output .= Calendar::PADDING;
            $output .= $weekDay->day;
        } else {
            $output .= $weekDay->day;
        }

        return $output;
    }
}
