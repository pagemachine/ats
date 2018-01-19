<?php
namespace PAGEmachine\Ats\Controller\Backend;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\NotFoundView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * AbstractBackendController
 */
class AbstractBackendController extends ActionController
{
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

        $pageRenderer->loadRequireJsModule('datatables');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Recordlist/Recordlist');
        $pageRenderer->loadRequireJsModule('TYPO3/CMS/Ats/ApplicationsModule');

        $pageRenderer->addCssFile('EXT:ats/Resources/Public/Css/backend.css');

        $this->buildMenu();
    }

    /**
     * Blank buildMenu function to be overriden by the different controllers
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function buildMenu()
    {
    }
}
