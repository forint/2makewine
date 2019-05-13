<?php

namespace AppBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\Flavor;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FlavorAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper

//            ->add('id', null, array('global_search' => true), null, array())
            ->add('id',null, array('global_search' => true), null, array())
            ->add('name',null, array('global_search' => true), null, array())
            ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label_icon' => 'fa fa-list-alt fa-1x',

                'header_style' => 'width: 75%',
                'row_align' => 'left',
            ])
            ->add('_action', null, [
                'label_icon' => 'fa fa-file-image-o fa-1x',
                'header_style' => 'width: 75%',
                'actions' => array(
//                    'show' => array(),
                    'delete' => array(),
                ),
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $flavor = $this->getSubject();
        $container = $this->getConfigurationPool()->getContainer();


        $helper = $container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($flavor, 'previewFile', Flavor::class);

        if(!$path){
            $path = '/upload/flavor/default_dominant_flavor.png';
        }
        $formMapper
            ->add('name')
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
                    ]
                ]
            ])
            ->add('previewFile', FileType::class, [
//                'data_class' => null,
//                'required' => false,
            ])

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
            ->with('Preview')
            ->add('name')
            ->add('img', null, ['label'=>'Image Name'])
            ->end();
    }

    public function toString($object)
    {
        return $object instanceof Flavor
            ? $object->getName()
            : 'Flavor'; // shown in the breadcrumb on the create view
    }
}
