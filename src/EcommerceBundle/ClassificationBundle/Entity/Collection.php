<?php

namespace EcommerceBundle\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseCollection as BaseCollection;

/**
 * Collection
 *
 * @ORM\Table(name="collection")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Collection extends BaseCollection
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
