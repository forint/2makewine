<?php

namespace AppBundle\Admin;

use AppBundle\Entity\WineryField;
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

class WineryFieldAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
//        $collection->remove('create');
        $collection->clearExcept(['edit', 'list']);
    }

    // Remove link only from dashboard!!

//    public function getDashboardActions() {
//        $actions = parent::getDashboardActions();
//        unset($actions['create']);
//        return $actions;
//    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('global_search' => true), null, array())
//            ->add('vines')
            ->add('progress', null, array('global_search' => true), null, array())
            ->add('title', null, ['global_search' => true], null,[])
        ;

    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
//                'associated_property' => 'id',
                'header_style' => 'text-align: center',
                'row_align' => 'center',
                'property_path' => '1'
            ])
            ->add('vines', null, [
                'editable' => true,
//                'header_style' => 'width: 35%; text-align: center',
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('progress', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('steps', null, [
                'associated_property' => 'fieldStep.id',
                'label' => 'Winery Field Steps ID',
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('_action', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center',
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

        $formMapper
            ->add('vines')
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
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('vines')
            ->add('progress');
    }
}
