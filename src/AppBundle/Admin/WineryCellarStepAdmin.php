<?php

namespace AppBundle\Admin;

use AppBundle\AppBundle;
use AppBundle\Entity\FieldStepDecide;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryFieldStep;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WineryCellarStepAdmin extends AbstractAdmin {

    protected function configureRoutes(RouteCollection $collection) {

        $collection->clearExcept(['edit', 'list']);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
            ->add('id',null, array('global_search' => true), null, array())
            ->add('cellarStep',null, array('global_search' => true), null, array())
            ->add('paymentStatus',null, array('global_search' => true), null, array())
            ->add('deadline',null, array('global_search' => true), null, array())
            ->add('isStepConfirm',null, array('global_search' => true), null, array());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
            ->addIdentifier('id', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('cellarStep', 'text')
            ->add('chosenSolution')
            ->add('paymentStatus', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('isStepConfirm', null, [
                'header_style' => 'text-align: center',
                'row_align' => 'center'
            ])
            ->add('deadline', 'date', [
                'header_style' => 'text-align: center',
                'row_align' => 'center',
                'format'=>'Y-m-d'
            ])
            ->add('_action', null, array(
                'header_style' => 'text-align: center',
                'row_align' => 'center',
                'actions' => [
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

            ->add('paymentStatus', null, [
                'disabled'=>true
            ])
            ->add('isStepConfirm', null, [
                'disabled'=>true
            ])
            ->add('deadline', 'sonata_type_date_picker', [
                'widget' => 'single_text',
                'datepicker_use_button' => true,
                'dp_min_date' => date('Y-mm-dd'),
                'dp_show_today' => true,
                'format' => 'y-MM-dd',
//                'disabled' => true,

            ])
            ->add('chosenSolution', null, [
                'disabled' => true,
//                'readonly' => true,

            ])
            ->add('cellarStep', null, [
                'disabled' => true,
//                'readonly' => true,

            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper) {
        $showMapper
            ->add('id')
            ->add('paymentStatus')
            ->add('deadline')
            ->add('isStepConfirm');
    }

    public function toString($object) {
        return $object instanceof WineryCellarStep
            ? $object->getId()
            : 'WineryCellarStep'; // shown in the breadcrumb on the create view
    }

}
