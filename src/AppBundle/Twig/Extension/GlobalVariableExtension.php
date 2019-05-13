<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Sonata\BasketBundle\Form\BasketType;

class GlobalVariableExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $container;

    private $formFactory;

    /**
     * GlobalVariableExtension constructor.
     *
     * @param ContainerInterface $container
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(ContainerInterface $container, FormFactoryInterface $formFactory)
    {
        $this->container = $container;
        $this->formFactory = $formFactory;

    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        $site = $this->container->get('sonata.page.manager.site')->findOneBy(['id'=>'1']);

        $basket = $this->container->get('sonata.basket');

        $form = $this->formFactory->create(BasketType::class, $basket, [
            'validation_groups' => ['elements'],
        ]);

        $provider = $this->container->get('sonata.product.pool')->getProvider('sonata.ecommerce.wine.product');

        // $provider->defineAddBasketForm($wineData, $form);

        return [
            'site' => $site,
            'form' => $form->createView(),
            'basket' => $basket,
            'provider' => $provider
        ];
    }

}