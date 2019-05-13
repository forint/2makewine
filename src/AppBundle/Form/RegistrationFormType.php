<?php

namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends BaseRegistrationFormType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'label' => 'form.email',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('firstname', null, [
                'label' => 'form.username',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('lastname', null, [
                'label' => 'form.username',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('plainPassword', 'password', [
                'label' => 'form.password',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->remove('username')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_token_id' => 'registration',
            // BC for SF < 2.8
            'intention' => 'registration',
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getName()
    {
        return 'AppBundle\Form\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

}