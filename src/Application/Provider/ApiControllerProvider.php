<?php

namespace CartBooking\Application\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use CartBooking\Application\Api\BookingController;
use CartBooking\Application\Api\PublisherController;
use Pimple\Container;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class ApiControllerProvider extends InjectorServiceProvider implements ControllerProviderInterface
{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $this->get(ControllerCollection::class);
        $controllers->match('/publishers', function () use ($app) {
            $controller = $this->injector->create(PublisherController::class);
            $action = $this->get(Request::class)->getMethod() . 'Action';
            return $this->injector->invoke($controller, $action);
        });
        $controllers->match('/bookings', function () use ($app) {
            $controller = $this->injector->create(BookingController::class);
            $action = $this->get(Request::class)->getMethod() . 'Action';
            return $this->injector->invoke($controller, $action);
        });
        return $controllers;
    }

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
    }
}
