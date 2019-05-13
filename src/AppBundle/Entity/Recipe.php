<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 *
 * @ORM\Table(name="recipe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecipeRepository")
 */
class Recipe
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var WineConstructor
     *
     * @ORM\ManyToOne(targetEntity="WineConstructor", inversedBy="recipes")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $wineConstructor;

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
     * @return Recipe
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

    public function __toString()
    {
        return (string)$this->getTitle() ?: '';
    }

}

