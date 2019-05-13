<?php

namespace AppBundle\Form;

use Sonata\CoreBundle\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;

class ProfileFormType extends BaseProfileFormType
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
        $constraintsOptions = array(
            'message' => 'fos_user.current_password.invalid',
        );

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = array(reset($options['validation_groups']));
        }

        $builder
            ->add('_username', HiddenType::class)
            ->add('_email', HiddenType::class)
                 /*->add('avatar', FileType::class,
                     array(
                         'data_class' => null,
                         'label' => false,
                         'required' => false
                     )
                 )*/
                 /*->add('current_password', 'password', array(
                     'label' => 'form.current_password',
                     //'translation_domain' => 'FOSUserBundle',
                     'mapped' => false,
                     'constraints' => new UserPassword($constraintsOptions),
                 ))*/
                 ->add('PlainPassword', 'repeated', array(
                     'type' => 'password',
                     'options' => array('translation_domain' => 'FOSUserBundle'),
                     'first_options' => array('empty_data' => 'form.new_password','label' => false),
                     'second_options' => array('empty_data' => 'form.new_password_confirmation','label' => false),
                     'invalid_message' => 'fos_user.password.mismatch',
                     'error_bubbling' => true
                 ))
                 ->remove('avatar')
                 ->remove('current_password')



        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_token_id' => 'profile',
            // BC for SF < 2.8
            'intention' => 'profile',
            // 'validation_groups' => array('Profile'),
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getName()
    {
        return 'AppBundle\Form\ProfileFormType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

}