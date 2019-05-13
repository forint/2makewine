<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FieldStepTranslation
 *
 * @ORM\Table(name="field_step_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FieldStepTranslationRepository")
 */
class FieldStepTranslation
{

    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column( type="string", length=50, nullable=true)
     */
    private $decideTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="stepTitle", type="string", length=50, nullable=true)
     */
    private $stepTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stepDescription;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $stepAdditionalTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stepAdditionalDescription;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $stepFooterTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $stepFooterDescription;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $stepFooterReadMoText;


    /**
     * @return string
     */
    public function getDecideTitle(): ?string
    {
        return $this->decideTitle;
    }

    /**
     * @param string $decideTitle
     * @return null
     */
    public function setDecideTitle( $decideTitle)
    {
        $this->decideTitle = $decideTitle;
    }


    /**
     * Set stepTitle
     *
     * @param string $stepTitle
     *
     * @return FieldStepTranslation
     */
    public function setStepTitle($stepTitle)
    {
        $this->stepTitle = $stepTitle;

        return $this;
    }

    /**
     * Get stepTitle
     *
     * @return string
     */
    public function getStepTitle()
    {
        return $this->stepTitle;
    }

    /**
     * Set stepDescription
     *
     * @param string $stepDescription
     *
     * @return FieldStepTranslation
     */
    public function setStepDescription($stepDescription)
    {
        $this->stepDescription = $stepDescription;

        return $this;
    }

    /**
     * Get stepDescription
     *
     * @return string
     */
    public function getStepDescription()
    {
        return $this->stepDescription;
    }

    /**
     * Set stepAdditionalTitle
     *
     * @param string $stepAdditionalTitle
     *
     * @return FieldStepTranslation
     */
    public function setStepAdditionalTitle($stepAdditionalTitle)
    {
        $this->stepAdditionalTitle = $stepAdditionalTitle;

        return $this;
    }

    /**
     * Get stepAdditionalTitle
     *
     * @return string
     */
    public function getStepAdditionalTitle()
    {
        return $this->stepAdditionalTitle;
    }

    /**
     * Set stepAdditionalDescription
     *
     * @param string $stepAdditionalDescription
     *
     * @return FieldStepTranslation
     */
    public function setStepAdditionalDescription($stepAdditionalDescription)
    {
        $this->stepAdditionalDescription = $stepAdditionalDescription;

        return $this;
    }

    /**
     * Get stepAdditionalDescription
     *
     * @return string
     */
    public function getStepAdditionalDescription()
    {
        return $this->stepAdditionalDescription;
    }

    /**
     * @return string
     */
    public function getStepFooterTitle(): ?string
    {
        return $this->stepFooterTitle;
    }

    /**
     * @param string
     * @return null
     */
    public function setStepFooterTitle($stepFooterTitle)
    {
        $this->stepFooterTitle = $stepFooterTitle;
    }

    /**
     * @return string
     */
    public function getStepFooterDescription(): ?string
    {
        return $this->stepFooterDescription;
    }

    /**
     * @param string
     * @return null
     */
    public function setStepFooterDescription($stepFooterDescription)
    {
        $this->stepFooterDescription = $stepFooterDescription;
    }

    /**
     * @return string
     */
    public function getStepFooterReadMoText(): ?string
    {
        return $this->stepFooterReadMoText;
    }

    /**
     * @param string
     * @return null
     */
    public function setStepFooterReadMoText($stepFooterReadMoText)
    {
        $this->stepFooterReadMoText = $stepFooterReadMoText;
    }


}
