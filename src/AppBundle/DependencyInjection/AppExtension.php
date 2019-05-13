<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AppExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /*$processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('doctrine.xml');

        dump($config);die;
        $listenerDefinition = $container->getDefinition('fos_user.user_listener');
        $listenerDefinition->addTag(self::$doctrineDrivers[$config['db_driver']]['tag']);
        if (isset(self::$doctrineDrivers[$config['db_driver']]['listener_class'])) {
            $listenerDefinition->setClass(self::$doctrineDrivers[$config['db_driver']]['listener_class']);
        }*/


    }
}
