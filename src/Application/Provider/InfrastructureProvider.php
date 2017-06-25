<?php

namespace CartBooking\Application\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\EmailService;
use CartBooking\Infrastructure\Persistence\Doctrine\Type\BookingIdType;
use CartBooking\Infrastructure\Persistence\Doctrine\Type\DateTimeImmutableType;
use CartBooking\Infrastructure\Persistence\Doctrine\Type\EmailType;
use CartBooking\Infrastructure\Persistence\Doctrine\Type\MarkersType;
use CartBooking\Model\Booking\BookingId;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Twig_Environment;

class InfrastructureProvider extends InjectorServiceProvider
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app
     * @return void
     */
    public function register(Container $app)
    {
        $initParams = $app['initParams'];

        $app->register(new LocaleServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new TranslationServiceProvider(), [
            'locale_fallbacks' => ['en'],
            'translator.domains' => [],
        ]);
        $this->bind('mailer', Swift_Mailer::class);
        $app['mailer'] = function () use ($initParams) {
            $transport = Swift_SmtpTransport::newInstance($initParams['smtp']['host'], $initParams['smtp']['port']);
            return Swift_Mailer::newInstance($transport);
        };

        $this->bind(EmailService::class, function (Container $app) {
            return new EmailService($app['mailer'], $app['communication']);
        });


        $this->alias('communication', Swift_Message::class);
        $app['communication'] = function () use ($initParams) {
            $email = Swift_Message::newInstance();
            $email->setFrom([$initParams['communication']['from_email'] => $initParams['communication']['from_name']]);
            return $email;
        };

        $this->alias('logger', Logger::class);
        $app['logger'] = function () use ($initParams) {
            $log = new Logger('name');
            $log->pushHandler(new StreamHandler($initParams['logger']['stream'], Logger::WARNING));
            return $log;
        };



        $this->alias(Twig_Environment::class, 'twig');
        $this->alias( Request::class, 'request');
        $app['request'] = function () {
            return new \Symfony\Component\HttpFoundation\Request($_GET, $_REQUEST, [], $_COOKIE, $_FILES, $_SERVER);
        };

        $this->alias(Response::class, 'response');
        $app['response'] = function () {
            return new Response();
        };
        $this->alias(ControllerCollection::class, 'controllers_factory');
        $app->register(new SecurityServiceProvider(), [
            'security.firewalls' => [
                'login_path' => [
                    'pattern' => '^/login$',
                    'anonymous' => true
                ],
                'default' => [
                    'pattern' => '^/.*$',
                    'anonymous' => true,
                    'form' => [
                        'login_path' => '/login',
                        'check_path' => '/login_check',
                    ],
                    'logout' => [
                        'logout_path' => '/logout',
                        'invalidate_session' => false
                    ],
                    'users' => function($app) {
                        return new UserProvider($app['db']);
                    },
                ]
            ],
            'security.access_rules' => [
                ['^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
                ['^/.+$', 'ROLE_USER']
            ]
        ]);
        $app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'host' => $initParams['db']['host'],
                'dbname' => $initParams['db']['name'],
                'user' => $initParams['db']['username'],
                'password' => $initParams['db']['password'],
                'charset' => 'utf8mb4',
                'logging' => true,
                'profiling' => true,
            ],
        ]);

        $app->register(new SessionServiceProvider());
        $app['session.storage.options'] = [
            'cookie_lifetime' => (int) $initParams['session']['lifetime'],
        ];
        $this->alias(Session::class, 'session');
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => APP_ROOT . '/templates',
            'twig.options' => [
                'cache' => APP_ROOT . '/cache',
                'auto_reload' => true,
                'debug' => true,
            ],
            'twig.date.format' => 'Y-m-d',
            'twig.form.templates' => [
                'bootstrap_3_layout.html.twig',
                'bootstrap_3_horizontal_layout.html.twig'
            ]
        ));
        $this->bind(EntityManager::class, function (Application $app) use ($initParams) {
            $dbParams = [
                'driver' => 'pdo_mysql',
                'user' => $initParams['db']['username'],
                'host' => $initParams['db']['host'],
                'password' => $initParams['db']['password'],
                'dbname' => $initParams['db']['name'],
            ];
            Type::addType(MarkersType::MARKERS, MarkersType::class);
            Type::addType(DateTimeImmutableType::DATE_TIME_IMMUTABLE, DateTimeImmutableType::class);
            Type::addType(BookingIdType::BOOKING_ID, BookingIdType::class);
            Type::addType(EmailType::TYPE, EmailType::class);

            $config = Setup::createXMLMetadataConfiguration([APP_ROOT . '/config/doctrine'], $app['debug']);
            return EntityManager::create($dbParams, $config);
        });
        $this->alias(TokenStorage::class, 'security.token_storage');
    }
}
