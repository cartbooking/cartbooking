<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;

$initParams = parse_ini_file(__DIR__ . '/config.ini');

date_default_timezone_set($initParams['timezone']);

$app = new Pimple\Container();

$app['db'] = function () use ($initParams) {
    return new CartBooking\Lib\Db\Db(
        new \CartBooking\Lib\Db\Host($initParams['db']['host']),
        new \CartBooking\Lib\Db\Name($initParams['db']['name']),
        new \CartBooking\Lib\Db\Username($initParams['db']['username']),
        new \CartBooking\Lib\Db\Password($initParams['db']['password'])
    );
};

$app['mailer'] = function () use ($initParams) {
    $transport = Swift_SmtpTransport::newInstance($initParams['smtp']['host'], $initParams['smtp']['port']);
    return Swift_Mailer::newInstance($transport);
};

$app[\CartBooking\Application\EmailService::class] = function (Container $app) {
    return new \CartBooking\Application\EmailService($app['mailer'], $app['communication']);
};

$app['communication'] = function () use ($initParams) {
    $email = Swift_Message::newInstance();
    $email->setFrom([$initParams['communication']['from_email'] => $initParams['communication']['from_name']]);
    return $email;
};

$app['logger'] = function () use ($initParams) {
    $log = new Logger('name');
    $log->pushHandler(new StreamHandler($initParams['logger']['stream'], Logger::WARNING));
    return $log;
};

$app['repository.pioneer'] = function (Container $app) {
    return new \CartBooking\Publisher\PioneerRepository($app['db'], new \CartBooking\Publisher\PioneerHydrator());
};

$app['repository.booking'] = function (Container $app) {
    return new \CartBooking\Booking\BookingRepository($app['db'], new \CartBooking\Booking\BookingHydrator());
};

$app['repository.shift'] = function (Container $app) {
    return new \CartBooking\Shift\ShiftRepository($app['db'], new \CartBooking\Shift\ShiftHydrator());
};

$app['repository.location'] = function (Container $app) {
    return new \CartBooking\Location\LocationRepository($app['db'], new \CartBooking\Location\LocationHydrator());
};

$app['twig'] = function () use ($initParams) {
    $twig = new Twig_Environment(new Twig_Loader_Filesystem(__DIR__ . '/templates/'), [
        'cache' => __DIR__  . '/cache',
        'auto_reload' => true,
        'debug' => true,
    ]);
    $twig->addExtension(new Twig_Extension_Debug());
    return $twig;
};

$app['request'] = function () {
    return new \Symfony\Component\HttpFoundation\Request($_GET, $_REQUEST, [], $_COOKIE, $_FILES, $_SERVER);
};

$app['response'] = function () {
    return new \Symfony\Component\HttpFoundation\Response();
};

$app[\Silex\Provider\SessionServiceProvider::class] = function (Container $app) {
    $sessionServiceProvider = new Silex\Provider\SessionServiceProvider();
    $sessionServiceProvider->register($app);
    return $sessionServiceProvider;
};

$app[\CartBooking\Lib\Utilities\FileSystem::class] = function () use ($initParams) {
    return new \CartBooking\Lib\Utilities\FileSystem($initParams);
};

$app[\CartBooking\Application\Http\MapsController::class] = function (Container $app) use ($initParams) {
    return new \CartBooking\Application\Http\MapsController($app['request'], $app['response'], $app['repository.location'], $initParams, $app['twig']);
};

$app[\CartBooking\Application\Http\StatisticsController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\StatisticsController($app['request'], $app['response'], $app['repository.booking'], $app['twig']);
};
$app[\CartBooking\Application\Http\ExperiencesController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\ExperiencesController($app['request'], $app['response'], $app['twig'], $app['repository.booking'], $app['repository.pioneer']);
};

$app[\CartBooking\Application\Http\PlacementsController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\PlacementsController(
        $app['request'],
        $app['response'],
        $app['twig'],
        $app['repository.booking'],
        $app['repository.location'],
        $app['repository.shift']
    );
};

$app[\CartBooking\Application\Http\CommunicationController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\CommunicationController(
        $app[\CartBooking\Application\EmailService::class],
        $app['request'],
        $app['response'],
        $app['twig'],
        $app['repository.booking'],
        $app['repository.location'],
        $app['repository.pioneer'],
        $app['repository.shift']
    );
};

$app[\CartBooking\Application\Http\PublishersController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\PublishersController($app['request'], $app['repository.booking'], $app['repository.pioneer'], $app['twig']);
};

$app[\CartBooking\Application\Http\ReportsController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\ReportsController($app['request'], $app['response'], $app['twig'], $app['repository.pioneer'], $app[\CartBooking\Lib\Utilities\FileSystem::class]);
};

$app[\CartBooking\Application\Http\BookingController::class] = function (Container $app) {
    return new \CartBooking\Application\Http\BookingController(
        $app['request'],
        $app['repository.booking'],
        $app['repository.location'],
        $app['repository.pioneer'],
        $app['repository.shift'],
        $app['twig']
    );
};

\CartBooking\Application\ServiceLocator::setContainer($app);
