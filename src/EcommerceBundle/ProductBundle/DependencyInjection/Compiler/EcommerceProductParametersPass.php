<?php

namespace EcommerceBundle\ProductBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EcommerceProductParametersPass implements CompilerPassInterface
{
    /**
     * Override delivery selector class parameter for change behavior, but not change service name
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('sonata.product.admin.product.class', 'AppBundle\Admin\WineProductAdmin');

        /**
         * Remove dependency
         */
        $container->removeDefinition('sonata.product.admin.product.variation');
        $definition = $container->getDefinition('sonata.product.admin.product');

        $definition->removeMethodCall('addChild');
        $definition->removeMethodCall('addChild');
        $definition->removeMethodCall('addChild');
        $definition->removeMethodCall('addChild');

        /**
         * Modify Sonata Admin Product Label $_tag
         */
        $_tag = $definition->getTag('sonata.admin');
        $definition->clearTag('sonata.admin');
        $_tag['0']['label'] = 'Product';
        $definition->addTag('sonata.admin', $_tag['0']);

    }
}
