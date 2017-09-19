<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Backend;


/*
 * This file is part of the PAGEmachine ATS project.
 */


use PAGEmachine\Ats\Controller\Backend\AbstractBackendController;
use Prophecy\Argument;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\NotFoundView;

/**
 * Testcase for AbstractBackendController
 */
class AbstractBackendControllerTest extends UnitTestCase
{
    /**
     * @var AbstractBackendController
     */
    protected $abstractBackendController;

    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var PageRenderer $pageRenderer
     */
    protected $pageRenderer;

    /**
     * @var Request $request
     */
    protected $request;
    
    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->abstractBackendController = $this->getMockBuilder(AbstractBackendController::class)
            ->setMethods([
                'buildMenu'
            ])->getMock();

        $this->view = $this->prophesize(BackendTemplateView::class);
        $this->pageRenderer = $this->prophesize(PageRenderer::class);
        $this->request = $this->prophesize(Request::class);
        $this->inject($this->abstractBackendController, "view", $this->view->reveal());
        $this->inject($this->abstractBackendController, "request", $this->request->reveal());

        $moduleTemplate = $this->prophesize(ModuleTemplate::class);
        $moduleTemplate->getPageRenderer()->willReturn($this->pageRenderer->reveal());

        $this->request->getControllerName()->willReturn("ControllerName");
        $this->request->getControllerActionName()->willReturn("ActionName");

        $this->view->getModuleTemplate()->willReturn($moduleTemplate->reveal());
    }

    /**
     * @test
     */
    public function assignsControllerAndActionToViewAndAddsJS()
    {

        //Add custom variables to settings (such as current action)
        $this->view->assign("controller", "ControllerName")->shouldBeCalled();
        $this->view->assign("action", "ActionName")->shouldBeCalled();

        $this->pageRenderer->loadRequireJsModule(Argument::type("string"))->shouldBeCalled();

        $this->abstractBackendController->initializeView($this->view->reveal());

        
    }

    /**
     * @test
     */
    public function returnsIfNoViewIsFound()
    {
        $this->abstractBackendController->expects($this->never())->method("buildMenu");

        $this->abstractBackendController->initializeView(new NotFoundView());
        
    }
    
}
