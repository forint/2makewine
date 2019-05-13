<?php

namespace AppBundle\Event;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Event\BeforeCalculatePriceEvent as BaseBeforeCalculatePriceEvent;

class AppBeforeCalculatePriceEvent extends BaseBeforeCalculatePriceEvent
{
    /**
     * @var int
     */
    protected $advancedQuantity;

    /**
     * @param ProductInterface  $product
     * @param CurrencyInterface $currency
     * @param bool              $vat
     * @param int               $quantity
     * @param int               $advancedQuantity
     */
    public function __construct(ProductInterface $product, CurrencyInterface $currency, $vat, $quantity, $advancedQuantity)
    {
        $this->product = $product;
        $this->currency = $currency;
        $this->vat = $vat;
        $this->quantity = $quantity;
        $this->advancedQuantity = $advancedQuantity;
    }

    /**
     * @param int $advancedQuantity
     */
    public function setAdvancedQuantity($advancedQuantity): void
    {
        $this->advancedQuantity = $advancedQuantity;
    }

    /**
     * @return int
     */
    public function getAdvancedQuantity()
    {
        return $this->advancedQuantity;
    }

}
