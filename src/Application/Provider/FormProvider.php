<?php

namespace CartBooking\Application\Provider;

use Bigcommerce\Injector\InjectorServiceProvider;
use Pimple\Container;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Form\FormFactory;

class FormProvider extends InjectorServiceProvider
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
        $app->register(New FormServiceProvider());
        $this->alias(FormFactory::class, 'form.factory');
    }
}
