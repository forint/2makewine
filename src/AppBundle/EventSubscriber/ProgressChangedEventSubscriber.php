<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\ProgressChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class ProgressChangedEventSubscriber implements EventSubscriberInterface
{


    public function onProgressChanged(ProgressChangedEvent $event)
    {
        printf("Stock %s progress changed to %d", (string)$event->getChild(), $event->getProgress());
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(

//            KernelEvents::REQUEST => array(array('onKernelRequest', 47)),
            ProgressChangedEvent::PROGRESS_CHANGED_EVENT => 'onProgressChanged',
        );
    }
}