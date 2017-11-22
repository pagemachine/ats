<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class FluidRenderingService implements SingletonInterface
{
    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @codeCoverageIgnore
     * @return FluidRenderingService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     *
     * @param StandaloneView|null $view
     */
    public function __construct(StandaloneView $view = null)
    {
        if (!$view) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $view = $objectManager->get(StandaloneView::class);
        }
        $this->view = $view;
    }

    /**
     * Renders field content
     *
     * @param  string $rawText
     * @param  array  $assignVariables
     * @return string $renderedText
     */
    public function render($rawText, $assignVariables)
    {
        $this->view->setTemplateSource(
            $rawText
        );

        $this->view->assignMultiple($assignVariables);

        $renderedText = $this->view->render();

        return $renderedText;
    }
}
