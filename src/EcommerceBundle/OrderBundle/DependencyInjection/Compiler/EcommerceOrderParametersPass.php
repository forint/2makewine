<?php

namespace EcommerceBundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EcommerceOrderParametersPass implements CompilerPassInterface
{
    /**
     * Override delivery selector class parameter for change behavior, but not change service name
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('sonata.order.order.manager.class', 'AppBundle\Provider\AppOrderManager');

        /** Remove dependency for variation product functionality */
        $container->removeDefinition('sonata.product.admin.product.variation');
        $definition = $container->getDefinition('sonata.product.admin.product');
        $definition->removeMethodCall('addChild');
        $definition->removeMethodCall('addChild');
        $definition->removeMethodCall('addChild');
        $definition->removeMethodCall('addChild');

        /** Change 4th argument to used service $recentOrdersDefinition */
        $recentOrdersDefinition = $container->getDefinition('sonata.order.block.recent_orders');
        $recentOrdersDefinition->replaceArgument(4, new Reference("security.authorization_checker"));

    }
}
