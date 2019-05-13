<?php

namespace EcommerceBundle\PaymentBundle\Entity;

use Sonata\PaymentBundle\Entity\BaseTransaction as BaseTransaction;
use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity(repositoryClass="EcommerceBundle\OrderBundle\Repository\TransactionRepository")
 */
class Transaction extends BaseTransaction
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}
