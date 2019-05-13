<?php

namespace EcommerceBundle\BasketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\BasketBundle\Entity\BaseBasketElement as BaseBasketElement;

/**
 * Basket Element
 *
 * @ORM\Table(name="basket_element")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class BasketElement extends BaseBasketElement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var float
     */
    protected $advancedUnitPrice = null;

    /**
     * Advanced quantity for bottles of wine
     */
    protected $advancedQuantity;

    /**
     * {@inheritdoc}
     */
    public function setAdvancedQuantity($advancedQuantity): void
    {
        $this->advancedQuantity = $advancedQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvancedQuantity()
    {
        return $this->advancedQuantity;
    }


    /**
     * {@inheritdoc}
     */
    public function setAdvancedUnitPrice($advancedUnitPrice): void
    {
        $this->advancedUnitPrice = $advancedUnitPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvancedUnitPrice($vat = false)
    {
        $price = (string) $this->advancedUnitPrice;

        if (!$vat && $this->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv((string) $this->getVatRate(), '100')));
        }

        if ($vat && !$this->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv((string) $this->getVatRate(), '100')));
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($vat = false)
    {
        $price = $this->price;

        if (!$vat && true === $this->isPriceIncludingVat()) {
            $price = bcdiv($price, bcadd('1', bcdiv($this->getVatRate(), 100)));
        }

        if ($vat && false === $this->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd('1', bcdiv($this->getVatRate(), 100)));
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal($vat = false)
    {
        $unitPrice = $this->getUnitPrice($vat);
        $advancedUnitPrice = $this->getAdvancedUnitPrice($vat);

        return bcmul((string) $unitPrice, (string) ($this->getQuantity()/10), 100) + bcmul((string) $advancedUnitPrice, (string) ($this->getAdvancedQuantity()), 100);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            'productId' => $this->productId,
            'position' => $this->position,
            'unitPrice' => $this->unitPrice,
            'price' => $this->price,
            'priceIncludingVat' => $this->priceIncludingVat,
            'quantity' => $this->quantity,
            'advancedQuantity' => $this->advancedQuantity,
            'vatRate' => $this->vatRate,
            'options' => $this->options,
            'name' => $this->name,
            'productCode' => $this->productCode,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data): void
    {
        $data = unserialize($data);

        $this->productId = $data['productId'];
        $this->position = $data['position'];
        $this->unitPrice = $data['unitPrice'];
        $this->price = $data['price'];
        $this->priceIncludingVat = $data['priceIncludingVat'];
        $this->vatRate = $data['vatRate'];
        $this->quantity = $data['quantity'];
        $this->advancedQuantity = $data['advancedQuantity'];
        $this->options = $data['options'];
        $this->name = $data['name'];
        $this->productCode = $data['productCode'];
    }
}
