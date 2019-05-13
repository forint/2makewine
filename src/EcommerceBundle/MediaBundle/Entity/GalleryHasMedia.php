<?php

namespace EcommerceBundle\MediaBundle\Entity;

use Sonata\MediaBundle\Entity\BaseGalleryHasMedia as BaseGalleryHasMedia;

/**
 * Gallery has Media
 *
 * @ORM\Table(name="gallery_has_media")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class GalleryHasMedia extends BaseGalleryHasMedia
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
