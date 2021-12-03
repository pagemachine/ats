<?php
namespace PAGEmachine\Ats\ViewHelpers\Workflow;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Workflow\WorkflowManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * ViewHelper to get allowed Application actions
 */
class ApplicationActionsViewHelper extends AbstractViewHelper
{
    /**
     * @var WorkflowManager $workflowManager
     */
    protected $workflowManager;

    /**
     * @var ConfigurationManagerInterface $configurationManager
     */
    protected $configurationManager;

    /**
     * Defined actions via pluginConfiguration
     *
     * @var array
     */
    protected $actions;

    /**
     * @param WorkflowManager $workflowManager
     */
    public function injectWorkflowManager(WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }


    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('application', Application::class, 'Application to get actions for', true);
        $this->registerArgument('controller', 'string', 'Controller to get actions for', true);
    }

    /**
     * Fetch allowed methods from pluginConfiguration
     *
     * @return void
     */
    public function initialize()
    {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->actions = $configuration['controllerConfiguration'][$this->arguments['controller']]['actions'];
    }

    /**
     *
     * @return array $actions Allowed actions
     */
    public function render()
    {
        $actions = [];
        foreach ($this->workflowManager->getWorkflow()->getEnabledTransitions($this->arguments['application']) as $transition) {
            if (in_array($transition->getName(), $this->actions) && $this->isAccessible($transition)) {
                if (!in_array($transition->getName(), $actions)) {
                    $actions[] = $transition->getName();
                }
            }
        }
        return $actions;
    }

    /**
     * Checks if a action is accessible. This is a compatibility feature with EXT:extbase_acl, if present
     *
     * @param  Transition  $transition
     * @return bool
     */
    protected function isAccessible($transition)
    {
        if (ExtensionManagementUtility::isLoaded('extbase_acl')) {
            if (!\Pagemachine\ExtbaseAcl\Manager\ActionAccessManager::getInstance()->isActionAllowed(
                $this->getControllerClass($this->arguments['controller']),
                $transition->getName()
            )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Converts a controller short (like Backend\Application) into a full classname
     *
     * @param  string $controllerShort
     * @return string
     */
    protected function getControllerClass($controllerShort)
    {
        return 'PAGEmachine\\Ats\\Controller\\' . $controllerShort . 'Controller';
    }
}
