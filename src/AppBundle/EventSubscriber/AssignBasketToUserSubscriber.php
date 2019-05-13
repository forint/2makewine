<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

class AssignBasketToUserSubscriber implements EventSubscriberInterface
{
    private $session;

    private $container;

    public function __construct(SessionInterface $session, ContainerInterface $container)
    {
        $this->session = $session;
        $this->container = $container;
    }
    /**
     * Assign basket & basket elements to user
     *
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        // $basket = $this->container->get('sonata.basket');
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => array(array('onInteractiveLogin', 17)),
        );
    }
}