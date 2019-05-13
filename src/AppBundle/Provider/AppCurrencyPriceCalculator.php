<?php

namespace AppBundle\Provider;

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Currency\CurrencyPriceCalculator as BaseCurrencyPriceCalculator;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AppCurrencyPriceCalculator extends BaseCurrencyPriceCalculator
{
    /**
     * {@inheritdoc}
     */
    public function getPrice(ProductInterface $product, CurrencyInterface $currency, $vat = false)
    {
        $price = $product->getPrice();

        if (!$vat && true === $product->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv((string)$product->getVatRate(), '100')));
        }

        if ($vat && false === $product->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv((string)$product->getVatRate(), '100')));
        }

        return $price;
    }
    /**
     * {@inheritdoc}
     */
    public function getAdvancedPrice(ProductInterface $product, CurrencyInterface $currency, $vat = false)
    {
        $price = $product->getAdvancedPrice();

        if (!$vat && true === $product->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv((string)$product->getVatRate(), '100')));
        }

        if ($vat && false === $product->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv((string)$product->getVatRate(), '100')));
        }

        return $price;
    }
}
