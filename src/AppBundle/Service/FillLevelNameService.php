<?php

namespace AppBundle\Service;


class FillLevelNameService
{
    public function createName($entity)
    {

        $stepLevelText = 'Level - ';
        $level = $entity->getStepLevel();
        $desc = $entity->getDescription();

        return $stepLevelText . $level . ': ' . $desc;
    }
}