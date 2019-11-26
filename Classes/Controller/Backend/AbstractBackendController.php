<?php
namespace PAGEmachine\Ats\Controller\Backend;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Traits\StaticCalling;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\NotFoundView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * AbstractBackendController
 */
class AbstractBackendController extends ActionController
{
    use StaticCalling;

    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;


    public function initializeView(ViewInterface $view)
    {
        //Do not build anything if there is no view (no template)
        if ($view instanceof NotFoundView) {
            return;
        }

        parent::initializeView($view);

        //Add custom variables to settings (such as current action)
        $this->view->assign("controller", $this->request->getControllerName());
        $this->view->assign("action", $this->request->getControllerActionName());


        //Add Datatables and custom JS module to view
        $pageRenderer = $view->getModuleTemplate()->getPageRenderer();

        $pageRenderer->addCssFile('EXT:ats/Resources/Public/Css/backend.css');

        if ($this->actionMethodName == 'listAction') {
            $pageRenderer->addCssFile('EXT:ats/Resources/Public/Css/libs/jquery.dataTables.min.css');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Ats/ApplicationList');
        } else {
            $pageRenderer->loadRequireJsModule('datatables');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Recordlist/Recordlist');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Ats/ApplicationsModule');
        }

        $this->buildMenu();
    }

    /**
     * Testing helper class
     *
     * @return MenuRegistry
     * @codeCoverageIgnore
     */
    public function getMenuRegistry()
    {
        return $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry();
    }

    /**
     * Builds the backend docheader menu with actions
     *
     * @return void
     */
    public function buildMenu()
    {
        if (empty($this->menuUrls)) {
            return;
        }
        if (!array_key_exists($this->request->getControllerActionName(), $this->menuUrls)) {
            return;
        }

        $menuRegistry = $this->getMenuRegistry();

        $uriBuilder = $this->controllerContext->getUriBuilder();

        $menu = $menuRegistry->makeMenu()
            ->setIdentifier("actions");

        foreach ($this->menuUrls as $url) {
            //If extbase_acl is loaded, reduce menu urls to the ones actually allowed
            if (ExtensionManagementUtility::isLoaded("extbase_acl")) {
                if (!\Pagemachine\ExtbaseAcl\Manager\ActionAccessManager::getInstance()->isActionAllowed(static::class, $url['action'])) {
                    continue;
                }
            }

            $isActive = $this->request->getControllerActionName() === $url['action'] ? true : false;
            $uri = $uriBuilder
                ->reset()
                ->uriFor($url['action'], [], $this->request->getControllerName(), null, null);
            $menuItem = $menu->makeMenuItem()
                ->setHref($uri)
                ->setTitle($this->callStatic(LocalizationUtility::class, 'translate', $url['label'], 'ats'))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
        }

        $menuRegistry->addMenu($menu);
    }

    /**
     *
     * We do not want technical errors in the ATS backend ("an error occured while calling...""),
     * so returning false disables the message completely.
     *
     * @return string The flash message or FALSE if no flash message should be set
     * @api
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }
}
