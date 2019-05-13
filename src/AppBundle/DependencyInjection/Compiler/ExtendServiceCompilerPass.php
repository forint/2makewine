<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class ExtendServiceCompilerPass
 */
class ExtendServiceCompilerPass implements CompilerPassInterface
{

    /**
     * Overwrite project specific services
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $formValidator = $container->getDefinition('form.type_extension.form.validator');
        $formValidator->setClass('AppBundle\Symfony\Component\Form\Extension\Validator\Type\AdvancedFormTypeValidatorExtension');

        /*$securityAuthorizationChecker = $container->getDefinition('security.authorization_checker');
        $securityAuthorizationChecker->setClass('AppBundle\Symfony\Component\Security\Core\Authorization\AdvancedAuthorizationChecker');*/

        $fosUserListener = $container->getDefinition('fos_user.user_listener');
        $fosUserListener->setClass('AppBundle\Doctrine\UserListener');
    }
}