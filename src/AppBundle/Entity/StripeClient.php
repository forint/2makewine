<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Stripe;

class StripeClient
{
    private $config;
    private $em;
    private $logger;

    public function __construct($secretKey, array $config, EntityManagerInterface $em, LoggerInterface $logger)
    {
        \Stripe\Stripe::setApiKey($secretKey);
        $this->config = $config;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function createCharge($token, $amount, $paymentDescription)
    {
        try {
            $charge = \Stripe\Charge::create([
                'amount' => $this->config['decimal'] ? $amount * 100 : $amount,
                'currency' => $this->config['currency'],
                'description' => $paymentDescription,
                'source' => $token
            ]);
        } catch (\Stripe\Error\Base $e) {
            $this->logger->error(sprintf('%s exception encountered while proceed payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);

            throw $e;
        }
        $this->em->flush();
    }
}
