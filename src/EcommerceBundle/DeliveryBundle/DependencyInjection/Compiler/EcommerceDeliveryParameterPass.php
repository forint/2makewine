<?php

namespace EcommerceBundle\DeliveryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EcommerceDeliveryParameterPass implements CompilerPassInterface
{
    /**
     * Override delivery selector class parameter for change behavior, but not change service name
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('sonata.delivery.selector.class', 'AppBundle\Provider\DeliverySelector');
    }
}
