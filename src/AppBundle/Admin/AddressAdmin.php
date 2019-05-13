<?php

namespace AppBundle\Admin;

use EcommerceBundle\CustomerBundle\Entity\Customer;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CustomerBundle\Admin\AddressAdmin as BaseAddressAdmin;

class AddressAdmin extends BaseAddressAdmin
{
    protected $translationDomain = 'AppBundle';

    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataCustomerBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('fulladdress', TextType::class, [
                'code' => 'getFullAddressHtml',
                'template' => 'SonataCustomerBundle:Admin:list_address.html.twig',
            ])
            ->add('customer', ModelListType::class, [
                'class' => Customer::class
            ])
            ->add('current')
            ->add('typeCode', 'trans', ['catalogue' => $this->translationDomain])
        ;
    }

}
