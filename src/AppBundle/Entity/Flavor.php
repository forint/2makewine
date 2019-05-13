<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Flavor
 *
 * @ORM\Table(name="flavor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FlavorRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class Flavor
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
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

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
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $img;


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
     * @return Flavor
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
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;


    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Flavor
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
     * @return string
     */
    public function getImg(): ?string
    {
        return $this->img;
    }

    /**
     * @param string $img
     */
    public function setImg(string $img = null)
    {
        $this->img = $img;
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

    public function __toString()
    {
        return 'd';
    }

}

