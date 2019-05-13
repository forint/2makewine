<?php

namespace AppBundle\Controller;

use AppBundle\Form\ConversationFormType;
use AppBundle\Repository\WineryFieldStepRepository;
use AppBundle\Repository\WineryCellarStepRepository;
use Sonata\BasketBundle\Form\BasketType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Contact;
use AppBundle\Repository\WineConstructorRepository;
use AppBundle\Service\ConstructorService;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     * @param Request $request
     * @param WineryFieldStepRepository $wineryFieldStepRepository
     * @param WineryCellarStepRepository $wineryCellarStepRepository
     * @return array
     */
    public function indexAction(Request $request, WineryFieldStepRepository $wineryFieldStepRepository, WineryCellarStepRepository $wineryCellarStepRepository)
    {
        $this->get('session')->set('sonata_basket_delivery_redirect', 'sonata_basket_delivery_address');

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_index_title', [], 'SonataBasketBundle'));

        $this->get('php_translation.edit_in_place.activator')->activate();

        $stepFieldId = $request->get('stepFieldId');
        $stepCellarId = $request->get('stepCellarId');
        $stepFieldIdObject = $wineryFieldStepRepository->findOneById($stepFieldId);
        $stepCellarIdObject = $wineryCellarStepRepository->findOneById($stepCellarId);

        if (isset($stepFieldIdObject) && !is_null($stepFieldIdObject)){
            return [ 'stepFieldIdObject' => $stepFieldIdObject ];
        }

        if (isset($stepCellarIdObject) && !is_null($stepCellarIdObject)){
            return [ 'stepCellarIdObject' => $stepCellarIdObject ];
        }

        return [];
    }

    /**
     * @Route("/about-us{sl}", name="about-us", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @return array
     */
    public function aboutAction(Request $request)
    {

        return [];
    }

    /**
     * @Route("/learn{sl}", name="learn", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @param WineConstructorRepository $wcRepository
     * @param ConstructorService $cService
     * @return array
     */
    public function learnAction(Request $request, WineConstructorRepository $wcRepository, ConstructorService $cService)
    {
        $startItems = $wcRepository->findByParent(null);
        $startItems = $cService->setNextStep($startItems, $this->generateUrl('make-wine'), $this->generateUrl('vineyard-map'));
        return array("items" => $startItems);
    }

    /**
     * @Route("/contact-us{sl}", name="contact-us", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @return array
     */
    public function contactAction(Request $request)
    {

        /** to do: need create own flash bag only for contact form */
//        $this->get('php_translation.edit_in_place.activator')->deactivate();

        $contact = new Contact();
        $em = $this->getDoctrine()->getManager();
        $tr = $this->get('translator');
        // Create the form according to the FormType created previously.
        // And give the proper parameters
        $form = $this->createForm('AppBundle\Form\ContactType', $contact, array(
            // To set the action use $this->generateUrl('route_identifier')
            'action' => $this->generateUrl('contact-us'),
            'method' => 'POST'
        ));

        if ($request->isMethod('POST')) {

            // Refill the fields in case the form is not valid.
            $form->handleRequest($request);

            if($form->isValid()){

                $em->persist($contact);
                $em->flush();

                $_addedContactId = $contact->getId();

                // Send mail
                if($_addedContactId && $this->sendEmail($form->getData())){

                    $this->addFlash("success", $tr->trans('contact.page.form.message.success'));
                    return $this->redirectToRoute('contact-us');

                }else{

                    $this->addFlash("error", "Form data is not saved. Please try again later or contact with system administrator by E-mail: {$this->container->getParameter('mailer_user')}");

                }

            }
        }

        return array(
            'formcontact' => $form->createView()
        );

    }

    private function sendEmail($data){

        $myappContactMail = $this->container->getParameter('mailer_user');
        $myappContactPassword = $this->container->getParameter('mailer_password');

        // In this case we'll use the ZOHO mail services.
        // If your service is another, then read the following article to know which smpt code to use and which port
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')
            ->setUsername($myappContactMail)
            ->setPassword($myappContactPassword);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance("Our Code World Contact Form ")
            ->setFrom(array($myappContactMail => "Message by ".$data->getFirstName()))
            ->setTo(array(
                $myappContactMail => $myappContactMail
            ))
            ->setBody($this->renderView(
                        'email/contact.html.twig',
                        array(
                            'first_name' => $data->getFirstName(),
                            'last_name' => $data->getLastName(),
                            'email' => $data->getEmail(),
                            'comment' => $data->getComment(),
                        )
                    ),
        'text/html'
            );

        return $mailer->send($message);
    }

    /**
     * @Route("/partnership{sl}", name="partnership", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @return array
     */
    public function partnershipAction(Request $request)
    {
        $this->get('session')->set('sonata_basket_delivery_redirect', 'sonata_basket_delivery_address');

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_index_title', [], 'SonataBasketBundle'));

        $this->get('php_translation.edit_in_place.activator')->activate();

        return [];
    }

    /**
     * @Route("/chat", name="chat")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function chatAction(Request $request)
    {
        $form = $this->createForm(ConversationFormType::class);

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/foreign", name="foreign")
     * @return void
     */
    public function deleteAllForeingKeys()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT TABLE_NAME as t,CONSTRAINT_NAME as c FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '2makewine'");
        $statement->execute();
        $foreignKeys = $statement->fetchAll();

        foreach ($foreignKeys as $foreingKey){
            $removeForeignStatement = $connection->prepare("ALTER TABLE `{$foreingKey['t']}` DROP FOREIGN KEY {$foreingKey['c']}");
            $removeForeignStatement->execute();
        }

        die('foreign');
        //SELECT TABLE_NAME as t,CONSTRAINT_NAME as c FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '2makewine'
    }

}
