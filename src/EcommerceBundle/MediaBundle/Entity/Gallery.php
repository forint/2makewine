<?php

namespace EcommerceBundle\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGallery as BaseGallery;

/**
 * Gallery
 *
 * @ORM\Table(name="gallery")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Gallery extends BaseGallery
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
