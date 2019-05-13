<?php


namespace AppBundle\Admin;

use Doctrine\DBAL\Types\StringType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\OrderBundle\Admin\OrderElementAdmin as BaseOrderElementAdmin;
use Symfony\Component\Form\FormTypeInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\OrderBundle\Form\Type\OrderStatusType;
use Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType;
use Sonata\Component\Currency\CurrencyFormType;

class OrderElementAdmin extends BaseOrderElementAdmin
{
    protected $translationDomain = 'AppBundle';

    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->parentAssociationMapping = 'order';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper): void
    {
        $productTypeOptions = [
            'choices' => array_flip(array_keys($this->productPool->getProducts())),
        ];

        // choice_as_value option is not needed in SF 3.0+
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
            $productTypeOptions['choices_as_values'] = true;
        }

        $formMapper
            ->with($this->trans('order_element.form.group_main_label', [], 'SonataOrderBundle'))
            ->add('productType', ChoiceType::class, $productTypeOptions)
            ->add('quantity')
            ->add('advancedQuantity','integer')
            ->add('price')
            ->add('vatRate')
            ->add('designation')
            ->add('description', null, ['required' => false])
            ->add('status', OrderStatusType::class, ['translation_domain' => 'SonataOrderBundle'])
            ->add('deliveryStatus', ProductDeliveryStatusType::class, ['translation_domain' => 'SonataDeliveryBundle'])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id');

        if (!$list->getAdmin()->isChild()) {
            $list->add('order');
        }

        $list
            ->add('quantity')
            ->add('advancedQuantity')
            ->add('getStatusName', 'trans', ['name' => 'status', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'status'])
            ->add('getDeliveryStatusName', 'trans', ['name' => 'deliveryStatus', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'deliveryStatus'])
            ->add('getTotalWithVat', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
            ->add('getTotal', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
        ;
    }
}