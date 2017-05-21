<?php

namespace CartBooking\Application;

use CartBooking\Booking\BookingRepository;
use CartBooking\Lib\Db\Db;
use CartBooking\Location\LocationRepository;
use CartBooking\Publisher\PioneerRepository;
use CartBooking\Shift\ShiftRepository;
use Monolog\Logger;
use Pimple\Container;
use Swift_Mailer;
use Swift_Message;

class ServiceLocator
{
    /** @var  Container */
    private static $app;
    public static function setContainer(Container $app)
    {
        self::$app = $app;
    }

    /**
     * @return Swift_Message
     */
    public static function getEmailMessage()
    {
        return self::$app['communication'];
    }
    /**
     * @return Swift_Mailer
     */
    public static function getMailer()
    {
        return self::$app['mailer'];
    }

    /**
     * @return Db
     */
    public static function getDb()
    {
        return self::$app['db'];
    }

    /**
     * @return Logger
     */
    public static function getLogger()
    {
        return self::$app['logger'];
    }

    /**
     * @return PioneerRepository
     */
    public static function getPioneerRepository()
    {
        return self::$app['repository.pioneer'];
    }

    /**
     * @return BookingRepository
     */
    public static function getBookingRepository()
    {
        return self::$app['repository.booking'];
    }

    /**
     * @return ShiftRepository
     */
    public static function getShiftRepository()
    {
        return self::$app['repository.shift'];
    }

    /**
     * @return LocationRepository
     */
    public static function getLocationRepository()
    {
        return self::$app['repository.location'];
    }
}
