<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\UpdateConversationMessageListener;
use AppBundle\Entity\Conversation;
use AppBundle\Entity\Message;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\Winery;
use AppBundle\Form\ConversationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Queue\Exception;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\Translator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ChatController extends Controller
{
    private $message;

    private $logger;

    private $entityManager;

    protected $container;

    private $winemakers = [];

    private $selfConversations = [];

    /**
     * ChatController constructor
     *
     * @param LoggerInterface $logger
     * @param Message $message
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(LoggerInterface $logger, Message $message, EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->message = $message;
        $this->logger = $logger;
        $this->container = $container;
    }

    /**
     * Index Chat
     *
     * @Route("/chat", name="index")
     * @Template()
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {
        if ($this->getUser() instanceof User) {

            /** @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper $helper */
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            $form = $this->createForm(ConversationFormType::class);

            $conversations = $this->getDoctrine()
                ->getRepository('AppBundle:Conversation')
                ->findSelfAssign($this->getUser());

            $specials = $this->getSpecialUsers();

            $harvesterConversation = [];
            foreach ($conversations as $key => $conversation) {
                $harvesterConversation[] = [
                    $conversation->getUser()->getId(),
                    $conversation->getRelatedUser()->getId()
                ];
            }

            foreach ($conversations as $key => $conversation) {

                /**
                 * If Admin have conversation but user create another conversation,
                 * it means that need exclude some users by corresponding condition below
                 */
                if ($conversation->getUser()->getId() == $conversation->getRelatedUser()->getId() ) {
                    $previousHypotetical = [ $conversation->getUser()->getId(), $this->getUser()->getId() ];
                    $previousHypoteticalReverse = array_reverse($previousHypotetical);

                    if (in_array($previousHypotetical, $harvesterConversation) || in_array($previousHypoteticalReverse, $harvesterConversation)) {
                        unset($conversations[$key]);
                    }
                }

                /** Set conversation state to true, if we have unread messages */
                foreach ($conversation->getMessages() as $message) {
                    if ($message->getIsRead() == false) {
                        $conversation->setState(true);
                    }
                }

                /** Need remove some users if conversation includes their  */
                foreach ($specials as $specialKey=>$special){
                    if ($conversation->getUser()->getId() == $special->getId() || $conversation->getRelatedUser()->getId() == $special->getId()){
                        unset($specials[$specialKey]);
                    }
                }
            }

            $response = [
                'conversations' => $conversations,
                'users' => $specials,
                'formchat' => $form->createView()
            ];

        } else {
            throw new \Exception("Supreme administrator don't have privileges for access to current page");
        }

        return $response;
    }

    /**
     * Return users' collections for concrete view
     *
     * @return mixed|null
     */
    function getSpecialUsers() {

        if ($this->getUser()->hasGroup('Admin') || $this->getUser()->hasGroup('Administrator')){
            return $this->getOwners();
        }elseif($this->getUser()->hasGroup('Winemaker')){
            return $this->getSingleUsersBindedWithOwners();
        }else{
            return $this->getDoctrine()->getRepository('AppBundle:User')->getRelatedUsers($this->getRelatedWinemakers());
        }
    }

    /**
     * Retrieve users with User role for Winemaker View
     *
     * @return mixed|null
     */
    public function getSingleUsersBindedWithOwners()
    {
        $users = [];

        $vineyards = $this->getDoctrine()
            ->getRepository('AppBundle:Vineyard')
            ->findBy(array('winemaker' => $this->getUser()->getId()), array('id' => 'ASC'),null ,0);

        foreach ($vineyards as $vineyard) {

            $wineries = $this->getDoctrine()
                ->getRepository('AppBundle:Winery')->findBy(['vineyard' => $vineyard->getId()]);


            foreach ($wineries as $winery) {
                if ($winery) {
                    $userWineries = $this->getDoctrine()->getRepository('AppBundle:User')->getUserByWinery($winery);

                    foreach ($userWineries as $userWinery) {
                        $users[] = $userWinery;
                    }
                }
            }

            $users = array_unique($users);
        }

        return $users;

    }
    /**
     * Retrieve related users with Winemaker role for Simple User View
     *
     * @param User $user
     * @return mixed|null
     */
    public function getOwner(User $user)
    {
        $ownerUser = null;

        $roleOwner = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(array('name' => 'ROLE_WINEMAKER'));

        if ($roleOwner) {
            $ownerUser = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->getUserByRole($roleOwner, $user);

        }

        return $ownerUser;

    }

    /**
     * Retrieve all users with Winemaker role for Administrator View
     *
     * @return mixed|null
     */
    public function getOwners()
    {
        $ownerUser = null;

        $roleOwner = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(array('name' => 'ROLE_WINEMAKER'));

        if ($roleOwner) {
            $ownerUser = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->getUsersByRole($roleOwner);

        }

        return $ownerUser;

    }

    /**
     * Create Conversation
     *
     * @Route("/user/{user}/related/{related}/conversation/add/", name="create_conversation")
     * @ParamConverter("user", options={"mapping": {"user": "id"}})
     *
     * @param User $user
     * @param User $related
     * @param Translator $translator
     *
     * @return JsonResponse
     */
    public function createConversationAction(User $user, User $related = null, Translator $translator)
    {
        /** @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper $helper */
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()->build();

        $selfConversations = $this->getDoctrine()
            ->getRepository('AppBundle:Conversation')
            ->findAnd($user, $this->getUser(), $related);

        $username = 'Administration';
        if (!$user->hasGroup('Admin') && $this->getUser()->getId() != $user->getId()) {
            $username = $user->getFirstName();
        }

        /** If we have existing self conversation, so show message about it */
        if (isset($selfConversations) && is_array($selfConversations) && sizeof($selfConversations) > 0) {
            $conversation = $selfConversations['0'];

            $responseData = [
                'conversation' => $conversation,
                'user' => $this->getUser(),
                'message' => $translator->trans('chat.page.conversation.create.message.success', ['relatedUser' => $username])
                // 'message' => $translator->trans('chat.page.conversation.failure.create.message.success')
            ];


            if ($conversation->getUser()->getId() == $conversation->getRelatedUser()->getId()){
                $this->updateConversation($conversation, $this->getUser());

                $updateMessageListener = $this->get('app.doctrine.update_message_listener');
                $updateMessageListener->setObject([$conversation]);
                $responseData['updated'] = true;
            }

            $conversation = $this->setConversationTemporarityAvatar($conversation);
            $responseData['conversation'] = $this->setConversationTemporarityAvatar($conversation);

            $_response = $serializer->serialize($responseData, 'json');
            return new JsonResponse($_response);
        }

        /** @var Conversation $conversation */
        $conversation = new Conversation();
        $conversation->setUser($this->getUser());
        $conversation->setRelatedUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($conversation);
        $em->flush();

        $this->setConversationTemporarityAvatar($conversation);

        $_response = $serializer->serialize([
            'created' => true,
            'conversation' => $conversation,
            'user' => $this->getUser(),
            'message' => $translator->trans('chat.page.conversation.create.message.success', ['relatedUser' => $username])
        ], 'json');


        return new JsonResponse($_response);
    }

    /**
     * Add Conversation Message
     *
     * @Route("/chat/{conversation}/message/add", name="chat_message")
     * @ParamConverter("conversation", options={"mapping": {"conversation": "id"}})
     *
     * @param Conversation $conversation
     *
     * @param Request $request
     *
     * @param ValidatorInterface $validator
     *
     * @return JsonResponse
     */
    public function addMessageAction(Conversation $conversation, Request $request, ValidatorInterface $validator)
    {
        $conversationFormData = $request->request->get('app_conversation');

        /** @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper $helper */
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()->build();

        $message = new Message();
        $message->setUser($this->getUser());
        $message->setText($conversationFormData['message']);
        $message->setConversation($conversation);
        $message->setCreatedAt(new \DateTime());
        $message->setUpdatedAt(new \DateTime());
        $message->setIsRead(false);

        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get('app_conversation')['attachmentFile']['file'];
        if ($file) {
            $message->setAttachmentFile($file);
        }

        /** @var \Symfony\Component\Validator\ConstraintViolationList $errors */
        $errors = [];
        $constraintViolationList = $validator->validate($message);
        $constraintIterator = $constraintViolationList->getIterator();

        foreach ($constraintIterator as $constraint) {
            $errors[] = $constraint->getMessage();
        }

        if (sizeof($errors) == 0) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            /** Change user avatars for stub, if absent */
            $path = $helper->asset($message->getUser(), 'imageFile', User::class);
            $message->getUser()->setTemporalityAvatar($path);
            if (is_null($path) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $path)) {
                $message->getUser()->setTemporalityAvatar('/images/updatePhoto.png');
            }

            $_response = $serializer->serialize(['message' => $message, 'user' => $this->getUser()], 'json');

        } else {

            $errorList = implode("\n", $errors);
            $_response = $serializer->serialize(['error' => $errorList], 'json');
        }


        return new JsonResponse($_response);
    }

    /**
     * Return mix object from exists conversations and available users
     *
     * @Route("/conversation/{user}/related/{related}/message/{message}", name="chat_conversation")
     *
     * @ParamConverter("user", options={"mapping": {"user": "id"}})
     * @ParamConverter("message", options={"mapping": {"message": "id"}})
     *
     * @param User $user
     * @param User $related
     * @param Message $message
     * @param Translator $translator
     *
     * @return Response
     * return JsonResponse
     */
    public function getConversationAction(User $user, User $related, Message $message = null, Translator $translator)
    {
        /** @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper $helper */
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

        $conversationList = $this->getDoctrine()
            ->getRepository('AppBundle:Conversation')
            ->getConcreteConversation($user, $this->getUser(), $related);

        // dump($conversationList);die;
        $_username = $user->getFirstName();
        if ($this->getUser()->getId() == $user->getId()) {
            $_username = 'Administration';
        }

        $responseData = [
            'user' => $this->getUser(),
            'users' => $this->getSpecialUsers(),
            'message' => $translator->trans('chat.page.conversation.create.message.success', ['relatedUser' => $_username])
        ];

        $selfConversations = $this->getDoctrine()->getRepository('AppBundle:Conversation')->findSelfAssign($user);
        if (isset($selfConversations) && is_array($selfConversations) && sizeof($selfConversations) > 0) {
            $selfConversation = $this->setConversationTemporarityAvatar($selfConversations[0]);
            $responseData['conversation'] = $selfConversation;
        }

        if (isset($conversationList) && is_array($conversationList) && sizeof($conversationList) > 0) {

            $conversation = $conversationList['0'];

            $username = $user->getFirstname();
            if ($conversation->getUser()->getId() == $conversation->getRelatedUser()->getId()) {
                $username = 'Administration';
            }

            if ($conversation->getRelatedUser()->getUsername() == $conversation->getUser()->getUsername()) {
                $this->updateConversation($conversation, $this->getUser());
            }

            $updateMessageListener = $this->get('app.doctrine.update_message_listener');
            $updateMessageListener->setObject([$conversation]);

            $this->setConversationTemporarityAvatar($conversation);
            $conversation = $this->setTemporarityAvatar($conversation);

            $responseData['updated'] = true;
            $responseData['message'] = $translator->trans('chat.page.conversation.create.message.success', ['relatedUser' => $username]);
            $responseData['conversation'] = $conversation;
        }

        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()->build();
        $_response = $serializer->serialize($responseData, 'json');

        return new JsonResponse($_response);
        // return new Response();
    }

    /**
     * @param $conversation
     * @return $conversation
     */
    public function setConversationTemporarityAvatar($conversation)
    {
        /** @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper $helper */
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

        $path = $helper->asset($conversation->getUser(), 'imageFile', User::class);
        $conversation->getUser()->setTemporalityAvatar($path);

        if (is_null($path) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $path)) {
            $conversation->getUser()->setTemporalityAvatar('/images/updatePhoto.png');
        }

        $path = $helper->asset($conversation->getRelatedUser(), 'imageFile', User::class);
        $conversation->getRelatedUser()->setTemporalityAvatar($path);

        if (is_null($path) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $path)) {
            $conversation->getRelatedUser()->setTemporalityAvatar('/images/updatePhoto.png');
        }

        return $conversation;
    }

    /**
     * @param $conversation
     * @return mixed
     */
    public function setTemporarityAvatar($conversation)
    {
        /** @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper $helper */
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');

        foreach ($conversation->getMessages() as $message) {

            $path = $helper->asset($message->getConversation()->getUser(), 'imageFile', User::class);
            $message->getConversation()->getUser()->setTemporalityAvatar($path);

            if (is_null($path) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $path)) {
                $message->getConversation()->getUser()->setTemporalityAvatar('/images/updatePhoto.png');
            }

            $path = $helper->asset($message->getConversation()->getRelatedUser(), 'imageFile', User::class);
            $message->getConversation()->getRelatedUser()->setTemporalityAvatar($path);

            if (is_null($path) || !file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $path)) {
                $message->getConversation()->getRelatedUser()->setTemporalityAvatar('/images/updatePhoto.png');
            }
        }

        return $conversation;
    }

    /**
     * @param Conversation $conversation
     * @param User $user
     */
    public function updateConversation(Conversation $conversation, User $user)
    {
        $conversation->setRelatedUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($conversation);
        $em->flush();
    }

    /**
     * Retrieve winemakers from vineyard
     *
     * @param $winery
     */
    private function retrieveWinemaker($winery)
    {
        $this->winemakers[] = $winery->getVineyard()->getWineMaker()->getId();
    }

    /**
     * Get unique winemakers
     *
     * @return array
     */
    public function getRelatedWinemakers()
    {
        $wineryCollection = $this->getUser()->getWineries();

        $wineries = $wineryCollection->getValues();
        array_walk($wineries, [$this, 'retrieveWinemaker']);

        return array_unique($this->winemakers);
    }
}