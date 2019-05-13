<?php

namespace AppBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\WineProduct;
use AppBundle\Entity\WineProductTranslation;

use AppBundle\Form\CountryTransformer;
use AppBundle\Form\Extension\CountryTypeExtension;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sonata\ProductBundle\Admin\ProductAdmin as BaseProductAdmin;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\Component\Currency\CurrencyFormType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\Component\Form\Type\DeliveryChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Intl\Intl;
use Sonata\ProductBundle\Admin\DeliveryAdmin as BaseDeliveryAdmin;

class DeliveryAdmin extends BaseDeliveryAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->setTranslationDomain('SonataProductBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list): void
    {
        if (!$this->isChild()) {
            $list
                ->addIdentifier('id')
                ->addIdentifier('product', null, [
                    'admin_code' => 'sonata.product.admin.product',
                ]);
        }

        $list
            ->addIdentifier('code')
            ->add('enabled')
            ->remove('perItem')
            ->add('countryCode')
            ->remove('zone')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper): void
    {

        $countries = Intl::getRegionBundle()->getCountryNames();
        $viewCountries = array_flip($countries);
        $countryCodeOptions = [
            'multiple' => true,
            'choices' => $viewCountries,
            'choices_as_values' => true
        ];
        $subject = $this->getSubject();
        $subjectCountryCodes = $subject->getCountryCode();
        if ($subjectCountryCodes){
            $definedCountries = explode('|', $subjectCountryCodes);
            foreach ($definedCountries as $countryCode){
                $selectedValues[$countryCode] = $countries[$countryCode];
            }

            $contryCodeOptions['choice_attr'] = function($val, $key, $index) use($selectedValues){
                if (array_key_exists($key, $selectedValues)){
                    return ['selected'=>'selected'];
                }
                return [];
            };

        }


        if (!$this->isChild()) {
            $formMapper->add('product', ModelListType::class, [], [
                'admin_code' => 'sonata.product.admin.product',
            ]);
        }

        $transformer = new CountryTransformer($this->getModelManager()->getEntityManager($this->getClass()));

        $formMapper
            ->add('enabled')
            ->add('code', DeliveryChoiceType::class)
            ->remove('perItem')
            ->add('countryCode', 'choice', $countryCodeOptions)
            //->add('save', ButtonType::class)
            ->remove('zone')
        ;

        $formMapper->get('countryCode')->setData($countries)->addModelTransformer($transformer);

    }

}
