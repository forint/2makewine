<?php

/*
 * This rewrite original part of the Symfony package.
 */

namespace AppBundle\Symfony\Component\Form\Extension\Validator\Type;

use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedFormTypeValidatorExtension extends FormTypeValidatorExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // Constraint should always be converted to an array
        $constraintsNormalizer = function (Options $options, $constraints) {
            return is_object($constraints) ? array($constraints) : (array) $constraints;
        };

        $resolver->setDefaults(array(
            'error_mapping' => array(),
            'constraints' => array(),
            'invalid_message' => 'This value is not valid.',
            'invalid_message_parameters' => array(),
            'allow_extra_fields' => true,
            'extra_fields_message' => 'This form should not contain extra fields.',
        ));

        $resolver->setNormalizer('constraints', $constraintsNormalizer);
    }

}
