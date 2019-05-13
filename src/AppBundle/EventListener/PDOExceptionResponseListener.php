<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Driver\PDOException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PDOExceptionResponseListener
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelResponse(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();

        $exception =  $event->getException();
        $message = $exception->getMessage();

        // Listen only on the expected exception
        if (!$exception instanceof PDOException) {
            return;
        }

        // You can make some checks on the message to return a different response depending on the MySQL error given.
        if (strpos($message, 'Integrity constraint violation')) {
            // Add your user-friendly error message
            $this->session->getFlashBag()->add('error', 'PDO Exception :'.$message);

        }
    }
}