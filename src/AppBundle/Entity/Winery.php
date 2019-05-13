<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Winery
 *
 * @ORM\Table(name="winery")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Winery
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
     * @var WineProduct
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WineProduct")
     * @Assert\NotBlank(message="WineProduct Can't be empty")
     */
    private $wineProduct;

    /**
     * @var WineryField
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\WineryField",  cascade={"persist"},  orphanRemoval=true)
     */
    private $wineryField;

    /**
     * @var WineryCellar
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\WineryCellar",  cascade={"persist"}, orphanRemoval=true)
     */
    private $wineryCellar;

    /**
     * @var Vineyard
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vineyard")
     * Assert\NotBlank(message="Vineyard can't be empty")
     */
    private $vineyard;


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
     * Set process
     *
     * @param integer $progress
     *
     * @return Winery
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get process
     *
     * @return integer
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set wineProduct
     *
     * @param \AppBundle\Entity\WineProduct $wineProduct
     *
     * @return Winery
     */
    public function setWineProduct(\AppBundle\Entity\WineProduct $wineProduct = null)
    {
        $this->wineProduct = $wineProduct;

        return $this;
    }

    /**
     * Get wineProduct
     *
     * @return \AppBundle\Entity\WineProduct
     */
    public function getWineProduct()
    {
        return $this->wineProduct;
    }



    /**
     * Set wineryField
     *
     * @param \AppBundle\Entity\WineryField $wineryField
     *
     * @return Winery
     */
    public function setWineryField(\AppBundle\Entity\WineryField $wineryField = null)
    {
        $this->wineryField = $wineryField;

        return $this;
    }

    /**
     * Get wineryField
     *
     * @return \AppBundle\Entity\WineryField
     */
    public function getWineryField()
    {
        return $this->wineryField;
    }

    /**
     * Set wineryCellar
     *
     * @param \AppBundle\Entity\WineryCellar $wineryCellar
     *
     * @return Winery
     */
    public function setWineryCellar(\AppBundle\Entity\WineryCellar $wineryCellar = null)
    {
        $this->wineryCellar = $wineryCellar;

        return $this;
    }

    /**
     * Get wineryCellar
     *
     * @return \AppBundle\Entity\WineryCellar
     */
    public function getWineryCellar()
    {
        return $this->wineryCellar;
    }

    /**
     * Set vineyard
     *
     * @param \AppBundle\Entity\Vineyard $vineyard
     *
     * @return Winery
     */
    public function setVineyard(\AppBundle\Entity\Vineyard $vineyard = null)
    {
        $this->vineyard = $vineyard;

        return $this;
    }

    /**
     * Get vineyard
     *
     * @return \AppBundle\Entity\Vineyard
     */
    public function getVineyard()
    {
        return $this->vineyard;
    }

    /**
     * @var string
     *
     * add this field for correct global search
     * otherwise we will got the wrong search result
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

    public function __toString()
    {
//        return 'Winery id - ' . $this->getId();
        return (string)$this->getId();
    }
}
