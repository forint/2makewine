<?php


namespace AppBundle\Service;


use AppBundle\Entity\FieldStepDecide;
use AppBundle\Entity\Winery;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryFieldStep;
use DateTime;

class CreateNotificationService
{

    public function createNotification(Winery $winery)
    {

        $notification = null;
        $today = new \DateTime('today');
        $future = $today->modify('+5 day');

        $wineryField = $winery->getWineryField();
        $wineryCellar = $winery->getWineryCellar();

        $wineryFieldSteps = $wineryField->getSteps();
        $fieldStep = null;
        $wineryCellarSteps = $wineryCellar->getSteps();
        $cellarStep = null;


        $wineryFieldStepArray = $wineryFieldSteps->toArray();
        $wineryCellarStepArray = $wineryCellarSteps->toArray();


        // WineryField section
        $filedStepCollection = $wineryFieldSteps
            ->filter(function (WineryFieldStep $step) use ($future) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() <= $future;
            });

        $filedApprovedStepCollection = $wineryFieldSteps
            ->filter(function (WineryFieldStep $step)  {
                return $step->getIsStepConfirm() == true && $step->getPaymentStatus() == true;
            });

        // WineryCellar section
        $cellarStepCollection = $wineryCellarSteps
            ->filter(function (WineryCellarStep $step) use ($future) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() <= $future;
            });

        $cellarApprovedStepCollection = $wineryCellarSteps
            ->filter(function (WineryCellarStep $step)  {
                return $step->getIsStepConfirm() == true && $step->getPaymentStatus() == true;
            });

/*
        if (!$filedStepCollection->isEmpty()) {
            $fieldStep = $filedStepCollection->first();

//            foreach ($filedStepCollection as $item) {
//                $fieldDecideCollection = $item->getFieldStep()->getStepDecide()
//                    ->filter(function (FieldStepDecide $fieldStepDecide) {
//                        return $fieldStepDecide->getIsStepGratis() == true;
//                    });
//                $item->setIsStepConfirm(true);
////                $item->setPaymentStatus(true);
//                $item->setChosenSolution($fieldDecideCollection->first());
//
//            }
        }
        */


//        exit;

        $notification['winery_id'] = $winery->getId();
        $notification['fieldStep_for_approval'] = $filedStepCollection;
        $notification['fieldStep_approved'] = $filedApprovedStepCollection;
        $notification['cellarStep_for_approval'] = $cellarStepCollection;
        $notification['cellarStep_approved'] = $cellarApprovedStepCollection;
        return $notification;
    }

}