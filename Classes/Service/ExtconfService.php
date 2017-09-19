<?php
namespace PAGEmachine\Ats\Service;

use PAGEmachine\Ats\Service\MarkerService;
use PAGEmachine\Ats\Workflow\InvalidWorkflowConfigurationException;
use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class ExtconfService implements SingletonInterface {

	protected $moduleName = "AtsAts_AtsApplications";

	/**
	 * Returns module configuration
	 * Not used yet. Module configuration is too late in loading order (after ext_localconf)
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function getModuleConfiguration() {
		return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['Ats']['modules'][$this->moduleName]['controllers'];
	}

	/**
	 * Returns defined roles for each job user/usergroup field
	 * (explodes some string fields for better traversal)
	 *
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function getJobRoleDefinitions() {

		return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['job']['roles'];
	}

	/**
	 * Returns marker config array
	 *
	 * @param  string $context
	 * @return array
	 * @codeCoverageIgnore
	 */
	public function getMarkerReplacements($context = MarkerService::CONTEXT_DEFAULT) {

		return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['replacemarkers'][$context];

	}

    /**
     * Returns the active workflow configuration
     *
     * @return array
     */
    public function getWorkflowConfiguration()
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['activeWorkflow'])) {

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['workflows'][$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['activeWorkflow']])) {

                return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['workflows'][$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['activeWorkflow']];
            }
            else {
                throw new InvalidWorkflowConfigurationException(sprintf('Could not find configuration for workflow "%s".', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['activeWorkflow']), 1499161222);
            }
        }
        else {
            throw new InvalidWorkflowConfigurationException('Active workflow is not set. Please set a workflow via $TYPO3_CONF_VARS["EXTCONF"]["ats"]["activeWorkflow"].', 1499161228);
        }

        return null;
    }



}
