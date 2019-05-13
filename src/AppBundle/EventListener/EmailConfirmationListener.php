<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailConfirmationListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;

    /**
     * EmailConfirmationListener constructor.
     *
     * @param MailerInterface         $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @param UrlGeneratorInterface   $router
     * @param SessionInterface        $session
     */
    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => array('onRegistrationSuccess', -14),
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();
        $request = $event->getRequest();

        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->mailer->sendConfirmationEmailMessage($user, 'app');

        $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());


        if (preg_match('/[\/(en|it)]*\/register/', $request->getPathInfo())){
            $url = $this->router->generate('fos_user_registration_check_email');
        }else{
            $url = $this->router->generate('app_user_registration_check_email');
        }

        if (preg_match('/[\/(en|it)]*\/shop\/registration/', $request->getPathInfo())){
            $url = $this->router->generate('app_user_shop_registration_check_email');
        }

        $event->setResponse(new RedirectResponse($url));
    }
}
