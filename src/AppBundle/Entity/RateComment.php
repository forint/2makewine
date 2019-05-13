<?php

namespace AppBundle\Entity;

use AppBundle\Admin\UserAdmin;
use Doctrine\ORM\Mapping as ORM;

/**
 * RateComment
 *
 * @ORM\Table(name="rate_comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RateCommentRepository")
 * @ORM\EntityListeners({"AppBundle\EventListener\RateCommentListener"})
 */
class RateComment
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
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $author;

    /**
     * @var WineProduct
     *
     * @ORM\ManyToOne(targetEntity="WineProduct")
     */
    private $wineProduct;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $rate;


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
     * Set comment
     *
     * @param string $comment
     *
     * @return RateComment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return WineProduct
     */
    public function getWineProduct()
    {
        return $this->wineProduct;
    }

    /**
     * @param WineProduct $wineProduct
     */
    public function setWineProduct($wineProduct)
    {
        $this->wineProduct = $wineProduct;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

}

