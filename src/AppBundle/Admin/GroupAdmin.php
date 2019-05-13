<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\GroupTranslation;
use Sonata\CoreBundle\Form\Type\CollectionType;

class GroupAdmin extends AbstractAdmin {
    protected $baseRouteName = 'sonata_group';

    protected function configureRoutes(RouteCollection $collection) {
        $collection->remove('show');

    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
            ->add('name',null, array('global_search' => true), null, array())
            ->add('roles',null, array('global_search' => true), null, array())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
            ->add('name')
            ->add('roles', 'choice', array(
                'multiple' => true,
                'delimiter' => ' | ',
                'choices' => User::getAllRoles()
            ))
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
            ->add('name')
//            ->add('roles', 'sonata_type_choice_field_mask', array(
//                'choices' => User::getAllRoles(),
//                'map' => array(
//                    'route' => array('route', 'parameters'),
//                    'uri' => array('uri'),
//                ),
//                'placeholder' => 'Choose an option',
//                'required' => false,
//                'multiple' => true,
//            ))
            ->add('roles', 'choice', array(
                'multiple' => true,
                'choices' => $this->getAllRoleList()
            ));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('roles');
    }

    private function getAllRoleList() {
        $em = $this->modelManager->getEntityManager('AppBundle:Role');
        $roles = $em->getRepository('AppBundle:Role')->findAll();
        $roleChoices = array();
        foreach ($roles as $role) {
            $roleChoices[$role->getName()] = $role->getName();
        }
        return $roleChoices;
    }

    public function toString($object) {
        return $object instanceof GroupTranslation
            ? $object->getTitle()
            : 'Group'; // shown in the breadcrumb on the create view
    }
}