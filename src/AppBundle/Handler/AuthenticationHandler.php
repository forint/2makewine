<?php

namespace AppBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationHandler implements LogoutSuccessHandlerInterface
{
    private $router;

    private $container;

    /**
     * AuthenticationHandler constructor.
     *
     * @param ContainerInterface $container
     * @param RouterInterface $router
     */
    public function __construct(ContainerInterface $container,RouterInterface $router)
    {
        $this->router = $router;
        $this->container = $container;
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $route = $request->attributes->get('_route');
        $referer = $request->headers->get('referer');

        if ($route == "app_basket_update" || $route == "sonata_basket_update" || $route == "sonata_basket_index"){
           $referer = $this->router->generate('app_basket_security_login');
        }

        return new RedirectResponse($referer);

    }
}