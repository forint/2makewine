<?php

namespace EcommerceBundle\CustomerBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\CustomerBundle\Entity\BaseCustomer as BaseCustomer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="EcommerceBundle\CustomerBundle\Repository\CustomerRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Customer extends BaseCustomer
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EcommerceBundle\OrderBundle\Entity\Order", mappedBy="customer", cascade={"persist","remove"})
     */
    protected $orders;

    /**
     * @var $user
     *
     * @ORM\ManyToOne(targetEntity="EcommerceBundle\CustomerBundle\Entity\Customer", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    public function getAddressesByType($type)
    {
        $addresses = new ArrayCollection();

        foreach ($this->getAddresses() as $address) {
            if ($type == $address->getType()) {
                $addresses->set($address->getId(), $address);
            }
        }

        return $addresses;
    }
}
