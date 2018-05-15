<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
     * @var StandaloneView
     */
    protected $sourceView;

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
     * @param StandaloneView|null $sourceView
     */
    public function __construct(StandaloneView $view = null, StandaloneView $sourceView = null)
    {
        $this->view = $view ?: $this->generateView();
        $this->sourceView = $sourceView ?: $this->generateView();
    }

    /**
     * Generates a fresh view and sets all necessary config options
     *
     * @return StandaloneView
     */
    protected function generateView()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $view = $objectManager->get(StandaloneView::class);

        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $configuration = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $view->setTemplateRootPaths($configuration['view']['templateRootPaths']);
        $view->setLayoutRootPaths($configuration['view']['layoutRootPaths']);
        $view->setPartialRootPaths($configuration['view']['partialRootPaths']);

        return $view;
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
        $this->sourceView->setTemplateSource(
            $rawText
        );

        $this->sourceView->assignMultiple($assignVariables);

        $renderedText = $this->sourceView->render();

        return $renderedText;
    }

    /**
     * Renders field content
     *
     * @param  string $rawText
     * @param  array  $assignVariables
     * @return string $renderedText
     */
    public function renderTemplate($templateName, $assignVariables = [])
    {
        $this->view->setTemplate($templateName);
        $this->view->assignMultiple($assignVariables);

        return $this->view->render();
    }
}
