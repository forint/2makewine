<?php

namespace AppBundle\Doctrine;


use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Security\UserProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class HashPasswordListener implements EventSubscriber
{
    private $encoderFactory;

    private $tokenStorage;
    /**
     * HashPasswordListener constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, TokenStorageInterface $tokenStorage)
    {
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof  User) {
            return;
        }

        $this->encodePassword($entity);

    }
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof  User) {
            return;
        }

        $this->encodePassword($entity);

        /** Pass User Object to Token Storage for True Validate Password where Change Password */

        //$this->tokenStorage->getToken()->setUser($entity);

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    public function getSubscribedEvents()
    {
        return [
            'preValidate',
            'prePersist',
            'preUpdate'
        ];
    }


    /**
     * @param User $entity
     */
    public function encodePassword(User $entity)
    {
        $plainPassword = $entity->getPlainPassword();

        if (0 === strlen($plainPassword)) {
            return;
        }

        $encoder = $this->encoderFactory->getEncoder($entity);

        if ($encoder instanceof BCryptPasswordEncoder) {
            $entity->setSalt(null);
        } else {
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            $entity->setSalt($salt);
        }

        $hashedPassword = $encoder->encodePassword($plainPassword, $entity->getSalt());
        $entity->setPassword($hashedPassword);

        // $entity->eraseCredentials();

        // its fail because its FAIL !
        // $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword()));
    }

}