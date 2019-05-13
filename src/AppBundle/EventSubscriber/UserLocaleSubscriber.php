<?php
/**
 * Created by PhpStorm.
 * User: sligus
 * Date: 21.10.17
 * Time: 15:38
 */

namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * In order to update the language immediately after
     * a user has changed their language preferences, you also need to update
     * the session when you change the User entity.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface){
            $this->session->set('_locale', $event->getRequest()->getDefaultLocale());
        }
        if ($user instanceof \AppBundle\Model\UserInterface) {
            if (null !== $user->getLocale()) {
                $this->session->set('_locale', $user->getLocale());
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => array(array('onInteractiveLogin', 16)),
        );
    }
}