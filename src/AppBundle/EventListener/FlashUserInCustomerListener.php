<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * Trigger when customer flush after persist operation
 */
class FlashUserInCustomerListener
{
    private $annotationReader;
    /**
     * FlashUserInCustomerListener constructor.
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Need detach entity User because persister try insert User entity when Customer flush
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        /** @var User $class */
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();



        /*foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User)
                $uow->detach($entity);
        }*/
        /*foreach ($uow->getScheduledEntityUpdates() as $entity) {
            dump('getScheduledEntityUpdates');
            dump($entity);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            dump('getScheduledEntityDeletions');
            dump($entity);
        }

        foreach ($uow->getScheduledCollectionDeletions() as $col) {
            dump('getScheduledCollectionDeletions');
            dump($col);
        }

        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            dump('getScheduledCollectionUpdates');
            dump($col);
        }*/
    }
}