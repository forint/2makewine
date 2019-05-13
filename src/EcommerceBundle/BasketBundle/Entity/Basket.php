<?php

namespace EcommerceBundle\BasketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\BasketBundle\Entity\BaseBasket as BaseBasket;
use Sonata\Component\Basket\BasketInterface;

/**
 * Basket
 *
 * @ORM\Table(name="basket")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Basket extends BaseBasket implements BasketInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();

        $this->setDeliveryMethodCode('free');
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function reset($full = true):void {

        parent::reset($full);

        $this->setDeliveryMethodCode('free');
    }

    public function getCptElements() {

        return $this->cptElement;

    }
}
