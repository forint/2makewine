<?php

namespace EcommerceBundle\CustomerBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;

/**
 * Class for user breadcrumbs.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class UserIndexBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.user.block.breadcrumb_index';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        return $menu;
    }
}
