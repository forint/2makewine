<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WineryFieldStep
 * @method setSpe()
 *
 * @ORM\Table(name="winery_field_step")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WineryFieldStepRepository")
 */
class WineryFieldStep
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
     * @var FieldStep
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FieldStep")
     */
    private $fieldStep;

    /**
     * @var FieldStepDecide
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FieldStepDecide")
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
     * @return WineryFieldStep
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
     * @return WineryFieldStep
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
     * @return WineryFieldStep
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
     * @param \AppBundle\Entity\FieldStep $fieldStep
     *
     * @return WineryFieldStep
     */
    public function setFieldStep(\AppBundle\Entity\FieldStep $fieldStep = null)
    {
        $this->fieldStep = $fieldStep;

        return $this;
    }

    /**
     * Get fieldStep
     *
     * @return \AppBundle\Entity\FieldStep
     */
    public function getFieldStep()
    {
        return $this->fieldStep;
    }

    /**
     * @return FieldStepDecide
     */
    public function getChosenSolution(): ?FieldStepDecide
    {
        return $this->chosenSolution;
    }

    /**
     * @param FieldStepDecide $chosenSolution
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
