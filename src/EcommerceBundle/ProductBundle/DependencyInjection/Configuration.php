<?php

declare(strict_types=1);


namespace EcommerceBundle\ProductBundle\DependencyInjection;

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
        $node = $treeBuilder->root('sonata_product');

        $this->addProductSection($node);
        $this->addModelSection($node);
        $this->addSeoSection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addProductSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->arrayNode('products')
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->children()
            ->scalarNode('provider')->isRequired()->end()
            ->scalarNode('manager')->isRequired()->end()
            ->arrayNode('variations')
            ->children()
            ->arrayNode('fields')
            ->isRequired()
            ->prototype('scalar')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
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
            ->scalarNode('product')->defaultValue('AppBundle\\Entity\\WineProduct')->end()
            ->scalarNode('package')->defaultValue('EcommerceBundle\\ProductBundle\\Entity\\Package')->end()
            ->scalarNode('product_category')->defaultValue('EcommerceBundle\\ProductBundle\\Entity\\ProductCategory')->end()
            ->scalarNode('product_collection')->defaultValue('EcommerceBundle\\ProductBundle\\Entity\\ProductCollection')->end()
            ->scalarNode('category')->defaultValue('EcommerceBundle\\ClassificationBundle\\Entity\\Category')->end()
            ->scalarNode('collection')->defaultValue('EcommerceBundle\\ClassificationBundle\\Entity\\Collection')->end()
            ->scalarNode('delivery')->defaultValue('EcommerceBundle\\ProductBundle\\Entity\\Delivery')->end()
            ->scalarNode('media')->defaultValue('EcommerceBundle\\MediaBundle\\Entity\\Media')->end()
            ->scalarNode('gallery')->defaultValue('EcommerceBundle\\MediaBundle\\Entity\\Gallery')->end()
            ->end()
            ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addSeoSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->arrayNode('seo')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('product')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('site')->defaultValue('@sonataproject')->end()
            ->scalarNode('creator')->defaultValue('@th0masr')->end()
            ->scalarNode('domain')->defaultValue('http://demo.sonata-project.org')->end()
            ->scalarNode('media_prefix')->defaultValue('http://demo.sonata-project.org')->end()
            ->scalarNode('media_format')->defaultValue('reference')->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();
    }
}
