<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Form\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use EcommerceBundle\CustomerBundle\Entity\Customer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{

    private $formFactory;

    private $entityManager;

    private $router;

    private $authorizationChecker;

    private $userPasswordEncoder;

    private $encoderFactory;

    private $container;

    private $inMemoryUserProvider;

    private $requestStack;

    private $sessionStrategy;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param ContainerInterface $container
     * @param InMemoryUserProvider $inMemoryUserProvider
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EncoderFactoryInterface $encoderFactory
     * @param RequestStack $requestStack
     * @param SessionAuthenticationStrategyInterface $sessionStrategy
     */
    public function __construct(
        ContainerInterface $container,
        InMemoryUserProvider $inMemoryUserProvider,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        AuthorizationCheckerInterface $authorizationChecker,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EncoderFactoryInterface $encoderFactory,
        RequestStack $requestStack,
        SessionAuthenticationStrategyInterface $sessionStrategy
    )
    {
        $this->container            = $container;
        $this->formFactory          = $formFactory;
        $this->entityManager        = $entityManager;
        $this->router               = $router;
        $this->authorizationChecker = $authorizationChecker;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->encoderFactory = $encoderFactory;
        $this->inMemoryUserProvider = $inMemoryUserProvider;
        $this->requestStack = $requestStack;
        $this->sessionStrategy = $sessionStrategy;
    }

    public function getCredentials(Request $request)
    {
        if (($request->getPathInfo() != '/login' &&
             $request->getPathInfo() != '/it/login' &&
             $request->getPathInfo() != '/shop/login' &&
             $request->getPathInfo() != '/it/shop/login'
            ) ||
             !$request->isMethod('POST')) {
            return;
        }

        $form = $this->formFactory->create(LoginFormType::class);
        $form->handleRequest($request);

        $data = $form->getData();

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['_username']
        );

        if ($request->getPathInfo() == '/shop/login' || $request->getPathInfo() == '/it/shop/login'){
            $request->getSession()->remove("_security.main.target_path");
        }

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];

        $dbUser = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(["email" => $username, "enabled" => true]);

        if (!$dbUser) {

            /** @var InMemoryUserProvider $inMemoryUsers */
            $inMemoryUsers = $this->container->get('security.user.provider.concrete.in_memory');
            $inMemoryUser = $inMemoryUsers->loadUserByUsername($username);

            return $inMemoryUser;
        } else {

            return $dbUser;

        }

        return false;

    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($user instanceof User) {

            /** Redirect User to Profile */
            $request = $this->requestStack->getCurrentRequest();

            if ($request->getPathInfo() != '/shop/login' && $request->getPathInfo() != '/it/shop/login'){
                $this->container->get('session')->set('_security.main.target_path', $this->router->generate('product_all'));
            }

            return $this->userPasswordEncoder->isPasswordValid($user,
                $credentials['_password'], $user->getSalt());
        } else {

            /** Set target path for user in memory  */
            $this->container->get('session')->set('_security.main.target_path', 'admin');

            $memoryUser
                = $this->inMemoryUserProvider->loadUserByUsername($credentials['_username']);

            return $this->userPasswordEncoder->isPasswordValid($memoryUser,
                $credentials['_password']);
        }

        return false;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }

    /**
     * Override to change what happens after a bad username/password is submitted.
     *
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->getSession() instanceof SessionInterface) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $this->getLoginUrl();
        if ($request->getPathInfo() == "/shop/login"){
            $url = $this->router->generate('app_basket_security_login');
        }

        return new RedirectResponse($url);
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->getPathInfo() == '/shop/login' || $request->getPathInfo() == '/it/shop/login'){
            return $this->router->generate('app_basket_delivery');
        }

        return $this->router->generate('product_all');
    }

    /**
     * Override to change what happens after successful authentication.
     * Set user into Basket Customer Object if exist
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($request->getSession() instanceof SessionInterface) {

            if ($request->getSession()->has("sonata/basket/factory/customer/new") && $request->getSession()->get("sonata/basket/factory/customer/new")->getCustomer() instanceof Customer){

                /** @var \EcommerceBundle\CustomerBundle\Entity\Customer $customer */
                $customer = $request->getSession()->get("sonata/basket/factory/customer/new")->getCustomer();

                $customer->setUser($token->getUser());
                $customer->setFirstname($token->getUser()->getUsername());
                $customer->setLastname($token->getUser()->getLastname());
                $customer->setEmail($token->getUser()->getEmail());

                $this->container->get('sonata.customer.manager')->save($customer);

                /** @var \Sonata\Component\Basket\Basket $basket */
                $basket = $this->container->get('sonata.basket');
                $basket->reset(false); // remove delivery and payment information
                $basket->clean(); // clean the basket
                // $basket->setBasketElements($request->getSession()->get("sonata/basket/factory/customer/new")->getBasketElements());
                $basket->setCustomer($customer);
                $this->container->get('sonata.basket.factory')->save($basket);

                /** Remove new customer from session */
                $this->container->get('session')->remove('sonata/basket/factory/customer/new');
            }
        }

        return parent::onAuthenticationSuccess( $request, $token, $providerKey );
    }
}