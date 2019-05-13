<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\WineTranslation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Entity\Vineyard;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\AdminHelper\AdminHelper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class VineyardAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_vineyard';

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('global_search' => true), null, array());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('winemaker')
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
        $image = $this->getSubject();

        $fileFieldOptions = AdminHelper::displayImage($image, $this);
        $fileFieldOptions["label"] = "Image";

        $formMapper
            ->with('Vineyard info', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-default',
                'description' => 'Map info',
            ])
            ->add('name')
            ->add('file', FileType::class, $fileFieldOptions)
            ->add('latitude')
            ->add('longitude')
            ->add('winemaker', 'sonata_type_model_list', [
                'btn_add' => false,
                'btn_delete' => false,
                'help' => '<span style="color:red">Add only winemaker from user list (use filter to find winemakers)</span>'
            ])
//            ->add('winemaker', 'choice', [
//'label'=>'WineMaker',
//                'choices'=>['ABC'=>'ABC', 'DEF'=>'DEF'],
//                'expanded'=>true
//            ])
            ->end()
            ->with('Translations', [
                'class' => 'col-md-8',
                'box_class' => 'box box-solid box-default',
                'description' => 'Language titles and descriptions',
            ])
            ->add('translations', TranslationsType::class, [
                'label' => 'Available translations',
                'default_locale' => ['en'],
                'fields' => [
                    'title' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Name',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 3, 'max' => 100])
                                ]
                            ],
                            'it' => [
                                'label' => 'Name IT'
                            ]
                        ]
                    ],
                    'country' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Country',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min' => 3, 'max' => 100])
                                ]
                            ],
                            'it' => [
                                'label' => 'Country IT'
                            ]
                        ]
                    ],
                    'description' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Description',
                                'constraints' => [
                                    new NotBlank()
                                ]
                            ],
                            'it' => [
                                'label' => 'Description IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;  ']
                    ],
                    'area' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Area',
                                'constraints' => [
                                    new NotBlank()
                                ]
                            ],
                            'it' => [
                                'label' => 'Area IT'
                            ]
                        ]
                    ],
                    'soil' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Soil',
                                'constraints' => [
                                    new NotBlank()
                                ]
                            ],
                            'it' => [
                                'label' => 'Soil IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;  ']
                    ]
                ],
            ])
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {


        $showMapper
            ->add('name')
            ->add('winemaker')
            ->add('latitude')
            ->add('longitude')
//            ->add('translations', null, [
//                'associated_property'=>'fullDescription'
//            ])
        ;
    }

    public function toString($object)
    {
        return $object instanceof Vineyard
            ? $object->getName()
            : 'Vineyard'; // shown in the breadcrumb on the create view
    }

    public function prePersist($item)
    {
        $this->manageFileUpload($item);
    }

    public function preUpdate($item)
    {
        $this->manageFileUpload($item);
    }

    private function manageFileUpload($imagePath)
    {
        if ($imagePath->getFile()) {
            $imagePath->upload();
        }
    }

}