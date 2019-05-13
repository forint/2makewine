<?php

declare(strict_types=1);

namespace EcommerceBundle\PriceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class EcommerceBundleSonataPriceExtension extends Extension
    implements PrependExtensionInterface
{
    /**
     * Loads the price configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = [ $this->bundleConfigs['EcommerceBundleSonataPriceBundle'] ];
        $containerParent = $configs['0'];
        $extensionConfig = $containerParent->getExtensionConfig('sonata_price');
        $configs = $extensionConfig['0'];

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, [$configs]);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('price.xml');

        $this->registerParameters($container, $config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function registerParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('sonata.price.currency', $config['currency']);
        $container->setParameter('sonata.price.precision', $config['precision']);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['SonataPriceBundle'])){
            $this->bundleConfigs['EcommerceBundleSonataPriceBundle'] = $container;

            /**  Remove parent extension for avoid load basket.xml with deprecated node syntax */
            if (isset($bundles['SonataPriceBundle'])){
                $propertyName = "extensions";
                $class = new \ReflectionClass(get_class($container));
                $property = $class->getProperty($propertyName);
                $property->setAccessible(true);
                $currentClassPropertyValue = $property->getValue($container);
                unset($currentClassPropertyValue['sonata_price']);
                $property->setValue($container, $currentClassPropertyValue);
            }
        }
    }
}
