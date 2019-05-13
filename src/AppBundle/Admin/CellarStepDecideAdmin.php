<?php

namespace AppBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\FieldStepDecide;
use AppBundle\Entity\CellarStepDecide;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CellarStepDecideAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection) {

//        $collection->clearExcept(['create','edit', 'list']);
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id',null, array('global_search' => true), null, array())
            ->add('title',null, array('global_search' => true), null, array())
;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('title', TextType::class, [
                'header_style' => 'width: 25%; ',
            ])
            ->add('stepPrice', CurrencyType::class, [
                'header_style' => 'width: 25%; ',
            ])
            ->add('isStepGratis', null, [
                'header_style' => 'width: 25%; '
            ])
            ->add('_action', null, [
                'header_style' => 'width: 25%;',
                'actions' => [
                    'edit' => [],
                    'delete' => []
                ],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $stepDecide = $this->getSubject();
        $container = $this->getConfigurationPool()->getContainer();


        $helper = $container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($stepDecide, 'previewFile', CellarStepDecide::class);

        if (!$path) {
            $path = '/upload/stepdecide/default_stepdecide.png';
        }

        $formMapper
            ->with('Step Info', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-info',
                'description' => 'Enter step info',
            ])
            ->add('title')
            ->add('translations', TranslationsType::class, [
                'label' => 'Available translations',
                'default_locale' => ['en'],
//                'required_locales' => ['en'],
                'fields' => [
                    'stepTitle' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step title',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 0, 'max' => 50])
                                ]
                            ],
                            'it'=> [
                                'label'=>'Step title IT'
                            ]
                        ]
                    ],
                    'stepDescription' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step description',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 0, 'max' => 255])
                                ],
                            ],
                            'it' => [
                                'label' => 'Step description IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ]
                ],


            ])
            ->add('previewFile', FileType::class, [])
            ->end()
            ->with('Step Type', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-info',
                'description' => 'Choose step type',
            ])

            ->add('isStepGratis', 'sonata_type_choice_field_mask', [
                'choices' => CellarStepDecide::getStepType(),
                'map' => [
                    false => ['stepPrice'],
                ],
                'required' => false,
                'placeholder' => 'Choose type of step',
                "label" => false,
                //                'expanded' => true,
                //                'multiple' => true,
            ])
            ->add('stepPrice', IntegerType::class, [
                "label" => "Step price",
            ])
            ->end()
            ->setHelps([
                'previewFile' => "<img src='{$path}'  class='admin-preview' style='max-height: 100px; max-width: 100px'/>",

            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('stepPrice')
            ->add('isStepGratis');
    }

    public function validate(ErrorElement $errorElement, $object)
    {

        if ($object->getIsStepGratis() === null) {
            $errorElement
                ->with('isStepGratis')
                ->assertNotBlank()
                ->end();
        }

        if ($object->getIsStepGratis() === false) {
            $errorElement
                ->with('stepPrice')
                ->assertNotBlank()
                ->end();
        }

    }

    public function toString($object)
    {
        return $object instanceof CellarStepDecide
            ? $object->getTitle()
            : 'CellarStepDecide'; // shown in the breadcrumb on the create view
    }
}
