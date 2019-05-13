<?php

namespace AppBundle\Service;

use AppBundle\Entity\CellarStepDecide;
use AppBundle\Entity\FieldStepDecide;
use AppBundle\Entity\Winery;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryFieldStep;
use Doctrine\ORM\EntityManager;


class WineryApproveStepService
{
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function approveStep(Winery $winery, $decideId, $fieldStepId)
    {
        $wineryField = $winery->getWineryField();
        $wineryFieldStep = $winery->getWineryField()->getSteps()->toArray();
        $stepFieldProgress = 100 / count($wineryFieldStep);
        $oldFieldProgress = $wineryField->getProgress();



        # -- Field Section -- #
        $wineryFieldStepCollection = $winery->getWineryField()->getSteps()
            ->filter(function (WineryFieldStep $wineryFieldStep) use ($fieldStepId) {
                return $wineryFieldStep->getFieldStep()->getId() == $fieldStepId;
            });

        $wineryFieldStep = $wineryFieldStepCollection->first();

        $decideCollection = $wineryFieldStep->getFieldStep()->getStepDecide()
            ->filter(function (FieldStepDecide $fieldStepDecide) use ($decideId) {
                return $fieldStepDecide->getId() == $decideId;
            });

        $stepDecide = $decideCollection->first();
        # -- Field Section -- #



        $newFiledProgress = (int)ceil($oldFieldProgress + $stepFieldProgress);

        if ($newFiledProgress > 100) {
            $newFiledProgress = 100;
        }

        $wineryField->setProgress($newFiledProgress);
        $wineryFieldStep->setIsStepConfirm(true);
        $wineryFieldStep->setPaymentStatus(true);
        $wineryFieldStep->setChosenSolution($stepDecide);

        return $winery;
    }

    public function approveCellarStep(Winery $winery, $decideId, $cellarStepId)
    {
        $wineryCellar = $winery->getWineryCellar();
        $wineryCellarStep = $winery->getWineryCellar()->getSteps()->toArray();
        $stepCellarProgress = 100 / count($wineryCellarStep);
        $oldCellarProgress = $wineryCellar->getProgress();



        # -- Cellar Section -- #
        $wineryCellarStepCollection = $winery->getWineryCellar()->getSteps()
            ->filter(function (WineryCellarStep $wineryCellarStep) use ($cellarStepId) {
                return $wineryCellarStep->getCellarStep()->getId() == $cellarStepId;
            });

        $wineryCellarStep = $wineryCellarStepCollection->first();

        $decideCollection = $wineryCellarStep->getCellarStep()->getStepDecide()
            ->filter(function (CellarStepDecide $cellarStepDecide) use ($decideId) {
                return $cellarStepDecide->getId() == $decideId;
            });

        $stepDecide = $decideCollection->first();
        # -- Cellar Section -- #



        $newCellarProgress = (int)ceil($oldCellarProgress + $stepCellarProgress);

        if ($newCellarProgress > 100) {
            $newCellarProgress = 100;
        }

        $wineryCellar->setProgress($newCellarProgress);
        $wineryCellarStep->setIsStepConfirm(true);
        $wineryCellarStep->setPaymentStatus(true);
        $wineryCellarStep->setChosenSolution($stepDecide);

        return $winery;
    }
}
