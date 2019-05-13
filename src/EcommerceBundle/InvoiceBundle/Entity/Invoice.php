<?php

namespace EcommerceBundle\InvoiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\InvoiceBundle\Entity\BaseInvoice as BaseInvoice;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class Invoice extends BaseInvoice
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
