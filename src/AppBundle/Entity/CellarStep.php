<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="cellar_step")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CellarStepRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 * UniqueEntity(fields={"name"}, message="It looks like you already have such a step name!")
 */
class CellarStep {

    use ORMBehaviors\Translatable\Translatable;

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
     * @ORM\Column( type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $description;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize="1M",
     *     mimeTypesMessage = "Please choose the correct picture type!",
     *     mimeTypes={ "image/jpg", "image/jpeg", "image/png", "image/gif" })
     * )
     * @Vich\UploadableField(mapping="winery_cellar_step_preview", fileNameProperty="previewTitle")
     */
    private $previewFile;

    /**
     *
     * @var string $previewTitle
     *
     * @ORM\Column( type="string", nullable=true)
     */
    private $previewTitle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var CellarStepDecide[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\CellarStepDecide")
     */
    private $stepDecide;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(
     *     value = 1
     * )
     */
    private $stepLevel;

    /**
     *
     * @ORM\Column(type="string")
     */
    private $stepLevelText;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stepDecide = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * @return File
     */
    public function getPreviewFile(): ?File
    {
        return $this->previewFile;
    }

    public function setPreviewFile(File $previewFile)
    {
        $this->previewFile = $previewFile;

        if ($previewFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function getPreviewTitle()
    {
        return $this->previewTitle;
    }

    public function setPreviewTitle($previewTitle)
    {
        $this->previewTitle = $previewTitle;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return CellarStep
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
     * Add stepDecide
     *
     * @param \AppBundle\Entity\CellarStepDecide $stepDecide
     *
     * @return CellarStep
     */
    public function addStepDecide(\AppBundle\Entity\CellarStepDecide $stepDecide)
    {
        $this->stepDecide[] = $stepDecide;

        return $this;
    }

    /**
     * Remove stepDecide
     *
     * @param \AppBundle\Entity\CellarStepDecide $stepDecide
     */
    public function removeStepDecide(\AppBundle\Entity\CellarStepDecide $stepDecide)
    {
        $this->stepDecide->removeElement($stepDecide);
    }

    /**
     * Get stepDecide
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStepDecide()
    {
        return $this->stepDecide;
    }

    /**
     * @return int
     */
    public function getStepLevel()
    {
        return $this->stepLevel;
    }

    /**
     * @param int $stepLevel
     */
    public function setStepLevel($stepLevel)
    {
        $this->stepLevel = $stepLevel;
    }

    /**
     * @return mixed
     */
    public function getStepLevelText()
    {
        return $this->stepLevelText;
    }

    /**
     * @param mixed $stepLevelText
     */
    public function setStepLevelText($stepLevelText)
    {
        $this->stepLevelText = $stepLevelText;


    }


    public function __toString()
    {
        return (string)$this->getDescription();
    }

}
