<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 */
class CellarStepDecideTranslation
{

    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $stepTitle;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stepDescription;

    /**
     * @return string
     */
    public function getStepTitle()
    {
        return $this->stepTitle;
    }

    /**
     * @param string
     * @return null
     */
    public function setStepTitle($stepTitle)
    {
        $this->stepTitle = $stepTitle;
    }

    /**
     * @return string
     */
    public function getStepDescription()
    {
        return $this->stepDescription;
    }

    /**
     * @param string
     * @return null
     */
    public function setStepDescription($stepDescription)
    {
        $this->stepDescription = $stepDescription;
    }

}

