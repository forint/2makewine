<?php

namespace EcommerceBundle\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\OrderBundle\Entity\BaseOrderElement as BaseOrderElement;

/**
 * Order Element
 *
 * @ORM\Table(name="order_element")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class OrderElement extends BaseOrderElement
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
     * Advanced quantity for bottles of wine
     */
    protected $advancedQuantity;
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
     * Return the total (price * quantity).
     *
     * if $vat = true, return the price with vat
     *
     * @param bool $vat
     *
     * @return float
     */
    public function getTotal($vat = false)
    {
        return bcmul($this->getUnitPrice($vat), (string)$this->getQuantity());
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($vat = false)
    {
        $unitPrice = $this->getUnitPriceExcl();

        if ($vat) {
            $unitPrice = $this->getUnitPriceInc();
        }

        return bcmul($unitPrice, (string)$this->getQuantity());
    }
}
