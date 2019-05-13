<?php

declare(strict_types=1);

namespace EcommerceBundle\PaymentBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class EcommerceBundleSonataPaymentExtension extends Extension
    implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $bundleConfigs;

    /**
     * Loads the payment configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = [ $this->bundleConfigs['EcommerceBundleSonataPaymentBundle'] ];
        $containerParent = $configs['0'];
        $extensionConfig = $containerParent->getExtensionConfig('sonata_payment');
        $configs = $extensionConfig['0'];

        $processor = new Processor();
        $configuration = new Configuration();

        $configs['services']['stripe'] = [
            'name' => 'Stripe',
            'code' => 'stripe',
            'transformers' => [ 'basket' => 'sonata.payment.transformer.basket', 'order' => 'sonata.payment.transformer.order' ],
            'options' => [],
            'browser' => 'sonata.payment.browser.curl'
        ];

        $config = $processor->processConfiguration($configuration, [ $configs ]);

        /*$processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);*/

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('consumer.xml');
        $loader->load('orm.xml');
        $loader->load('payment.xml');
        $loader->load('generator.xml');
        $loader->load('transformer.xml');
        $loader->load('selector.xml');
        $loader->load('browser.xml');
        $loader->load('form.xml');

        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
        $this->configurePayment($container, $config);
        $this->configureSelector($container, $config['selector']);
        $this->configureTransformer($container, $config['transformers']);

        $container->setAlias('sonata.generator', $config['generator']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $config
     */
    public function registerParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('sonata.payment.transaction.class', $config['class']['transaction']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     *
     * @throws \RuntimeException
     */
    public function configurePayment(ContainerBuilder $container, array $config): void
    {
        // create the payment method pool
        $pool = $container->getDefinition('sonata.payment.pool');

        $internal = [
            'debug' => 'sonata.payment.method.debug',
            'pass' => 'sonata.payment.method.pass',
            'check' => 'sonata.payment.method.check',
            'scellius' => 'sonata.payment.method.scellius',
            'ogone' => 'sonata.payment.method.ogone',
            'paypal' => 'sonata.payment.method.paypal',
            'stripe' => 'sonata.payment.method.stripe',
        ];

        $configured = [];

        // define the payment method
        foreach ($config['services'] as $id => $settings) {
            if (array_key_exists($id, $internal)) {
                $id = $internal[$id];

                $name = $settings['name'] ?? 'n/a';
                $options = $settings['options'] ?? [];

                $code = $settings['code'] ?? false;

                if (!$code) {
                    throw new \RuntimeException('Please provide a code for the payment handler');
                }

                $definition = $container->getDefinition($id);

                $definition->addMethodCall('setName', [$name]);
                $definition->addMethodCall('setOptions', [$options]);
                $definition->addMethodCall('setCode', [$code]);

                foreach ((array) $settings['transformers'] as $name => $serviceId) {
                    $definition->addMethodCall('addTransformer', [$name, new Reference($serviceId)]);
                }

                $configured[$code] = $id;
            }
        }

        foreach ($config['methods'] as $code => $id) {
            if (array_key_exists($code, $configured)) {
                // Internal service
                $id = $configured[$code];
            }

            if ($container->hasDefinition($id)) {
                $definition = $container->getDefinition($id);
                $definition->addMethodCall('setEnabled', [true]);
            }

            $pool->addMethodCall('addMethod', [new Reference($id)]);
        }

        if (isset($config['services']['debug'])) {
            $container->getDefinition('sonata.payment.method.debug')
                ->replaceArgument(1, new Reference($config['services']['debug']['browser']));
        }

        if (isset($config['services']['pass'])) {
            $container->getDefinition('sonata.payment.method.pass')
                ->replaceArgument(1, new Reference($config['services']['pass']['browser']));
        }

        if (isset($config['services']['check'])) {
            $container->getDefinition('sonata.payment.method.check')
                ->replaceArgument(2, new Reference($config['services']['check']['browser']));
        }

        if (isset($config['services']['scellius'])) {
            $container->getDefinition('sonata.payment.method.scellius')
                ->replaceArgument(3, new Reference($config['services']['scellius']['generator']));
        }

        // Remove unconfigured services
        foreach ($internal as $code => $id) {
            if (false === array_search($id, $configured)) {
                $container->removeDefinition($id);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $selector
     */
    public function configureSelector(ContainerBuilder $container, $selector): void
    {
        $container->setAlias('sonata.payment.selector', $selector);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $transformers
     */
    public function configureTransformer(ContainerBuilder $container, array $transformers): void
    {
        $pool = $container->getDefinition('sonata.payment.transformer.pool');

        foreach ($transformers as $type => $id) {
            $pool->addMethodCall('addTransformer', [$type, new Reference($id)]);
        }
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['transaction'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['transaction'], 'mapManyToOne', [
            'fieldName' => 'order',
            'targetEntity' => $config['class']['order'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'order_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addIndex($config['class']['transaction'], 'status_code', [
            'status_code',
        ]);

        $collector->addIndex($config['class']['transaction'], 'state', [
            'state',
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

        if (isset($bundles['SonataPaymentBundle'])){
            $this->bundleConfigs['EcommerceBundleSonataPaymentBundle'] = $container;

            /**  Remove parent extension for avoid load basket.xml with deprecated node syntax */
            if (isset($bundles['SonataBasketBundle'])){
                $propertyName = "extensions";
                $class = new \ReflectionClass(get_class($container));
                $property = $class->getProperty($propertyName);
                $property->setAccessible(true);
                $currentClassPropertyValue = $property->getValue($container);
                unset($currentClassPropertyValue['sonata_payment']);
                $property->setValue($container, $currentClassPropertyValue);

                /*$extensions = $container->getExtensions();
                dump($extensions);*/
            }
        }
    }
}
