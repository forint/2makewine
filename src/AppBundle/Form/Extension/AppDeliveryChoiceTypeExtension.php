<?php

namespace AppBundle\Form\Extension;

use Sonata\Component\Delivery\Pool;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AppDeliveryChoiceTypeExtension extends AbstractTypeExtension
{

    protected $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        // use FormType::class to modify (nearly) every field in the system
        return FileType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = [];

        foreach ($this->pool->getMethods() as $name => $instance) {
            $choices[$instance->getName()] = $name;
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

}