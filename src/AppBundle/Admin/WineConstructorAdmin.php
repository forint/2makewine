<?php

namespace AppBundle\Admin;

use AppBundle\Entity\WineConstructor;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\AdminHelper\AdminHelper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class WineConstructorAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_wine_constructor';

    /**
     * Change label on breadcrumb
     */
    public function configure()
    {
        parent::configure();
        $this->classnameLabel = "Parameters";
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $image = $this->getSubject();

        $fileFieldOptions = AdminHelper::displayImage($image, $this);
        $fileFieldOptions["label"] = "Image";

        $formMapper
            ->with('Parameter info', [
                'class' => 'col-md-6',
                'box_class' => 'box box-solid box-default',
            ])
            ->add('name')
            ->add('file', FileType::class, $fileFieldOptions)
            ->add('translations', TranslationsType::class, [
                'label' => 'Available translations',
                'default_locale' => ['en'],
//                'required_locales' => ['en'],
                'fields' => [
                    'title' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Title',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['min'=>3, 'max'=>50])
                                ]
                            ],
                            'it' => [
                                'label' => 'Title IT'
                            ]
                        ]
                    ],
                    'description' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Description',
                                'constraints' => array(
                                    new Length(['min'=>3, 'max'=>150])
                                ),
                            ],
                            'it' => [
                                'label' => 'Description IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;  ']
                    ],
                    'wineExamples' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Wine examples',
                                'constraints' => array(
                                    new Length(['min'=>3, 'max'=>150])
                                ),
                            ],
                            'it' => [
                                'label' => 'Wine examples IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;  ']
                    ],
                    'childrenDescription' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Description for next step',
                                'constraints' => array(
                                    new Length(['min'=>3]),
                                ),
                            ],
                            'it' => [
                                'label' => 'Description for next step IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;  ']
                    ]
                ]
            ])
            ->end()

            ->with('Constructor', [
                'class' => 'col-md-6',
                'box_class' => 'box box-solid box-default',
                'description' => 'Set a place in wine scheme for this parameter',
            ])
            ->add('step', 'sonata_type_model', array(
                'class' => 'AppBundle\Entity\Step',
                'property' => 'name',
                'multiple' => false,
                "label" => "Step name",
                'btn_add'=>false
            ))
            ->add('parent', 'sonata_type_model', array(
                'class' => 'AppBundle\Entity\WineConstructor',
                'property' => 'name',
                'multiple' => false,
                'placeholder' => '',
                'btn_add'=>false
            ))
            ->add('isLast')
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name',null, array('global_search' => true), null, array())
            ->add('imagePath',null, array('global_search' => true), null, array())
            //->add('description')
        ;
    }
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            /*->add('parent', 'entity', array(
                'class' => 'AppBundle\Entity\WineConstructor'
            ))
            ->add('islast')*/
            ->add('breadcrumbs', 'entity', ["label" => "Path"])
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('imagePath')
            ->add('parent', 'entity', array(
                'class' => 'AppBundle\Entity\WineConstructor'
            ))
            ->add('step', 'entity', array(
                'class' => 'AppBundle\Entity\Step'
            ))
            ->add('isLast')
        ;
    }

    public function prePersist($item) {
        $this->setItemBreadcrumbs($item);
        $item->removeIsLastParent();
        $this->manageFileUpload($item);
    }

    public function preUpdate($item) {
        $this->setItemBreadcrumbs($item);
        $item->removeIsLastParent();
        $this->manageFileUpload($item);
    }

    private function manageFileUpload($imagePath)
    {
        if ($imagePath->getFile()) {
            $imagePath->upload();
        }
    }

    private function setItemBreadcrumbs($item)
    {
        $item->createBreadcrumbs($item);
    }

    public function toString($object)
    {
        return $object instanceof WineConstructor
            ? $object->getName()
            : 'Wine parameter'; // shown in the breadcrumb on the create view
    }
}
