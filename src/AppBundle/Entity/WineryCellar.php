<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * WineryCellar
 *
 * @ORM\Table(name="winery_cellar")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineryCellarRepository")
 * @ORM\EntityListeners({"AppBundle\EventListener\WineryListener"})
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class WineryCellar
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
     * @var int
     *
     * @ORM\Column(name="progress", type="integer")
     */
    private $progress = 0;


    /**
     * @var WineryCellarStep
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\WineryCellarStep", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="winery_cellar_step_production",
     *      joinColumns={@ORM\JoinColumn(name="winerycellar_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="winerycellarstep_id", referencedColumnName="id", unique=true, onDelete="CASCADE")}
     *      )
     *
     *
     */
    private $steps;


    /**
     * Constructor
     */
    public function __construct() {
        $this->steps = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Set process
     *
     * @param integer $progress
     *
     * @return WineryCellar
     */
    public function setProgress($progress) {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get process
     *
     * @return int
     */
    public function getProgress() {
        return $this->progress;
    }

    /**
     * Add step
     *
     * @param \AppBundle\Entity\WineryCellarStep $step
     *
     * @return WineryCellar
     */
    public function addStep(\AppBundle\Entity\WineryCellarStep $step) {
        $this->steps[] = $step;

        return $this;
    }

    /**
     * Remove step
     *
     * @param \AppBundle\Entity\WineryCellarStep $step
     */
    public function removeStep(\AppBundle\Entity\WineryCellarStep $step) {
        $this->steps->removeElement($step);
    }

    /**
     * Get steps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSteps() {
        return $this->steps;
    }

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function __toString() {
        return (string)$this->getId();
    }


}
