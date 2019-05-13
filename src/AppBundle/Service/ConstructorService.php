<?php

namespace AppBundle\Service;

use AppBundle\Repository\WineProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;


/*
 *
 * @param RequestStack $requestStack
 *
 * */
class ConstructorService {

    private $wineProductsRepository;
    private $path;

    public function __construct(RequestStack $requestStack, WineProductRepository $wpRepository) {
        $this->wineProductsRepository = $wpRepository;
        $this->path = $requestStack->getCurrentRequest()->getPathInfo();
    }

    public function setNextStep ($parents, $path) {
        foreach ($parents as &$parentObj) {
            if ($parentObj->isIsLast()) {
                $parentObj->url = $path;
                $parentObj->wines = $this->wineProductsRepository->findByProductionRecipe($parentObj->getRecipesId());
            } else {
                $parentObj->url = $path."?parent=".$parentObj->getId();
            }
        }
        return $parents;
    }

    public function getStepData($parent) {
        if($parent == null) {
            return array(
                "stepDescription" => "",
                "previousStepName" => "",
                "previousStepUrl" => ""
            );
        } else {
            $previousParent = $parent->getParent();
            $previousUrl = ($previousParent)? $this->path."?parent=".$previousParent->getId(): $this->path;

            return array(
                "stepDescription" => $parent->translate()->getChildrenDescription(),
                "previousStepName" => $parent->getStep()->translate()->getTitle(),
                "previousStepUrl" => $previousUrl
            );
        }
    }

    public function getBreadcrumbs($parent){
        $stepsTree = array();
        if ($parent) {
            $breadcrumbs = $parent->getBreadcrumbs();
            foreach ($breadcrumbs as $brItem) {
                $stepItem = array(
                    "id" => $brItem->getId(),
                    "name" => $brItem->translate()->getTitle(),
                    "icon" => $brItem->getImagePath()
                );
                if ($brItem->getParent() != null) {
                    $stepItem["url"] = $this->path."?parent=".$brItem->getParent()->getId();
                } else {
                    $stepItem["url"] = $this->path;
                }
                $stepsTree[] = $stepItem;
            }
        }
        return $stepsTree;
    }

}