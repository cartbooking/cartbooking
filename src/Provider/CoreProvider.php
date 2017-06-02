<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\Adapter\ArrayContainerAdapter;
use Bigcommerce\Injector\Cache\ArrayServiceCache;
use Bigcommerce\Injector\Injector;
use Bigcommerce\Injector\InjectorInterface;
use Bigcommerce\Injector\Reflection\ParameterInspector;
use Bigcommerce\Injector\ServiceProvider\BindingClosureFactory;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CoreProvider
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app A container instance
     */
    public function register(Application $app)
    {
        $injector = new Injector(
            new ArrayContainerAdapter($app),
            new ParameterInspector(new ArrayServiceCache())
        );
        $app[Injector::class] = $injector;
        $app[InjectorInterface::class] = $injector;

        $serviceProvider = new ServiceProvider(
            $app[Injector::class],
            $app,
            new BindingClosureFactory(new LazyLoadingValueHolderFactory(), $app[Injector::class])
        );
        $repositoryProvider = new RepositoryProvider(
            $app[Injector::class],
            $app,
            new BindingClosureFactory(new LazyLoadingValueHolderFactory(), $app[Injector::class])
        );

        $infrastructureProvider = new InfrastructureProvider(
            $app[Injector::class],
            $app,
            new BindingClosureFactory(new LazyLoadingValueHolderFactory(), $app[Injector::class])
        );

        $serviceProvider->register($app);
        $repositoryProvider->register($app);
        $infrastructureProvider->register($app);
    }

    public function mount(Application $app)
    {
        $controllerProvider = new ControllerProvider(
            $app[Injector::class],
            $app,
            new BindingClosureFactory(new LazyLoadingValueHolderFactory(), $app[Injector::class])
        );
        $controllerProvider->register($app);
        $app->mount('/', $controllerProvider);
        $app->get('/login', function(Request $request) use ($app) {
            return $app['twig']->render('login.html', array(
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));
        });
    }

}
