<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use FOS\UserBundle\Model\GroupManagerInterface;
use FOS\UserBundle\Model\GroupManager;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Group constructor.
     *
     */
    public function __construct($name = null, array $roles = array()) {

        parent::__construct($name, $roles);

    }
    /**
     * Retrieve a list of all site roles
     *
     * @return array
     */
    static public function getAllRoles(){
        $roles = array();

        // we need to make sure to have at least one role
        $roles['ROLE_USER'] = User::ROLE_DEFAULT;
        $roles['ROLE_ADMIN'] = User::ROLE_ADMIN;

        return array_unique($roles);
    }
    /**
     * Retrieve a list of all site roles
     *
     * @return array
     */
    static public function getGroups(){

        $groups = array();

        // we need to make sure to have at least one role
        $roles['ROLE_USER'] = User::ROLE_DEFAULT;
        $roles['ROLE_ADMIN'] = User::ROLE_ADMIN;

        return array_unique($roles);
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}