<?php
namespace AppBundle\Provider;

use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\Component\Delivery\ServiceDeliverySelectorInterface;
use Sonata\Component\Product\Pool as ProductPool;

class DeliverySelector implements ServiceDeliverySelectorInterface
{
    /**
     * @var DeliveryPool
     */
    protected $deliveryPool;

    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param \Sonata\Component\Delivery\Pool $deliveryPool
     * @param \Sonata\Component\Product\Pool  $productPool
     */
    public function __construct(DeliveryPool $deliveryPool, ProductPool $productPool)
    {
        $this->productPool = $productPool;
        $this->deliveryPool = $deliveryPool;
    }

    /**
     * @return DeliveryPool
     */
    public function getDeliveryPool()
    {
        return $this->deliveryPool;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return ProductPool
     */
    public function getProductPool()
    {
        return $this->productPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableMethods(BasketInterface $basket = null, AddressInterface $deliveryAddress = null)
    {
        $instances = [];

        if (!$basket) {
            return $instances;
        }

        $deliveryMethods = $this->getDeliveryPool()->getMethods();

        foreach ($deliveryMethods as $deliveryCode => $deliveryMethod){
            if ($deliveryMethod->getEnabled())
                $instances[$deliveryMethod->getCode()] = $deliveryMethod;
        }

       /* $this->log(sprintf('[sonata::getAvailableDeliveryMethods] product.id: %d - code : %s selected', $basketElement->getProductId(), $productDelivery->getCode()));

        $instances[$deliveryMethod->getCode()] = $deliveryMethod;*/

        // STEP 2 : We select the delivery methods with the highest priority
        $instances = array_values($instances);
        usort($instances, [self::class, 'sort']);

        return $instances;
    }

    /**
     * @param ServiceDeliveryInterface $a
     * @param ServiceDeliveryInterface $b
     *
     * @return int
     */
    public static function sort(ServiceDeliveryInterface $a, ServiceDeliveryInterface $b)
    {
        if ($a->getPriority() === $b->getPriority()) {
            return 0;
        }

        return $a->getPriority() > $b->getPriority() ? -1 : 1;
    }

    /**
     * @param string $message
     */
    protected function log($message): void
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }
}