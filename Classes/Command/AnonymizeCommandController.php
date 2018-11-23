<?php
namespace Pagemachine\Ats\Command;

use PAGEmachine\Ats\Service\AnonymizationService;
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
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $applicationRepository;

    /**
     * Command to anonymize applications
     */
    public function applicationsCommand()
    {
        $this->outputLine("Starting anonymization of applications...");

        $anonymizationService = AnonymizationService::getInstance();

        $anonymizationService->anonymize('PAGEmachine\Ats\Domain\Model\Application');

        $this->outputLine();
        $this->outputLine('Done.');
    }
}
