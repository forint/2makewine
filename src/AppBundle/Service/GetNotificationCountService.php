<?php

namespace AppBundle\Service;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Entity\Winery;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryFieldStep;
use AppBundle\Repository\ConversationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GetNotificationCountService
{
    /**
     * @var EntityManager $em
     */
    private $em;
    /**
     * @var ConversationRepository $conversationRepository
     */
    private $conversationRepository;

    /**
     * @var array $harvester
     */
    private $harvester = [];

    /**
     * GetNotificationCountService constructor.
     *
     * @param EntityManager $entityManager
     * @param ConversationRepository $conversationRepository
     */
    public function __construct(EntityManager $entityManager, ConversationRepository $conversationRepository)
    {
        $this->em = $entityManager;
        $this->conversationRepository = $conversationRepository;
    }

    public function getCount(User $user = null)
    {
        $count = 0;

        if (!$user) {
            return $count;
        }

        $wineries = $user->getWineries();

        if (!$wineries) {
            return $count;
        }

        foreach ($wineries as $item) {
            $count += $this->getCountNotification($item);
        }

        return $count;
    }

    public function getCountMessage(User $user = null)
    {
        $count = 0;

        if (!$user) {
            return $count;
        }

        $conversations = $this->conversationRepository->getMyConversation($user);

        if (!$conversations) {
            return $count;
        }

        $resultCount = [];
        $resultConversations = [];

        /**
         * Collect conversations related with current user
         */
        foreach ($conversations as $conversation) {
            $tempHarvest = $conversation->getMessages()
                ->filter(function (Message $message) use ($user, $conversation) {
                    if ($message->getUser() != $user) {
                        return true;
                    }
                });

            foreach ($tempHarvest as $temp) {
                $this->harvester[] = $temp->getConversation();
            }
        }

        foreach ($conversations as $item) {

            $tempCount = $item->getMessages()
                ->filter(function (Message $message) use ($user, $item) {
                    if ($message->getUser() != $user && $message->getIsRead() == false) {
                        return true;
                    }

                });

            foreach ($tempCount as $temp) {
                $resultConversations[] = $temp->getConversation();

            }
            foreach ($tempCount as $temp) {
                $resultCount[] = $temp;
            }

            /**
             * Intersection full-unread conversations with conversations which related with not current admin
             */
            if (isset($resultConversations) && sizeof($resultConversations) > 0) {
                $intersection = array_intersect($resultConversations, $this->harvester);
                if (!empty($intersection)) {
                    foreach ($intersection as $value) {
                        foreach ($tempCount as $key => $temper) {
                            if ($temper == $value) {
                                unset($tempCount[$key]);
                            }
                        }
                    }
                }
            }
        }


//        foreach ($tempCount as $temp) {
//            $resultCount[] = $temp;
//        }


        return count($resultCount);
    }

    public function getCountNotification(Winery $winery)
    {
        $cont = 0;

        $today = new \DateTime('today');
        $future = $today->modify('+5 day');

        $wineryField = $winery->getWineryField();
        $wineryCellar = $winery->getWineryCellar();
        $wineryFieldSteps = $wineryField->getSteps();
        $wineryCellarSteps = $wineryCellar->getSteps();

        // WineryField section
        $filedStepCollection = $wineryFieldSteps
            ->filter(function (WineryFieldStep $step) use ($future) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() <= $future;
            });

        // WineryCellar section
        $cellarStepCollection = $wineryCellarSteps
            ->filter(function (WineryCellarStep $step) use ($future) {
                return $step->getIsStepConfirm() == false && $step->getDeadline() !== null && $step->getDeadline() <= $future;
            });

        $cont = $filedStepCollection->count() + $cellarStepCollection->count();

        return $cont;
    }

    /**
     * Return counter unread message, whose conversations not related with current user
     *
     * @param User|null $user
     * @return int
     */
    public function getCountMessageForNotCurrentUser(User $user = null)
    {

        $resultCount = [];

        if (!$user) {
            return 0;

        }


        if ($user->hasRole('ROLE_ADMIN')) {
            foreach ($this->harvester as $item) {
                $tempCount = $item->getMessages()
                    ->filter(function (Message $message) use ($user, $item) {
                        if ($message->getUser() != $user && $message->getIsRead() == false) {
                            return true;
                        }
                    });

                foreach ($tempCount as $temp) {
                    $resultCount[] = $temp;
                }
            }
        }


        return count($resultCount);
    }
}