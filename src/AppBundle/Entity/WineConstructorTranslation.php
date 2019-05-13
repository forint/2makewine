<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * StepTranslation
 *
 * @ORM\Table(name="wineconstructor_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineConstructorTranslationRepository")
 */
class WineConstructorTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=50)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\Length(
     *      min = 3,
     *      max = 150,
     *      minMessage = "Field must be at least {{ limit }} characters long",
     *      maxMessage = "Field cannot be longer than {{ limit }} characters"
     *     )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="wine_examples", type="text", nullable=true)
     * @Assert\Length(
     *      min = 3,
     *      max = 150,
     *      minMessage = "Field must be at least {{ limit }} characters long",
     *      maxMessage = "Field cannot be longer than {{ limit }} characters"
     *     )
     */
    private $wineExamples;

    /**
     * @var string
     *
     * @ORM\Column(name="children_description", type="text", nullable=true)
     */
    private $childrenDescription;

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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WineConstructor
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
     * @param string $wineExamples
     */
    public function setWineExamples($wineExamples)
    {
        $this->wineExamples = $wineExamples;
    }

    /**
     * @return string
     */
    public function getWineExamples()
    {
        return $this->wineExamples;
    }

    /**
     * @return string
     */
    public function getChildrenDescription()
    {
        return $this->childrenDescription;
    }

    /**
     * @param string $childrenDescription
     */
    public function setChildrenDescription($childrenDescription)
    {
        $this->childrenDescription = $childrenDescription;
    }


}

