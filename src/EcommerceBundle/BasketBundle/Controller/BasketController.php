<?php

declare(strict_types=1);

namespace EcommerceBundle\BasketBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Vineyard;
use AppBundle\Entity\WineProduct;
use AppBundle\Entity\Winery;
use AppBundle\Form\AppAddressType;
use AppBundle\Form\AppPaymentType;
use AppBundle\Form\AppShippingType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use EcommerceBundle\BasketBundle\Entity\Basket;
use EcommerceBundle\CustomerBundle\Entity\Address;
use EcommerceBundle\CustomerBundle\Entity\Customer;
use EcommerceBundle\OrderBundle\Entity\Order;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sonata\BasketBundle\Form\AddressType;
use Sonata\BasketBundle\Form\BasketType;
use Sonata\BasketBundle\Form\PaymentType;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\UndeliverableCountryException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Sonata\BasketBundle\Controller\BasketController as BaseBasketController;
use AppBundle\Form\LoginFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\Serializer\SerializationContext;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\RestBundle\Context\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Payment\InvalidTransactionException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use AppBundle\Entity\WineryCellar;
use AppBundle\Entity\WineryCellarStep;
use AppBundle\Entity\WineryField;
use AppBundle\Entity\WineryFieldStep;
use JMS\Serializer\SerializerBuilder;
use Doctrine\Common\Collections\Criteria;

/**
 * This controller manages the Basket operation and most of the order process.
 */
class BasketController extends BaseBasketController
{
    use ControllerTrait;

    /**
     * Shows the basket.
     *
     * @param Form $form
     *
     * @return Response
     */
    public function indexAction($form = null)
    {
        $form = $form ?: $this->createForm(BasketType::class, $this->container->get('sonata.basket'), [
            'validation_groups' => ['elements'],
        ]);

        // always validate the basket
        if (!$form->isSubmitted()) {
            if ($violations = $this->container->get('validator')->validate($form)) {
                $violationMapper = new ViolationMapper();
                foreach ($violations as $violation) {
                    $violationMapper->mapViolation($violation, $form, true);
                }
            }
        }

        $this->container->get('session')->set('sonata_basket_delivery_redirect', 'sonata_basket_delivery_address');

        $this->container->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_index_title', [], 'SonataBasketBundle'));

        return $this->render('SonataBasketBundle:Basket:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Adds a product to the basket.
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function addProductAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $params = $request->get('add_basket');

        if ('POST' != $request->getMethod()) {
            throw new MethodNotAllowedException(['POST']);
        }

        // retrieve the product
        /*dump($this->get('sonata.product.set.manager'));
        dump(get_class($this->get('sonata.product.set.manager')));
        dump(get_class_methods($this->get('sonata.product.set.manager')));die;*/
        $product = $this->get('sonata.product.set.manager')->findOneBy(['id' => $params['productId']]);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $params['productId']));
        }

        // retrieve the custom provider for the product type
        $provider = $this->get('sonata.product.pool')->getProvider('sonata.ecommerce.wine.product');


        $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', 'form', null, [
            'data_class' => $this->container->getParameter('sonata.basket.basket_element.class'),
            'csrf_protection' => false,
        ]);

        $provider->defineAddBasketForm($product, $formBuilder);

        // load and bind the form
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // if the form is valid add the product to the basket
        if ($form->isValid()) {
            $basket = $this->get('sonata.basket');
            $basketElement = $form->getData();

            $quantity = $basketElement->getQuantity();
            $currency = $this->get('sonata.basket')->getCurrency();

            $price = $provider->calculatePrice($product, $currency, true, $quantity, 10);


            if ($basket->hasProduct($product)) {
                $basketElement = $provider->basketMergeProduct($basket, $product, $basketElement);
            } else {
                $basketElement = $provider->basketAddProduct($basket, $product, $basketElement);
            }

            $basket = $this->get('sonata.basket');
            $productPool = $basket->getProductPool();

            $code = $productPool->getProductCode($product);
            $productDefinition = $productPool->getProduct($code);

            // $basketElement = $basketElement ? $this->getBasketElement($basketElement) : $this->get('sonata.basket_element.manager')->create();
            $basketElement->setProductDefinition($productDefinition);

            $this->get('sonata.basket.factory')->save($basket);


            if ($request->isXmlHttpRequest() && $provider->getOption('product_add_modal')) {
                return $this->render('SonataBasketBundle:Basket:add_product_popin.html.twig', [
                    'basketElement' => $basketElement,
                    'locale' => $basket->getLocale(),
                    'product' => $product,
                    'price' => $price,
                    'currency' => $currency,
                    'quantity' => $quantity,
                    'provider' => $provider,
                ]);
            }

            if ($request->isXmlHttpRequest() && $provider->getOption('product_scroll_basket')) {

                return $this->render('AppBundle:WineProduct:top_block_basket.html.twig', [
                    'basketElement' => $basketElement,
                    'formView' => $form->createView(),
                    'basket' => $basket,
                    'locale' => $basket->getLocale(),
                    'product' => $product,
                    'price' => $price,
                    'currency' => $currency,
                    'quantity' => $quantity,
                    'provider' => $provider,
                ]);
            }

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        // an error occur, forward the request to the view
        return $this->forward('SonataProductBundle:Product:view', [
            'productId' => $product,
            'slug' => $product->getSlug(),
        ]);
    }

    /**
     * Order process step 4: choose a billing address from existing ones or create a new one.
     *
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function paymentAddressStepAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $basket = $this->get('sonata.basket');

        if (0 == $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_BILLING);

        // Show address creation / selection form
        $form = $this->createForm(AppAddressType::class, null); //, ['addresses' => $addresses->toArray()]
        $template = 'SonataBasketBundle:Basket:payment_address_step.html.twig';

        $form->handleRequest($request);

        if ('POST' == $request->getMethod() && $form->isSubmitted() && $request->request->has('app_basket_address')) {

            if ($form->isValid()) {
                if ($form->has('useSelected') && $form->get('useSelected')->isClicked()) {
                    $address = $form->get('addresses')->getData();
                } else {
                    $address = $form->getData();
                    $address->setType(AddressInterface::TYPE_DELIVERY);

                    $customer->addAddress($address);

                    $this->get('sonata.customer.manager')->save($customer);

                    $this->get('session')->getFlashBag()->add('sonata_customer_success', 'address_add_success');
                }

                $basket->setCustomer($customer);
                $basket->setDeliveryAddress($address);
                $basket->setBillingAddress($address);

                // save the basket
                $this->get('sonata.basket.factory')->save($basket);

                //return new RedirectResponse($this->generateUrl('sonata_basket_delivery'));
                return new RedirectResponse($this->generateUrl('sonata_basket_payment'));
            } else {

                $errors = $form->getErrors(true, false);
                $childFormMessageError = $errors->current()->getMessage();
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add("error", $childFormMessageError);
            }
        }
        return $this->render($template, [
            'form' => $form->createView(),
            'addresses' => $addresses,
        ]);
    }

    /**
     * E-commerce authentication in checkout process
     *
     * @Template()
     * @return array
     */
    public function basketLoginAction()
    {
        $customer = $this->get('sonata.customer.selector')->get();

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($customer->getAddresses()->count() > 0) {
                return $this->redirectToRoute('app_basket_payment');
            } else {
                return $this->redirectToRoute('app_basket_delivery');
            }
        }

        $this->get('event_dispatcher')->removeListener('on_updated', 'onUpdate');

        $authUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // create login form
        $form = $this->createForm(LoginFormType::class, [
            '_username' => $lastUsername
        ]);

        return [
            'formbasketlogin' => $form->createView(),
            'error' => $error
        ];

    }

    /**
     * E-commerce registration in checkout process
     *
     * @Template()
     * @return array
     */
    public function basketRegisterAction(Request $request)
    {
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_basket_delivery');
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('app_user_shop_registration_confirmed');

                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                return $response;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return ['formbasketregister' => $form->createView()];

    }

    /**
     * Order process step 3: choose delivery mode.
     *
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function deliveryStepAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $basket = $this->get('sonata.basket');

        if (0 == $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        try {
            $form = $this->createForm(AppShippingType::class, $basket, [
                'validation_groups' => ['delivery'],
            ]);
        } catch (UndeliverableCountryException $ex) {

            $countryName = Intl::getRegionBundle()->getCountryName($ex->getAddress()->getCountryCode());
            $message = $this->get('translator')->trans('undeliverable_country', ['%country%' => $countryName], 'SonataBasketBundle');
            $this->get('session')->getFlashBag()->add('error', $message);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $template = 'EcommerceBundleSonataBasketBundle:Basket:delivery_step.html.twig';

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // save the basket
                $this->get('sonata.basket.factory')->save($form->getData());
                return new RedirectResponse($this->generateUrl('sonata_basket_payment_address'));
            }
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', [], 'SonataBasketBundle'));

        return $this->render($template, [
            'customer' => $customer,
            'shipping_form' => $form->createView(),
            'basket' => $basket
        ]);
    }

    /**
     * Order process step 2: choose an address from existing ones or create a new one.
     *
     * @throws NotFoundHttpException
     * @Template()
     * @return RedirectResponse|Response
     */
    public function deliveryAddressStepAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $customer = $this->get('sonata.customer.selector')->get();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $basket = $this->get('sonata.basket');
        $basket->setCustomer($customer);

        if (0 == $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_DELIVERY);

        $em = $this->container->get('sonata.address.manager')->getEntityManager();
        foreach ($addresses as $key => $address) {
            // Prevents usage of not persisted addresses in AddressType to avoid choice field error
            // This case occurs when customer is taken from a session
            if (!$em->contains($address)) {
                unset($addresses[$key]);
            }
        }

        // Show address creation / selection form
        $form = $this->createForm(AppAddressType::class, null, ['addresses' => $addresses]);
        $template = 'SonataBasketBundle:Basket:delivery_address_step.html.twig';

        if ('POST' == $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($form->has('useSelected') && $form->get('useSelected')->isClicked()) {
                    $address = $form->get('addresses')->getData();
                } else {
                    $address = $form->getData();
                    $address->setType(AddressInterface::TYPE_DELIVERY);

                    $customer->addAddress($address);

                    $this->get('sonata.customer.manager')->save($customer);

                    $this->get('session')->getFlashBag()->add('sonata_customer_success', 'address_add_success');
                }

                $basket->setCustomer($customer);
                $basket->setDeliveryAddress($address);
                // save the basket
                $this->get('sonata.basket.factory')->save($basket);

                return new RedirectResponse($this->generateUrl('sonata_basket_delivery'));
            } else {

                $errors = $form->getErrors(true, false);
                $childFormMessageError = $errors->current()->getMessage();
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add("error", $childFormMessageError);
            }
        }

        // Set URL to be redirected to once edited address
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_basket_delivery_address'));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', [], 'SonataBasketBundle'));

        return $this->render($template, [
            'form' => $form->createView(),
            'addresses' => $addresses,
            'basket' => $basket
        ]);
    }

    /**
     * Deletes a basket element from a basket.
     *
     * ORM\Route("/basket/{basketId}/element/{elementId}", name="app_basket_element_delete")
     *
     * ParamConverter("basketId", options={"mapping": {"basket": "id"}}, defaults={"basketId" = null})
     * ParamConverter("elementId", options={"mapping": {"basketElement": "id"}}, defaults={"elementId" = null})
     *
     * @param $elementId Basket element identifier
     * @param $basketId  Basket identifier
     *
     * @throws NotFoundHttpException
     *
     * @return JsonResponse
     */
    public function deleteBasketBasketElementsAction($elementId = null, $basketId = null)
    {
        $basket = $this->getBasket($basketId);
        $provider = $this->container->get('sonata.product.pool')->getProvider('sonata.ecommerce.wine.product');
        $form = $this->createForm(BasketType::class, $this->container->get('sonata.basket'), [
            'validation_groups' => ['elements'],
        ]);

        if ($basket) {
            try {
                $elements = $basket->getBasketElements();
                $basket->removeElement($elements[$elementId]);

                $this->get('sonata.basket.builder')->build($basket);
                $this->get('sonata.basket.entity.factory')->save($basket);
                // $this->get('sonata.basket.entity.factory')->reset($basket, true);
            } catch (\Exception $e) {
                return \FOS\RestBundle\View\View::create(['error' => $e->getMessage()], 400);
            }
        }

        /** Create View for Main Basket */
        $view = View::create($basket);
        $view->setTemplate('EcommerceBundleSonataBasketBundle:Basket:basket.html.twig');
        $view->setTemplateVar('basket');
        $view->setTemplateData(function (ViewHandlerInterface $viewHandler, View $view) use ($basket, $form) {
            return array(
                'formView' => $form,
                'basket' => $basket,
            );
        });
        // $view = \FOS\RestBundle\View\View::create($basket);

        // BC for FOSRestBundle < 2.0
        if (method_exists($view, 'setSerializationContext')) {
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(['sonata_api_read']);
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);
        } else {
            $context = new Context();
            $context->setGroups(['sonata_api_read']);
            $context->setMaxDepth(0);
            $view->setContext($context);
        }
        $handler = $this->get('fos_rest.view_handler');

        if (!$handler->isFormatTemplating($view->getFormat())) {
            $templatingHandler = function ($handler, $view, $request) {
                // if a template is set, render it using the 'params'
                // and place the content into the data
                if ($view->getTemplate()) {
                    $data = $view->getData();

                    if (empty($data['params'])) {
                        $params = array();
                    } else {
                        $params = $data['params'];
                        unset($data['params']);
                    }

                    $view->setData($params);
                    $data['html'] = $handler->renderTemplate($view, 'html');

                    $view->setData($data);
                }

                return $handler->createResponse($view, $request, $format);
            };

            $handler->registerHandler($view->getFormat(), $templatingHandler);
        }

        /** Create View for Top Basket */
        $topView = View::create($basket);
        $topView->setTemplate('AppBundle:WineProduct:top_block_basket.html.twig');
        $topView->setTemplateVar('basket');
        $topView->setTemplateVar('provider');
        $topView->setTemplateData(function (ViewHandlerInterface $viewHandler, View $view) use ($basket, $form, $provider) {
            return array(
                'formView' => $form->createView(),
                'basket' => $basket,
                'provider' => $provider
            );
        });
        // BC for FOSRestBundle < 2.0
        if (method_exists($topView, 'setSerializationContext')) {
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(['sonata_api_read']);
            $serializationContext->enableMaxDepthChecks();
            $topView->setSerializationContext($serializationContext);
        } else {
            $context = new Context();
            $context->setGroups(['sonata_api_read']);
            $context->setMaxDepth(50);
            $topView->setContext($context);
        }
        $handlerTop = $this->get('fos_rest.view_handler');
        if (!$handlerTop->isFormatTemplating($topView->getFormat())) {
            $templatingHandlerTop = function ($handlerTop, $topView, $request) {
                // if a template is set, render it using the 'params'
                // and place the content into the data
                if ($topView->getTemplate()) {
                    $data = $topView->getData();

                    if (empty($data['params'])) {
                        $params = array();
                    } else {
                        $params = $data['params'];
                        unset($data['params']);
                    }

                    $topView->setData($params);
                    $data['html'] = $handlerTop->renderTemplate($topView, 'html');

                    $topView->setData($data);
                }

                return $handlerTop->createResponse($topView, $request, $format);
            };

            $handlerTop->registerHandler($topView->getFormat(), $templatingHandlerTop);
        }

        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()->build();
        $_response = $serializer->serialize([
            'countBasketElements' => $basket->countBasketElements(),
            'topBasket' => $handlerTop->handle($topView),
            'mainBasket' => $handler->handle($view)
        ], 'json');

        return new JsonResponse($_response);

    }

    /**
     * Retrieves basket with id $id or throws an exception if it doesn't exist.
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return BasketInterface
     */
    protected function getBasket($id)
    {
        $customer = $this->get('sonata.customer.selector')->get();
        $basket = $this->get('sonata.basket.manager')->findOneBy(['id' => $id]);

        if (null === $basket) {
            /** @var Basket $basket */
            if ($customer){
                return $this->get('sonata.basket.session.factory')->load($customer);
                //return $this->get('sonata.basket.session.factory')->getSessionVarName($customer);
            }else{
                return $this->get('session')->get("sonata/basket/factory/customer/new");
            }
        }

        return $basket;
    }

    /**
     * Delivery
     *
     * @throws NotFoundHttpException
     * @Template()
     *
     * @return RedirectResponse|Response
     */
    public function deliveryAction()
    {
        $user = $this->getUser();
        $basket = $this->get('sonata.basket');

        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_basket_security_login');
        }

        /**
         * Verification for presence of existing deliveries on products which added to basket products
         */
        if ($basket->countBasketElements() > 0 && !$this->getBasketDeliveryCountries($basket)) {
            $message = "Some products aren't available in any country in the world";
            $this->get('session')->getFlashBag()->add('error', $message);
            return $this->redirectToRoute('sonata_basket_index');
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $customer = $this->get('sonata.customer.selector')->get();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found');
        }

        $basket->setCustomer($customer);

        if ($basket->countBasketElements() == 0) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_DELIVERY);


        $em = $this->container->get('sonata.address.manager')->getEntityManager();
        foreach ($addresses as $key => $address) {
            /**
             * Prevents usage of not persisted addresses in AddressType to avoid choice field error
             * this case occurs when customer is taken from a session
             */
            if (!$em->contains($address)) {
                unset($addresses[$key]);
            }
        }

        /**
         * Retrieve last inserted address, this need if we went from
         */
        if ($lastAddedProductAddress = $customer->getAddresses()->last()){
            $addresses->clear();
            $addresses->add($lastAddedProductAddress);
            $customer->getAddresses()->clear();
            $customer->addAddress($lastAddedProductAddress);
        }elseif ($customer->getUser()) {

            $userAddress = $this->getUserAddress($user);

            if (!$userAddress) {
                $userAddress = $this->createCustomerUserAddressFormUserProfile($user, $customer);
            }

            /**
             * Add extracted or created address into customer
             */
            if (isset($userAddress) && $userAddress !== false) {

                if ($userAddress instanceof ArrayCollection) {
                    foreach ($userAddress->getIterator() as $item) {
                        $addresses->clear();
                        $addresses->add($item);
                        $customer->getAddresses()->clear();
                        $customer->addAddress($item);
                        break;
                    }
                }else{
                    $addresses->clear();
                    $addresses->add($userAddress);
                    $customer->getAddresses()->clear();
                    $customer->addAddress($userAddress);
                }
            }
        }

        /**
         * Show address creation / selection form
         */
        $form = $this->createForm(AppAddressType::class, null, [
            'addresses' => $addresses
        ]);
        $template = 'SonataBasketBundle:Basket:delivery.html.twig';

        try {
            $shippingForm = $this->createForm(AppShippingType::class, $basket, [
                'validation_groups' => ['delivery'],
            ]);
        } catch (UndeliverableCountryException $ex) {

            $countryName = Intl::getRegionBundle()->getCountryName($ex->getAddress()->getCountryCode());
            $message = $this->get('translator')->trans('undeliverable_country', ['%country%' => $countryName], 'SonataBasketBundle');
            $this->get('session')->getFlashBag()->add('error', $message);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        if ('POST' == $request->getMethod() && $request->request->has('app_basket_address')) {
            $form->handleRequest($request);


            if ($form->isValid()) {


                if ($form->has('useSelected') && $form->get('useSelected')->isClicked()) {
                    $address = $form->get('addresses')->getData();
                } else {

                    $address = $form->getData();
                    $address->setType(AddressInterface::TYPE_DELIVERY);

                    $existAddress = $customer->getAddresses()->exists(function($key, $element) use ($address){
                        return (
                            $element->getAddress1() == $address->getAddress1() &&
                            $element->getPostcode() == $address->getPostcode() &&
                            $element->getCity() == $address->getCity() &&
                            $element->getCountryCode() == $address->getCountryCode() &&
                            $element->getPhone() == $address->getPhone()
                        );
                    });


                    if (!$existAddress){
                        /** Remove current state from previous addresses in database */
                        foreach ($customer->getAddresses() as $previousAddress) {
                            $previousAddress->setCurrent(false);
                            $this->get('sonata.address.manager')->save($previousAddress);
                         }
                        $address->setName((string)$user->getUsername());
                        $address->setFirstname((string)$user->getFirstname());
                        $address->setLastname((string)$user->getLastname());
                        $address->setCurrent(true);
                        $customer->addAddress($address);
                    }else{

                        foreach ($customer->getAddresses() as $currentAddress) {
                            $currentAddress->setCurrent(true);
                            $address = $currentAddress;
                            break;
                        }
                    }


                    $this->get('sonata.customer.manager')->save($customer);


                    $em = $this->getDoctrine()->getManager();
                    $em->refresh($customer);

                    $address->setCustomer($customer);


                    $this->get('sonata.address.manager')->save($address);

                    $this->get('session')->getFlashBag()->add('sonata_customer_success', 'address_add_success');

                }

                $basket->setCustomer($customer);

                $basket->setDeliveryAddress($address);
                $basket->setBillingAddress($address);

                // save the basket
                $this->get('sonata.basket.factory')->save($basket);

                return new RedirectResponse($this->generateUrl('app_basket_payment'));
                // return new RedirectResponse($this->generateUrl('sonata_basket_payment_address'));
            } else {

                $errors = $form->getErrors(true, false);
                $childFormMessageError = $errors->current()->getMessage();
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add("error", $childFormMessageError);


            }
        }

        /**
         * Set URL to be redirected to once edited address
         */
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_basket_delivery_address'));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', [], 'SonataBasketBundle'));

        return $this->render($template, [
            'formdevivery' => $form->createView(),
            'addresses' => $addresses,
            'shipping_form' => $shippingForm->createView()
        ]);
    }

    /**
     * Create new User Address form User Entity Fields if it does exist
     *
     * @param User $user
     * @param Customer $customer
     *
     * @return mixed
     */
    public function createCustomerUserAddressFormUserProfile(User $user, Customer $customer)
    {
        $userAddress = new Address();
        $userAddress->setType(AddressInterface::TYPE_DELIVERY);
        $userAddress->setCity((string)$user->getCity());
        $userAddress->setPhone((string)$user->getPhone());
        $userAddress->setName((string)$user->getUsername());
        $userAddress->setFirstname((string)$user->getFirstname());
        $userAddress->setLastname((string)$user->getLastname());
        $userAddress->setAddress1((string)$user->getAddress());
        $userAddress->setPostcode((string)$user->getZipCode());
        $userAddress->setCountryCode((string)$user->getCountry());

        $customer->setEmail($user->getEmail());
        $userAddress->setCustomer($customer);

        $this->get('sonata.address.manager')->save($userAddress);


        return $userAddress;
    }

    /**
     * Retrieve user address by current user fields (including filter by user id)
     *
     * @param User $user
     *
     * @return bool
     */
    public function getUserAddress(User $user)
    {
        /** Check if user address data isset in database */
        $singleUserAddress = $this->get('sonata_ecommerce_customer_address_repository')->findOneBy([
            'city' => $user->getCity(),
            'phone' => $user->getPhone(),
            'name' => $user->getUsername(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'address1' => $user->getAddress(),
            'postcode' => $user->getZipCode(),
            'countryCode' => $user->getCountry(),
        ]);

        /** Filtering user address */
        if ($singleUserAddress) {
            $collection = new ArrayCollection([$singleUserAddress]);
            $userAddress = $collection->filter(function(Address $singleUserAddress) use ($user){
                return $singleUserAddress->getCustomer()->getUser()->getId() == $user->getId();
            });

            return $userAddress;
        }

        return false;
    }

    /**
     * Returns basket elements delivery countries.
     *
     * $param $basket
     *
     * @return array
     */
    protected function getBasketDeliveryCountries($basket)
    {
        $countries = [];

        foreach ($basket->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            foreach ($product->getDeliveries() as $delivery) {
            
                $code = $delivery->getCountryCode();

                if (!isset($countries[$code])) {
                    $countries[$code] = Intl::getRegionBundle()->getCountryName($code);
                }
            }
        }

        return $countries;
    }

    /**
     * Payment
     *
     * @throws HttpException
     *
     * @return RedirectResponse|Response
     */
    public function paymentAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $basket = $this->get('sonata.basket');

        if (0 == $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new HttpException('Invalid customer');
        }

        if ($customer->getAddresses()->count() == 0) {
            return $this->redirectToRoute('app_basket_delivery');
        }



        if (null === $basket->getBillingAddress()) {
            if (null === $basket->getDeliveryAddress()) {
                return new RedirectResponse($this->generateUrl('app_basket_delivery'));
            }else{
                // If no payment address is specified, we assume it's the same as the delivery
                $billingAddress = clone $basket->getDeliveryAddress();
                $billingAddress->setType(AddressInterface::TYPE_BILLING);
                $basket->setBillingAddress($billingAddress);
            }
        }

        $form = $this->createForm(AppPaymentType::class, $basket);
        $paymentSelector = $this->get('sonata.payment.selector');
        $paymentPool = $paymentSelector->getPaymentPool();
        $paymentMethods = $paymentPool->getMethods();

        if (sizeof($paymentMethods) > 1){

            if ('POST' == $request->getMethod()) {

                $paymentMethod = $request->request->get('app_basket_payment_form');
                $paymentSelector = $this->get('sonata.payment.selector');
                $basket->setPaymentMethod($paymentSelector->getPayment($paymentMethod['paymentMethod']));

                $this->get('sonata.basket.factory')->save($basket);

                return new RedirectResponse($this->generateUrl('app_basket_final'));
            }

            $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_payment_title', [], 'SonataBasketBundle'));

            return $this->render('SonataBasketBundle:Basket:payment.html.twig', [
                'basket' => $basket,
                'formpayment' => $form->createView(),
                'customer' => $customer,
            ]);

        }else{

            $paymentSelector = $this->get('sonata.payment.selector');
            $basket->setPaymentMethod($paymentSelector->getPayment(key($paymentMethods)));

            $this->get('sonata.basket.factory')->save($basket);

            return new RedirectResponse($this->generateUrl('app_basket_final'));
            // echo 'IT is single payment';die;

        }
    }

    /**
     * Update basket form rendering & saving.
     */
    public function updateAction()
    {


        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form = $this->createForm(BasketType::class, $this->get('sonata.basket'), ['validation_groups' => ['elements']]);

        $form->submit($request->request->get($form->getName()), false);
        // $form->handleRequest($request);

        if ($form->isValid()) {

            $basket = $form->getData();
            $basket->reset(false); // remove delivery and payment information
            $basket->clean(); // clean the basket

            // $basketElement = $provider->basketMergeProduct($basket, $product, $basketElement);

            $this->get('sonata.basket.factory')->save($basket);
        } else {
            $errors = $form->getErrors(true, false);
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_basket_delivery');
            // return new RedirectResponse($this->container->get('router')->generate('app_basket_delivery'));
        } else {
            return $this->redirectToRoute('app_basket_security_login');
            /*return $this->forward('EcommerceBundleSonataBasketBundle:Basket:basketLogin', [
                'form' => $form,
                'basket' => $basket
            ]);*/
            // return new RedirectResponse($this->container->get('router')->generate('app_basket_security_login'));
        }

    }

    /**
     * Order's review & conditions acceptance checkbox.
     *
     * @return RedirectResponse|Response
     */
    public function finalReviewAction()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        // $paymentFormParameters = $request->get('app_basket_payment_form');
        $basket = $this->get('sonata.basket');

        /*$paymentMethod = $paymentFormParameters["paymentMethod"];
        $request->get('app_basket_payment_form');
        $basket->setPaymentMethod($paymentMethod);
        $this->get('sonata.basket.factory')->save($basket);*/

        /*dump($this->get('validator'));die;
        dump($basket);die;*/

        $constraint = new NotBlank();

        $violations = $this
            ->get('validator')
            // ->validate($basket, ['elements', 'delivery', 'payment']);
            ->validate($basket, $constraint);

        if ($violations->count() > 0) {
            // basket not valid
            // todo : add flash message rendering in template
            foreach ($violations as $violation) {
                $this->get('session')->getFlashBag()->add('error', 'Error: ' . $violation->getMessage());
            }
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        if ('POST' == $request->getMethod()) {
            // send the basket to the payment callback
            return $this->forward('SonataPaymentBundle:Payment:sendbank');
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_review_title', [], 'SonataBasketBundle'));

        return $this->render('EcommerceBundleSonataBasketBundle:Basket:final_review.html.twig', [
            'basket' => $basket,
            'tac_error' => 'POST' == $request->getMethod(),
        ]);
    }

    /**
     * Order's thank you page
     *
     * @return RedirectResponse|Response
     */
    public function orderSuccessAction()
    {
        return $this->render('EcommerceBundleSonataBasketBundle:Basket:order_success.html.twig', []);
    }

    /**
     * Order's payment callback
     *
     * @return RedirectResponse|Response
     */
    public function paymentCallbackAction()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $reference = $request->request->get('reference');
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->findOneBy(['reference' => $reference]);

        if ($order->getOrderElements()->count() == 0){
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        foreach ($order->getOrderElements() as $orderElement) {

            $wineProduct = $this->getDoctrine()
                ->getRepository(WineProduct::class)
                ->findOneBy(['id' => $orderElement->getProductId()]);

            $winery = new Winery();
            $winery->setWineProduct($wineProduct);

            $wineryField = new WineryField();
            $wineryField->setVines($orderElement->getQuantity());
            $wineryCellar = new WineryCellar();
            $rawProduct = $orderElement->getRawProduct();
            $vineyardId = $rawProduct['vineyard']['id'];

            $vineyard = $this->getDoctrine()
                ->getRepository(Vineyard::class)
                ->findOneBy(['id' => $vineyardId]);

            foreach ($wineProduct->getProductionRecipe()->getFieldSteps() as $item) {

                $wineryFieldStep = new WineryFieldStep();

                $wineryFieldStep->setFieldStep($item);
                $wineryField->addStep($wineryFieldStep);
            }

            foreach ($wineProduct->getProductionRecipe()->getCellarSteps() as $item) {

                $wineryCellarStep = new WineryCellarStep();

                $wineryCellarStep->setCellarStep($item);
                $wineryCellar->addStep($wineryCellarStep);
            }

            $winery->setWineryField($wineryField);
            $winery->setWineryCellar($wineryCellar);
            $winery->setVineyard($vineyard);

            $this->getUser()->addWinery($winery);
        }

        try {
            $response = $this->get('sonata.payment.handler')->getPaymentCallbackResponse($request);
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return new RedirectResponse($this->generateUrl('app_basket_success'));
    }

}
