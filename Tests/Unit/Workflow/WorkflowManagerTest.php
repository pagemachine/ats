<?php
namespace PAGEmachine\Ats\Tests\Unit\Workflow;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Service\ExtconfService;
use PAGEmachine\Ats\Workflow\InvalidWorkflowConfigurationException;
use PAGEmachine\Ats\Workflow\WorkflowManager;
use Prophecy\Argument;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for WorkflowManager
 */
class WorkflowManagerTest extends UnitTestCase
{
    /**
     * @var WorkflowManager
     */
    protected $workflowManager;

    /**
     * @var ExtconfService
     */
    protected $extconfService;


    /**
     * @var DefinitionBuilder $definitionBuilder
     */
    protected $definitionBuilder;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->workflowManager = new WorkflowManager();

        $this->extconfService = $this->prophesize(ExtconfService::class);
        GeneralUtility::setSingletonInstance(ExtconfService::class, $this->extconfService->reveal());

        $this->definitionBuilder = $this->prophesize(DefinitionBuilder::class);
        GeneralUtility::addInstance(DefinitionBuilder::class, $this->definitionBuilder->reveal());

        //Set some standard values
        $this->workflowManager->setAvailableTransitions([
            'onetotwo',
            'twotothree',
        ]);

        $this->workflowManager->setMarkingProperty("property");

        $this->definitionBuilder->addPlaces(Argument::type("array"))->willReturn(null);

        $this->definitionBuilder->build()->willReturn(new Definition(
            ['place'],
            [],
            'place'
        ));
    }

    /**
     * @test
     */
    public function buildsWorkflowFromDefinitionArray()
    {

        $this->extconfService->getWorkflowConfiguration()->willReturn([
            'places' => [
                'one',
                'two',
            ],
            'transitions' => [
                'onetotwo' => ['from' => 'one', 'to' => 'two'],
            ],
        ]);

        $this->definitionBuilder->addPlaces(['one', 'two'])->shouldBeCalled();

        $this->definitionBuilder->addTransition(new Transition(
            'onetotwo',
            'one',
            'two'
        ))->shouldBeCalled();

        $this->workflowManager->getWorkflow();
    }

    /**
     * @test
     */
    public function buildsWorkflowWithCustomNames()
    {

        $this->extconfService->getWorkflowConfiguration()->willReturn([
            'places' => [
                'one',
                'two',
            ],
            'transitions' => [
                'specialTransition' => ['name' => 'onetotwo', 'from' => 'one', 'to' => 'two'],
            ],
        ]);

        $this->definitionBuilder->addPlaces(['one', 'two'])->shouldBeCalled();

        $this->definitionBuilder->addTransition(new Transition(
            'onetotwo',
            'one',
            'two'
        ))->shouldBeCalled();

        $this->workflowManager->getWorkflow();
    }

    /**
     * @test
     */
    public function throwsErrorIfTransitionIsNotAllowed()
    {
        $this->extconfService->getWorkflowConfiguration()->willReturn([
            'places' => [
                'one',
                'two',
            ],
            'transitions' => [
                'forbiddenTransition' => ['name' => 'thisisnotallowed', 'from' => 'one', 'to' => 'two'],
            ],
        ]);

        $this->expectException(InvalidWorkflowConfigurationException::class);

        $this->workflowManager->getWorkflow();
    }
}
