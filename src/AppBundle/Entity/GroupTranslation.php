<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * GroupTranslation
 *
 * @ORM\Table(name="group_translation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupTranslationRepository")
 */
class GroupTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $username
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}

