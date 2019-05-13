<?php

namespace AppBundle\Controller;

use AppBundle\Provider\WineProductProvider;
use AppBundle\Repository\RateCommentRepository;
use AppBundle\Repository\WineProductRepository;
use AppBundle\Repository\WineryCellarStepRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\ProductBundle\Controller\ProductController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\RateComment;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\FormView;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Repository\WineryFieldStepRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class WineProductController extends ProductController implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ControllerTrait;

    /**
     * @Route("/wine{sl}", name="wine", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @param WineProductRepository $wpRepository
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function indexAction(Request $request, WineProductRepository $wpRepository)
    {
        $wineId = $request->request->get('product');
        $vinesCount = 10;
        $mapWines = $request->request->get('wines');
        $wineData = $wpRepository->findOneById($wineId);

        // $provider = $this->get('sonata.product.pool')->getProvider($wineId);
        // $provider = $this->get('sonata.product.pool')->getProvider('sonata.ecommerce.wine.product');

        $currency = $this->get('sonata.basket')->getCurrency();

        if (!$wineData) {
            return $this->redirectToRoute('make-wine');
        }

        $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', 'form', null, [
            'data_class' => $this->container->getParameter('sonata.basket.basket_element.class'),
            'csrf_protection' => false,
        ]);
        $form = $formBuilder->getForm();

        // $provider->defineAddBasketForm($wineData, $formBuilder);

        return [
            // 'provider' => $provider,
            // 'form' => $formBuilder->getForm()->createView(),
            "vinesCount" => $vinesCount,
            "wine" => $wineData,
            "mapWines" => $mapWines,
            'currency' => $currency,
            // 'basket' => $basket,
            // 'basketElement' => $basket->getBasketElements(),
        ];
    }

    /**
     * @Route("/wine-completed/{wine}", name="wine-completed")
     * Template()
     * @param int $wine
     * @param Request $request
     * @param WineProductRepository $wpRepository
     * @param WineryFieldStepRepository $wineryFieldStepRepository
     * @param WineryCellarStepRepository $wineryCellarStepRepository
     * @param RateCommentRepository $repository
     * @return array|Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function completedAction($wine, Request $request, WineProductRepository $wpRepository, WineryFieldStepRepository $wineryFieldStepRepository, WineryCellarStepRepository $wineryCellarStepRepository, RateCommentRepository $repository)
    {
        $user = $this->getUser();

        if (!$user){
//            throw new AccessDeniedHttpException('You cannot access this page!'); # return 403 page
            throw $this->createAccessDeniedException('You cannot access this page!'); # redirect to login
        }
        $wineries = $user->getWineries();
        $wineData = $wpRepository->findOneById($wine);
        $isExistedWineProduct = true;
        $wineProductId = [];

        foreach ($wineries as $item) {
            $wineProductId[] = $item->getWineProduct()->getId();
        }

        $isExistedWineProduct = in_array($wine, $wineProductId);


        if (!$isExistedWineProduct) {
            $this->addFlash('product.notice', 'You try to rate not existed product!');
            return $this->redirectToRoute('product_all');
        }

        $userRatingMessage = $repository->getUserRatingMessage($user, $wineData);

        if ($userRatingMessage) {
            $this->addFlash('product.notice', 'You try to rate already rated product!');
            return $this->redirectToRoute('product_all');
        }


        $comment = new RateComment();
        $comment->setAuthor($this->getUser());
        $comment->setWineProduct($wineData);
        $tr = $this->get('translator');
        $form = $this->createFormBuilder($comment)
            ->add('rate', HiddenType::class)
            ->add('comment', TextareaType::class, ['constraints' => [new NotBlank()]])
            ->add('save', SubmitType::class, [
                'label' => $tr->trans('wine_completed.page.rateblock.button_done'),
                'attr' => ['class' => 'transition']
            ])
            ->getForm();;

        if ($request->isMethod('POST')) {
            // Refill the fields in case the form is not valid.
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                $_addedContactId = $comment->getId();

                if ($_addedContactId) {
                    $this->addFlash("success", 'comment saved');
                    return $this->redirectToRoute('product_all');
                } else {
                    $this->addFlash("error", "comment not saved");
                }
            }
        }

        $stepFieldId = $request->get('stepFieldId');
        $stepCellarId = $request->get('stepCellarId');
        $stepFieldIdObject = $wineryFieldStepRepository->findOneById($stepFieldId);
        $stepCellarIdObject = $wineryCellarStepRepository->findOneById($stepCellarId);


        $response = [
            "wine" => $wineData,
            "form" => $form->createView()
        ];

        if (isset($stepFieldIdObject) && !is_null($stepFieldIdObject)) {
            return array_merge($response, ['stepMetaIdObject' => $stepFieldIdObject]);
        }

        if (isset($stepCellarIdObject) && !is_null($stepCellarIdObject)) {
            return array_merge($response, ['stepMetaIdObject' => $stepCellarIdObject]);
        }

//        return $response;
        return $this->render('@App/WineProduct/completed.html.twig', ["wine" => $wineData, "form" => $form->createView()]);
    }

    /**
     * @param FormView $formView
     * @param BasketElementInterface $basketElement
     * @param BasketInterface $basket
     * @param $mode
     *
     * @return Response
     */
    public function renderFormBasketElementAction(FormView $formView, BasketElementInterface $basketElement, BasketInterface $basket, $mode = 'main')
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $route = $this->get('router')->match($request->getPathInfo())['_route'];

        $provider = $this->get('sonata.product.pool')->getProvider('sonata.ecommerce.wine.product');

        $template = 'form_basket_element.html.twig';
        if ($mode == 'top') {
            $template = 'form_basket_element_ajax.html.twig';
        }
        /*if ($route != "app_basket_element_delete" && $formView->vars['id'] == "sonata_basket_basket" || $request->isXmlHttpRequest()){
            $template = 'form_basket_element_ajax.html.twig';
        }*/

        return $this->render(sprintf('%s:' . $template, $provider->getBaseControllerName()), [
            'formView' => $formView,
            'basketElement' => $basketElement,
            'basket' => $basket,
            'provider' => $provider
        ]);
    }
}