<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * FlavorTranslation
 * @ORM\Table(name="flavor_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FlavorRepository")
 */
class FlavorTranslation
{

    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;


    /**
     * Set title
     *
     * @param string $title
     *
     * @return FlavorTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}

