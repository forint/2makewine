<?php

namespace AppBundle\Form\Extension;

use Sonata\BasketBundle\Form\BasketType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Sonata\Component\Form\EventListener\BasketResizeFormListener;

class BasketTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return BasketType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_token_id' => '_token',
            'invalid_message' => 'This value is not valid.',
            'invalid_message_parameters' => array(),
            'allow_extra_fields' => true,
            'extra_fields_message' => 'This form should not contain extra fields.',
            'compound' => true,
            'allow_add' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // always clone the basket, so the one in session is never altered
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        // should create a custom basket elements here
        $basketElementBuilder = $builder->create('basketElements', FormType::class, [
            'by_reference' => false,
            'allow_add' => false
        ]);

        $basketElementBuilder->addEventSubscriber(new BasketResizeFormListener($builder->getFormFactory(), $basket));

        $builder->add($basketElementBuilder);
    }
}