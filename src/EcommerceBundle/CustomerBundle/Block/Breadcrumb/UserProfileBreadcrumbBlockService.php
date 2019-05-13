<?php

namespace EcommerceBundle\CustomerBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;

/**
 * Class for user breadcrumbs.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class UserProfileBreadcrumbBlockService extends BaseUserProfileBreadcrumbBlockService
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.user.block.breadcrumb_profile';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('sonata_user_profile_breadcrumb_edit', [
            'route' => 'sonata_user_profile_edit',
            'extras' => ['translation_domain' => 'SonataUserBundle'],
        ]);

        return $menu;
    }
}
