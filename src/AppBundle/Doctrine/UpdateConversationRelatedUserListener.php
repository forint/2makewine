<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Sonata\BlockBundle\Event\BlockEvent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateConversationRelatedUserListener implements EventSubscriberInterface
{
    private $container;

    private $entityManager;

    /**
     * UpdateConversationRelatedUserListener constructor.
     *
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;

    }

    public static function getSubscribedEvents()
    {
        return array(
            'sonata.admin.event.persistence.post_update' => array('onUpdateConversationRelatedUser', 14)
        );
    }

    /**
     * Update related user after persist conversation with admin role
     *
     * @param PersistenceEvent $event
     * @throws \Exception
     */
    public function onUpdateConversationRelatedUser(PersistenceEvent $event)
    {
        try {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $conversation = $event->getObject();

            if ($conversation instanceof Conversation && $conversation->getUser()->getId() == $conversation->getRelatedUser()->getId() && $user->hasRole('ROLE_ADMIN')) {

                $conversation->setRelatedUser($user);
                $this->entityManager->persist($conversation);
                $this->entityManager->flush();

                return;
            }

        }catch (\Exception $e) {
            throw $e;
        }
    }

}
