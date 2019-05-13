<?php

namespace AppBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UpdateConversationMessageListener implements EventSubscriberInterface
{
    /**
     * Messages IDs whose statuses were updated
     *
     * @var array
     */
    public $isRead = [];

    private $session;

    private $entityManager;

    private $object;

    private $container;

    /**
     * UpdateConversationMessageListener constructor.
     *
     * @param SessionInterface $session
     * @param EntityManagerInterface $entityManager
     *
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => array('onUpdateConversationMessagesIsRead', 24)
        );
    }

    /**
     * Update isRead property Message's entity after render template
     *
     * @param PostResponseEvent $postResponseEvent
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onUpdateConversationMessagesIsRead(PostResponseEvent $postResponseEvent)
    {

        if (isset($this->object) && is_array($this->object) && sizeof($this->object)) {
            foreach ($this->object as $existConversation) {
                foreach ($existConversation->getMessages() as $unreadMessage) {

                    $user = $this->container->get('security.token_storage')->getToken()->getUser();
                    if ($user->getId() !== $unreadMessage->getUser()->getId()) {
                        $unreadMessage->setIsRead(1);
                        $this->entityManager->persist($unreadMessage);
                        $this->entityManager->flush();
                        $this->isRead[$existConversation->getId()] = $unreadMessage->getId();
                    }

                }
            }

        }

        // $postResponseEvent->setData('updatedMessage', $this->isRead);
    }

    /**
     * Return messages IDs whose statuses were updated
     *
     * @return array
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    public function setObject(array $existNonSelfConversations)
    {
        $this->object = $existNonSelfConversations;
    }

}
