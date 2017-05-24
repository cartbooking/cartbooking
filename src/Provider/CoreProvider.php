<?php

namespace CartBooking\Provider;

use Bigcommerce\Injector\Adapter\ArrayContainerAdapter;
use Bigcommerce\Injector\Cache\ArrayServiceCache;
use Bigcommerce\Injector\Injector;
use Bigcommerce\Injector\InjectorInterface;
use Bigcommerce\Injector\Reflection\ParameterInspector;
use Bigcommerce\Injector\ServiceProvider\BindingClosureFactory;
use Pimple\Container;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class CoreProvider
{

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
        $controllerProvider = new ControllerProvider(
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
        $controllerProvider->register($app);
        $infrastructureProvider->register($app);
    }

}
