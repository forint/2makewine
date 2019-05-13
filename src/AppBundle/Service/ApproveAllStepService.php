<?php

namespace AppBundle\Service;

use AppBundle\Entity\CellarStepDecide;
use AppBundle\Entity\FieldStepDecide;
use AppBundle\Entity\Winery;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryFieldStep;
use Doctrine\ORM\EntityManager;

class ApproveAllStepService
{

    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }


    public function approveAllStep(Winery $winery)
    {
        $today = new \DateTime('today');

        $wineryField = $winery->getWineryField();
        $wineryCellar = $winery->getWineryCellar();

        $wineryFieldSteps = $wineryField->getSteps();
        $wineryCellarSteps = $wineryCellar->getSteps();

        $wineryFieldStepArray = $wineryFieldSteps->toArray();
        $wineryCellarStepArray = $wineryCellarSteps->toArray();

        $stepFieldProgress = 100 / count($wineryFieldStepArray);
        $stepCellarProgress = 100 / count($wineryCellarStepArray);

        $oldFieldProgress = $wineryField->getProgress();
        $oldCellarProgress = $wineryCellar->getProgress();

        $updatedFieldProgress = $oldFieldProgress;
        $updatedCellarProgress = $oldCellarProgress;


        // WineryField section
        $filedStepCollection = $wineryFieldSteps
            ->filter(function (WineryFieldStep $step) use ($today) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() <= $today;
            });


        if (!$filedStepCollection->isEmpty()) {
            foreach ($filedStepCollection as $item) {
                $fieldDecideCollection = $item->getFieldStep()->getStepDecide()
                    ->filter(function (FieldStepDecide $fieldStepDecide) {
                        return $fieldStepDecide->getIsStepGratis() == true;
                    });
                $item->setIsStepConfirm(true);
//                $item->setPaymentStatus(true);
                $item->setChosenSolution($fieldDecideCollection->first());

                $updatedFieldProgress += $stepFieldProgress;
            }
        }

        // WineryCellar section
        $cellarStepCollection = $wineryCellarSteps
            ->filter(function (WineryCellarStep $step) use ($today) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() <= $today;
            });


        if (!$cellarStepCollection->isEmpty()) {
            foreach ($cellarStepCollection as $item) {

                $cellarDecideCollection = $item->getCellarStep()->getStepDecide()
                    ->filter(function (CellarStepDecide $cellarStepDecide) {
                        return $cellarStepDecide->getIsStepGratis() == true;
                    });
                $item->setIsStepConfirm(true);
//                $item->setPaymentStatus(true);
                $item->setChosenSolution($cellarDecideCollection->first());

                $updatedCellarProgress += $stepCellarProgress;
            }
        }

        $newFiledProgress = (int)ceil($updatedFieldProgress);

        if ($newFiledProgress > 100) {
            $newFiledProgress = 100;
        }


        $newCellarProgress = (int)ceil($updatedCellarProgress);

        if ($newCellarProgress > 100) {
            $newCellarProgress = 100;
        }
        $wineryField->setProgress($newFiledProgress);
        $wineryCellar->setProgress($newCellarProgress);

        return $winery;
    }


}