<?php

declare(strict_types=1);

namespace EcommerceBundle\CustomerBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;

class AppCustomerAddressBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.customer.block.breadcrumb_address';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('sonata_customer_addresses_breadcrumb', [
            'route' => 'sonata_customer_addresses',
            'extras' => ['translation_domain' => 'SonataCustomerBundle'],
        ]);

        return $menu;
    }
}
