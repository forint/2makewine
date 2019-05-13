<?php

declare(strict_types=1);

namespace  EcommerceBundle\BasketBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EcommerceBundleGlobalVariableCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('twig')
            ->addMethodCall('addGlobal', ['sonata_basket', new Reference('sonata.basket.twig.global')]);

    }
}
