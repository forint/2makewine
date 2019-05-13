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
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Event\InvoiceTransformEvent;
use Sonata\Component\Event\TransformerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceAfterPaymentListener
{

    private $container;

    private $eventDispatcher;

    /**
     * @param ContainerInterface $container
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onInvokePostCallback(PaymentEvent $event)
    {
        $order = $event->getOrder();
        $invoice = $this->container->get('sonata.invoice.manager')->findOneBy(['reference' => $order->getReference()]);

        $invoice->setStatus(InvoiceInterface::STATUS_PAID);
        $event = new InvoiceTransformEvent($invoice);
        $this->eventDispatcher->dispatch(TransformerEvents::POST_ORDER_TO_INVOICE_TRANSFORM, $event);

    }

    public static function getSubscribedEvents()
    {
        return array(
            PaymentEvents::POST_CALLBACK => array(array('onInvokePostCallback', 7))
        );
    }

}