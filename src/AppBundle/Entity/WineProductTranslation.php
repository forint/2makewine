<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 * WineProductTranslation
 *
 * @ORM\Table(name="wineproduct_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineProductTranslationRepository")
 */
class WineProductTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $grapeVariety;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $vinification;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $tasteDescription;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $servingTemperature;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $ageingPotential;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $winemakerQuote;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $tasteWithDescription;

    /**
     * @return string
     */
    public function getGrapeVariety()
    {
        return $this->grapeVariety;
    }

    /**
     * @param string $grapeVariety
     */
    public function setGrapeVariety($grapeVariety)
    {
        $this->grapeVariety = $grapeVariety;
    }

    /**
     * @return string
     */
    public function getVinification()
    {
        return $this->vinification;
    }

    /**
     * @param string $vinification
     */
    public function setVinification($vinification)
    {
        $this->vinification = $vinification;
    }

    /**
     * @return string
     */
    public function getTasteDescription()
    {
        return $this->tasteDescription;
    }

    /**
     * @param string $tasteDescription
     */
    public function setTasteDescription($tasteDescription)
    {
        $this->tasteDescription = $tasteDescription;
    }

    /**
     * @return string
     */
    public function getServingTemperature()
    {
        return $this->servingTemperature;
    }

    /**
     * @param string $servingTemperature
     */
    public function setServingTemperature($servingTemperature)
    {
        $this->servingTemperature = $servingTemperature;
    }

    /**
     * @return string
     */
    public function getAgeingPotential()
    {
        return $this->ageingPotential;
    }

    /**
     * @param string $ageingPotential
     */
    public function setAgeingPotential($ageingPotential)
    {
        $this->ageingPotential = $ageingPotential;
    }

    /**
     * @return mixed
     */
    public function getWinemakerQuote()
    {
        return $this->winemakerQuote;
    }

    /**
     * @param mixed $winemakerQuote
     */
    public function setWinemakerQuote($winemakerQuote)
    {
        $this->winemakerQuote = $winemakerQuote;
    }

    /**
     * @return mixed
     */
    public function getTasteWithDescription()
    {
        return $this->tasteWithDescription;
    }

    /**
     * @param mixed $tasteWithDescription
     */
    public function setTasteWithDescription($tasteWithDescription)
    {
        $this->tasteWithDescription = $tasteWithDescription;
    }

}

