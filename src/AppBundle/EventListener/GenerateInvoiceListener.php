<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\Component\Event\PaymentEvent;
use Sonata\Component\Event\PaymentEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Transformer\InvoiceTransformer;

class GenerateInvoiceListener
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

    public function onInvokePostSendBank(PaymentEvent $event)
    {
        $order = $event->getOrder();

        if (null === $order) {
            throw new AccessDeniedException();
        }

        $invoice = $this->getInvoiceManager()->create();

        $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);
        $this->getInvoiceManager()->save($invoice);

    }

    public static function getSubscribedEvents()
    {
        return array(
            PaymentEvents::POST_SENDBANK => array(array('onInvokePostSendBank', 9))
        );
    }

    /**
     * @return InvoiceManagerInterface
     */
    protected function getInvoiceManager()
    {
        return $this->container->get('sonata.invoice.manager');
    }

    /**
     * @return OrderManagerInterface
     */
    protected function getOrderManager()
    {
        return $this->container->get('sonata.order.manager');
    }

    /**
     * @return InvoiceTransformer
     */
    protected function getInvoiceTransformer()
    {
        return $this->container->get('sonata.payment.transformer.invoice');
    }
}