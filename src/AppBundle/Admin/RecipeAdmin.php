<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Recipe;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\AdminHelper\AdminHelper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class RecipeAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_recipe';

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title',null, array('global_search' => true), null, array())
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            /*->add('wineConstructor',ChoiceType::class,
                array(
                    'choices'=> $this->getAvailableRecipes(),
                    'placeholder' => '',
                    'label' => 'Decisions tree path'
                ))*/
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
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('wineConstructor',ChoiceType::class,
                array(
                    'choices'=> $this->getAvailableRecipes(),
                    'placeholder' => '',
                    'label' => 'Decisions tree path'
                ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('wineConstructor')
        ;
    }

    public function toString($object)
    {
        return $object instanceof Recipe
            ? $object->getTitle()
            : 'Result wine'; // shown in the breadcrumb on the create view
    }

    public function getAvailableRecipes(){
        $em = $this->modelManager->getEntityManager('AppBundle:WineConstructor');
        $wineConstructors = $em->getRepository('AppBundle:WineConstructor')->findBy(array('isLast' => true));
        $recipes = array();
        foreach ($wineConstructors as $wcData) {
                $recipes[$wcData->getBreadcrumbsString()] = $wcData;
        }
        return $recipes;
    }
}