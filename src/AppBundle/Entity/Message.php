<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Annotation\Xss as Xss;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="chat_message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class Message
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
     */
    private $conversation;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     *
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     * @Xss\AllowedTags(tagNames={"<b>", "<i>", "<u>", "<ol>", "<ul>", "<li>", "<p>"})
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRead", type="boolean")
     */
    private $isRead;

    /**
     * @var File
     * @Assert\File(
     *     maxSize="4M",
     *     mimeTypesMessage = "Please choose the correct picture type!",
     *     mimeTypes={ "image/jpg", "image/jpeg", "image/png", "image/gif" })
     * )
     * @Vich\UploadableField(mapping="message_attachment", fileNameProperty="attachment")
     */
    private $attachmentFile;

    /**
     * @ORM\Column(name="attachment", type="string", nullable=true)
     * @Assert\File(mimeTypes={ "image/png","image/jpeg","image/bmp","image/gif","application/pdf","application/x-pdf" })
     */
    private $attachment;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set conversation
     *
     * @param Conversation $conversation
     *
     * @return Message
     */
    public function setConversation(Conversation $conversation)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * Get conversation
     *
     * @return int
     */
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Message
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Message
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     *
     * @return Message
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return bool
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param mixed $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setAttachmentFile(File $attachmentFile = null)
    {
        $this->attachmentFile = $attachmentFile;

        if ($attachmentFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function getAttachmentFile()
    {
        return $this->attachmentFile;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime("now"));
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime("now"));
    }

}

