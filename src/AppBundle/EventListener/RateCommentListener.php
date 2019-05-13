<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\RateComment;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class RateCommentListener{
    public function postPersist(RateComment $comment, LifecycleEventArgs $event){
        $this->calculateWineRate($comment, $event);
    }
    public function postUpdate(RateComment $comment, LifecycleEventArgs $event){
        $this->calculateWineRate($comment, $event);
    }

    public function calculateWineRate(RateComment $comment, LifecycleEventArgs $event){
        $em = $event->getEntityManager();
        $rateRepository = $em->getRepository('AppBundle:RateComment');
        $wine = $comment->getWineProduct();
        $wineComments = $rateRepository->findByWineProduct($wine);
        $rateSum = 0;
        $rateCount = count($wineComments);
        foreach ($wineComments as $wineComment){
            $rateSum += $wineComment->getRate();
        }
        $rateResult = round($rateSum/$rateCount);
        $wine->setRating($rateResult);
        $em->persist($wine);
        $em->flush();
    }
}