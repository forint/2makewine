<?php

namespace AppBundle\TranslationActivator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Translation\Bundle\EditInPlace\ActivatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class RoleActivator implements ActivatorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;



    public function __construct( AuthorizationCheckerInterface $authorizationChecker)
    {
//        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function checkRequest(Request $request = null)
    {
        try {
//            dump($this->authorizationChecker->isGranted(['ROLE_SUPER_ADMIN']));

            return $this->authorizationChecker->isGranted(['ROLE_EDITOR']);
//            return true;
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }
}