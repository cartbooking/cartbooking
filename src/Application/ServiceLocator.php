<?php

namespace CartBooking\Application;

use CartBooking\Publisher\PublisherRepository;
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
     * @return Logger
     */
    public static function getLogger()
    {
        return self::$app['logger'];
    }

    /**
     * @return PublisherRepository
     */
    public static function getPioneerRepository()
    {
        return self::$app['repository.pioneer'];
    }
}
