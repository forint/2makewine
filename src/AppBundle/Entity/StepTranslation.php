<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * StepTranslation
 *
 * @ORM\Table(name="step_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StepTranslationRepository")
 */
class StepTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=50)
     */
    private $title;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function __toString()
    {
        return (string)$this->getTitle() ?: '';
    }

}

