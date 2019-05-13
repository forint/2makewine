<?php

namespace EcommerceBundle\CustomerBundle;

use EcommerceBundle\CustomerBundle\DependencyInjection\EcommerceBundleSonataCustomerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use EcommerceBundle\CustomerBundle\DependencyInjection\Compiler\EcommerceCustomerValidationPass;


/**
 * This file has been generated by the SonataEasyExtendsBundle.
 *
 * @link https://sonata-project.org/easy-extends
 *
 * References:
 * @link http://symfony.com/doc/current/book/bundles.html
 */
class EcommerceBundleSonataCustomerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EcommerceCustomerValidationPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataCustomerBundle';
    }

    public function getContainerExtension()
    {
        return new EcommerceBundleSonataCustomerExtension();
    }
}