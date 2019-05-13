<?php

namespace AppBundle\Entity;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


#use EcommerceBundle\ProductBundle\Entity\Product as BaseProduct;
use Sonata\ProductBundle\Entity\BaseProduct as BaseProduct;

/**
 * WineProduct
 *
 * @ORM\Table(name="wine_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class WineProduct extends BaseProduct
{
    use ORMBehaviors\Translatable\Translatable;

    const DEFAULT_TASTE = 'Low-Low';

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
     * @Assert\NotBlank(message="Empty filed")
     */
    private $title;


    /**
     * @var Flavor[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Flavor")
     *
     */
    private $flavorDominant;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize="4M",
     *     mimeTypesMessage = "Please choose the correct picture type!",
     *     mimeTypes={ "image/jpg", "image/jpeg", "image/png", "image/gif" })
     * )
     * @Vich\UploadableField(mapping="wineproduct_preview", fileNameProperty="img")
     */
    private $previewFile;

    /**
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $img;


//    TASTE
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Empty filed")
     *
     */
    private $tasteFruit = self::DEFAULT_TASTE;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Empty filed")
     */
    private $tasteBody = self::DEFAULT_TASTE;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Empty filed")
     */
    private $tasteTannin = self::DEFAULT_TASTE;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Empty filed")
     */
    private $tasteAcidity = self::DEFAULT_TASTE;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Empty filed")
     */
    private $tasteAlcohol = self::DEFAULT_TASTE;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Empty filed")
     */
    private $tasteSweetness = self::DEFAULT_TASTE;


//FLAVOR
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorAstringency = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorLether = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorBakingSpice = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorPepper = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorHerbaceous = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorFloral = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorBlackFruit = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Empty filed")
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Min {{ limit }} to enter",
     *      maxMessage = "Max {{ limit }} to enter"
     * )
     */
    private $flavorRedFruit = 0;



    /**
     * @var Vineyard;
     * @ORM\ManyToOne(targetEntity="Vineyard")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $vineyard;

    /**
     * @var Icon;
     * @ORM\ManyToMany(targetEntity="Icon")
     */
    private $tasteWithIcon;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @var ProductionRecipe
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductionRecipe")
     * ORM\OrderBy({"name" = "ASC"})
     * @Assert\NotBlank()
     */
    private $productionRecipe;

    /**
     * Price by Bottle
     *
     * @ORM\Column(type="decimal", precision=19, scale=4)
     * @Assert\NotBlank()
     */
    private $advancedPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setProductType();
        $this->setVatRate(0);
        $this->flavorDominant = new \Doctrine\Common\Collections\ArrayCollection();
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
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void

    {
        $this->updatedAt = $updatedAt;
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
    public function getImg()
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
    public function onPreUpdate(LifecycleEventArgs $args)
    {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * Get id
     *
     * @return integer
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
     * @return WineProduct
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
     * Add flavorDominant
     *
     * @param \AppBundle\Entity\Flavor $flavorDominant
     *
     * @return WineProduct
     */
    public function addFlavorDominant(\AppBundle\Entity\Flavor $flavorDominant)
    {
        $this->flavorDominant[] = $flavorDominant;

        return $this;
    }

    /**
     * Remove flavorDominant
     *
     * @param \AppBundle\Entity\Flavor $flavorDominant
     */
    public function removeFlavorDominant(\AppBundle\Entity\Flavor $flavorDominant)
    {
        $this->flavorDominant->removeElement($flavorDominant);
    }

    /**
     * Get flavorDominant
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlavorDominant()
    {
        return $this->flavorDominant;
    }

    /**
     * @return string
     */
    public function getTasteFruit(): ?string
    {
        return $this->tasteFruit;
    }

    /**
     * @param string $tasteFruit
     */
    public function setTasteFruit($tasteFruit)
    {
        $this->tasteFruit = $tasteFruit;
    }

    /**
     * @return string
     */
    public function getTasteBody(): ?string
    {
        return $this->tasteBody;
    }

    /**
     * @param string $tasteBody
     */
    public function setTasteBody($tasteBody)
    {
        $this->tasteBody = $tasteBody;
    }

    /**
     * @return string
     */
    public function getTasteTannin(): ?string
    {
        return $this->tasteTannin;
    }

    /**
     * @param string $tasteTannin
     */
    public function setTasteTannin($tasteTannin)
    {
        $this->tasteTannin = $tasteTannin;
    }

    /**
     * @return string
     */
    public function getTasteAcidity(): ?string
    {
        return $this->tasteAcidity;
    }

    /**
     * @param string $tasteAcidity
     */
    public function setTasteAcidity($tasteAcidity)
    {
        $this->tasteAcidity = $tasteAcidity;
    }

    /**
     * @return string
     */
    public function getTasteAlcohol(): ?string
    {
        return $this->tasteAlcohol;
    }

    /**
     * @param string $tasteAlcohol
     */
    public function setTasteAlcohol($tasteAlcohol)
    {
        $this->tasteAlcohol = $tasteAlcohol;
    }

    /**
     * @return string
     */
    public function getTasteSweetness(): ?string
    {
        return $this->tasteSweetness;
    }

    /**
     * @param string $tasteSweetness
     */
    public function setTasteSweetness($tasteSweetness)
    {
        $this->tasteSweetness = $tasteSweetness;
    }

    /**
     * @return int
     */
    public function getFlavorAstringency(): ?int
    {
        return $this->flavorAstringency;
    }

    /**
     * @param int $flavorAstringency
     */
    public function setFlavorAstringency($flavorAstringency)
    {
        $this->flavorAstringency = $flavorAstringency;
    }

    /**
     * @return int
     */
    public function getFlavorLether(): ?int
    {
        return $this->flavorLether;
    }

    /**
     * @param int $flavorLether
     */
    public function setFlavorLether($flavorLether)
    {
        $this->flavorLether = $flavorLether;
    }

    /**
     * @return int
     */
    public function getFlavorBakingSpice(): ?int
    {
        return $this->flavorBakingSpice;
    }

    /**
     * @param int $flavorBakingSpice
     */
    public function setFlavorBakingSpice($flavorBakingSpice)
    {
        $this->flavorBakingSpice = $flavorBakingSpice;
    }

    /**
     * @return int
     */
    public function getFlavorPepper(): ?int
    {
        return $this->flavorPepper;
    }

    /**
     * @param int $flavorPepper
     */
    public function setFlavorPepper($flavorPepper)
    {
        $this->flavorPepper = $flavorPepper;
    }

    /**
     * @return int
     */
    public function getFlavorHerbaceous(): ?int
    {
        return $this->flavorHerbaceous;
    }

    /**
     * @param int $flavorHerbaceous
     */
    public function setFlavorHerbaceous($flavorHerbaceous)
    {
        $this->flavorHerbaceous = $flavorHerbaceous;
    }

    /**
     * @return int
     */
    public function getFlavorFloral(): ?int
    {
        return $this->flavorFloral;
    }

    /**
     * @param int $flavorFloral
     */
    public function setFlavorFloral($flavorFloral)
    {
        $this->flavorFloral = $flavorFloral;
    }

    /**
     * @return int
     */
    public function getFlavorBlackFruit(): ?int
    {
        return $this->flavorBlackFruit;
    }

    /**
     * @param int $flavorBlackFruit
     */
    public function setFlavorBlackFruit($flavorBlackFruit)
    {
        $this->flavorBlackFruit = $flavorBlackFruit;
    }

    /**
     * @return int
     */
    public function getFlavorRedFruit(): ?int
    {
        return $this->flavorRedFruit;
    }

    /**
     * @param int $flavorRedFruit
     */
    public function setFlavorRedFruit($flavorRedFruit)
    {
        $this->flavorRedFruit = $flavorRedFruit;
    }

    public static function listOfTaste()
    {
        $list = [];
        $list['Low-low'] = 'Low-low';
        $list['Low'] = 'Low';
        $list['Low-high'] = 'Low-high';
        $list['Medium-low'] = 'Medium-low';
        $list['Medium'] = 'Medium';
        $list['Medium-high'] = 'Medium-high';
        $list['High-low'] = 'High-low';
        $list['High'] = 'High';
        $list['High-high'] = 'High-high';

        return $list;
    }


    /**
     * @return Vineyard
     */
    public function getVineyard()
    {
        return $this->vineyard;
    }

    /**
     * @param Vineyard $vineyard
     */
    public function setVineyard($vineyard)
    {
        $this->vineyard = $vineyard;
    }

    /**
     * @return string
     */
    public function getPrice($vat = false)
    {
        return $this->price;
    }

    /**
     * Sets price.
     *
     * @param float $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return Icon
     */
    public function getTasteWithIcon()
    {
        return $this->tasteWithIcon;
    }

    /**
     * @param \AppBundle\Entity\Icon $tasteWithIcon
     * @return WineProduct
     */
    public function addTasteWithIcon(\AppBundle\Entity\Icon $tasteWithIcon)
    {
        $this->tasteWithIcon[] = $tasteWithIcon;
        return $this;
    }

    /**
     * Remove Icon
     *
     * @param \AppBundle\Entity\Icon $flavorDominant
     */
    public function removeTasteWithIcon(\AppBundle\Entity\Icon $tasteWithIcon)
    {
        $this->tasteWithIcon->removeElement($tasteWithIcon);
    }

    /**
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param string $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return productionRecipe
     */
    public function getProductionRecipe(): ?productionRecipe
    {
        return $this->productionRecipe;
    }

    /**
     * @param productionRecipe $productionRecipe
     */
    public function setProductionRecipe($productionRecipe)
    {
        $this->productionRecipe = $productionRecipe;
    }

    public function getProductType()
    {
        return (string) 'sonata.ecommerce.wine.product';
    }

    public function setProductType()
    {
        $this->product_type = 'sonata.ecommerce.wine.product';
    }

    /**
     * @return mixed
     */
    public function getAdvancedPrice()
    {
        return $this->advancedPrice;
    }

    /**
     * @param mixed $advancedPrice
     */
    public function setAdvancedPrice($advancedPrice)
    {
        $this->advancedPrice = $advancedPrice;
    }


    public function __toString()
    {
        return (string)$this->getTitle();
    }
}
