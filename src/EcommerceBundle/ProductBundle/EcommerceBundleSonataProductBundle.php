<?php

namespace EcommerceBundle\ProductBundle;

use EcommerceBundle\ProductBundle\DependencyInjection\Compiler\EcommerceProductParametersPass;
use EcommerceBundle\ProductBundle\DependencyInjection\EcommerceBundleSonataProductExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sonata\ProductBundle\DependencyInjection\Compiler\AddProductProviderCompilerPass;


class EcommerceBundleSonataProductBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataProductBundle';
    }

    public function getContainerExtension()
    {
        return new EcommerceBundleSonataProductExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EcommerceProductParametersPass());
        // $container->addCompilerPass(new AddProductProviderCompilerPass());
    }
}
