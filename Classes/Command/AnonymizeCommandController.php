<?php
namespace Pagemachine\Ats\Command;

use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Service\AnonymizationService;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/*
 * This file is part of the Pagemachine ATS project.
 */

/**
 * Anonymize command controller
 * Anonymizes applications and user data
 */
class AnonymizeCommandController extends CommandController
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
     */
    public function applicationsCommand()
    {
        $this->outputLine("Starting anonymization of applications...");

        $anonymizationService = AnonymizationService::getInstance();

        $anonymizationService->anonymize(
            'PAGEmachine\Ats\Domain\Model\Application',
            $this->getMinimumAnonymizationAge(),
            $this->getAnonymizationConfigurationForClassName('PAGEmachine\Ats\Domain\Model\Application')
        );

        $this->outputLine();
        $this->outputLine('Done.');
    }

    /**
     * @return string
     */
    protected function getMinimumAnonymizationAge()
    {
        $settings = TyposcriptService::getInstance()->getSettings();
        return $settings['anonymization']['minimumAge'] ?: '120 days';
    }

    /**
     * Fetches config for anonymization for given class
     *
     * @param  string $className
     * @return array
     */
    protected function getAnonymizationConfigurationForClassName($className)
    {
        $settings = TyposcriptService::getInstance()->getSettings();

        if ($settings['anonymization']['objects'][$className]) {
            return $settings['anonymization']['objects'][$className];
        } else {
            throw new \PAGEmachine\Ats\Exception(sprintf('Could not find anonymization configuration for class %1$s. Check your TypoScript setup in path "module.tx_ats.anonymization.objects.%1$s.', $className), 1542970640);
        }
    }
}
