<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProfileAvatarFormType extends BaseProfileFormType
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
            ->add('_username', HiddenType::class)
            ->add('_email', HiddenType::class)
            ->add('imageFile', VichImageType::class,
                array(
                    'label' => false,
                    'allow_delete' => false,
                    'download_link' => false,
//                    'image_uri' => false, # removed after composer install -- TODO
                    'required' => true,
                    'attr' => array(
                        'style' => 'display:none;',
                        'id' => 'app_user_profile_avatar_imageFile_file',
                        'class' => 'uploadPhoto'
                    )

                ))
            /*->add('imageFile', FileType::class,
                array(
                    'data_class' => null,
                    'label' => false,
                    'required' => true,
                    'error_bubbling' => true
                )
            )*/
            ->remove('PlainPassword')
            ->remove('current_password')
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_token_id' => 'profile_avatar',
            // BC for SF < 2.8
            'intention' => 'profile_avatar',
            // 'validation_groups' => array('Avatar'),
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getName()
    {
        return 'AppBundle\Form\ProfileAvatarFormType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_profile_avatar';
    }

}