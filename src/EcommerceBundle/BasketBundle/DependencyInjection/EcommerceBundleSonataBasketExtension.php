<?php

declare(strict_types=1);

namespace EcommerceBundle\BasketBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * BasketExtension.
 *
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class EcommerceBundleSonataBasketExtension extends Extension
     implements PrependExtensionInterface
{

    /**
     * @var array
     */
    private $bundleConfigs;

    /**
     * Loads the url shortener configuration.
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
        // dump(new FileLocator(__DIR__.'/../Resources/config'));die;
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('orm.xml');
        $loader->load('basket_entity.xml');
        $loader->load('basket_session.xml');
        $loader->load('basket.xml');
        $loader->load('validator.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
        }

        $container->setAlias('sonata.basket.builder', $config['builder']);
        $container->setAlias('sonata.basket.factory', $config['factory']);
        $container->setAlias('sonata.basket.loader', $config['loader']);

        $this->registerParameters($container, $config);
        $this->registerDoctrineMapping($config);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['basket'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['basket'], 'mapManyToOne', [
            'fieldName' => 'customer',
            'targetEntity' => $config['class']['customer'],
            'cascade' => [
                'all'
            ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'customer_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                    'unique' => true,
                ],
            ],
            'orphanRemoval' => false,
            'fetch' => 'EAGER'
        ]);

        $collector->addAssociation($config['class']['basket'], 'mapOneToMany', [
            'fieldName' => 'basketElements',
            'targetEntity' => $config['class']['basket_element'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'basket',
            'orphanRemoval' => true,
        ]);

        $collector->addAssociation($config['class']['basket_element'], 'mapManyToOne', [
            'fieldName' => 'basket',
            'targetEntity' => $config['class']['basket'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => 'basketElements',
            'joinColumns' => [
                [
                    'name' => 'basket_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);
    }

    /**
     * Loads the url shortener configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    /*public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $configs = [ $this->bundleConfigs['EcommerceBundleSonataBasketBundle'] ];

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerParameters($container);

        $loader->load('basket.xml');

    }*/

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $config
     */
    public function registerParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('sonata.basket.basket.class', $config['class']['basket']);
        $container->setParameter('sonata.basket.basket_element.class', $config['class']['basket_element']);

        $container->setParameter('sonata.customer.customer.class', $this->bundleConfigs['EcommerceBundleSonataCustomerBundle']['class']['customer']);
        $container->setParameter('sonata.customer.address.class', $this->bundleConfigs['EcommerceBundleSonataCustomerBundle']['class']['address']);
        $container->setParameter('sonata.customer.selector.class', $this->bundleConfigs['EcommerceBundleSonataCustomerBundle']['class']['customer_selector']);

        $container->setParameter('sonata.customer.admin.customer.entity', $this->bundleConfigs['EcommerceBundleSonataCustomerBundle']['class']['customer']);
        $container->setParameter('sonata.customer.admin.address.entity', $this->bundleConfigs['EcommerceBundleSonataCustomerBundle']['class']['address']);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['SonataBasketBundle'])){
            $this->bundleConfigs['EcommerceBundleSonataBasketBundle'] = current($container->getExtensionConfig('sonata_basket'));
        }
        if (isset($bundles['EcommerceBundleSonataCustomerBundle'])){
            $this->bundleConfigs['EcommerceBundleSonataCustomerBundle'] = current($container->getExtensionConfig('sonata_customer'));
        }

        /**  Remove parent extension for avoid load basket.xml with deprecated node syntax */
        if (isset($bundles['SonataBasketBundle'])){
            $propertyName = "extensions";
            $class = new \ReflectionClass(get_class($container));
            $property = $class->getProperty($propertyName);
            $property->setAccessible(true);
            $currentClassPropertyValue = $property->getValue($container);
            unset($currentClassPropertyValue['sonata_basket']);
            $property->setValue($container, $currentClassPropertyValue);
            /*$extensions = $container->getExtensions();
            dump($extensions);*/
        }
    }
}
