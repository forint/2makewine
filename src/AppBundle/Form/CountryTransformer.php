<?php

namespace AppBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
# use Symfony\Component\Form\Extension\Core\ObjectChoiceList;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CountryTransformer implements DataTransformerInterface
{
    private $choices = [];
    private $selectedChoices = [];
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function transform($values)
    {
        $selectedChoices = [];

        if (is_string($values)){
            $selectedChoices = explode('|', $values);
            return $selectedChoices;
        }
    }


    public function reverseTransform($values)
    {
        if (!$values) return array();
        /*dump(implode('|', $values));die;
        foreach ($values as $key=>$value)
        {
            $this->selectedChoices[] = key($value, $this->choices);
        }
        dump($this->selectedChoices);die;*/
        return implode('|', $values);
    }
}