<?php

namespace AppBundle\Controller;

use AppBundle\Repository\CellarStepDecideRepository;
use AppBundle\Repository\FieldStepDecideRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\Component\Order\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Service\WineryApproveStepService;
use Symfony\Component\Translation\Translator;
use AppBundle\Entity\Winery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use EcommerceBundle\OrderBundle\Entity\Order;

class CartController extends Controller
{

    private $WineryApproveStepService;

    public function __construct(WineryApproveStepService $WineryApproveStepService)
    {
        $this->WineryApproveStepService = $WineryApproveStepService;
    }

    /**
     * @Route("/cart", name="cart")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
    }

    /**
     * @Route("/payment{sl}", name="payment", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @param FieldStepDecideRepository $fsdRepository
     * @param CellarStepDecideRepository $csdRepository
     * @param Translator $translator
     * @return array
     */
    public function paymentAction(
        Request $request,
        FieldStepDecideRepository $fsdRepository,
        CellarStepDecideRepository $csdRepository,
        Translator $translator
    ) {

        $formId = 'payment-form';

        if ($request->isMethod('POST')) {
            // Winery id
            $wineryId = intval(
                $request->request->get('winery')?
                    :$request->request->get($formId)['winery']
            );
            // WineryFieldStep / WineryCellarStep id
            $wineryStepId = intval(
                $request->request->get('wineryStep')?
                    :$request->request->get($formId)['wineryStep']
            );
            // FieldStepDecide id
            $stepDecideId = intval(
                $request->request->get('stepDecide')?
                    :$request->request->get($formId)['stepDecide']
            );
            // field/cellar
            $stepType = $request->request->get('stepType')?
                :$request->request->get($formId)['stepType'];


            $form = $this->get('form.factory')
                ->createNamedBuilder($formId)
                ->add('token', HiddenType::class, [
                    'constraints' => [new NotBlank()],
                ])
                ->add('winery', HiddenType::class, [
                    'constraints' => [new NotBlank()],
                    'data' => $wineryId
                ])
                ->add('wineryStep', HiddenType::class, [
                    'constraints' => [new NotBlank()],
                    'data' => $wineryStepId
                ])
                ->add('stepDecide', HiddenType::class, [
                    'constraints' => [new NotBlank()],
                    'data' => $stepDecideId
                ])
                ->add('stepType', HiddenType::class, [
                    'constraints' => [new NotBlank()],
                    'data' => $stepType
                ])
                ->add('submit', SubmitType::class,
                    ['label' =>  $translator->trans( 'payment.form.button.submit')])
                ->getForm();

            if($stepType == 'field') {
                //field step data
                $stepDecide = $fsdRepository->findOneById($stepDecideId);
                $paymentDescription =
                    $stepDecide->translate()->getStepTitle().": ".$stepDecide->translate()->getStepDescription();
            } else {
                //cellar step data
                $stepDecide = $csdRepository->findOneById($stepDecideId);
                $paymentDescription =
                    $stepDecide->translate()->getStepTitle().": ".$stepDecide->translate()->getStepDescription();
            }
            $amount = round($stepDecide->getStepPrice(),2);
            $redirectRoute = $stepType."_page";

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // collect data
                $winery = $this->getUser()->getWineries()
                    ->filter(function (Winery $winery) use ($wineryId) {
                        return $winery->getId() == $wineryId;
                    })->first();

                // handle payment
                try {
                    $this->get('app.stripe')->createCharge($form->get('token')->getData(), $amount, $paymentDescription);
                } catch (\Stripe\Error\Base $e) {
                    // payment failed
                    $errMess = strlen($e->getMessage()) > 0 ? lcfirst($e->getMessage()) : 'please try again.';
                    $this->addFlash('payment-error', sprintf('Unable to take payment, %s', $errMess));
                } finally {
                    $flashbag = $this->get('session')->getFlashBag();
                    if (empty($flashbag->peek("payment-error"))) {
                        // success, approve step
                        if($stepType == 'field') {
                            $this->WineryApproveStepService->approveStep($winery, $stepDecideId, $wineryStepId);
                        } else {
                            $this->WineryApproveStepService->approveCellarStep($winery, $stepDecideId, $wineryStepId);
                        }

                        $em = $this->getDoctrine()->getManager();
                        $em->flush();

                        // redirect back to winery
                        return $this->redirectToRoute($redirectRoute, array("id"=> $wineryId), 301);
                    }
                }
            }

            return [
                'form' => $form->createView(),
                'stripe_public_key' => $this->getParameter('stripe_public_key'),
                'description' => $paymentDescription,
                'amount' => $amount." $",
                'routeData' => ['route' => $redirectRoute, 'id' => $wineryId]
            ];

        } else {
            throw $this->createNotFoundException();
        }
    }

    /**
     * @Route("/payment/{order}", name="payment_order")
     * @ParamConverter("order", class="EcommerceBundleSonataOrderBundle:Order", options={"mapping": {"order": "reference"}})
     * @Template()
     * @param Order $order
     * @param Translator $translator
     * @return array
     */
    public function paymentOrderAction(Order $order, Translator $translator) {
        $formId = 'payment-form';
        $amount = round($order->getTotalInc() + $order->getDeliveryCost(), 2);
        $form = $this->get('form.factory')
            ->createNamedBuilder($formId)
            ->add('token', HiddenType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('bank', HiddenType::class, [
                'data' => 'stripe',
            ])
            ->add('order', HiddenType::class, [
                'data' => $order,
            ])
            ->add('amount', HiddenType::class, [
                'data' => $amount,
            ])
            ->add('submit', SubmitType::class,
                ['label' =>  $translator->trans( 'payment.form.button.submit')])
            ->getForm();

        return [
            'form' => $form->createView(),
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ];

    }
}
