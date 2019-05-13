<?php

namespace EcommerceBundle\CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Intl;
use Sonata\CustomerBundle\Entity\BaseAddress as BaseAddress;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="EcommerceBundle\CustomerBundle\Repository\AddressRepository")
 */
class Address extends BaseAddress
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

    /**
     * Formats an address in an array form.
     *
     * @param array $address The address array (required keys: firstname, lastname, address1, postcode, city, country_code)
     * @param string $sep The address separator
     *
     * @return string
     */
    public static function formatAddress(array $address, $sep = ', ')
    {
        $address = array_merge(
            [
                'phone' => '',
                'address1' => '',
                'postcode' => '',
                'city' => '',
                'country_code' => '',
            ],
            $address
        );

        $values = array_map('trim', [
            $address['phone'],
            $address['address1'],
            $address['postcode'],
            $address['city'],
        ]);

        foreach ($values as $key => $val) {
            if (!$val) {
                unset($values[$key]);
            }
        }

        $fullAddress = implode($sep, $values);

        if ($countryCode = trim($address['country_code'])) {
            if ($fullAddress) {
                $fullAddress .= ' ';
            }

            $fullAddress .= sprintf('<br>%s', $countryCode);
        }

        return $fullAddress;
    }

    /**
     * @return array
     */
    public function getAddressArrayForRender()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'firstname' => $this->getFirstName(),
            'lastname' => $this->getLastname(),
            'phone' => $this->getPhone(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'address3' => $this->getAddress3(),
            'postcode' => $this->getPostcode(),
            'city' => $this->getCity(),
            'country_code' => Intl::getRegionBundle()->getCountryName($this->getCountryCode()),
        ];
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function getFullAddress($sep = ', ')
    {
        return self::formatAddress($this->getAddressArrayForRender(), $sep);
    }


    public function __toString()
    {
        return (string)$this->getName();
    }

}
