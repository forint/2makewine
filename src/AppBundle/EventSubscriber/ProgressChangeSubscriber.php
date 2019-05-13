<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\WineryCellar;
use AppBundle\Entity\WineryField;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;


class ProgressChangeSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
        );
    }


    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof WineryField) {
            $entityManager = $args->getEntityManager();
            dump($entity);
            exit;
            // ... do something with the Product
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

        $this->index($args);
    }



}