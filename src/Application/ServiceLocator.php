<?php

namespace CartBooking\Application;

use Monolog\Logger;
use Pimple\Container;

class ServiceLocator
{
    /** @var  Container */
    private static $app;

    /**
     * @param Container $app
     */
    public static function setContainer(Container $app)
    {
        self::$app = $app;
    }

    /**
     * @return Logger
     */
    public static function getLogger()
    {
        return self::$app['logger'];
    }
}
