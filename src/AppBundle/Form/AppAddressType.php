<?php

declare(strict_types=1);

namespace AppBundle\Form;

use Sonata\Component\Basket\BasketInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class AppAddressType extends AbstractType
{
    /**
     * @var string
     */
    protected $addressClass;

    /**
     * @var BasketInterface
     */
    protected $basket;

    /**
     * @var BasketInterface
     */
    protected $requestStack;

    /**
     * @var BasketInterface
     */
    protected $controllerResolver;
    /**
     * @var BasketInterface
     */
    protected $container;

    /**
     * @param string          $addressClass An address entity class name
     * @param BasketInterface $basket       Sonata current basket
     * @param RequestStack $requestStack
     * @param ControllerResolverInterface $controllerResolver
     */
    public function __construct(
        $addressClass,
        BasketInterface $basket,
        RequestStack $requestStack,
        ControllerResolverInterface $controllerResolver,
        ContainerInterface $container
    )
    {
        $this->addressClass = $addressClass;
        $this->basket = $basket;
        $this->requestStack = $requestStack;
        $this->controllerResolver = $controllerResolver;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $addresses = $options['addresses'];
        $cityOptions = ['required' => !count($addresses)];
        $postcodeOptions = ['required' => !count($addresses)];
        $phoneOptions = ['required' => !count($addresses)];
        $addressOptions = ['required' => !count($addresses)];

        if (count($addresses) > 0) {

            $defaultAddressArray = current($addresses);
            $defaultAddress = reset($defaultAddressArray);

            $defaultCity = $defaultAddress->getCity();
            if ($defaultCity){
                $cityOptions['data'] = $defaultCity;
            }

            $defaultPostcode = $defaultAddress->getPostcode();
            if ($defaultPostcode){
                $postcodeOptions['data'] = $defaultPostcode;
            }

            $defaultPhone = $defaultAddress->getPhone();
            if ($defaultPhone){
                $phoneOptions['data'] = $defaultPhone;
            }

            $defaultAddress1 = $defaultAddress->getAddress1();
            if ($defaultAddress1){
                $addressOptions['data'] = $defaultAddress1;
            }

            $defaultCountryCode = $defaultAddress->getCountryCode();
            /*foreach ($addresses as $address) {
                if ($address->getCurrent()) {
                    $defaultAddress = $address['0']['0'];

                    break;
                }
            }*/

            /*$builder->add('addresses', EntityType::class, [
                'choices' => $addresses,
                'preferred_choices' => [$defaultAddress],
                'class' => $this->addressClass,
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ])
            ->add('useSelected', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-bottom:20px;',
                ],
                'translation_domain' => 'SonataBasketBundle',
                'validation_groups' => false,
            ]);*/
        }

        // $builder->add('name', null, ['required' => !count($addresses)]);

        /*if (isset($options['types'])) {
            $typeOptions = [
                'choices' => array_flip($options['types']),
                'translation_domain' => 'SonataCustomerBundle',
            ];

            // choice_as_value option is not needed in SF 3.0+
            if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
                $typeOptions['choices_as_values'] = true;
            }

            $builder->add('type', ChoiceType::class, $typeOptions);
        }*/
        // dump($cityOptions);die;
        $builder
            //->add('firstname', null, ['required' => !count($addresses)])
            //->add('lastname', null, ['required' => !count($addresses)])
            ->add('address1', null, $addressOptions)
            ->add('postcode', null, $postcodeOptions)
            ->add('city', null, $cityOptions)
            ->add('phone', null, $phoneOptions)
            ->remove('firstname')
            ->remove('lastname')
        ;

        $countries = $this->getBasketDeliveryCountries();


        // $countryOptions = ['required' => !count($addresses)];
        // dump($defaultCountryCode);die;
        if (count($countries) > 0) {
            // choice_as_value options is not needed in SF 3.0+
            if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
                $countryOptions['choices_as_values'] = true;
            }
            if (isset($countries) && !empty($countries)){
                $countryOptions['choices'] = array_flip($countries);
            }

        }else{
            /*
            $request = $this->requestStack->getCurrentRequest();
            $controllerArray = $this->controllerResolver->getController($request);
            $controller = $controllerArray['0'];
            dump(get_class_methods($controller));die;
            $controller->forward('sonata_basket_index');*/
            /*dump($controller);
            dump(get_class_methods($controller));die;
            $message = "Some elements in your basket cannot be delivered in country";
            $this->get('session')->getFlashBag()->add('error', $message);*/

            //return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $builder->add('countryCode', CountryType::class, $countryOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['addresses'] = $options['addresses'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->addressClass,
            'addresses' => [],
            'validation_groups' => ['front'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_basket_address';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns basket elements delivery countries.
     *
     * @return array
     */
    protected function getBasketDeliveryCountries()
    {
        $countries = [];

        foreach ($this->basket->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            foreach ($product->getDeliveries() as $delivery) {

                $code = $delivery->getCountryCode();
                $countryCodes = explode('|', $code);

                foreach ($countryCodes as $_code){
                    $countries[$_code] = Intl::getRegionBundle()->getCountryName($_code);
                }
            }
        }

        return $countries;
    }

    /**
     * {@inheritdoc}
     */
    /*public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->addressClass,
            'addresses' => [],
            'validation_groups' => ['front'],
        ]);
    }*/
}
