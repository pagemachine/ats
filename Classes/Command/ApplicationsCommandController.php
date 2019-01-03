<?php
namespace Pagemachine\Ats\Command;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Service\AnonymizationService;
use PAGEmachine\Ats\Service\Cleanup\ApplicationCleanupService;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/*
 * This file is part of the Pagemachine ATS project.
 */

/**
 * Application related commandcontroller
 */
class ApplicationsCommandController extends CommandController
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
     * Command to anonymize applications
     * @param string $preset The configuration preset to use, see TS: module.tx_ats.settings.anonymization.[className].[preset]
     */
    public function anonymizeCommand($preset = "default")
    {
        $this->outputLine("Starting anonymization of applications...");

        $anonymizationService = AnonymizationService::getInstance();

        $anonymizationService->anonymize(
            Application::class,
            $this->getAnonymizationConfigurationForClassName(Application::class, $preset)
        );

        $this->outputLine();
        $this->outputLine('Done.');
    }

    /**
     * Command to remove (hard-delete) old and anonymized applications
     */
    public function cleanupCommand()
    {
        // Limit this command to TYPO3 >= 8.7
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8007000) {
            $this->outputLine('You need at least TYPO3 version 8.7 to use this command.');
            return;
        }
        $this->outputLine("Starting cleanup of applications...");

        $cleanupService = ApplicationCleanupService::getInstance();

        $affectedApplications = $cleanupService->cleanupUnfinishedApplications($this->getMinimumApplicationCleanupAge());

        $this->outputLine();
        $this->outputLine(sprintf("Removed %s unfinished applications.", $affectedApplications));
    }

    /**
     * @return string
     */
    protected function getMinimumApplicationCleanupAge()
    {
        $settings = TyposcriptService::getInstance()->getSettings();
        return $settings['cleanup']['unfinishedApplicationsLifetime'] ?: '60 days';
    }

    /**
     * Fetches config for anonymization for given class
     *
     * @param string $className
     * @param string $preset
     * @return array
     */
    protected function getAnonymizationConfigurationForClassName($className, $preset)
    {
        $settings = TyposcriptService::getInstance()->getSettings();

        if ($settings['anonymization']['objects'][$className][$preset]) {
            return $settings['anonymization']['objects'][$className][$preset];
        } else {
            throw new \PAGEmachine\Ats\Exception(sprintf('Could not find anonymization configuration for class %1$s and preset "%2s". Check your TypoScript setup in path "module.tx_ats.anonymization.objects.%1$s.%2s".', $className, $preset), 1542970640);
        }
    }
}
