<?php

namespace EcommerceBundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\ProductBundle\Entity\BaseProductCollection as BaseProductCollection;

/**
 * ProductCollection
 *
 * @ORM\Table(name="product_collection")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class ProductCollection extends BaseProductCollection
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
