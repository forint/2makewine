<?php

namespace EcommerceBundle\ClassificationBundle\Entity;

use Sonata\ClassificationBundle\Entity\BaseContext as BaseContext;

/**
 * Collection
 *
 * @ORM\Table(name="context")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Context extends BaseContext
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
