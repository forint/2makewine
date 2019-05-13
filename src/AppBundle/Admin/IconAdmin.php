<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Icon;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class IconAdmin extends AbstractAdmin
{
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
            ->addIdentifier('name')
            ->add('img')
            ->add('_action', null, [
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
            ]);

    }


    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $icon = $this->getSubject();
        $container = $this->getConfigurationPool()->getContainer();


        $helper = $container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($icon, 'previewFile', Icon::class);

        $formMapper
            ->add('name')
            ->add('previewFile', FileType::class, ["label" => "Icon"])
            ;
        if($path)
            $formMapper
                ->setHelps([
                    'previewFile' => "<div style='display:flex; justify-content: center;'><img src='{$path}'  class='admin-preview' style='max-height: 200px; max-width: 200px'/></div>"

                ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name');
    }

    public function toString($object)
    {
        return $object instanceof WineProduct
            ? $object->getName()
            : 'Icon'; // shown in the breadcrumb on the create view
    }
}