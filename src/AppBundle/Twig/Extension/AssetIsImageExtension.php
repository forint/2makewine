<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;

class AssetIsImageExtension extends \Twig_Extension
{

    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getFunctions()
    {
        return array(
            'asset_is_image' => new \Twig_SimpleFunction($this->getName(), [ $this, 'asset_is_image' ]),
        );
    }

    public function asset_is_image($name)
    {
        if (preg_match('/\.(jpg|png|jpeg|gif)$/', $name)) {
            return true;
        }

        return false;
    }

    public function getName()
    {
        return 'asset_is_image';
    }

}