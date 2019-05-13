<?php

namespace AppBundle\Admin;

use AppBundle\Entity\CellarStep;
use AppBundle\Entity\FieldStep;
use AppBundle\Entity\ProductionRecipe;
use AppBundle\Entity\User;
use AppBundle\Entity\WineConstructor;
use AppBundle\Form\FieldStepType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Tests\Fixtures\Entity;

class ProductionRecipeAdmin extends AbstractAdmin
{
    // Count items on page
//    protected $perPageOptions = array(5, 32, 64, 128, 192, 1000000);

    protected function configureRoutes(RouteCollection $collection)
    {
        // ALL route
        // batch
        // create
        // delete
        // export
        // edit
        // list
        // show
        $collection->remove('show');
//        $collection->clearExcept(['create', 'delete','list', 'edit']);
    }


//     Remove Batch Actions
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('global_search' => true), null, array())
            ->add('title', null, array('global_search' => true), null, array());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title')
            ->add('shortDescription')
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
//        $em = $this->modelManager->getEntityManager('AppBundle:FieldStep');

        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $queryField = $em->createQueryBuilder()
            ->select("fs")
            ->from(FieldStep::class, "fs");

        $queryCellar = $em->createQueryBuilder()
            ->select("cs")
            ->from(CellarStep::class, "cs");


        $fields = $queryField->getQuery()->getResult();
        $cellars = $queryCellar->getQuery()->getResult();

//         -- HOOK --
//        $arrayFieldResult = [];
//
//
//        foreach ($fields as $key => $value) {
//
//            $arrayFieldResult[$value->getDescription().' Step level - ' .$value->getStepLevel()] = $value;
//
//        }

//         -- HOOK --

        $formMapper
            ->with('General info', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-primary',
                'description' => 'Info about recipe',
            ])
            ->add('title')
            ->add('shortDescription')
            ->add('wineConstructor', ChoiceType::class, [
                'choices' => $this->getAvailableRecipes(),
                'placeholder' => '',
                'label' => 'Wine structure formula'
            ])
            ->end()
//            ->add('fieldSteps', 'collection', [
//                'allow_add' => true
//            ])
//            ->add('fieldSteps', 'choice', array('choices'=>$arrayType))
//            ->add('cellarSteps')
            ->with('Field Steps', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-success',
                'description' => 'Field step',
            ])
            ->add('fieldSteps', CollectionType::class, [
                'entry_type' => 'choice',
                'entry_options' => array(
                    'label' => '',
                    'choice_label' => 'stepLevelText',
                    'choices' => $fields,
                ),
//                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->end()
            ->with('Cellar Steps', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-warning',
                'description' => 'Cellar step',
            ])
            ->add('cellarSteps', CollectionType::class, [
                'entry_type' => 'choice',
                'entry_options' => array(
                    'label' => '',
                    'choice_label' => 'stepLevelText',
                    'choices' => $cellars,
                ),
//                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('shortDescription')
;
    }

    public function getAvailableRecipes()
    {
        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $wineConstructors = $em->getRepository('AppBundle:WineConstructor')->findBy(array('isLast' => true));
        $recipes = array();
        foreach ($wineConstructors as $wcData) {
            $recipes[$wcData->getBreadcrumbsString()] = $wcData;
        }
        return $recipes;
    }


    public function toString($object)
    {
        return $object instanceof ProductionRecipe
            ? $object->getTitle()
            : 'ProductionRecipe'; // shown in the breadcrumb on the create view
    }

}
