<?php

namespace EcommerceBundle\OrderBundle;

use EcommerceBundle\OrderBundle\DependencyInjection\Compiler\EcommerceOrderParametersPass;
use EcommerceBundle\OrderBundle\DependencyInjection\EcommerceBundleSonataOrderExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EcommerceBundleSonataOrderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataOrderBundle';
    }

    public function getContainerExtension()
    {
        return new EcommerceBundleSonataOrderExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EcommerceOrderParametersPass());
    }
}