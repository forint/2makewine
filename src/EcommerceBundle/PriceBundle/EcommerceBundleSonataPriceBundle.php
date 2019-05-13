<?php

namespace EcommerceBundle\PriceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use EcommerceBundle\PriceBundle\DependencyInjection\EcommerceBundleSonataPriceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * This file has been generated by the SonataEasyExtendsBundle.
 *
 * @link https://sonata-project.org/easy-extends
 *
 * References:
 * @link http://symfony.com/doc/current/book/bundles.html
 */
class EcommerceBundleSonataPriceBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataPriceBundle';
    }

    public function getContainerExtension()
    {
        return new EcommerceBundleSonataPriceExtension();
    }
}