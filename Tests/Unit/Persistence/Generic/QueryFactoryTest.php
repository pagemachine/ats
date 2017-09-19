<?php
namespace PAGEmachine\Ats\Tests\Unit\Persistence\Generic;

use PAGEmachine\Ats\Persistence\Generic\QueryFactory;
use PAGEmachine\Ats\Persistence\OpenRepositoryInterface;
use Prophecy\Argument;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryFactory as ExtbaseQueryFactory;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Testcase for overriden QueryFactory
 */
class QueryFactoryTest extends UnitTestCase {

	/**
	 * QueryFactory
	 *
	 * @var QueryFactory
	 */
	protected $queryFactory;

	/**
	 *
	 * @var QueryInterface
	 */
	protected $query;

	/**
	 *
	 * @var OpenRepositoryInterface
	 */
	protected $repository;

	/**
	 *
	 * @var ObjectManager
	 */
	protected $objectManager;

	protected function setUp() {

		$this->query = $this->prophesize(QueryInterface::class);

		$this->queryFactory = $this->getMockBuilder(QueryFactory::class)->setMethods(['callStatic'])->getMock();

		$this->queryFactory->method('callStatic')->with(ExtbaseQueryFactory::class, 'create', 'Foo\\Bar\\Domain\\Model\\Test')->willReturn($this->query->reveal());

		$this->repository = $this->prophesize(OpenRepositoryInterface::class);

		$this->objectManager = $this->prophesize(ObjectManager::class);
		$this->inject($this->queryFactory, 'objectManager', $this->objectManager->reveal());

	}

	/**
	 * @test
	 */
	public function setsQuerySettingsAndOrderingsByRepository() {

		/** @var QuerySettingsInterface */
		$querySettings = $this->prophesize(QuerySettingsInterface::class);

		
		$this->repository->getDefaultQuerySettings()->willReturn($querySettings->reveal());
		$this->repository->getDefaultOrderings()->willReturn(['foo' => 'bar']);
		
		$this->objectManager->get('Foo\\Bar\\Domain\\Repository\\TestRepository')->willReturn($this->repository->reveal());

		$this->query->setQuerySettings(Argument::type(QuerySettingsInterface::class))->shouldBeCalled();
		$this->query->setOrderings(['foo' => 'bar'])->shouldBeCalled();

		$this->queryFactory->create('Foo\\Bar\\Domain\\Model\\Test');

	}

	/**
	 * @test
	 */
	public function doesNothingIfNoRepositoryIsFound() {

		$this->objectManager->get('Foo\\Bar\\Domain\\Repository\\TestRepository')->willReturn(null);

		$this->query->setQuerySettings(Argument::type(QuerySettingsInterface::class))->shouldNotBeCalled();
		$this->query->setOrderings(Argument::any())->shouldNotBeCalled();

		$this->queryFactory->create('Foo\\Bar\\Domain\\Model\\Test');

	}

	/**
	 * @test
	 */
	public function leavesQuerySettingsIfRepositoryHasNoSettings() {

		$this->repository->getDefaultQuerySettings()->willReturn(null);
		$this->repository->getDefaultOrderings()->willReturn([]);

		$this->objectManager->get('Foo\\Bar\\Domain\\Repository\\TestRepository')->willReturn($this->repository->reveal());

		$this->query->setQuerySettings(Argument::type(QuerySettingsInterface::class))->shouldNotBeCalled();
		$this->query->setOrderings(Argument::any())->shouldNotBeCalled();

		$this->queryFactory->create('Foo\\Bar\\Domain\\Model\\Test');

	}

}