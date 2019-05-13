<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShopLoginUpdatePathInfoListener
{

    private $container;

    /**
     * AuthenticationHandler constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Check if current request is sonata_basket_update
     * set security_logout to REQUEST_URI for pass security strategy
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /*if ( $event->getRequest()->getPathInfo() == '/update'){
            $event->getRequest()->server->set('REQUEST_URI', '/logout');
            $event->getRequest()->initialize(
                $event->getRequest()->query->all(),
                $event->getRequest()->request->all(),
                $event->getRequest()->attributes->all(),
                $event->getRequest()->cookies->all(),
                $event->getRequest()->files->all(),
                $event->getRequest()->server->all(),
                $event->getRequest()->getContent()
            );
        }*/
    }

    public static function getSubscribedEvents()
    {
        return array(
                KernelEvents::REQUEST => array(array('onKernelRequest', 9))
        );
    }

}