<?php

declare(strict_types=1);



namespace EcommerceBundle\BasketBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Sonata\BasketBundle\DependencyInjection\Configuration as BaseConfiguration;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration extends BaseConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = parent::getConfigTreeBuilder();

        $reflectedClass = new \ReflectionObject($treeBuilder);
        $property = $reflectedClass->getProperty("root");
        $property->setAccessible(true);

        $node = $property->getValue($treeBuilder);

        $this->addModelSection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('basket')->defaultValue('EcommerceBundle\\BasketBundle\\Entity\\Basket')->end()
                        ->scalarNode('basket_element')->defaultValue('EcommerceBundle\\BasketBundle\\Entity\\BasketElement')->end()
                        ->scalarNode('customer')->defaultValue('EcommerceBundle\\CustomerBundle\\Entity\\Customer')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

}
