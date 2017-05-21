<?php

namespace CartBooking\Application;

class Calendar
{
    public static function generateOutputClass($selectedDay, $result, $month): string
    {
        if ($selectedDay == $result) {
            if ($month == date('F') && $result == date('j') && $selectedDay == $result) {
                $output_class = 'date-dt date-selected current-date';
            } else {
                $output_class = 'date-dt date-selected';
            }
        } elseif ($month == date('F') && $result == date('j')) {
            $output_class = 'current-date';
        } else {
            $output_class = 'date-dt';
        }
        return $output_class;
    }

    public static function createCalendarDate()
    {

    }
}
