<?php

namespace AppBundle\Service;

use AppBundle\Entity\Winery;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryFieldStep;

class WineryFieldAddDateNotifierService
{
    public function getDate(Winery $winery)
    {
        $notifierDate = [];
        $notifierDate['field'] = null;
        $notifierDate['cellar'] = null;


        $today = new \DateTime('today');

        $wineryField = $winery->getWineryField();
        $wineryCellar = $winery->getWineryCellar();

        $wineryFieldSteps = $wineryField->getSteps();
        $wineryCellarSteps = $wineryCellar->getSteps();

        $filedStepCollection = $wineryFieldSteps
            ->filter(function (WineryFieldStep $step) use ($today) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() >= $today;
            });

        $filedStepLeftDay = $filedStepCollection->first();

        $cellarStepCollection = $wineryCellarSteps
            ->filter(function (WineryCellarStep $step) use ($today) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() >= $today;
            });

        $cellarStepLeftDay = $cellarStepCollection->first();

        if ($filedStepLeftDay) {
            $fieldLeftDay = $filedStepLeftDay->getDeadLine()->diff($today);
            $notifierDate['field'] = $fieldLeftDay->days;
        }

        if ($cellarStepLeftDay) {
            $cellarLeftDay = $cellarStepLeftDay->getDeadLine()->diff($today);
            $notifierDate['cellar'] = $cellarLeftDay->days;
        }


//        return $leftDay->days;
        return $notifierDate;
    }
}
