<?php

namespace AppBundle\Symfony\Component\Security\Core\Authorization;

use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * AuthorizationChecker is the main authorization point of the Security component.
 *
 * It gives access to the token representing the current user authentication.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AdvancedAuthorizationChecker extends AuthorizationChecker
{
    private $tokenStorage;
    private $accessDecisionManager;
    private $authenticationManager;
    private $alwaysAuthenticate;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager An AuthenticationManager instance
     * @param AccessDecisionManagerInterface $accessDecisionManager An AccessDecisionManager instance
     * @param bool                           $alwaysAuthenticate
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, AccessDecisionManagerInterface $accessDecisionManager, $alwaysAuthenticate = false)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->alwaysAuthenticate = $alwaysAuthenticate;
    }

    /**
     * Check if Token Storage has exist token
     *
     * @return bool
     */
    public function hasToken() {
        return (null === ($token = $this->tokenStorage->getToken()));

    }
}
