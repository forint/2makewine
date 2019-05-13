<?php

namespace AppBundle\Provider;

use AppBundle\Event\AppAfterCalculatePriceEvent;
use AppBundle\Event\AppBeforeCalculatePriceEvent;
use JMS\Serializer\SerializerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\ProductBundle\Model\BaseProductProvider;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Event\BasketEvents;
use Sonata\Component\Event\BeforeCalculatePriceEvent;
use Sonata\Component\Event\AfterCalculatePriceEvent;
use Sonata\CoreBundle\Exception\InvalidParameterException;
use Sonata\Component\Basket\BasketElementInterface;
use Symfony\Component\Form\FormBuilder;
use Sonata\Component\Form\Transformer\QuantityTransformer;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Delivery\ServiceDeliveryInterface;

/**
 *  Wine Product Provider
 */
class WineProductProvider extends BaseProductProvider
{
    /**
     * {@inheritdoc}
     */
    public function __construct(SerializerInterface $serializer)
    {
        parent::__construct($serializer);

        $this->serializer = $serializer;

        $this->setOptions(array(
            'product_add_modal' => false,
            'product_scroll_basket' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(ProductInterface $product, CurrencyInterface $currency, $vat = false, $quantity = 1, $advancedQuantity = 1, $ratio = 1)
    {
        $event = new AppBeforeCalculatePriceEvent($product, $currency, $vat, $quantity, $advancedQuantity);
        $this->getEventDispatcher()->dispatch(BasketEvents::PRE_CALCULATE_PRICE, $event);

        /** Set VAT Rate to null */
        $product->setVatRate('0');

        $vat = $event->getVat();

        $quantity = $event->getQuantity();

        if (!is_int($quantity) || $quantity < 1) {
            throw new InvalidParameterException('Expected integer >= 1 for quantity, '.$quantity.' given.');
        }

        if (isset($ratio) && is_numeric($ratio) && $ratio > 0){
            $quantity = (int)$quantity/$ratio;
        }

        $price = (float) (bcmul((string) $this->currencyPriceCalculator->getPrice($product, $currency, $vat), (string) $quantity));
        $advancedPrice = (float) (bcmul((string) $this->currencyPriceCalculator->getAdvancedPrice($product, $currency, $vat), (string) $advancedQuantity));

        $afterEvent = new AppAfterCalculatePriceEvent($product, $currency, $vat, $quantity, $price, $advancedPrice, $advancedQuantity);
        $this->getEventDispatcher()->dispatch(BasketEvents::POST_CALCULATE_PRICE, $afterEvent);

        return $afterEvent->getPrice();
    }

    public function getBaseControllerName()
    {
        return 'AppBundle:WineProduct';
    }

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement A basket element instance
     * @param string                                          $format        A format to obtain raw product
     *
     * @return \Sonata\Component\Order\OrderElementInterface
     */
    public function createOrderElement(BasketElementInterface $basketElement, $format = 'json')
    {
        /** @var OrderElementInterface $orderElement */
        $orderElement = new $this->orderElementClassName();
        $orderElement->setQuantity($basketElement->getQuantity());
        $orderElement->setAdvancedQuantity($basketElement->getAdvancedQuantity());
        $orderElement->setUnitPriceExcl($basketElement->getUnitPrice(false));
        $orderElement->setUnitPriceInc($basketElement->getUnitPrice(true));
        $orderElement->setPrice($basketElement->getPrice(true));
        $orderElement->setVatRate($basketElement->getVatRate());
        $orderElement->setDesignation($basketElement->getName());
        $orderElement->setProductType($this->getCode());
        $orderElement->setStatus(OrderInterface::STATUS_PENDING);
        $orderElement->setDeliveryStatus(ServiceDeliveryInterface::STATUS_OPEN);
        $orderElement->setCreatedAt(new \DateTime());
        $orderElement->setOptions($basketElement->getOptions());

        $product = $basketElement->getProduct();
        $orderElement->setDescription($product->getDescription());
        $orderElement->setProductId($product->getId());
        $orderElement->setRawProduct($this->getRawProduct($product, $format));

        return $orderElement;
    }

    /**
     * This function return the form used in the product view page.
     *
     * @param \Sonata\Component\Product\ProductInterface $product      A Sonata product instance
     * @param \Symfony\Component\Form\FormBuilder        $formBuilder  Symfony form builder
     * @param bool                                       $showQuantity Specifies if quantity field will be displayed (default true)
     * @param array                                      $options      An options array
     */
    public function defineAddBasketForm(ProductInterface $product, FormBuilder $formBuilder, $showQuantity = true, array $options = []): void
    {
        $basketElement = $this->createBasketElement($product);

        // create the product form
        $formBuilder
            ->setData($basketElement)
            ->add('productId', 'hidden');

        if ($showQuantity) {
            $formBuilder
                ->add('quantity', 'integer')
                ->add('advancedQuantity', 'integer');
        } else {
            $transformer = new QuantityTransformer();
            $formBuilder->add(
                $formBuilder
                    ->create('quantity', 'hidden', ['data' => 1])
                    ->create('advancedQuantity', 'hidden', ['data' => 1])
                    ->addModelTransformer($transformer)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildBasketElement(BasketElementInterface $basketElement, ProductInterface $product = null, array $options = []): void
    {
        if ($product) {
            $basketElement->setProduct($this->code, $product);

            if (!$basketElement->getQuantity() && 0 !== $basketElement->getQuantity()) {
                $basketElement->setQuantity(1);
            }

            if (!$basketElement->getAdvancedQuantity() && 0 !== $basketElement->getAdvancedQuantity()) {
                $basketElement->setAdvancedQuantity(1);
            }
        }

        $basketElement->setOptions($options);
    }

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Symfony\Component\Form\FormBuilder             $formBuilder
     * @param array                                           $options
     */
    public function defineBasketElementForm(BasketElementInterface $basketElement, FormBuilder $formBuilder, array $options = []): void
    {
        $formBuilder
            ->add('quantity', 'integer')
            ->add('advancedQuantity', 'integer')
            ->add('productId', 'hidden');
    }

    /**
     * {@inheritdoc}
     */
    public function updateComputationPricesFields(BasketInterface $basket, BasketElementInterface $basketElement, ProductInterface $product): void
    {
        $unitPrice = $this->calculatePrice($product, $basket->getCurrency(), $product->isPriceIncludingVat(), 1);
        $price = $this->calculatePrice($product, $basket->getCurrency(), $product->isPriceIncludingVat(), $basketElement->getQuantity(), $basketElement->getAdvancedQuantity(), 10);

        $basketElement->setUnitPrice($unitPrice);
        $basketElement->setAdvancedUnitPrice($product->getAdvancedPrice());
        $basketElement->setPrice($price);
        $basketElement->setPriceIncludingVat($product->isPriceIncludingVat());
        $basketElement->setVatRate($product->getVatRate());
    }

}