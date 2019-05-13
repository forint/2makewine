<?php

namespace EcommerceBundle\PageBundle;

use EcommerceBundle\PageBundle\DependencyInjection\EcommerceBundleSonataPageExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EcommerceBundleSonataPageBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataPageBundle';
    }

    public function getContainerExtension()
    {
        return new EcommerceBundleSonataPageExtension();
    }
}