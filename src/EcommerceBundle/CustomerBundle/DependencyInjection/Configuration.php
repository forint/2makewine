<?php

namespace EcommerceBundle\CustomerBundle\DependencyInjection;

use Sonata\CustomerBundle\DependencyInjection\Configuration as BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration extends BaseConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = parent::getConfigTreeBuilder();

        //protected attribute access workaround
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
                        ->scalarNode('customer')->defaultValue('EcommerceBundle\\CustomerBundle\\Entity\\Customer')->end()
                        ->scalarNode('customer_selector')->defaultValue('AppBundle\\Provider\\CustomerSelector')->end()
                        ->scalarNode('address')->defaultValue('EcommerceBundle\\CustomerBundle\\Entity\\Address')->end()
                        ->scalarNode('order')->defaultValue('EcommerceBundle\\OrderBundle\\Entity\\Order')->end()
                        ->scalarNode('user')->defaultValue('AppBundle\\Entity\\User')->end()
                        ->scalarNode('user_identifier')->defaultValue('id')->end()
                    ->end()
                ->end()
                ->arrayNode('field')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('user')->defaultValue('id')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

    }
}
