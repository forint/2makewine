<?php

declare(strict_types=1);

namespace EcommerceBundle\OrderBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;
use EcommerceBundle\CustomerBundle\Block\Breadcrumb\BaseUserProfileBreadcrumbBlockService;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class AppUserOrderBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.order.block.breadcrumb_order';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('sonata_order_user_breadcrumb', [
            'route' => 'sonata_order_index',
            'extras' => ['translation_domain' => 'SonataOrderBundle'],
        ]);

        return $menu;
    }
}
