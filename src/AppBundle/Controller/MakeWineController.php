<?php

namespace AppBundle\Controller;

use AppBundle\Repository\ProductionRecipeRepository;
use AppBundle\Repository\WineConstructorRepository;
use AppBundle\Repository\WineProductRepository;
use AppBundle\Service\ConstructorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MakeWineController extends Controller
{
    /**
     * @Route("/make-wine{sl}", name="make-wine", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @Template()
     * @param Request $request
     * @param WineConstructorRepository $wcRepository
     * @param ConstructorService $cService
     * @return array
     */
    public function indexAction(Request $request, WineConstructorRepository $wcRepository, ConstructorService $cService)
    {
        $parentId = $request->query->get('parent');
        $parent = $wcRepository->findOneById($parentId);

        $stepItems = $wcRepository->findByParent($parentId);
        $stepItems = $cService->setNextStep($stepItems, $this->generateUrl('make-wine'));

        $stepData = $cService->getStepData($parent);
        $breadcrumbs = $cService->getBreadcrumbs($parent);

        return array(
            "wineParams" => $stepItems,
            "isFirstStep" => ($parentId == null)? true: false,
            "stepsNavigationTree" => $breadcrumbs,
            "stepDescription" => $stepData["stepDescription"],
            "previousStep" => array(
                "stepName" => $stepData["previousStepName"],
                "url" => $stepData["previousStepUrl"]
            ),
            "locale" => $request->getLocale()
        );
    }
    /**
     * @Route("/search-wine{sl}", name="search-wine", requirements={"sl": "/?"}, defaults={"sl": ""})
     * @param Request $request
     * @param WineProductRepository $wpRepository
     * @param WineConstructorRepository $wcRepository
     * @param ProductionRecipeRepository $recipeRepository
     * @return Response
     */
    public function searchAction(Request $request, WineProductRepository $wpRepository, ProductionRecipeRepository $recipeRepository)
    {
        $queryString = $request->query->get('name');
        $recipesFound = $recipeRepository->FindByQueryName($queryString);
        $result = [];
        foreach ($recipesFound as $recipe) {
            $products = $wpRepository->findByProductionRecipe($recipe);
            if (!empty($products)) {
                $wines = [];
                foreach ($products as $p) {
                    $wines[] = $p->getId();
                }
                $result[] = [
                    "name" => $recipe->getTitle(),
                    "description" => $recipe->getWineConstructor()->getBreadcrumbsString(),
                    "imagePath" => "/upload/wineproduct/".$products[0]->getImg(),
                    "wines" => $wines
                ];
            }
        }
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
