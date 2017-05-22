<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\Adapter\ArrayContainerAdapter;
use Bigcommerce\Injector\Cache\ArrayServiceCache;
use Bigcommerce\Injector\Injector;
use Bigcommerce\Injector\InjectorInterface;
use Bigcommerce\Injector\Reflection\ParameterInspector;
use CartBooking\Application\EmailService;
use CartBooking\Application\Http\BookingController;
use CartBooking\Application\Http\CommunicationController;
use CartBooking\Application\Http\ExperiencesController;
use CartBooking\Application\Http\MapsController;
use CartBooking\Application\Http\PlacementsController;
use CartBooking\Application\Http\PublishersController;
use CartBooking\Application\Http\ReportsController;
use CartBooking\Application\Http\StatisticsController;
use CartBooking\Booking\BookingRepository;
use CartBooking\Lib\Utilities\FileSystem;
use CartBooking\Location\LocationRepository;
use CartBooking\Publisher\PioneerRepository;
use CartBooking\Shift\ShiftRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\SessionServiceProvider;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

class CoreProvider implements ServiceProviderInterface
{

    /** @var array */
    private $initParams;

    public function __construct(array $initParams = [])
    {
        $this->initParams = $initParams;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $initParams = $this->initParams;

        $injector = new Injector(
            new ArrayContainerAdapter($app),
            new ParameterInspector(new ArrayServiceCache())
        );
        $app[Injector::class] = $injector;
        $app[InjectorInterface::class] = $injector;



        $app['db'] = function () use ($initParams) {
            return new \CartBooking\Lib\Db\Db(
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

        $app[EmailService::class] = function (Container $app) {
            return new EmailService($app['mailer'], $app['communication']);
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
            return new PioneerRepository($app['db'], new \CartBooking\Publisher\PioneerHydrator());
        };

        $app['repository.booking'] = function (Container $app) {
            return new BookingRepository($app['db'], new \CartBooking\Booking\BookingHydrator());
        };

        $app['repository.shift'] = function (Container $app) {
            return new ShiftRepository($app['db'], new \CartBooking\Shift\ShiftHydrator());
        };

        $app['repository.location'] = function (Container $app) {
            return new LocationRepository($app['db'], new \CartBooking\Location\LocationHydrator());
        };

        $app['twig'] = function () use ($initParams) {
            $twig = new Twig_Environment(new Twig_Loader_Filesystem(APP_ROOT . '/templates/'), [
                'cache' => APP_ROOT  . '/cache',
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

        $app[SessionServiceProvider::class] = function (Container $app) {
            $sessionServiceProvider = new SessionServiceProvider();
            $sessionServiceProvider->register($app);
            return $sessionServiceProvider;
        };

        $app[FileSystem::class] = function () use ($initParams) {
            return new FileSystem($initParams);
        };

        $app[MapsController::class] = function (Container $app) use ($initParams) {
            return new MapsController($app['request'], $app['response'], $app['repository.location'], $initParams, $app['twig']);
        };

        $app[StatisticsController::class] = function (Container $app) {
            return new StatisticsController($app['request'], $app['response'], $app['repository.booking'], $app['twig']);
        };
        $app[ExperiencesController::class] = function (Container $app) {
            return new ExperiencesController($app['request'], $app['response'], $app['twig'], $app['repository.booking'], $app['repository.pioneer']);
        };

        $app[PlacementsController::class] = function (Container $app) {
            return new PlacementsController(
                $app['request'],
                $app['response'],
                $app['twig'],
                $app['repository.booking'],
                $app['repository.location'],
                $app['repository.shift']
            );
        };

        $app[CommunicationController::class] = function (Container $app) {
            return new CommunicationController(
                $app[EmailService::class],
                $app['request'],
                $app['response'],
                $app['twig'],
                $app['repository.booking'],
                $app['repository.location'],
                $app['repository.pioneer'],
                $app['repository.shift']
            );
        };

        $app[PublishersController::class] = function (Container $app) {
            return new PublishersController($app['request'], $app['repository.booking'], $app['repository.pioneer'], $app['twig']);
        };

        $app[ReportsController::class] = function (Container $app) {
            return new ReportsController($app['request'], $app['response'], $app['twig'], $app['repository.pioneer'], $app[FileSystem::class]);
        };

        $app[BookingController::class] = function (Container $app) {
            return new BookingController(
                $app['request'],
                $app['repository.booking'],
                $app['repository.location'],
                $app['repository.pioneer'],
                $app['repository.shift'],
                $app['twig']
            );
        };
    }

}
