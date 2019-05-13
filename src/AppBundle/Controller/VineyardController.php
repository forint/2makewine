<?php

namespace AppBundle\Controller;

use AppBundle\Repository\WineProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\VineyardService;
use Symfony\Component\Translation\Translator;

class VineyardController extends Controller
{
    /**
     * @Route("/vineyard-map{sl}", name="vineyard-map", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @param WineProductRepository $wpRepository
     * @param VineyardService $vyService
     * @param Translator $translator
     * @return array
     */
    public function indexAction(Request $request, VineyardService $vyService, WineProductRepository $wpRepository,
        Translator $translator)
    {
        $wines = $request->request->get('wine');

        // default - all
        $wineProducts = $wpRepository->findAll();

        // only search wines
        if (!empty($wines)) {
            $wineProducts = $wpRepository->findById($wines);
        }

        if(empty($wines) || empty($wineProducts)) {
            $this->addFlash('vineyards-error', sprintf($translator->trans( 'vineyard_map.error.not_found')));
            return $this->redirectToRoute('make-wine', array(), 301);
        }

        return [
            "maplist" => $wineProducts,
            "wines" => $wines
        ];
    }
}