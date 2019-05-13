<?php

namespace AppBundle\Provider;

use AppBundle\Entity\User;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Customer\CustomerSelectorInterface;
use Sonata\IntlBundle\Locale\LocaleDetectorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sonata\Component\Basket\BasketSessionFactory;

/**
 *  Wine Product Provider
 */
class CustomerSelector implements CustomerSelectorInterface
{

    /**
     * @var \Sonata\Component\Customer\CustomerManagerInterface
     */
    private $customerManager;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $locale;
    /**
     * @var string
     */
    private $basketSessionFactory;

    /**
     * @param CustomerManagerInterface      $customerManager
     * @param SessionInterface              $session
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param LocaleDetectorInterface       $localeDetector
     * @param BasketSessionFactory $basketSessionFactory
     */
    public function __construct(
        CustomerManagerInterface $customerManager,
        SessionInterface $session,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        LocaleDetectorInterface $localeDetector,
        BasketSessionFactory $basketSessionFactory
    ) {
        $this->customerManager = $customerManager;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->locale = $localeDetector->getLocale();
        $this->basketSessionFactory = $basketSessionFactory;
    }
    /**
     * Get the customer.
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    public function get()
    {
        $customer = null;
        $user = null;

        $token = $this->tokenStorage->getToken();

        if (!is_null($token) && !$this->tokenStorage->getToken() instanceof AnonymousToken) {

            if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {


                $user = $this->tokenStorage->getToken()->getUser();

                if (!$user instanceof User && !$user instanceof \Symfony\Component\Security\Core\User\User) {
                    throw new \RuntimeException('User must be an instance of Symfony\Component\Security\Core\User\UserInterface');
                }

                if (!$user instanceof \Symfony\Component\Security\Core\User\User){
                    $customer = $this->customerManager->findOneBy([
                        'user' => $user->getId()
                    ],['id' => 'DESC']);
                }

            }

        }

        if (!$customer) {
            $basket = $this->getBasket();

            if ($basket && $basket->getCustomer()) {
                $customer = $basket->getCustomer();
            }
        }

        if (!$customer) {
            $customer = $this->customerManager->create();
        }

        if (!$customer->getLocale()) {
            $customer->setLocale($this->locale);
        }

        if ($user && $customer) {
            $customer->setUser($user);
            $basket = $this->basketSessionFactory->load($customer);
            if (!$user instanceof \Symfony\Component\Security\Core\User\User){
                $basket->setCustomer($customer);
            }
        }

        return $customer;
    }

    /**
     * @return \Sonata\Component\Basket\BasketInterface
     */
    private function getBasket()
    {
        return $this->session->get('sonata/basket/factory/customer/new');
    }
}