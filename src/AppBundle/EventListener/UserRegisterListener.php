<?php

namespace AppBundle\EventListener;

// use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\GroupManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisterListener implements EventSubscriberInterface
{

    /**
     * @var GroupManagerInterface
     */
    private $groupManager;

    /**
     * UserRegisterListener constructor.
     *
     * @param GroupManagerInterface $groupManager
     */
    public function __construct(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationAddDefaultUserGroup',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationAfterUserCreateRedirect'
        ];
    }

    public function onRegistrationAddDefaultUserGroup( UserEvent $event )
    {
        /** Retrieve User Group for Assign It with New User Created */
        $userGroup = $this->groupManager->findGroupByName('user');
        $user = $event->getUser();
   
        if ($user && $userGroup) {

            /** Initialize User Group for Created User */
            $user->addGroup($userGroup);
        }
    }

    /**
     * Redirect user after success registration to corresponding page
     */
    public function onRegistrationAfterUserCreateRedirect(FilterUserResponseEvent $event){

        //if ($event->getRequest()->getPathInfo() == "/shop/registration")
        //    $event->setResponse(new RedirectResponse($event->getRequest()->getPathInfo()));


    }
}