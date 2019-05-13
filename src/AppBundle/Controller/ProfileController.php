<?php

namespace AppBundle\Controller;


use AppBundle\Entity\FieldStepDecide;
use AppBundle\Entity\User;
use AppBundle\Entity\Winery;
use AppBundle\Entity\WineryFieldStep;
use AppBundle\Form\ProfileAccountFormType;
use AppBundle\Form\ProfileAvatarFormType;
use AppBundle\Form\ProfileFormType;
use AppBundle\Handler\FileUploadHandler;
use AppBundle\Repository\RateCommentRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\WineryRepository;
use AppBundle\Service\CreateNotificationService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;

use FOS\UserBundle\Model\UserInterface;

// If you want get user in controller from method  (...., UserInterface $user = null)
// you must use  -  use Symfony\Component\Security\Core\User\UserInterface;
// https://symfony.com/blog/new-in-symfony-3-2-user-value-resolver-for-controllers

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\ProfileController as BaseProfileController;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Service\WineryApproveStepService;
use AppBundle\Service\WineryFieldAddDateNotifierService;
use Psr\Log\LoggerInterface;


class ProfileController extends BaseProfileController
{
    private $avatarStatus = true;

    private $fileUpload;
    private $dateNotifier;
    private $wineryApproveStep;

    public function __construct(
        FileUploadHandler $fileUpload,
        WineryFieldAddDateNotifierService $dateNotifier,
        WineryApproveStepService $wineryApproveStep)
    {
        $this->fileUpload = $fileUpload;
        $this->dateNotifier = $dateNotifier;
        $this->wineryApproveStep = $wineryApproveStep;
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get("form.factory")->createNamedBuilder('app_user_profile', ProfileFormType::class)->getForm();
        $form->setData($user);

        $formAvatar = $this->get("form.factory")->createNamedBuilder('app_user_profile_avatar', ProfileAvatarFormType::class)->getForm();
        $formAvatar->setData($user);

        $formAccount = $this->get("form.factory")->createNamedBuilder('app_user_account_profile', ProfileAccountFormType::class)->getForm();
        $formAccount->setData($user);

        /** In handle method below ocuur processing validation */
        if ($request->request->has('app_user_profile_avatar')) {
            $formAvatar->handleRequest($request);
            //$formAvatar->submit($request->request->get($formAvatar->getName()), true);
        }
        if ($request->request->has('app_user_profile')) {
            $form->submit($request->request->get($form->getName()), false);
        }
        if ($request->request->has('app_user_account_profile')) {
            $formAccount->submit($request->request->get($formAccount->getName()), false);
        }


        if ($form->isSubmitted() && $request->request->has('app_user_profile')) {

            if ($form->isValid()) {

                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                /** Set Plain Password ( behind the scenes set password to null), then run encode with new plain password */
                $user->setPlainPassword($form->getData()->getPlainPassword());
                $this->get('app.doctrine.hash_password_listener')->encodePassword($user, $user->getPlainPassword());

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $flashBag = $this->get('session')->getFlashBag();
                    if ($this->avatarStatus)
                        $flashBag->add("success_app_user_profile", 'Password was changed successfully');

                    $url = $this->generateUrl('app_user_profile_edit') . '#account';
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            } else {


                $errors = $form->getErrors(true, false);
                $childFormMessageError = $errors->current()->getMessage();
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add("error", $childFormMessageError);
            }
        }

        if ($formAvatar->isSubmitted() && $request->request->has('app_user_profile_avatar')) {

            if ($formAvatar->isValid()) {
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                /** Set Plain Password ( behind the scenes set password to null), then run encode with new plain password */
                //$user->setPlainPassword($form->getData()->getPlainPassword());
                //$this->get('app.doctrine.hash_password_listener')->encodePassword($user, $user->getPlainPassword());

                $event = new FormEvent($formAvatar, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    // Retrieve flashbag from the controller
                    $flashBag = $this->get('session')->getFlashBag();
                    if ($this->avatarStatus) {
                        $flashBag->add("success_app_user_profile_avatar", 'Avatar upload successfully');
                    } else {
                        $flashBag->add("success_app_user_profile_avatar", 'Avatar was changed successfully');
                    }
                    $url = $this->generateUrl('app_user_profile_edit') . '#account';
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            } else {


                $errors = $formAvatar->getErrors(true, false);
                $childFormMessageError = $errors->current()->current()->getMessage();
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add("error", $childFormMessageError);
            }
        }

        if ($formAccount->isSubmitted() && $request->request->has('app_user_account_profile')) {

            if ($formAccount->isValid()) {

                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                $event = new FormEvent($formAccount, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $flashBag = $this->get('session')->getFlashBag();
                    if ($this->avatarStatus) {
                        $flashBag->add("success_app_user_account_profile", 'Personal information was updated successfully');
                    }
                    $url = $this->generateUrl('app_user_profile_edit') . '#personal';
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'profile' => $form->createView(),
            'account' => $formAccount->createView(),
            'avatar' => $formAvatar->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @param UserRepository $userRepository
     * @param UserInterface|null $user
     * @return array
     * @Route("/profile/product{sl}", name="product_all", requirements={"sl": "/?"}, defaults={"sl": ""})
     * Route("/profile/product", name="product_all")
     * @Template()
     */
    public function productAction(Request $request, UserRepository $userRepository, UserInterface $user = null)
    {
        $wineries = $this->getUser()->getWineries();

        return ['wineries' => $wineries];

    }


    /**
     * @param Request $request
     * @param CreateNotificationService $notificationService
     * @return array
     * @Route("/profile/notification{sl}", name="notification_all", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     */
    public function notificationAction(Request $request, CreateNotificationService $notificationService)
    {
        $user = $this->getUser();
        // Alternative $this->getUser() == = $this->get('security.token_storage')->getToken()->getUser()


        $wineries = $user->getWineries();
        $notifications = [];

        if ($wineries) {
            foreach ($wineries as $item) {
                $notification = $notificationService->createNotification($item);
                $notifications[] = $notification;
            }
        }

        return ['notifications' => $notifications];

    }

    /**
     * @return Response
     * @Route(
     *     "/profile/{type}/{id}{sl}",
     *     name="product_page",
     *     requirements={
     *          "type":"product|kol",
     *          "id": "\d+",
     *          "sl": "/?"},
     *     defaults={"sl": ""})
     */
    public function testAction()
    {

        return new Response('<div>Simple test Route slug</div>');
    }

    /**
     * @param $id
     * @return array|RedirectResponse
     * @Route(
     *     "/profile/product/{id}{sl}",
     *     name="product_page",
     *     defaults={"sl": ""},
     *     requirements={
     *          "id": "\d+",
     *          "sl": "/?"})
     * @Template()
     */
    public function productShowAction($id)
    {
        $wineryCollection = $this->getUser()->getWineries()
            ->filter(function (Winery $winery) use ($id) {
                return $winery->getId() == $id;
            });

        $winery = $wineryCollection->first();

//        Var 02
        /*        $criteria =
                    Criteria::create()
                        ->where(Criteria::expr()
                            ->eq('id', (int)$id)
                        );

                $winery = $this->getUser()->getWineries()->matching($criteria);
        */


        if (!$winery) {
            $this->addFlash('product.notice', 'Winery not found');
            return $this->redirectToRoute('product_all');
            // throw $this->createNotFoundException('Winery not found');
        }

        $notifierDate = $this->dateNotifier->getDate($winery);

        return ['winery' => $winery, 'notifierDate' => $notifierDate];
    }

    /**
     * @param $id
     * @param RateCommentRepository $repository
     * @return array|RedirectResponse
     * @Route(
     *     "/profile/field/{id}{sl}",
     *     name="field_page",
     *     defaults={"sl": ""},
     *     requirements={
     *          "id": "\d+",
     *          "sl": "/?"})
     * @Template()
     */
    public function fieldShowAction($id, RateCommentRepository $repository)
    {
        $wineryCollection = $this->getUser()->getWineries()
            ->filter(function (Winery $winery) use ($id) {
                return $winery->getId() == $id;
            });

        $winery = $wineryCollection->first();

        if (!$winery) {
            $this->addFlash('product.notice', 'Field not found');
            return $this->redirectToRoute('product_all');
        }
        $userRatingMessage = $repository->getUserRatingMessage($this->getUser(), $winery->getWineProduct());

        if ($winery->getProgress() >= 100 && !$userRatingMessage) {
            return $this->redirectToRoute('wine-completed', ['wine' => $winery->getWineProduct()->getId()]);
        }

        return ['winery' => $winery];
    }

    /**
     * @param $id
     * @return array|RedirectResponse
     * @Route(
     *     "/profile/cellar/{id}{sl}",
     *     name="cellar_page",
     *     defaults={"sl": ""},
     *     requirements={
     *          "id": "\d+",
     *          "sl": "/?"})
     * @Template()
     */
    public function cellarShowAction($id, RateCommentRepository $repository)
    {
        $wineryCollection = $this->getUser()->getWineries()
            ->filter(function (Winery $winery) use ($id) {
                return $winery->getId() == $id;
            });

        $winery = $wineryCollection->first();

        if (!$winery) {
            $this->addFlash('product.notice', 'Cellar not found');
            return $this->redirectToRoute('product_all');
        }

        $userRatingMessage = $repository->getUserRatingMessage($this->getUser(), $winery->getWineProduct());

        if ($winery->getProgress() >= 100 && !$userRatingMessage) {
            return $this->redirectToRoute('wine-completed', ['wine' => $winery->getWineProduct()->getId()]);
        }

        return ['winery' => $winery];
    }

    /**
     *
     * @Route("/profile/winery/edit", name="approve_winery_decide")
     * Method({ "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function wineryEditAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse('You can access this only using Ajax!', 400);
            throw $this->createNotFoundException();
        }

        // POST
        $decideId = $request->request->get('decideId');
        $fieldStepId = $request->request->get('fieldStepId');
        $wineryId = $request->request->get('wineryId');
        $response = [];
        $updatedWinery = null;


        $wineryCollection = $this->getUser()->getWineries()
            ->filter(function (Winery $winery) use ($wineryId) {
                return $winery->getId() == $wineryId;
            });

        $winery = $wineryCollection->first();

        if (!$winery) {
            $response['errorMessage'] = 'Error with decide!';
            $response['status'] = 'Not saved!';
            return new JsonResponse(array('response' => $response));
        }

        $updatedWinery = $this->wineryApproveStep->approveStep($winery, $decideId, $fieldStepId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($updatedWinery);
        $em->flush();

        $response['status'] = "Saved";

        return new JsonResponse(array('response' => $response));
    }

    /**
     *
     * @Route("/profile/winery/edit/cellar", name="approve_winery_cellar_decide")
     * Method({ "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function wineryEditCellarAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse('You can access this only using Ajax!', 400);
            throw $this->createNotFoundException();
        }

        // POST
        $decideId = $request->request->get('decideId');
        $cellarStepId = $request->request->get('cellarStepId');
        $wineryId = $request->request->get('wineryId');
        $response = [];
        $updatedWinery = null;


        $wineryCollection = $this->getUser()->getWineries()
            ->filter(function (Winery $winery) use ($wineryId) {
                return $winery->getId() == $wineryId;
            });

        $winery = $wineryCollection->first();

        if (!$winery) {
            $response['errorMessage'] = 'Error with decide!';
            $response['status'] = 'Not saved!';
            return new JsonResponse(array('response' => $response));
        }

        $updatedWinery = $this->wineryApproveStep->approveCellarStep($winery, $decideId, $cellarStepId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($updatedWinery);
        $em->flush();

        $response['status'] = "Saved";

        return new JsonResponse(array('response' => $response));
    }
}