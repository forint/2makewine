<?php

namespace EcommerceBundle\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseTag as BaseTag;

/**
 * Collection
 *
 * @ORM\Table(name="tag")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Tag extends BaseTag
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
