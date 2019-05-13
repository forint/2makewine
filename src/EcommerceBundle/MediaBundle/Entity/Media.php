<?php

namespace EcommerceBundle\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Media extends BaseMedia
{
    /**
     * @var int $id
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
