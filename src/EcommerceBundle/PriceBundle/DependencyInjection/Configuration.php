<?php

declare(strict_types=1);

namespace EcommerceBundle\PriceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Intl\Intl;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_price');

        $this->addPriceSection($node);
        $this->addPrecisionSection($node);

        return $treeBuilder;
    }

    /**
     * Sets the price precision section
     * Precision parameter will be given to bcscale() used in bundle boot() method.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPrecisionSection(ArrayNodeDefinition $node): void
    {
        $node->children()->scalarNode('precision')->defaultValue(3)->end();
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addPriceSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
            ->scalarNode('currency')
            ->isRequired()
            ->validate()
            ->ifNotInArray(array_keys(Intl::getCurrencyBundle()->getCurrencyNames()))
            ->thenInvalid("Invalid currency '%s'")
            ->end()
            ->end()
            ->end()
        ;
    }
}

