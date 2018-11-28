<?php
namespace Pagemachine\Ats\Command;

use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Exception;
use PAGEmachine\Ats\Service\CleanupService;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/*
 * This file is part of the Pagemachine ATS project.
 */

/**
 * (FE-)User related commandcontroller
 */
class UsersCommandController extends CommandController
{
    /**
     * @var ApplicationRepository
     */
    protected $applicationRepository;

    /**
     * @param ApplicationRepository $applicationRepository
     * @return void
     */
    public function injectApplicationRepository(ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * Command to remove (hard-delete) old and anonymized applications
     *
     * @param int $userGroup The uid of the ATS users group (plugin.tx_ats.settings.feUserGroup)
     */
    public function cleanupCommand($userGroup)
    {
        $this->outputLine("Starting cleanup of frontend users...");

        if (!$userGroup) {
            throw new Exception("No valid usergroup for cleanup given (ID must be greater than 0). Aborting.");
            return;
        }

        $cleanupService = CleanupService::getInstance();

        $affectedUsers = $cleanupService->cleanupUsers($userGroup, $this->getMinimumUsersAge());

        $this->outputLine();
        $this->outputLine(sprintf("Removed %s users.", $affectedUsers));
    }

    /**
     * @return string
     */
    protected function getMinimumUsersAge()
    {
        $settings = TyposcriptService::getInstance()->getSettings();
        return $settings['cleanup']['deleteInactiveUsersAfter'] ?: '2 years';
    }
}
