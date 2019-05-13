<?php

namespace EcommerceBundle\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseCategory as BaseCategory;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Category extends BaseCategory
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
