<?php

namespace EcommerceBundle\PaymentBundle\Payment;


use Psr\Container\ContainerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\BasePayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\ProductInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class StripeClient extends BasePayment
{

    protected $config;

    protected $em;

    protected $logger;

    protected $container;

    protected $templating;

    protected $session;

    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * StripeClient constructor.
     */
    public function __construct(
        $secretKey,
        array $config,
        EntityManagerInterface $em,
        ContainerInterface $container,
        EngineInterface $templating,
        RouterInterface $router,
        LoggerInterface $logger,
        SessionInterface $session
    )
    {
        \Stripe\Stripe::setApiKey($secretKey);

        $this->config = $config;
        $this->em = $em;
        $this->logger = $logger;
        $this->container = $container;
        $this->templating = $templating;
        $this->router = $router;
        $this->session = $session;

        $this->setName('Stripe');
        $this->setCode('stripe');
        $this->setEnabled(true);
    }

    public function sendbank(OrderInterface $order)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return new RedirectResponse($this->router->generate('payment_order', [
            'order' => $order
        ]));

//        return $this->redirectToRoute('payment_order', ['order' => $order]);

        /*dump($this->templating->render('SonataPaymentBundle:Payment:sendbank.html.twig', []));die;
        return $this->templating->render('SonataPaymentBundle:Payment:sendbank.html.twig', []);*/
    }

    public function createCharge($token, $amount, $paymentDescription, $transaction)
    {
        try {
            $charge = \Stripe\Charge::create([
                'amount' => $this->config['decimal'] ? $amount * 100 : $amount,
                'currency' => $this->config['currency'],
                'description' => $paymentDescription,
                'source' => $token
            ]);
        } catch (\Stripe\Error\Base $e) {
            // payment failed
            $errMess = strlen($e->getMessage()) > 0 ? lcfirst($e->getMessage()) : 'please try again.';
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_UNKNOWN);
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setInformation('Error while payment: '.$errMess);
            return false;
        }
        return true;
    }

    public function isCallbackValid(TransactionInterface $transaction)
    {
        // TODO: Implement isCallbackValid() method.
    }

    public function handleError(TransactionInterface $transaction)
    {
        $this->session->getFlashBag()->add(
            'payment-error',
            $transaction->getInformation()
        );
        $transactionParams = $transaction->getParameters();
        return new RedirectResponse($this->router->generate('payment_order', [
            'order' => $transactionParams["payment-form"]["order"]
        ]));
    }

    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        if ($transaction->getOrder()->isOpen()) {
            $transaction->getOrder()->setValidatedAt(new \DateTime());
            $transaction->getOrder()->setPaymentStatus($transaction->getStatusCode());
        }
        return new Response('', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function isRequestValid(TransactionInterface $transaction)
    {
        return true;
    }

    public function isBasketValid(BasketInterface $basket)
    {
        return true;
    }

    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        // TODO: Implement isAddableProduct() method.
    }

    public function applyTransactionId(TransactionInterface $transaction)
    {
        //$transactionId = $transaction->get('txn_id', null);
        $transactionParameters = $transaction->getParameters();
        $transactionId = $transactionParameters["payment-form"]["token"];
        if (!$transactionId) {
            // no transaction id provided
            $transactionId = -1;
        }

        $transaction->setTransactionId($transactionId);
    }

    public function getOrderReference(TransactionInterface $transaction)
    {
        return $transaction->get('reference');
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'stripe';
    }

    public function callback(TransactionInterface $transaction)
    {
        // check if the order exists
        if (!$transaction->getOrder()) {
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_UNKNOWN);
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setInformation('The order does not exist');

            return $this->handleError($transaction);
        }

        // check if the request is valid
        if (!$this->isRequestValid($transaction)) {
            $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_REQUEST);
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setInformation('The request is not valid');

            return $this->handleError($transaction);
        }

        // init payment process
        $transactionParams = $transaction->getParameters();
        if (!$this->createCharge(
            $transactionParams["payment-form"]["token"],
            $transactionParams["payment-form"]["amount"],
        "order #".$transactionParams["payment-form"]["order"],
            $transaction

        )) {
            return $this->handleError($transaction);
        } else {
            $transaction->setState(TransactionInterface::STATE_OK);
            $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);
        }

        // apply the transaction id
        $this->applyTransactionId($transaction);

        // if the order is not open, then something already happen ... (duplicate callback)
        if (!$transaction->getOrder()->isOpen()) {
            $transaction->setState(TransactionInterface::STATE_OK); // the transaction is valid, but not the order state
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_NOT_OPEN);
            $transaction->setInformation('The order is not open, then something already happen ... (duplicate callback)');

            return $this->handleError($transaction);
        }

        // send the confirmation request to the bank
        if (!($response = $this->sendConfirmationReceipt($transaction))) {
            $transaction->setInformation('Fail to send the confirmation receipt');

            $response = $this->handleError($transaction);
        }

        return $response;
    }

}