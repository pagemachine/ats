<?php
namespace PAGEmachine\Ats\Workflow;

use PAGEmachine\Ats\Service\ExtconfService;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class WorkflowManager implements SingletonInterface
{
    /**
     *
     * @return WorkflowManager
     * @codeCoverageIgnore
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * All available transitions that can modify workflow (since they modify the application)
     *
     * @var array
     */
    protected $availableTransitions = [
        'submit',
        'acknowledge',
        'backToPerso',
        'close',
        'notes',
        'show',
        'edit',
        'editStatus',
        'note',
        'reply',
        'invite',
        'history',
        'clone',
        'reject',
        'rating',
        'ratingPerso',
        'moveToPool',
    ];

    /**
     * @param array $transitions
     * @codeCoverageIgnore
     */
    public function setAvailableTransitions($transitions = [])
    {
        $this->availableTransitions = $transitions;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getAvailableTransitions()
    {
        return $this->availableTransitions;
    }

    /**
     * The application property to store the marking in.
     *
     * @var string
     */
    protected $markingProperty = "statusPlain";


    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getMarkingProperty()
    {
        return $this->markingProperty;
    }

    /**
     * @param string $markingProperty
     * @codeCoverageIgnore
     */
    public function setMarkingProperty($markingProperty)
    {
        $this->markingProperty = $markingProperty;
    }

    /**
     * The current workflow
     *
     * @var Workflow
     */
    protected $workflow = null;

    /**
     * Returns the current workflow
     *
     * @return Workflow
     */
    public function getWorkflow()
    {
        if ($this->workflow == null) {
            $this->buildWorkflow();
        }

        return $this->workflow;
    }

    /**
     * Returns the current places
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getPlaces()
    {
        return $this->getWorkflow()->getDefinition()->getPlaces();
    }

    /**
     * Loads workflow configuration and builds a Workflow object from it
     *
     * @return void
     */
    protected function buildWorkflow()
    {
        $workflowConfiguration = GeneralUtility::makeInstance(ExtconfService::class)->getWorkflowConfiguration();

        $definitionBuilder = GeneralUtility::makeInstance(DefinitionBuilder::class);

        $definitionBuilder->addPlaces($workflowConfiguration['places']);

        foreach ($workflowConfiguration['transitions'] as $transitionName => $transition) {
            $name = $transition['name'] ?: $transitionName;

            if (!in_array($name, $this->availableTransitions)) {
                throw new InvalidWorkflowConfigurationException(sprintf('Workflow transition "%s" is not defined, please check your workflow configuration.', $name), 1499161275);
            }
            $definitionBuilder->addTransition(new Transition(
                $name,
                $transition['from'],
                $transition['to']
            ));
        }

        $definition = $definitionBuilder->build();

        $marking = new SingleStateMarkingStore($this->markingProperty);
        $workflow = new Workflow($definition, $marking);

        $this->workflow = $workflow;
    }
}
