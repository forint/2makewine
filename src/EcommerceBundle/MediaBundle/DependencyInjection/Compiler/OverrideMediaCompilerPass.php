<?php

namespace EcommerceBundle\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideMediaCompilerPass implements CompilerPassInterface
{

    /**
     * Overwrite project specific services
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /*dump(get_class_methods($container));die;
        $defNewService = $container->getDefinition('service.id.you.want.to.override');
        $defNewService ->setClass('AppBundle\Service\NewService');*/

    }
}