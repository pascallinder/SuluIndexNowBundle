<?php

namespace Linderp\SuluIndexNowBundle\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\AdminBundle\Exception\NavigationItemNotFoundException;
use Sulu\Bundle\PageBundle\Admin\PageAdmin;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class IndexNowAdmin extends Admin
{
    public const SECURITY_CONTEXT = 'sulu.module.index_now';

    // Key of TranslatorConfigView.js as registered in app.js
    public const INDEX_NOW_CONFIG_VIEW = 'index_now.config';


    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly SecurityCheckerInterface $securityChecker
    ) {
    }

    /**
     * @throws NavigationItemNotFoundException
     */
    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(IndexNowAdmin::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $indexNowAdminNavigationItem = new NavigationItem('app.index_now_config_headline');
            $indexNowAdminNavigationItem->setPosition(500);
            $indexNowAdminNavigationItem->setView(self::INDEX_NOW_CONFIG_VIEW);
            $navigationItemCollection->get(Admin::SETTINGS_NAVIGATION_ITEM)->addChild($indexNowAdminNavigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($this->securityChecker->hasPermission(IndexNowAdmin::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createViewBuilder(self::INDEX_NOW_CONFIG_VIEW, '/index-now', self::INDEX_NOW_CONFIG_VIEW)
            );
        }
    }

    public function getSecurityContexts(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Index Now Usage' => [
                    self::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                    ],
                ],
            ],
        ];
    }

    public static function getPriority(): int
    {
        return PageAdmin::getPriority() - 1;
    }

    public function getConfigKey(): ?string
    {
        return 'index_now';
    }
}
