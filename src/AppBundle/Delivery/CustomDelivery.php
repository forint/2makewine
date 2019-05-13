<?php

declare(strict_types=1);

namespace AppBundle\Delivery;

use Sonata\Component\Delivery\BaseServiceDelivery;

/**
 * A free delivery method, used this only for testing.
 */
class CustomDelivery extends BaseServiceDelivery
{
    protected $enabled = true;

    /**
     * @var bool
     */
    protected $isAddressRequired;

    /**
     * @param bool $isAddressRequired
     */
    public function __construct($isAddressRequired)
    {
        $this->isAddressRequired = $isAddressRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isAddressRequired()
    {
        return $this->isAddressRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Custom delivery';
    }
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'custom';
    }
}
