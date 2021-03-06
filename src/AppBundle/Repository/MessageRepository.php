<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Get Conversation Unread Messages
     *
     * @param User $user
     *
     * @return array
     */
    public function getConversationUnreadMessages(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('m');

        $result = $queryBuilder
            ->select('m')
            ->innerJoin('AppBundle:Conversation', 'c', 'WITH', 'm.conversation = c.id')
            ->innerJoin('AppBundle:User', 'u', 'WITH', 'u.id = c.user OR u.id = c.relatedUser')
            ->andWhere('u.id = :user AND m.isRead = :unread AND m.user != :user')
            ->setParameters([
                'user' => $user,
                'unread' => '0'
            ])
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;

    }
    /**
     * Gets the messages created before current
     *
     * @param Message $message
     *
     * @return array
     */
    public function findLessByCurrentMeesages(Message $message)
    {
        $queryBuilder = $this->createQueryBuilder('m');

        $result = $queryBuilder
            ->select('m')
            ->where(
                $queryBuilder->expr()->lte('m.id', $message->getId())
            )
            ->getQuery()
            ->getResult();

        return $result;

    }
}
