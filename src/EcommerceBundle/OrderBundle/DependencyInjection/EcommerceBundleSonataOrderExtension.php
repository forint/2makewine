<?php

namespace EcommerceBundle\OrderBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class EcommerceBundleSonataOrderExtension extends Extension
    implements PrependExtensionInterface
{
    /**
     * Loads the order configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('block.xml');
        $loader->load('orm.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('serializer.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        if (isset($bundles['SonataSeoBundle'])) {
            $loader->load('seo_block.xml');
        }

        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function registerParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('sonata.order.order.class', $config['class']['order']);
        $container->setParameter('sonata.order.order_element.class', $config['class']['order_element']);

        $container->setParameter('sonata.order.admin.order.entity', $config['class']['order']);
        $container->setParameter('sonata.order.admin.order_element.entity', $config['class']['order_element']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['order'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['order'], 'mapOneToMany', [
            'fieldName' => 'orderElements',
            'targetEntity' => $config['class']['order_element'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'order',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['order'], 'mapManyToOne', [
            'fieldName' => 'customer',
            'targetEntity' => $config['class']['customer'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => 'orders',
            'joinColumns' => [
                [
                    'name' => 'customer_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['order_element'], 'mapManyToOne', [
            'fieldName' => 'order',
            'targetEntity' => $config['class']['order'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => 'orderElements',
            'joinColumns' => [
                [
                    'name' => 'order_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addIndex($config['class']['order_element'], 'product_type', [
            'product_type',
        ]);

        $collector->addIndex($config['class']['order_element'], 'order_element_status', [
            'status',
        ]);

        $collector->addIndex($config['class']['order'], 'order_status', [
            'status',
        ]);

        $collector->addIndex($config['class']['order'], 'payment_status', [
            'payment_status',
        ]);
    }
    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['SonataOrderBundle'])){
            $this->bundleConfigs['EcommerceBundleSonataOrderBundle'] = $container;

            /**  Remove parent extension for avoid load basket.xml with deprecated node syntax */
            if (isset($bundles['SonataOrderBundle'])){
                $propertyName = "extensions";
                $class = new \ReflectionClass(get_class($container));
                $property = $class->getProperty($propertyName);
                $property->setAccessible(true);
                $currentClassPropertyValue = $property->getValue($container);
                unset($currentClassPropertyValue['sonata_order']);
                $property->setValue($container, $currentClassPropertyValue);
            }
        }
    }
}
