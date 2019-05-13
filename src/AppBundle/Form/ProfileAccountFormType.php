<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;

class ProfileAccountFormType extends BaseProfileFormType
{
    /**
     * @param string $class The User class name
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,array('empty_data' => 'First Name','label' => false,'validation_groups' => 'Account'))
            ->add('firstname',TextType::class,array('empty_data' => 'First Name','label' => false,'validation_groups' => 'Account'))
            ->add('lastname', TextType::class, array('empty_data' => 'Last Name','label' => false,'validation_groups' => 'Account'))
            ->add('phone', TextType::class, array('empty_data' => 'Phone','label' => false,'validation_groups' => 'Account'))
            ->add('email', EmailType::class, array('empty_data' => 'E-mail','label' => false,'validation_groups' => 'Account'))

            //->add('country', TextType::class, array('empty_data' => 'Country','label' => false,'validation_groups' => 'Account'))
            ->add('country', CountryType::class, array('empty_data' => 'Country','label' => false,'validation_groups' => 'Account'))
            ->add('city', TextType::class, array('empty_data' => 'City','label' => false,'validation_groups' => 'Account'))
            ->add('state', TextType::class, array('empty_data' => 'State','label' => false,'validation_groups' => 'Account'))
            ->add('zipCode', TextType::class, array('empty_data' => 'Zip Code','label' => false,'validation_groups' => 'Account'))
            ->add('address', TextType::class, array('empty_data' => 'Address','label' => false,'validation_groups' => 'Account'))
            ->remove('current_password')
            ->remove('avatar')
            ->remove('PlainPassword')

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_token_id' => 'profile_account',
            // BC for SF < 2.8
            'intention' => 'profile_account',
            'validation_groups' => array('Account'),
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getName()
    {
        return 'AppBundle\Form\ProfileAccountFormType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_account_profile';
    }

}