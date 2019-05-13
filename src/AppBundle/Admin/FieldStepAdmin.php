<?php

namespace AppBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\AppBundle;
use AppBundle\Entity\FieldStep;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use AppBundle\Service\FillLevelNameService;


class FieldStepAdmin extends AbstractAdmin
{
    private $nameService;

    /* private $container;


     public function __construct(
         $code,
         $class,
         $baseControllerName,
         $container
     )
     {
         parent::__construct($code, $class, $baseControllerName);

         $this->container = $container;
     }*/

    protected function configureRoutes(RouteCollection $collection)
    {

//        $collection->clearExcept(['create','edit', 'list']);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('description', null, array('global_search' => true), null, array())
            ->add('updatedAt', null, array('global_search' => true), null, array());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('description', null, [
                'header_style' => 'width: 50%; ',

            ])
            ->add('updatedAt', null, [
                'format' => 'Y-m-d',
                'header_style' => 'width: 25%; ',
            ])
            ->add('_action', null, [
                'header_style' => 'width: 25%; ',
                'actions' => [
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

        $fieldStep = $this->getSubject();
        $container = $this->getConfigurationPool()->getContainer();


        $helper = $container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($fieldStep, 'previewFile', FieldStep::class);

        if (!$path) {
            $path = '/upload/wineryfield/step/default_fieldstep.png';
        }
        $formMapper
            ->with('Field Step info', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-primary',
                'description' => 'Info about step',
            ])
            ->add('description')
            ->add('stepLevel', IntegerType::class, [
                'label' => 'Step level at sequence'
            ])
            ->add('stepDecide', 'sonata_type_model', [
                'multiple' => true,
                'property' => 'title',
                'required' => false
            ])
            ->add('previewFile', FileType::class, [])
            ->end()
            ->with('Step translations', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-primary',
                'description' => 'Info about step translations',
            ])
            ->add('translations', TranslationsType::class, [
                'label' => false,
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
                            'it' => [
                                'label' => 'Step title IT'
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
                                ]
                            ],
                            'it' => [
                                'label' => 'Step description IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                    'stepAdditionalTitle' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step additional title',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 0, 'max' => 50])
                                ]
                            ],
                            'it' => [
                                'label' => 'Step additional title IT'
                            ]
                        ]
                    ],
                    'stepAdditionalDescription' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step additional description',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 0, 'max' => 255])
                                ]
                            ],
                            'it' => [
                                'label' => 'Step additional description IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                    'decideTitle' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Decide title',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 0, 'max' => 50])
                                ]
                            ],
                            'it' => [
                                'label' => 'Decide title IT'
                            ]
                        ]
                    ],
                    'stepFooterTitle' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step footer title',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 0, 'max' => 50])
                                ]
                            ],
                            'it' => [
                                'label' => 'Step footer title IT'
                            ]
                        ]
                    ],
                    'stepFooterDescription' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step footer description',
                                'constraints' => [
                                    new NotBlank()
                                ]
//                                'attr' => ['class' => 'ckeditor', 'style' => 'resize: vertical; min-height: 100px;  ']
                            ],
                            'it' => [
                                'label' => 'Step footer description IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                    'stepFooterReadMoText' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Step footer read mo text',
                                'constraints' => [
                                    new NotBlank()
                                ]
//                                'attr' => ['class' => 'ckeditor', 'style' => 'resize: vertical; min-height: 100px;  ']
                            ],
                            'it' => [
                                'label' => 'Step footer read mo text IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                ],


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
            ->add('description')
            ->add('updatedAt');
    }


    /**
     * @param $nameService
     * @return self
     */
    public function setCreateNameService(FillLevelNameService $nameService): self
    {

        $this->nameService = $nameService;
        return $this;
    }

    public function prePersist($object)
    {
        $name = $this->nameService->createName($object);

        $object->setStepLevelText($name);

    }

    public function preUpdate($object)
    {

        $name = $this->nameService->createName($object);

        $object->setStepLevelText($name);

    }


    public function toString($object)
    {
        return $object instanceof FieldStep
            ? $object->getDescription()
            : 'FieldStep'; // shown in the breadcrumb on the create view
    }
}
