<?php

namespace EcommerceBundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\ProductBundle\Entity\BaseDelivery as BaseDelivery;

/**
 * Delivery
 *
 * @ORM\Table(name="delivery")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Delivery extends BaseDelivery
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
}
