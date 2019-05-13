<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * VineyardTranslation
 *
 * @ORM\Table(name="vineyards_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VineyardTranslationRepository")
 */
class VineyardTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $soil;

    /**
     * Set title
     *
     * @param string $title
     *
     * @return WineTranslation
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WineTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getSoil()
    {
        return $this->soil;
    }

    /**
     * @param string $soil
     */
    public function setSoil($soil)
    {
        $this->soil = $soil;
    }

//    public function fullDescription()
//    {
//        return (string)$this->title . ' '
//            . $this->description . ' '
//            . $this->country . ' '
//            . $this->area . ' '
//            . $this->soil;
//    }

    public function __toString()
    {
        return (string) $this->title.": ".$this->description;
    }

}

