<?php
namespace PAGEmachine\Ats\Command;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Service\AnonymizationService;
use PAGEmachine\Ats\Service\TyposcriptService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/*
 * This file is part of the Pagemachine ATS project.
 */

/**
 * Application related commandcontroller
 */
class AnonymizeApplicationsCommand extends Command
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
        $this->setDescription('Anonymize applications')
            ->addArgument(
                'preset',
                InputArgument::OPTIONAL,
                'The configuration preset to use, see TS: module.tx_ats.settings.anonymization.[className].[preset]. Defaults: "archived" or "pooled".',
                'archived'
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
        $io->writeln('Starting anonymization of applications...');

        $preset = $input->getArgument('preset');
        $anonymizationService = AnonymizationService::getInstance();

        $anonymizationService->anonymize(
            Application::class,
            $this->getAnonymizationConfigurationForClassName(Application::class, $preset)
        );

        $io->writeln('');
        $io->writeln('Done.');

        return Command::SUCCESS;
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
