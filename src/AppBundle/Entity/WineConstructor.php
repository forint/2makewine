<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WineConstructor
 *
 * @ORM\Table(name="wine_constructors")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineConstructorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class WineConstructor
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="WineConstructor")
     */
    private $parent;

    /**
     * @var Step
     * @ORM\ManyToOne(targetEntity="Step")
     * @ORM\JoinColumn(name="step_id", referencedColumnName="id")
     */
    private $step;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $imagePath;

    /**
     * Unmapped property to handle file uploads
     * @Assert\Image(
     *     maxSize="2M",
     *     mimeTypes={"image/*"},
     *     mimeTypesMessage="This file is not a valid image type (allows: jpeg, png, gif)"
     * )
     */
    private $file;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_last", type="boolean", nullable=true)
     */
    private $isLast = true;

    /**
     * @var WineConstructor[]
     *
     * @ORM\ManyToMany(targetEntity="WineConstructor")
     */
    private $breadcrumbs;

    /**
     * @var ProductionRecipe[]
     *
     * @ORM\OneToMany(targetEntity="ProductionRecipe", mappedBy="wineConstructor")
     */
    private $recipes;

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
     * Set parent
     *
     * @param integer $parent
     *
     * @return WineConstructor
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set step
     *
     * @param integer $step
     *
     * @return WineConstructor
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return WineConstructor
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
     * Set image
     *
     * @param string $imagePath
     *
     * @return WineConstructor
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @return bool
     */
    public function isIsLast()
    {
        return $this->isLast;
    }

    /**
     * @param bool $isLast
     */
    public function setIsLast($isLast)
    {
        $this->isLast = $isLast;
    }

    public function removeIsLastParent()
    {
        if ($parent = $this->getParent()) {
            $parent->setIsLast(false);
        }
    }

    /**
     * @return WineConstructor[]
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * @param WineConstructor[] $breadcrumbs
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param WineConstructor $parent
     */
    public function createBreadcrumbs($item)
    {
        if (($parent = $item->getParent()) != null) {
            $breadcrumbs = clone $parent->getBreadcrumbs();
        } else {
            $breadcrumbs = new \Doctrine\Common\Collections\ArrayCollection();
        }
        $breadcrumbs->add($item);
        $this->setBreadcrumbs($breadcrumbs);
    }

    public function getBreadcrumbsString()
    {
        $breadcrumbs = $this->getBreadcrumbs();
        foreach ($breadcrumbs as $bcItem) {
            $bcResult[] = $bcItem->translate()->getTitle();
        }
        return implode(', ', $bcResult);
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        $this->imagePath = $this->getUploadDir() . '/' . $this->getFile()->getClientOriginalName();

        $this->setFile(null);
    }

    public function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    public function getUploadDir()
    {
        return '/upload/content';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * @return Recipe[]
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @return array[]
     */
    public function getRecipesId()
    {
        $recipes = [];
        foreach ($this->recipes as $recipe) {
            $recipes[] = $recipe->getId();
        }
        return $recipes;
    }

    /**
     * @param LifecycleEventArgs $args
     * @ORM\PostRemove
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $parent = $args->getObject()->getParent();
        $entityManager = $args->getObjectManager();
        $repo = $entityManager->getRepository(WineConstructor::class);
        $wineConstructors = $repo->findBy(['parent' => $parent]);

        if (!count($wineConstructors)) {
            $parent->setIsLast(true);
        }

        $entityManager->persist($parent);
        $entityManager->flush($parent);
    }


    public function __toString()
    {
        return (string)$this->translate()->getTitle();
    }

}

