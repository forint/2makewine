<?php

namespace AppBundle\Admin;

use AppBundle\Entity\WineryCellar;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class WineryCellarAdmin extends AbstractAdmin {

    protected function configureRoutes(RouteCollection $collection) {
//        $collection->remove('create');
        $collection->clearExcept(['edit', 'list']);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
            ->add('id',null, array('global_search' => true), null, array())
            ->add('progress',null, array('global_search' => true), null, array())
            ->add('title', null, ['global_search' => true], null,[])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {


        $listMapper
            ->add('id', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('progress', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('steps', null, [
                'associated_property' => 'cellarStep.id',
                'label' => 'Winery Cellar Steps ID',
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('_action', null, array(
                'header_style' => 'text-align: center',
                'row_align' => 'center',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper) {

        $formMapper
            ->add('progress', IntegerType::class, [
                'disabled' => true,
            ])
            ->add('steps', CollectionType::class, array(
                'disabled' => true,
            ));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $showMapper
            ->add('progress');
    }
}
