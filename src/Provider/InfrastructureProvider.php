<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\EmailService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Silex\ControllerCollection;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Extension_Core;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

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



        $this->alias('twig', Twig_Environment::class);
        $app['twig'] = function () {
            $twig = new Twig_Environment(new Twig_Loader_Filesystem(APP_ROOT . '/templates/'), [
                'cache' => APP_ROOT  . '/cache',
                'auto_reload' => true,
                'debug' => true,
            ]);
            $twig->getExtension(Twig_Extension_Core::class)->setDateFormat('Y-m-d', '%d days');
            $twig->addExtension(new Twig_Extension_Debug());
            return $twig;
        };

        $this->alias( Request::class, 'request');
        $app['request'] = function () {
            return new \Symfony\Component\HttpFoundation\Request($_GET, $_REQUEST, [], $_COOKIE, $_FILES, $_SERVER);
        };

        $this->alias(Response::class, 'response');
        $app['response'] = function () {
            return new \Symfony\Component\HttpFoundation\Response();
        };
        $this->alias(ControllerCollection::class, 'controllers_factory');
    }
}
