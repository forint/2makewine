<?php

namespace AppBundle\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class YourExceptionListener
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /*public function onPdoException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();


        if ($exception instanceof \Sonata\AdminBundle\Exception\ModelManagerException || $exception->getPrevious() instanceof \Sonata\AdminBundle\Exception\ModelManagerException) {
            //now you can do whatever you want with this exception
            $this->session->getFlashBag()->add('error', 'PDO Exception :');

            dump($this->session);

        }
    }*/

    public function onPdoException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        $response = $event->getResponse();
        $request = $event->getRequest();
        $message = 'Integrity constraint violation: Cannot delete or update a parent row: a foreign key constraint';


        if ($exception->getPrevious() instanceof ForeignKeyConstraintViolationException) {
            //now you can do whatever you want with this exception
            $this->session->getFlashBag()->add('error', 'PDO Exception: ' . $message);

            $response = new RedirectResponse($request->headers->get('referer'));
            $event->setResponse($response);
        }

    }
}
