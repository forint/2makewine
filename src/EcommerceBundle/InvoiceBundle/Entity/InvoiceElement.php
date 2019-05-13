<?php

namespace EcommerceBundle\InvoiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\InvoiceBundle\Entity\BaseInvoiceElement as BaseInvoiceElement;

/**
 * Invoice Element
 *
 * @ORM\Table(name="invoice_element")
 * ORM\Entity(repositoryClass="AppBundle\Repository\WineProductRepository")
 */
class InvoiceElement extends BaseInvoiceElement
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
