<?php

namespace AppBundle\Admin;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\AdminHelper\AdminHelper;
use AppBundle\Entity\Step;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class StepAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_step';

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Step configuration')
            ->add('name', 'text', [
                'label' => 'Name of step',
                'label_attr'=>[
//                    'icon' => 'fa fa-sitemap fa-1x',
                ],
                'attr' => [
                    'maxlength' => 50,
                ]

            ])
            ->add('translations', TranslationsType::class, [
                'label' => 'Available translations',
                'default_locale' => ['en'],       // [1]
                'required_locales' => ['en'],            // [1]
                'fields' => [                               // [2]
                    'title' => [
                        'field_type' => 'text',
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step title',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min'=>3, 'max'=>50])
                                ]
                            ],
                            'it' => [
                                'label' => 'Step title IT'
                            ]
                        ]
                    ]
                ],
//                'exclude_fields' => ['description']
            ])
            ->end()
            ->setHelps(array(
                'name' => $this->trans('Enter your name of Step'),
                'translations' => $this->trans('Enter your translations of description'),

            ));
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name',null, array('global_search' => true), null, array());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->addIdentifier('name', null, [
                'label_icon' => 'fa fa-sitemap fa-1x',
                'header_style' => 'width: 40%',
                'row_align' => 'left',
                'collapse' => array(
                    'height' => 20, // height in px
                    'read_more' => 'I want to see the full description', // content of the "read more" link
                    'read_less' => 'This text is too long, reduce the size' // content of the "read less" link
                )
            ])
            ->add('translations')
            ->add('_action', null, array(
                'header_style' => 'width: 20%',
                'label_icon' => 'fa fa-thumbs-o-up fa-1x',
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')//            ->add('description')
        ;
    }

    public function toString($object)
    {
        return $object instanceof Step
            ? $object->getName()
            : 'Step'; // shown in the breadcrumb on the create view
    }

}
