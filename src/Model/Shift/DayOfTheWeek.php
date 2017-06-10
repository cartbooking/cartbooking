<?php

namespace CartBooking\Model\Shift;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static DayOfTheWeek MONDAY
 * @method static DayOfTheWeek TUESDAY
 * @method static DayOfTheWeek WEDNESDAY
 * @method static DayOfTheWeek THURSDAY
 * @method static DayOfTheWeek FRIDAY
 * @method static DayOfTheWeek SATURDAY
 * @method static DayOfTheWeek SUNDAY
 */
final class DayOfTheWeek extends AbstractEnumeration
{
    const MONDAY = 0;
    const TUESDAY = 1;
    const WEDNESDAY = 2;
    const THURSDAY = 3;
    const FRIDAY = 4;
    const SATURDAY = 5;
    const SUNDAY = 6;
}
