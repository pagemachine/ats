<?php
namespace PAGEmachine\Ats\Command;

use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Exception;
use PAGEmachine\Ats\Service\Cleanup\UserCleanupService;
use PAGEmachine\Ats\Service\TyposcriptService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/*
 * This file is part of the Pagemachine ATS project.
 */

/**
 * (FE-)User related commandcontroller
 */
class CleanupUsersCommand extends Command
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

    public function configure() 
    {
        $this->setDescription('Remove (hard-delete) old frontend users')
            ->addArgument(
                'userGroup',
                InputArgument::REQUIRED,
                'The uid of the ATS users group (plugin.tx_ats.settings.feUserGroup)'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    public function execute(InputInterface $input, OutputInterface $output) 
    {        
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        // Limit this command to TYPO3 >= 8.7
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8007000) {
            $io->writeln('You need at least TYPO3 version 8.7 to use this command.');
            return Command::FAILURE;
        }

        $io->writeln('Starting cleanup of frontend users...');

        $userGroup = $input->getArgument('userGroup');

        if (!$userGroup) {
            throw new Exception("No valid usergroup for cleanup given (ID must be greater than 0). Aborting.");
            return;
        }

        $cleanupService = UserCleanupService::getInstance();

        $affectedUsers = $cleanupService->cleanupUsers($userGroup, $this->getMinimumUsersAge());

        $io->writeln('');
        $io->writeln(sprintf("Removed %s users.", $affectedUsers));

        return Command::SUCCESS;
    }

    /**
     * @return string
     */
    protected function getMinimumUsersAge()
    {
        $settings = TyposcriptService::getInstance()->getSettings();
        return $settings['cleanup']['inactiveUsersLifetime'] ?: '2 years';
    }
}
