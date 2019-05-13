<?php

namespace AppBundle\Admin;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ConversationAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('id', null, array('global_search' => true), null, array())
            ->add('id',null, array('global_search' => true), null, array())
            ->add('createdAt',null, array('global_search' => true), null, array())
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('user', TextType::class)
            ->add('relatedUser', TextType::class)
            ->add('counter', TextType::class, [
                'header_style' => 'width: 5%; text-align: center',
                'label' => 'Read/Unread',
                'template' => 'AppBundle:CRUD:list__action_count.html.twig'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $conversation = $this->getSubject();

        $_fieldTitle = [
            'attr' => array('class' => 'sonata__admin-conversation__edit'),
            'mapped' => false,
            'label' => false
        ];

        // set conversation title if exist
        if ($conversation->getUser() instanceof ArrayCollection && $conversation->getRelatedUser() instanceof ArrayCollection &&
            $conversation->getUser()->count() > 0 && $conversation->getRelatedUser()->count() > 0){

            $conversationTitle =
                ($conversation->getUser()->getUsername() !== $conversation->getRelatedUser()->getUsername())
                    ?
                    '' . $conversation->getUser()->getUsername() . ' - ' . $conversation->getRelatedUser()->getUsername()
                    :
                    $conversation->getUser()->getUsername();

            $_fieldTitle['sonata_help'] = '<h2 class="sonata__admin-conversation__title">' . $conversationTitle . '</h2>';

        }
        $formMapper
            ->add('title', FormType::class, $_fieldTitle)
            ->add('user')
            ->add('relatedUser')
            ->add('messages', 'sonata_type_collection', [
                'data_class' => null,
                'btn_add' => 'Answer',
                'compound' => true,
                'mapped' => true,
                'by_reference' => false
            ], [
                'edit' => 'inline', //standard
                'inline' => 'table',
                'sortable' => 'id',
                'target_entity' => 'AppBundle\Entity\Message'
            ]);

    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('createdAt');
    }

    /**
     * Override this method because need option "allow_extra_fields"
     * For save nested forms with CollectionType
     *
     * @return \Symfony\Component\Form\FormBuilder|FormBuilderInterface
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $this->formOptions['allow_extra_fields'] = true;
        $this->formOptions['validation_groups'] = false;
        $this->formOptions['error_bubbling'] = false;

        $formBuilder = $this->getFormContractor()->getFormBuilder(
            $this->getUniqid(),
            $this->formOptions
        );

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

}
