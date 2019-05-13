<?php

namespace AppBundle\Twig\Extension;

class DateExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('leftdays', [$this, 'leftDaysFilter']),
        );
    }

    public function leftDaysFilter($object)
    {
        $today = new \DateTime('today');
        $leftDay = $object->diff($today);
        return $leftDay->days;
    }
}