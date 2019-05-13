<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use EcommerceBundle\CustomerBundle\Entity\Customer;
use FOS\UserBundle\Model\User as BaseUser;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Intl\Intl;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    use ORMBehaviors\Translatable\Translatable;

    private $locale;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastname;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstname;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $country;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $city;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $state;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $zipCode;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $address;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    protected $plainPassword;

    /**
     * @var File
     * @Assert\Image(
     *     maxSize="4M",
     *     minWidth=298,
     *     minHeight=223,
     *     maxWidth=745,
     *     maxHeight=558,
     *     mimeTypesMessage = "Please choose the correct picture type!",
     *     mimeTypes={ "image/jpg", "image/jpeg", "image/png", "image/gif" })
     * )
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatar")
     */
    private $imageFile;

    /**
     *
     * @var string $avatar
     *
     * @ORM\Column(name="avatar", type="string", nullable=true)
     */
    private $avatar;

    /**
     * This value uses for display avatar or stub image if avatar file is absent
     *
     * @var $temporalityAvatar
     */
    private $temporalityAvatar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * Many User have Many Wineries.
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Winery", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinTable(name="users_wineries",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="winery_id", referencedColumnName="id", unique=true, onDelete="CASCADE")}
     *      )
     */
    private $wineries;


    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isWineMaker = false;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $winemakerDescription;


    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
        $this->wineries = new ArrayCollection();
        $this->customers = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function isWineMaker()
    {
        return $this->isWineMaker;
    }

    /**
     * @param bool $isWineMaker
     */
    public function setIsWineMaker($isWineMaker)
    {
        $this->isWineMaker = $isWineMaker;
    }


    /**
     * Add winery
     *
     * @param \AppBundle\Entity\Winery $winery
     *
     * @return User
     */
    public function addWinery(\AppBundle\Entity\Winery $winery)
    {
        $this->wineries[] = $winery;

        return $this;
    }

    /**
     * Remove winery
     *
     * @param \AppBundle\Entity\Winery $winery
     */
    public function removeWinery(\AppBundle\Entity\Winery $winery)
    {
        $this->wineries->removeElement($winery);
    }

    /**
     * Get wineries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWineries()
    {
        return $this->wineries;
    }


    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Retrieve a list of all site roles
     *
     * @return array
     */
    static public function getAllRoles()
    {
        $roles = array();

        // we need to make sure to have at least one role
        $roles['Admin'] = static::ROLE_ADMIN;
        $roles['User'] = static::ROLE_DEFAULT;
        $roles['Supreme'] = static::ROLE_SUPER_ADMIN;

        return array_unique($roles);
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        //if ($image) {
        // if 'updatedAt' is not defined in your entity, use another property
        //$this->updatedAt = new \DateTime('now');
        //}
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getCountryTitle(){
        if ($this->getCountry()){
            return Intl::getRegionBundle()->getCountryName($this->getCountry());
        }
        return null;
    }
    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Message
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
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
     * @return mixed
     */
    public function getWinemakerDescription()
    {
        return $this->winemakerDescription;
    }

    /**
     * @param mixed $winemakerDescription
     */
    public function setWinemakerDescription($winemakerDescription)
    {
        $this->winemakerDescription = $winemakerDescription;
    }

    static public function getUserType()
    {

        return [
            'WineMaker' => true,
            'User' => false
        ];
    }

    /**
     * @return mixed
     */
    public function getTemporalityAvatar()
    {
        return $this->temporalityAvatar;
    }

    /**
     * @param mixed $temporalityAvatar
     */
    public function setTemporalityAvatar($temporalityAvatar)
    {
        $this->temporalityAvatar = $temporalityAvatar;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [];

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }




    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomers()
    {
        return $this->customers ?: $this->customers = new ArrayCollection();
    }

    /**
     * Set customer
     *
     * @param $customer
     */
    public function setCustomers($customer)
    {
        if (is_array($customer)) {
            $this->customers = $customer;
        } else {
            $this->customers->clear();
            $this->customers->add($customer);
        }
    }

    /**
     * Add customer
     *
     * @param Customer $customer
     *
     * @return User
     */
    public function addCustomer(Customer $customer)
    {
        $customer->setCustomer($this);
        $this->customers->add($customer);

        return $this;
    }

    /**
     * Remove customer
     *
     * @param Customer $customer
     *
     */
    public function removeCustomer(Customer $customer)
    {
        $this->customers->removeElement($customer);
    }


    /**
     * Gets triggered only on insert
     *
     * @ORM\PrePersist
     * @param LifecycleEventArgs $args
     */
    public function onPrePersist(LifecycleEventArgs $args)
    {
        $this->setCreatedAt(new \DateTime("now"));
        $this->setUpdatedAt(new \DateTime("now"));

        $this->setUsername($this->getEmail());
        $this->setUsernameCanonical($this->getEmail());

    }


    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime("now"));

        $this->setUsername($this->getEmail());
        $this->setUsernameCanonical($this->getEmail());
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }


    public function __toString()
    {
        return (string)$this->getUsername();
    }

    /**
     * Here I initialize properties not handled by Doctrine
     * ORM\PostLoad()
     */
    public function appPostLoad(LifecycleEventArgs $args)
    {
        $user = $args->getObject();
        $this->setCity($user->getCity());
        dump($args);
    }
}
