<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WineryCellar;
use AppBundle\Entity\WineryField;
use AppBundle\Entity\Winery;
use AppBundle\Repository\WineProductRepository;
use AppBundle\Repository\WineryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\VineyardService;


class WineryController extends Controller
{
    /*
      @Route("/winery", name="winery")
      @Template()
      @param WineryRepository $repository
      @return array
     */
    public function indexAction(WineryRepository $repository)
    {


//        $winery = new Winery();
//        $winery->setProcess(10);
//        $winery->setWineryCellar(new WineryCellar());
//        $winery->setWineField(new WineField());
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($winery);
//        $em->flush();
//        $winery = $repository->find(19);
//        dump($winery);
//        exit;

        return [

        ];
    }
}