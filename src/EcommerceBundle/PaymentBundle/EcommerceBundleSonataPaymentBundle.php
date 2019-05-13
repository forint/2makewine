<?php

namespace EcommerceBundle\PaymentBundle;

use EcommerceBundle\PaymentBundle\DependencyInjection\EcommerceBundleSonataPaymentExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EcommerceBundleSonataPaymentBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataPaymentBundle';
    }


    public function getContainerExtension()
    {
        return new EcommerceBundleSonataPaymentExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }
}