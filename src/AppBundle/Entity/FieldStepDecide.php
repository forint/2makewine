<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * StepDecide
 *
 * @ORM\Table(name="step_decide")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FieldStepDecideRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class FieldStepDecide
{

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
     * @ORM\Column(name="title", type="string", length=50)
     *
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="stepPrice", type="integer", nullable=true)
     */
    private $stepPrice;

    /**
     * @var bool
     *
     * @ORM\Column(name="isStepGratis", type="boolean")
     */
    private $isStepGratis = true;


    /**
     * @var File
     * @Assert\Image(
     *     maxSize="1M",
     *     mimeTypesMessage = "Please choose the correct picture type!",
     *     mimeTypes={ "image/jpg", "image/jpeg", "image/png", "image/gif" })
     * )
     * @Vich\UploadableField(mapping="step_decide_preview", fileNameProperty="previewTitle")
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
     * @return FieldStepDecide
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
     * Set stepPrice
     *
     * @param integer $stepPrice
     *
     * @return FieldStepDecide
     */
    public function setStepPrice($stepPrice)
    {
        $this->stepPrice = $stepPrice;

        return $this;
    }

    /**
     * Get stepPrice
     *
     * @return int
     */
    public function getStepPrice()
    {
        return $this->stepPrice;
    }

    /**
     * Set isStepGratis
     *
     * @param boolean $isStepGratis
     *
     * @return FieldStepDecide
     */
    public function setIsStepGratis($isStepGratis)
    {
        $this->isStepGratis = $isStepGratis;

        return $this;
    }

    /**
     * Get isStepGratis
     *
     * @return bool
     */
    public function getIsStepGratis()
    {
        return $this->isStepGratis;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * @return File
     */
    public function getPreviewFile(): ?File {
        return $this->previewFile;
    }

    public function setPreviewFile(File $previewFile) {
        $this->previewFile = $previewFile;

        if ($previewFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function getPreviewTitle() {
        return $this->previewTitle;
    }

    public function setPreviewTitle($previewTitle) {
        $this->previewTitle = $previewTitle;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    static public function getStepType()
    {
        return [
            'Free' => true,
            'Paid' => false
        ];
    }

    public function __toString()
    {
     return (string)$this->getTitle();
    }
}

