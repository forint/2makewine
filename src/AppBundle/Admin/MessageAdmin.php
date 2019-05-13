<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Message;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MessageAdmin extends AbstractAdmin
{
    private $container;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, ContainerInterface $container)
    {

        parent::__construct($code, $class, $baseControllerName);

        $this->container = $container;

    }

    /*public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        unset($actions['create']);

        return $actions;
    }*/


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id',null, array('global_search' => true), null, array())
//            ->add('user','doctrine_orm_model_autocomplete', array('global_search' => false), null, ['property'=>'username'])
//            ->add('text',null, array('global_search' => false), null, array())
//            ->add('createdAt',null, array('global_search' => false), null, array())
//            ->add('updatedAt',null, array('global_search' => false), null, array())
            ->add('isRead',null, array('global_search' => false), null, array())
            ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('user')
            ->add('text')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('isRead')
            ->add('attachment', NULL, array(
                'header_style' => 'width: 18%; text-align: center',
                'label'=>'Attachment',
                'template' => 'AppBundle:CRUD:list__action_attachment.html.twig'
            ))
            ->add('_action', null, array(
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'answer' => [
                        'template' => 'AppBundle:CRUD:list__action_answer.html.twig'
                    ]
                ]
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $message = $this->getSubject();
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $currentUser = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        if ($message)
            $path = $helper->asset($message, 'attachmentFile', Message::class);

        $attachmentFieldOptions = [
            'data_class' => null,
            'required'   => false,
            'label'      => ''
        ];

        /**
         * Use sonata_help option
         * if you requested from related entity
         * with sonata_type_collection field's type
         */
        if (isset($path)){
            if (preg_match('/^.*\.(jpg|jpeg|png|gif)$/i',$path)) {
                $attachmentFieldOptions['sonata_help'] = "<img src=" . $path . " class='admin-preview' style='max-height: 100px; max-width: 100px'/>";
            }else{
                $attachmentFieldOptions['sonata_help'] = "<a href=" . $path . " />Download file</a>";
            }
        }


        /** Set user default options */
        $userByDefaultOptions = [
            'btn_add' => false,
            'allow_extra_fields' => true
        ];

        /**
         * Set current user as default where add new message
         */
        if ($message){
            $userByDefault = $message->getUser();
            if (!isset($userByDefault) || is_null($userByDefault)){
                $userByDefaultOptions['data'] = $currentUser;
            }
        }

        $formMapper
            ->add('conversation','sonata_type_model',[
                'allow_extra_fields' => true,
                'btn_add' => false,
                'required' => true
            ])
            ->add('user', 'sonata_type_model', $userByDefaultOptions)
            ->add('text')
            //->add('createdAt','sonata_type_datetime_picker')
            //->add('updatedAt','sonata_type_datetime_picker')
            ->add('isRead', null, [
                'disabled' => true
            ])
            ->add('attachmentFile', VichImageType::class,
                $attachmentFieldOptions, [
                'admin_code' => 'app.admin.message',
                'identifier' => true
            ])
        ;


    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('user')
            ->add('text')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('isRead')
            ->add('attachment');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('answer', $this->getRouterIdParameter().'/edit');
        $collection->remove('create');
    }


    /**
     * Override this method because need option "allow_extra_fields"
     * For save form & child form with CollectionType
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

    public function toString($object)
    {
        return $object instanceof Message
            ? $object->getText()
            : 'Message'; // shown in the breadcrumb on the create view
    }
}
