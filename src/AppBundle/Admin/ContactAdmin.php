<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity\Contact;

class ContactAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id',null, array('global_search' => true), null, array())
            ->add('firstName',null, array('global_search' => true), null, array())
            ->add('lastName',null, array('global_search' => true), null, array())
            ->add('email',null, array('global_search' => true), null, array())
            ->add('comment',null, array('global_search' => true), null, array())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('comment')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('comment')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('comment')
        ;
    }

    public function toString($object)
    {
        return $object instanceof Contact
            ? $object->getId()
            : 'Contact Admin'; // shown in the breadcrumb on the create view
    }
}
