<?php

namespace EcommerceBundle\DeliveryBundle;

use EcommerceBundle\DeliveryBundle\DependencyInjection\Compiler\EcommerceDeliveryParameterPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This file has been generated by the SonataEasyExtendsBundle.
 *
 * @link https://sonata-project.org/easy-extends
 *
 * References:
 * @link http://symfony.com/doc/current/book/bundles.html
 */
class EcommerceBundleSonataDeliveryBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataDeliveryBundle';
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EcommerceDeliveryParameterPass());
    }
}