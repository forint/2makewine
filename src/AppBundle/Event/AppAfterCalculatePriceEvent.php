<?php

namespace AppBundle\Event;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Product\ProductInterface;


class AppAfterCalculatePriceEvent extends AppBeforeCalculatePriceEvent
{

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $advancedPrice;

    /**
     * @param ProductInterface  $product
     * @param CurrencyInterface $currency
     * @param bool              $vat
     * @param int               $quantity
     * @param float             $price
     * @param float             $advancedPrice
     * @param int             $advancedQuantity
     */
    public function __construct(ProductInterface $product, CurrencyInterface $currency, $vat, $quantity, $price, $advancedPrice, $advancedQuantity)
    {
        parent::__construct($product, $currency, $vat, $quantity, $advancedQuantity);

        $this->price = $price;
        $this->advancedPrice = $advancedPrice;
    }

    /**
     * @param float $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $advancedPrice
     */
    public function setAdvancedPrice($advancedPrice): void
    {
        $this->advancedPrice = $advancedPrice;
    }

    /**
     * @return float
     */
    public function getAdvancedPrice()
    {
        return $this->advancedPrice;
    }
}