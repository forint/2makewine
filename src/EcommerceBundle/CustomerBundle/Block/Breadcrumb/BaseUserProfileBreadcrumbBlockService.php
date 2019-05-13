<?php

namespace EcommerceBundle\CustomerBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;

/**
 * Abstract class for user breadcrumbs.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
abstract class BaseUserProfileBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService
{
    /**
     * {@inheritdoc}
     */
    protected function getRootMenu(BlockContextInterface $blockContext)
    {
        $menu = parent::getRootMenu($blockContext);

        $menu->addChild('sonata_user_profile_breadcrumb_index', [
            'route' => 'sonata_user_profile_show',
            'extras' => ['translation_domain' => 'SonataUserBundle'],
        ]);

        return $menu;
    }
}
