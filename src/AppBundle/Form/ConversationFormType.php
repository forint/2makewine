<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ConversationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('conversation', HiddenType::class)
            ->add('message', TextType::class, ['empty_data' => 'Type your message', 'label' => false])
                ->add('attachmentFile', VichImageType::class, ['label' => false, 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Conversation',
            'csrf_token_id' => 'conversation',
            // BC for SF < 2.8
            'intention' => 'conversation'
        ]);
    }

    public function getName()
    {
        return 'AppBundle\Form\ConversationFormType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_conversation';
    }

}
