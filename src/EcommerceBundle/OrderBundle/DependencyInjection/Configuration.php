<?php

namespace EcommerceBundle\OrderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_order');

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
            ->scalarNode('order')->defaultValue('EcommerceBundle\\OrderBundle\\Entity\\Order')->end()
            ->scalarNode('order_element')->defaultValue('EcommerceBundle\\OrderBundle\\Entity\\OrderElement')->end()
            ->scalarNode('customer')->defaultValue('EcommerceBundle\\CustomerBundle\\Entity\\Customer')->end()
            ->end()
            ->end()
            ->end()
        ;
    }

}
