<?php

namespace AppBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\WineProduct;
use AppBundle\Entity\WineProductTranslation;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sonata\ProductBundle\Admin\ProductAdmin as BaseProductAdmin;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\Component\Currency\CurrencyFormType;

class WineProductAdmin extends BaseProductAdmin
{
    protected $translationDomain = 'messages';

    public function configure(): void
    {
        $this->setTranslationDomain('messages');
        $this->setTemplate('app_create_button','AppBundle:Button:create_button.html.twig');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('enabled')
            ->add('id', null, array('global_search' => true), null, array())
            ->add('title', null, array('global_search' => true), null, array());

        // $filter
        //->add('title')
        //->add('enabled')
        //->add('productCategories.category', null, ['field_options' => ['expanded' => false, 'multiple' => true]])
        //->add('productCollections.collection', null, ['field_options' => ['expanded' => false, 'multiple' => true]])
        //;
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title', null, [
//                'label_icon' => 'fa fa-file-image-o fa-1x',
                'header_style' => 'width: 35%;',
                'label' => 'Title',

            ])
            //->add('vineyard', null, ['header_style' => 'width: 25%'])
            //->addIdentifier('sku')
            //->add('isVariation', BooleanType::class)
            ->add('enabled', null, ['editable' => true])
            ->add('price', MoneyType::class, [
                "label" => "Wine Price",
                'header_style' => 'width: 12%; text-align: center',
            ])
            ->add('advancedPrice', MoneyType::class, [
                "label" => "Item Price",
                'header_style' => 'width: 12%; text-align: center',
            ])
            ->add('vineyard', null, [
                'header_style' => 'width: 25%',
                'label' => 'Vineyard',
            ])
            /*->add('wineRecipe', null, [
                'header_style' => 'width: 25%',
                'label' => 'Wine Recipe',
            ])*/
            /*->add('product_type', null, [
                'label' => 'Product Type'
            ])*/
            ->add('_action', null, [
//                'label_icon' => 'fa fa-file-image-o fa-1x',
                'label' => 'Actions',
                'header_style' => 'width: 30%',
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
            ])
            //->add('productCategories', null, ['associated_tostring' => 'getCategory'])
            //->add('productCollections', null, ['associated_tostring' => 'getCollection'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper): void
    {
        // this admin class works only from a request scope
        if (!$this->hasRequest()) {
            return;
        }

        $wineProduct = $this->getSubject();
        $container = $this->getConfigurationPool()->getContainer();


        $helper = $container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($wineProduct, 'previewFile', WineProduct::class);

        $formMapper
            ->with('Wine Product', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-primary',
                'description' => 'Title and description product',
            ])
            ->add('enabled')
//            ->add('id', ChoiceFieldMaskType::class, array(
//                'choices' => array(
//                    'With title' => 'title',
//                    'With flavorDominant' => 'flavorDominant',
//                ),
//                'map' => array(
//                    'flavorDominant' => array('flavorDominant'),
//                    'title' => array('title'),
//                ),
//                'placeholder' => 'Choose an option',
//                'required' => false
//            ))
            ->add('title', null, ['label' => 'Title'])
            ->add('previewFile', FileType::class, ["label" => "Bottle label"])
            ->add('productionRecipe', 'sonata_type_model_list', [
//                'btn_add' => false,
                'btn_delete' => false
            ])
            ->add('vineyard', 'sonata_type_model_list', [
                'btn_add' => false,
                'btn_delete' => false,
            ])
            //->add('wineRecipe', null, [ 'label' => 'Wine Recipe' ])
            ->add('productionRecipe', 'sonata_type_model_list', [
//                'btn_add' => false,
                'label' => 'Production Recipe',
                'btn_delete' => false,
            ])
            //->add('vineyard', null, [ 'label' => 'Vineyard' ])
            ->add('price', MoneyType::class, ["required" => true, "label" => "Price for 10 vines", "currency" => "USD"])
            ->add('advancedPrice', MoneyType::class, ["required" => true, "label" => "Price for 1 additional item", "currency" => "USD"])
            ->add('priceIncludingVat')
            ->add('vatRate', 'number')
            ->add('stock', 'integer')
            ->add('product_type', TextType::class, [
                'disabled'=>true
            ])
            ->end()
            ->with('Wine card', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-success',

            ])
            ->add('translations', TranslationsType::class, [
                'label' => 'Available translations',
                'default_locale' => ['en'],
                'fields' => [
                    'grapeVariety' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Grape variety',
                                'constraints' => [
                                    new NotBlank()
                                ]
                            ],
                            'it' => [
                                'label' => 'Grape variety IT'

                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;  ']
                    ],
                    'vinification' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Vinification',
                                'constraints' => [
                                    new NotBlank()
                                ]
                            ],
                            'it' => [
                                'label' => 'Vinification IT'

                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                    'servingTemperature' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Serving temperature',
                                'constraints' => [
                                    new NotBlank()
                                ]
                            ],
                            'it' => [
                                'label' => 'Serving temperature IT'
                            ]
                        ]
                    ],
                    'ageingPotential' => [
                        'field_type' => TextType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Ageing potential',
                                'constraints' => [
                                    new NotBlank()
                                ]

                            ],
                            'it' => [
                                'label' => 'Ageing potential IT'
                            ]
                        ]
                    ],
                    'tasteDescription' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'How does it look and taste',
                                'constraints' => [
                                    new NotBlank()
                                ],
                            ],
                            'it' => [
                                'label' => 'How does it look and taste IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                    'winemakerQuote' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Quote from winemaker',
                                'constraints' => [
                                    new NotBlank(),
                                    new Length(['max' => 100])
                                ],
                            ],
                            'it' => [
                                'label' => 'Quote from winemaker IT'
                            ]
                        ],
                        'attr' => ['style' => 'resize: vertical; min-height: 100px;']
                    ],
                    'tasteWithDescription' => [
                        'field_type' => TextareaType::class,
                        'locale_options' => [
                            'en' => [
                                'label' => 'Tastes with',
                                'constraints' => [
                                    new NotBlank()
                                ],
                            ],
                            'it' => [
                                'label' => 'Tastes with IT'
                            ]
                        ],

                    ]
                ],
            ])
            ->add('tasteWithIcon', null, ["label" => "Icons for 'tastes with'"])
            ->end()
            ->with('Taste', [
                'class' => 'col-md-2',
                'box_class' => 'box box-solid box-success',
                'description' => 'Choose level of tasted product',

            ])
            ->add('tasteFruit', ChoiceType::class, [
                'label' => 'label.admin.wineproduct.fruit',
                'choices' => WineProduct::listOfTaste()
            ])
            ->add('tasteBody', ChoiceType::class, [
                'label' => 'Body',
                'choices' => WineProduct::listOfTaste()
            ])
            ->add('tasteTannin', ChoiceType::class, [
                'label' => 'Tannin',
                'choices' => WineProduct::listOfTaste()
            ])
            ->add('tasteAcidity', ChoiceType::class, [
                'label' => 'Acidity',
                'choices' => WineProduct::listOfTaste()
            ])
            ->add('tasteAlcohol', ChoiceType::class, [
                'label' => 'Alcohol',
                'choices' => WineProduct::listOfTaste()
            ])
            ->add('tasteSweetness', ChoiceType::class, [
                'label' => 'Sweetness',
                'choices' => WineProduct::listOfTaste()
            ])
            ->end()
            ->with('Flavor', [
                'class' => 'col-md-2',
                'position' => 'left',
                'box_class' => 'box box-solid box-info',
                'description' => 'Flavor product params Min 0 Max 5',
            ])
            ->add('flavorAstringency', IntegerType::class, [
                'label' => 'Astringency',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorLether', IntegerType::class, [
                'label' => 'Lether',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorBakingSpice', IntegerType::class, [
                'label' => 'Baking Spice',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorPepper', IntegerType::class, [
                'label' => 'Pepper',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorHerbaceous', IntegerType::class, [
                'label' => 'Herbaceous',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorFloral', IntegerType::class, [
                'label' => 'Floral',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorBlackFruit', IntegerType::class, [
                'label' => 'Black Fruit',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->add('flavorRedFruit', IntegerType::class, [
                'label' => 'Red Fruit',
                'attr' => ['min' => 0, 'max' => 5]
            ])
            ->end()
            ->with('Flavor Dominant', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-warning',
                'description' => 'Flavor dominant in product',

            ])
            ->add('flavorDominant', 'sonata_type_model', [
                'property' => 'name',
                'multiple' => true,
                'btn_add' => false,
                'label' => false
            ])
            ->end()
            ->setHelps([
                'previewFile' => "<div style='display:flex; justify-content: center;'><img src='{$path}'  class='admin-preview' style='max-height: 200px; max-width: 200px'/></div>",
                'flavorDominant' => 'Choose the dominant taste of your wine'

            ]);


        /*$product = $this->getProduct();
        $provider = $this->getProductProvider($product);

        if ($product->getId() > 0) {
            $provider->buildEditForm($formMapper, $product->isVariation());
        } else {
            $provider->buildCreateForm($formMapper);
        }*/
    }

    public function getAvailableRecipes()
    {
        $em = $this->modelManager->getEntityManager('AppBundle:WineConstructor');
        $wineConstructors = $em->getRepository('AppBundle:WineConstructor')->findBy(array('isLast' => true));
        $recipes = array();
        foreach ($wineConstructors as $wcData) {
            $recipes[$wcData->getRecipeName() . ": " . $wcData->getBreadcrumbsString()] = $wcData;
        }
        return $recipes;
    }

    /**
     * Duplicating name from title field
     *
     * @param $object
     */
    public function prePersist($object)
    {
        $object->setName((string)$object->getTitle());
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object): void
    {
//        $errorElement
//            ->assertCallback(['validateOneMainCategory'])
//        ;
    }

    public function configureActionButtons($action, $object = null)
    {
        $list = [];

        if (in_array($action, ['tree', 'show', 'edit', 'delete', 'list', 'batch'])
            && $this->hasAccess('create')
            && $this->hasRoute('create')
        ) {
            $list['create'] = [
                'template' => $this->getTemplate('app_create_button'),
            ];
        }

        if (in_array($action, ['show', 'delete', 'acl', 'history'])
            && $this->canAccessObject('edit', $object)
            && $this->hasRoute('edit')
        ) {
            $list['edit'] = [
                'template' => $this->getTemplate('button_edit'),
            ];
        }

        if (in_array($action, ['show', 'edit', 'acl'])
            && $this->canAccessObject('history', $object)
            && $this->hasRoute('history')
        ) {
            $list['history'] = [
                'template' => $this->getTemplate('button_history'),
            ];
        }

        if (in_array($action, ['edit', 'history'])
            && $this->isAclEnabled()
            && $this->canAccessObject('acl', $object)
            && $this->hasRoute('acl')
        ) {
            $list['acl'] = [
                'template' => $this->getTemplate('button_acl'),
            ];
        }

        if (in_array($action, ['edit', 'history', 'acl'])
            && $this->canAccessObject('show', $object)
            && count($this->getShow()) > 0
            && $this->hasRoute('show')
        ) {
            $list['show'] = [
                'template' => $this->getTemplate('button_show'),
            ];
        }

        if (in_array($action, ['show', 'edit', 'delete', 'acl', 'batch'])
            && $this->hasAccess('list')
            && $this->hasRoute('list')
        ) {
            $list['list'] = [
                'template' => $this->getTemplate('button_list'),
            ];
        }

        return $list;
    }

    public function toString($object)
    {
        return $object instanceof WineProduct
            ? $object->getTitle()
            : 'WineProduct'; // shown in the breadcrumb on the create view
    }
}
