<?php
namespace PAGEmachine\Ats\ViewHelpers\Workflow;

use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * ViewHelper to get allowed Application actions
 */
class ApplicationActionsViewHelper extends AbstractViewHelper
{
    /**
     * @var \PAGEmachine\Ats\Workflow\WorkflowManager
     * @inject
     */
    protected $workflowManager;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('application', Application::class, 'Application to get actions for', true);
        $this->registerArgument('controller', 'string', 'Controller to get actions for', true);
    }

    /**
     *
     * @return array $actions Allowed actions
     */
    public function render()
    {
        $actions = [];
        foreach ($this->workflowManager->getWorkflow()->getEnabledTransitions($this->arguments['application']) as $transition) {
            if ($this->isAccessible($transition)) {
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
