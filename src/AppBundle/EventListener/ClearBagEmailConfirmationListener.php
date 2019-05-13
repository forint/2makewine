<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class ClearBagEmailConfirmationListener implements EventSubscriberInterface
{
    private $flashBag;
    /**
     * UserRegisterListener constructor.
     */
    public function __construct(FlashBag $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onClearFlashBag',
        );
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function onClearFlashBag(FilterUserResponseEvent $event)
    {
        /** @var $this->flashBag Symfony\Component\HttpFoundation\Session\Flash\FlashBag */
        $this->flashBag->get('success');
    }
}
