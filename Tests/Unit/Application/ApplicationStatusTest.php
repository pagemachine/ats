<?php
namespace PAGEmachine\Ats\Tests\Unit\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationStatus;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Testcase for PAGEmachine\Ats\Application\ApplicationStatus
 */
class ApplicationStatusTest extends UnitTestCase {

	/**
	 * 
	 */
	protected function setUp() {

	}

	/**
	 * @test
	 */
	public function returnsIsSubmitted() {

		$submittedStatus = new ApplicationStatus(ApplicationStatus::NEW_APPLICATION);
		$this->assertEquals(true, $submittedStatus->isSubmitted());

		$unfinishedStatus = new ApplicationStatus(ApplicationStatus::INCOMPLETE);
		$this->assertEquals(false, $unfinishedStatus->isSubmitted());

	}

	/**
	 * @test
	 */
	public function returnsIsInProgress() {

		$ongoingStatus = new ApplicationStatus(ApplicationStatus::PERSO);
		$this->assertEquals(true, $ongoingStatus->isInProgress());

		// New applications are not in progress
		$newStatus = new ApplicationStatus(ApplicationStatus::NEW_APPLICATION);
		$this->assertEquals(false, $newStatus->isInProgress());

		//Employed oder dismissed applications are not in progress as well
		$employedStatus = new ApplicationStatus(ApplicationStatus::EMPLOYED);
		$this->assertEquals(false, $employedStatus->isInProgress());

	}

	/**
	 * @test
	 */
	public function returnsIsNew() {

		$newStatus = new ApplicationStatus(ApplicationStatus::NEW_APPLICATION);
		$this->assertEquals(true, $newStatus->isNew());

		$unfinishedStatus = new ApplicationStatus(ApplicationStatus::INCOMPLETE);
		$this->assertEquals(false, $unfinishedStatus->isNew());

		$ongoingStatus = new ApplicationStatus(ApplicationStatus::PERSO);
		$this->assertEquals(false, $ongoingStatus->isNew());


	}

	/**
	 * @test
	 */
	public function returnsFlippedConstants() {

		$constants = ApplicationStatus::getFlippedConstants();

		$this->assertContainsOnly('string', $constants);
		$this->assertContainsOnly('integer', array_keys($constants));

	}

	/**
	 * @test
	 */
	public function neverReturnsIncompleteConstantForWorkflow() {

		$constants = ApplicationStatus::getConstantsForWorkflow();

		$this->assertArrayNotHasKey(ApplicationStatus::INCOMPLETE, $constants);

	}

	/**
	 * @test
	 */
	public function returnsCompletionConstants() {

		$constants = ApplicationStatus::getConstantsForCompletion();

		$this->assertArrayHasKey(ApplicationStatus::CANCELLED_BY_EMPLOYER, $constants);
		$this->assertArrayHasKey(ApplicationStatus::EMPLOYED, $constants);
		$this->assertArrayNotHasKey(ApplicationStatus::PERSO_FINAL, $constants);

	}



}
