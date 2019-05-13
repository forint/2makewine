<?php

namespace EcommerceBundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\ProductBundle\Entity\BaseProductCategory as BaseProductCategory;

/**
 * ProductCategory
 *
 * @ORM\Table(name="product_category")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class ProductCategory extends BaseProductCategory
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="orders")
     * ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $category;
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
