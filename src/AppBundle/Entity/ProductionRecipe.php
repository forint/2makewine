<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProductionRecipe
 *
 * @ORM\Table(name="production_recipe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductionRecipeRepository")
 */
class ProductionRecipe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="0",
     *     max="50"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="0",
     *     max="255"
     * )
     */
    private $shortDescription;

    /**
     * @var WineConstructor
     *
     * @ORM\ManyToOne(targetEntity="WineConstructor", inversedBy="recipes")
     * @Assert\NotBlank()
     */
    private $wineConstructor;



    /**
     * @var FieldStep
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\FieldStep")
     * @ORM\OrderBy({"stepLevel" = "ASC"})
     */
    private $fieldSteps;

    /**
     * @var CellarStep
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\CellarStep")
     * @ORM\OrderBy({"stepLevel" = "ASC"})
     */
    private $cellarSteps;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fieldSteps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cellarSteps = new \Doctrine\Common\Collections\ArrayCollection();

    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ProductionRecipe
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
     * @return string
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     * @return null
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * Add fieldStep
     *
     * @param \AppBundle\Entity\FieldStep $fieldStep
     *
     * @return ProductionRecipe
     */
    public function addFieldStep(\AppBundle\Entity\FieldStep $fieldStep)
    {
        $this->fieldSteps[] = $fieldStep;

        return $this;
    }

    /**
     * Remove fieldStep
     *
     * @param \AppBundle\Entity\FieldStep $fieldStep
     */
    public function removeFieldStep(\AppBundle\Entity\FieldStep $fieldStep)
    {
        $this->fieldSteps->removeElement($fieldStep);
    }

    /**
     * Get fieldSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFieldSteps()
    {
        return $this->fieldSteps;
    }

    /**
     * Add cellarStep
     *
     * @param \AppBundle\Entity\CellarStep $cellarStep
     *
     * @return ProductionRecipe
     */
    public function addCellarStep(\AppBundle\Entity\CellarStep $cellarStep)
    {
        $this->cellarSteps[] = $cellarStep;

        return $this;
    }

    /**
     * Remove cellarStep
     *
     * @param \AppBundle\Entity\CellarStep $cellarStep
     */
    public function removeCellarStep(\AppBundle\Entity\CellarStep $cellarStep)
    {
        $this->cellarSteps->removeElement($cellarStep);
    }

    /**
     * Get cellarSteps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCellarSteps()
    {
        return $this->cellarSteps;
    }

    /**
     * @return WineConstructor
     */
    public function getWineConstructor()
    {
        return $this->wineConstructor;
    }

    /**
     * @param WineConstructor $wineConstructor
     */
    public function setWineConstructor($wineConstructor)
    {
        $this->wineConstructor = $wineConstructor;
    }


}
