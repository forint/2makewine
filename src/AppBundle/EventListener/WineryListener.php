<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\WineryField;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class WineryListener
{

    public function preFlush($child, PreFlushEventArgs $event)
    {


        $name = lcfirst((new \ReflectionClass($child))->getShortName());

        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $repo = $em->getRepository('AppBundle:Winery');
        $winery = $repo->findOneBy([$name => $child->getId()]);

        if ($winery) {
            $wineryField = $winery->getWineryField();
            $wineryFieldProcess = $wineryField->getProgress();

            $wineryCellar = $winery->getWineryCellar();
            $wineryCellarProcess = $wineryCellar->getProgress();


            $temp = 0;
            $temp = (int)ceil(($wineryFieldProcess + $wineryCellarProcess) / 2);


            if ($temp > 100) {
                $temp = 100;
            }

            $winery->setProgress($temp);
            $em->persist($winery);

            $metaData = $em->getClassMetadata(get_class($winery));
            $uow->computeChangeSet($metaData, $winery);
        }

    }
}