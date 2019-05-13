<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Conversation
 *
 * @ORM\Table(name="chat_conversation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConversationRepository")
 * @UniqueEntity(
 *     fields={"user", "relatedUser"},
 *     message="This conversation is already exists."
 * )
 */
class Conversation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="User",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="User",cascade={"persist"})
     * @ORM\JoinColumn(name="related_id", referencedColumnName="id")
     */
    private $relatedUser;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $messages;

    /**
     * @var bool
     */
    private $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    private $counter;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->relatedUser = new ArrayCollection();
        $this->createdAt = new \DateTime("now");
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getRelatedUser()
    {
        return $this->relatedUser;
    }

    /**
     * @param string $relatedUser
     */
    public function setRelatedUser($relatedUser)
    {
        $this->relatedUser = $relatedUser;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Conversation
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages ?: $this->messages = new ArrayCollection();
    }

    /**
     * Set message
     *
     * @param $message
     */
    public function setMessages($message)
    {
        // die('why this method not run ?');
        if (is_array($message)) {
            $this->messages = $message;
        } else {
            $this->messages->clear();
            $this->messages->add($message);
        }
    }

    /**
     * Add message
     *
     * @param Message $message
     *
     * @return Conversation
     */
    public function addMessage(Message $message)
    {
        $message->setConversation($this);
        $this->messages->add($message);

        return $this;
    }

    /**
     * Remove message
     *
     * @param Message $message
     *
     */
    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Return Read/Unread count of conversation messages
     *
     * @return array
     */
    public function getCounter()
    {
        $messages = [
            'read' => 0,
            'unread' => 0
        ];

        foreach ($this->messages as $message) {
            if ($message->getIsRead()) {
                $messages['read']++;
            } else {
                $messages['unread']++;
            }
        }

        return $messages;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist($conversation)
    {
        $this->preUpdate($conversation);
        $this->setCreatedAt(new \DateTime("now"));
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate($conversation)
    {
        $conversation->setMessages($conversation->getMessages());
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

}
