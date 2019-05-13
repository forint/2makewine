<?php

namespace EcommerceBundle\CustomerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class EcommerceCustomerValidationPass implements CompilerPassInterface
{
    /**
     * Remove original validation call, because we can't override form fields constraint
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('validator.builder')) {

            foreach ($container->getDefinition('validator.builder')->getMethodCalls() as $key => $method){
                if ($method['0'] == 'addXmlMappings'){
                    if (isset($method['1']) && is_array($method['1'])){
                        if (isset($method['1']['0']) && is_array($method['1']['0'])){

                            $projectDirectory = str_replace('app','',$container->getParameter('kernel.root_dir'));
                            $oldCustomerValidationConfiguration = $projectDirectory."vendor/sonata-project/ecommerce/src/CustomerBundle/Resources/config/validation.xml";

                            $removeKey = array_search($oldCustomerValidationConfiguration, $method['1']['0']);
                            unset($method['1']['0'][$removeKey]);

                            $keyCalls = $key;
                            $clearCustomerValidationConfiguration = $method['1']['0'];
                        }
                    }
                }
            }

            $container->getDefinition('validator.builder')->removeMethodCall('addXmlMappings');
            $container->getDefinition('validator.builder')->addMethodCall('addXmlMapping', array_values($clearCustomerValidationConfiguration) );

        }

        /** Change CustomerAddressBreadcrumbBlockService for custom realization because sonata-ecomerce not incompatible with sonata-use-bundle less than 4.0 */
        $container->setParameter('sonata.customer.block.breadcrumb_address.class', 'EcommerceBundle\CustomerBundle\Block\Breadcrumb\AppCustomerAddressBreadcrumbBlockService');


        /** Change original sonata customer & delivery admin service */
        $container->setParameter('sonata.customer.admin.address.class', 'AppBundle\Admin\AddressAdmin');
        $container->setParameter('sonata.product.admin.delivery.class', 'AppBundle\Admin\DeliveryAdmin');
        $container->setParameter('sonata.order.admin.order_element.class', 'AppBundle\Admin\OrderElementAdmin');


    }
}