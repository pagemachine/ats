<?php
namespace PAGEmachine\Ats\Tests\Unit\Domain\Repository;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Domain\Model\AbstractApplication;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\History;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\AbstractApplicationRepository;
use PAGEmachine\Ats\Workflow\WorkflowManager;
use Prophecy\Argument;
use Symfony\Component\Workflow\Workflow;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Service\EnvironmentService;

/**
 * Testcase for ApplicationRepository
 */
class AbstractApplicationRepositoryTest extends UnitTestCase
{
    /**
     * @var AbstractApplicationRepository
     */
    protected $applicationRepository;

    /**
     * @var Query|Prophecy\Prophecy\ObjectProphecy
     */
    protected $query;


    /**
     * @var PersistenceManager $persistenceManager
     */
    protected $persistenceManager;


    /**
     * @var Workflow $workflow
     */
    protected $workflow;

    /**
     * Set up this testcase
     */
    public function setUp()
    {
        $this->query = $this->prophesize(Query::class);

        $this->applicationRepository = $this->getMockBuilder(AbstractApplicationRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['createQuery', 'add', 'update'])
            ->getMock();

        $this->applicationRepository->method("createQuery")->willReturn($this->query->reveal());

        $this->persistenceManager = $this->prophesize(PersistenceManager::class);
        $this->inject($this->applicationRepository, "persistenceManager", $this->persistenceManager->reveal());
    }

    /**
     * @test
     * @dataProvider possibleApplications
     * @param  bool $isNew
     * @param  string $saveMethod The method to call
     */
    public function addsOrUpdatesApplicationAndPersists($isNew, $saveMethod)
    {
        $application = $this->prophesize(Application::class);

        $this->persistenceManager->persistAll()->shouldBeCalled();

        $this->persistenceManager->isNewObject($application->reveal())->willReturn($isNew);

        $this->applicationRepository
            ->expects($this->once())
            ->method($saveMethod)
            ->with($application->reveal());

        $this->applicationRepository->addOrUpdate($application->reveal());
    }


    /**
     * @return array
     */
    public function possibleApplications()
    {

        return [
            'new application' => [true, 'add'],
            'existing application' => [false, 'update'],
        ];
    }

    /**
     * @test
    */
    public function findsByUserAndJob()
    {

        $job = $this->prophesize(Job::class);
        $user = $this->prophesize(FrontendUser::class);


        $this->query->matching("logicalAndExpression")->willReturn($this->query->reveal());
        $this->query->logicalAnd(["Comparison", "Comparison", "Comparison", "Comparison", "Comparison"])->willReturn("logicalAndExpression");
        $this->query->equals("job", $job->reveal())->willReturn("Comparison");
        $this->query->equals("user", $user->reveal())->willReturn("Comparison");
        $this->query->equals("anonymized", false)->willReturn("Comparison");
        $this->query->greaterThanOrEqual("status", 10)->willReturn("Comparison");
        $this->query->lessThanOrEqual("status", 50)->willReturn("Comparison");

        $result = $this->prophesize(QueryResultInterface::class);
        $this->query->execute()->willReturn($result->reveal());

        $application = $this->prophesize(Application::class);
        $result->getFirst()->willReturn($application->reveal());

        $application = $this->applicationRepository->findByUserAndJob($user->reveal(), $job->reveal(), 10, 50);

        $this->assertInstanceOf(Application::class, $application);
    }

    /**
     * @test
     * @dataProvider invalidStatusArguments
     * @param mixed $minStatus
     * @param mixed $maxStatus
     */
    public function throwsErrorIfStatusRangeIsInvalid($minStatus, $maxStatus)
    {

        $job = $this->prophesize(Job::class);
        $user = $this->prophesize(FrontendUser::class);

        $this->expectException(InvalidArgumentTypeException::class);

        $this->applicationRepository->findByUserAndJob($user->reveal(), $job->reveal(), $minStatus, $maxStatus);
    }

    /**
     * @return array
     */
    public function invalidStatusArguments()
    {

        return [
            'invalidMinStatus' => ["invalid", 10],
            'invalidMaxStatus' => [10, "invalid"],
        ];
    }

    /**
     * @test
     */
    public function findsByBackendUser()
    {

        $backendUser = new BackendUserAuthentication();
        $backendUser->user = [
            'uid' => 1,
        ];

        $backendUser->userGroups = [
            ['uid' => 2],
            ['uid' => 3],
        ];

        $this->query->contains("job.userPa", 1)->shouldBeCalled()->willReturn("userPaComparison");

        $this->query->contains("job.department", 2)->shouldBeCalled()->willReturn("group1department");
        $this->query->contains("job.officials", 2)->shouldBeCalled()->willReturn("group1officials");
        $this->query->contains("job.contributors", 2)->shouldBeCalled()->willReturn("group1contributors");

        $this->query->contains("job.department", 3)->shouldBeCalled()->willReturn("group2department");
        $this->query->contains("job.officials", 3)->shouldBeCalled()->willReturn("group2officials");
        $this->query->contains("job.contributors", 3)->shouldBeCalled()->willReturn("group2contributors");
        $this->query->lessThan("status", 100)->shouldBeCalled()->willReturn("statusless");
        $this->query->equals("anonymized", false)->shouldBeCalled()->willReturn("non-anonymized");

        $this->query->logicalOr(Argument::size(7))->shouldBeCalled()->willReturn("logicalor");
        $this->query->logicalAnd("logicalor", "statusless", "non-anonymized")->shouldBeCalled()->willReturn("matching");
        $this->query->matching("matching")->shouldBeCalled();

        $this->query->execute()->willReturn("something");

        $this->applicationRepository->findByBackendUser($backendUser);
    }

    /**
     * @test
     */
    public function createsHistory()
    {
        $dataMapper = $this->prophesize(DataMapper::class);
        $this->inject($this->applicationRepository, "dataMapper", $dataMapper->reveal());

        $environmentService = $this->prophesize(EnvironmentService::class);
        $environmentService->isEnvironmentInBackendMode()->willReturn(true);

        $this->inject($this->applicationRepository, "environmentService", $environmentService->reveal());

        $application = $this->getMockBuilder(AbstractApplication::class)
            ->setMethods(
                [
                '_getProperties',
                '_isDirty',
                '_getCleanProperty',
                ]
            )->getMock();

        $application->method("_getProperties")->willReturn([
            'dirtyProperty' => 'dirtyValue',
        ]);
        $application->method("_isDirty")->with("dirtyProperty")->willReturn(true);

        $application->method('_getCleanProperty')->with('dirtyProperty')->willReturn('oldValue');

        $dataMapper->convertPropertyNameToColumnName('dirtyProperty')->willReturn('dirty_property');

        $dataMapper->getPlainValue('oldValue')->willReturn('plainOldValue');
        $dataMapper->getPlainValue('dirtyValue')->willReturn('plainDirtyValue');

        $GLOBALS['BE_USER'] = new \StdClass();
        $GLOBALS['BE_USER']->user = [
            'uid' => 1,
        ];

        $dataMapper->map(BackendUser::class, [['uid' => 1]])->willReturn([0 => new BackendUser()]);

        $expectedArray = [
            'oldRecord' => [
                'dirty_property' => 'plainOldValue',
            ],
            'newRecord' => [
                'dirty_property' => 'plainDirtyValue',
            ],
        ];

        $history = $this->applicationRepository->createHistory($application, 'updateaction', ['foo' => 'bar']);

        $this->assertEquals($expectedArray, $history->getHistoryData());
    }

    /**
     * @test
     */
    public function skipsHistoryCreationWithoutChanges()
    {
        $application = $this->getMockBuilder(AbstractApplication::class)
            ->setMethods(
                [
                '_getProperties',
                '_isDirty',
                '_getCleanProperty',
                ]
            )->getMock();

        $application->method("_getProperties")->willReturn([
            'cleanProperty' => 'cleanValue',
        ]);

        $application->method("_isDirty")->with("cleanProperty")->willReturn(false);

        $this->assertEquals(null, $this->applicationRepository->createHistory($application, "something", []));
    }

    /**
     * @test
     */
    public function updatesWithLoggingAndWorkflowTransition()
    {
        $application = $this->prophesize(Application::class);

        $this->applicationRepository = $this->getMockBuilder(AbstractApplicationRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['createHistory', 'update'])
            ->getMock();

        $this->inject($this->applicationRepository, "persistenceManager", $this->persistenceManager->reveal());

        $this->workflow = $this->prophesize(Workflow::class);
        $workflowManager = $this->prophesize(WorkflowManager::class);
        $workflowManager->getWorkflow()->willReturn($this->workflow->reveal());
        $this->applicationRepository->injectWorkflowManager($workflowManager->reveal());

        $history = new History();
        $this->applicationRepository->method("createHistory")->with($application->reveal(), "foo", [])->willReturn($history);

        $application->addHistoryEntry($history)->shouldBeCalled();

        $this->workflow->can($application->reveal(), "foo")->willReturn(true);
        $this->workflow->apply($application->reveal(), "foo")->shouldBeCalled();

        $this->applicationRepository->expects($this->once())->method("update");

        $this->applicationRepository->updateAndLog($application->reveal(), "foo");
    }
}
