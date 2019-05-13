<?php

namespace AppBundle\Admin;

use AppBundle\Entity\WineryCellar;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryField;
use AppBundle\Entity\Winery;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\EqualType;

use AppBundle\Entity\WineryFieldStep;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class WineryAdmin extends AbstractAdmin
{

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
//        $collection->remove('edit');

        // It's hook - to enable on direct link go to the winery
        // $collection->add('edit', $this->getRouterIdParameter() . '/show');

        $collection->remove('delete');
//        $collection->clearExcept(['create', 'delete','list']);
    }

    public function getExportFields()
    {

        return ['progress', 'wineryField', 'wineryCellar'];
    }

    // Remove Batch Actions
//    public function getBatchActions()
//    {
//        $actions = parent::getBatchActions();
//        unset($actions['delete']);
//
//        return $actions;
//    }
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, ['global_search' => true], null, [])
            ->add('progress', null, ['global_search' => true], null, [])
            ->add('title', null, ['global_search' => true], null, [])
//            ->add('wineProduct', null, ['global_search' => false], null,[])
//            ->add('wineryField', null, ['global_search' => false], null,[])
//            ->add('wineryCellar', null, ['global_search' => false], null,[])
//            ->add('vineyard', null, ['global_search' => false], null,[])
        ;


    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, [
                'header_style' => 'width:5%; text-align: center',
                'row_align' => 'center'
            ])
            ->add('progress', null, [
                'header_style' => 'width:10%; text-align: center',
                'row_align' => 'center'
            ])
            ->add('wineryField', null, [
                'header_style' => 'width:10%; text-align: center',
                'row_align' => 'center'
            ])
            ->add('wineryField.steps', null, [
//                'associated_property' => 'fieldStep.id',
                'label' => 'Winery Field Steps ID',
                'header_style' => 'width:20%; text-align: center',
                'row_align' => 'center',
                'collapse' => [
                    'height' => 20, // height in px
                    'read_more' => 'I want to see the full description', // content of the "read more" link
                    'read_less' => 'This text is too long, reduce the size' // content of the "read less" link
                ]
            ])
            ->add('wineryCellar', null, [
                'header_style' => 'width:10%; text-align: center',
                'row_align' => 'center'
            ])
            ->add('wineryCellar.steps', null, [
//                'associated_property' => 'cellarStep.id',
                'label' => 'Winery Cellar Steps ID',
                'header_style' => 'width:20%; text-align: center',
                'row_align' => 'center',
                'collapse' => [
                    'height' => 20, // height in px
                    'read_more' => 'I want to see the full description', // content of the "read more" link
                    'read_less' => 'This text is too long, reduce the size' // content of the "read less" link
                ]
            ])
            ->add('_action', null, [
                'header_style' => 'width:20%; text-align: center',
                'row_align' => 'center',
                'actions' => [
                    'show' => array(),
//                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $winery = $this->getSubject();
//        $container = $this->getConfigurationPool()->getContainer();




        if ($this->isCurrentRoute('create')) {
            $formMapper
                ->with('Wine Product and Vineyard', [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-solid box-default',
                ])
                ->add('wineProduct')
                ->end();
        } else {
            $formMapper
                ->with('Wine Product and Vineyard', [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-solid box-default',
                ])
                ->add('wineProduct', null, [
                    'disabled'=> true
                ])
                ->end();
        }


        $formMapper
            ->with('Progress info', [
                'class' => 'col-md-3',
                'box_class' => 'box box-solid box-default',
            ])
            ->add('progress', NumberType::class, [
                'disabled' => true,
            ])
            ->end();

        if ($winery->getWineryField()) {
            $formMapper
                ->with('Winery Field', [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-solid box-default',
                ])
                ->add('wineryField', EntityType::class, [
                    'disabled' => true,
                    'placeholder' => '',
                    'class' => 'AppBundle:WineryField',
                ])
                ->add('wineryField.steps', CollectionType::class, [
                    'disabled' => true,
                ])
                ->end();

        }

        if ($winery->getWineryCellar()) {
            $formMapper
                ->with('Winery Cellar', [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-solid box-default',
                ])
                ->add('wineryCellar', EntityType::class, [
                    'disabled' => true,
                    'placeholder' => '',
                    'class' => 'AppBundle:WineryCellar',
                ])
                ->add('wineryCellar.steps', CollectionType::class, [
                    'disabled' => true,
                ])
                ->end();

//            ->add('wineryField.steps','sonata_type_model', [
//                'disabled' => true,
//                'property'=>'fieldStep.description',
//                'multiple'=>true
//            ])

        }


    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {

        /*        $wineProduct = $this->getSubject();
                $container = $this->getConfigurationPool()->getContainer();


                $helper = $container->get('vich_uploader.templating.helper.uploader_helper');
                $path = $helper->asset($wineProduct, 'previewFile', WineProduct::class);*/


        $showMapper
            ->add('id')
            ->add('progress')
            ->add('wineProduct')
            ->add('vineyard')
            ->add('wineryField')
            ->add('wineryField.steps')
            ->add('wineryCellar')
            ->add('wineryCellar.steps')
            /*            ->setHelps([
                'previewFile' => "<div style='display:flex; justify-content: center;'><img src='{$path}'  class='admin-preview' style='max-height: 200px; max-width: 200px'/></div>",
                'flavorDominant' => 'Choose the dominant taste of your wine'

            ])*/
        ;
    }


//    public function configureActionButtons($action, $object = null)
//    {
//        $list = parent::configureActionButtons($action, $object);
//
//        unset($list['show']);
//
//        return $list;
//    }


    public function prePersist($object)
    {

        $wineryField = new WineryField();
        $wineryCellar = new WineryCellar();
        $vineyard = $object->getWineProduct()->getVineyard();


        foreach ($object->getWineProduct()->getProductionRecipe()->getFieldSteps() as $item) {

            $wineryFieldStep = new WineryFieldStep();

            $wineryFieldStep->setFieldStep($item);
            $wineryField->addStep($wineryFieldStep);
        }

        foreach ($object->getWineProduct()->getProductionRecipe()->getCellarSteps() as $item) {

            $wineryCellarStep = new WineryCellarStep();

            $wineryCellarStep->setCellarStep($item);
            $wineryCellar->addStep($wineryCellarStep);
        }


        $object->setWineryField($wineryField);
        $object->setWineryCellar($wineryCellar);
        $object->setVineyard($vineyard);


    }

    public function toString($object)
    {
        return $object instanceof Winery
            ? $object->getId()
            : 'Winery'; // shown in the breadcrumb on the create view
    }


}
