<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use FOS\UserBundle\DependencyInjection\Configuration as BaseConfiguration;

class Configuration extends BaseConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
       /* $treeBuilder = parent::getConfigTreeBuilder();

        //protected attribute access workaround
        $reflectedClass = new \ReflectionObject($treeBuilder);

        $property = $reflectedClass->getProperty("root");
        dump(get_class_methods($property));
        dump($treeBuilder);
        die;
        $property->setAccessible(true);

        $node = $property->getValue($treeBuilder);

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_user');
        $rootNode->addDefaultChildrenIfNoneSet($node->children());

        return $treeBuilder;*/
    }

}
