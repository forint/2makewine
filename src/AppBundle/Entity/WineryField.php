<?php

namespace AppBundle\Entity;

use AppBundle\Event\ProgressChangedEvent;
use AppBundle\Repository\WineryFieldRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * WineryField
 *
 * @ORM\Table(name="winery_field")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineryFieldRepository")
 * @ORM\EntityListeners({"AppBundle\EventListener\WineryListener"})
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class WineryField {

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
     * @ORM\Column(name="vines", type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 10
     * )
     */
    private $vines = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="progress", type="integer")
     */
    private $progress = 0;


    /**
     * @var WineryFieldStep
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\WineryFieldStep", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="winery_field_step_production",
     *      joinColumns={@ORM\JoinColumn(name="wineryfield_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="wineryfieldstep_id", referencedColumnName="id", unique=true, onDelete="CASCADE")}
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
     * Set vines
     *
     * @param integer $vines
     *
     * @return WineryField
     */
    public function setVines($vines) {
        $this->vines = $vines;

        return $this;
    }

    /**
     * Get vines
     *
     * @return int
     */
    public function getVines() {
        return $this->vines;
    }

    /**
     * Set process
     *
     * @param integer $progress
     *
     * @return WineryField
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
     * @param \AppBundle\Entity\WineryFieldStep $step
     *
     * @return WineryField
     */
    public function addStep(\AppBundle\Entity\WineryFieldStep $step) {
        $this->steps[] = $step;

        return $this;
    }

    /**
     * Remove step
     *
     * @param \AppBundle\Entity\WineryFieldStep $step
     */
    public function removeStep(\AppBundle\Entity\WineryFieldStep $step) {
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
