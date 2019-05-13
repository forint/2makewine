<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WineryFieldStep
 * @method setSpe()
 *
 * @ORM\Table(name="winery_cellar_step")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineryCellarStepRepository")
 */
class WineryCellarStep
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
     * @var bool
     *
     * @ORM\Column(name="paymentStatus", type="boolean")
     */
    private $paymentStatus = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var bool
     *
     * @ORM\Column(name="isStepConfirm", type="boolean")
     */
    private $isStepConfirm = 0;

    /**
     * @var CellarStep
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CellarStep")
     */
    private $cellarStep;

    /**
     * @var CellarStepDecide
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CellarStepDecide")
     */
    private $chosenSolution;


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
     * Set paymentStatus
     *
     * @param boolean $paymentStatus
     *
     * @return WineryCellarStep
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return boolean
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return WineryCellarStep
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set isStepConfirm
     *
     * @param boolean $isStepConfirm
     *
     * @return WineryCellarStep
     */
    public function setIsStepConfirm($isStepConfirm)
    {
        $this->isStepConfirm = $isStepConfirm;

        return $this;
    }

    /**
     * Get isStepConfirm
     *
     * @return boolean
     */
    public function getIsStepConfirm()
    {
        return $this->isStepConfirm;
    }

    /**
     * Set fieldStep
     *
     * @param CellarStep|null $cellarStep
     * @return WineryCellarStep
     */
    public function setCellarStep(\AppBundle\Entity\CellarStep $cellarStep = null)
    {
        $this->cellarStep = $cellarStep;

        return $this;
    }

    /**
     * Get fieldStep
     *
     * @return CellarStep
     */
    public function getCellarStep()
    {
        return $this->cellarStep;
    }

    /**
     * @return CellarStepDecide
     */
    public function getChosenSolution(): ?CellarStepDecide
    {
        return $this->chosenSolution;
    }

    /**
     * @param CellarStepDecide $chosenSolution
     */
    public function setChosenSolution($chosenSolution)
    {
        $this->chosenSolution = $chosenSolution;
    }


    public function __toString()
    {
        return (string)'ID - ' . $this->getId();
//        return (string)$this->getFieldStep()->getDescription();
    }
}
