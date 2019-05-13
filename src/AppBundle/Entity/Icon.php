<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Icon
 *
 * @ORM\Table(name="icons")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IconsRepository")
 * @Vich\Uploadable
 */
class Icon
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="imagePath", type="string", length=255)
     */
    private $img;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize="4M",
     *     mimeTypesMessage = "Please choose the correct picture type!",
     *     mimeTypes={ "image/jpg", "image/jpeg", "image/png", "image/gif" })
     * )
     * @Vich\UploadableField(mapping="flavor_preview", fileNameProperty="img")
     */
    private $previewFile;

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
     * Set name
     *
     * @param string $name
     *
     * @return Icon
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set imagePath
     *
     * @param string $img
     *
     * @return Icon
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get imagePath
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @return File
     */
    public function getPreviewFile(): ?File
    {
        return $this->previewFile;
    }

    /**
     * @param File $previewFile
     */
    public function setPreviewFile(File $previewFile)
    {
        $this->previewFile = $previewFile;
        if ($previewFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return WineProduct
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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

    function __toString()
    {
        return $this->getName()?: '';
    }


}

