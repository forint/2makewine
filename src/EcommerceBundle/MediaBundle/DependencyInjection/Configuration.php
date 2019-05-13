<?php

namespace EcommerceBundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Sonata\MediaBundle\DependencyInjection\Configuration as BaseConfiguration;

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
     * @param ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('media')->defaultValue('EcommerceBundle\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('gallery')->defaultValue('EcommerceBundle\\MediaBundle\\Entity\\Gallery')->end()
                        ->scalarNode('gallery_has_media')->defaultValue('EcommerceBundle\\MediaBundle\\Entity\\GalleryHasMedia')->end()
                        ->scalarNode('category')->defaultValue('EcommerceBundle\\ClassificationBundle\\Entity\\Category')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

}
