<?php
namespace Pagemachine\Ats\Command;

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
        /**
         * Future config vars
         */
        $anonymizeLimit = "90 days";

        $config = [
            'title' => '*',
            'firstname' => '*',
            'surname' => '*',
            'nationality' => '*',
            'street' => '*',
            'zipcode' => '*',
            'city' => '*',
            'email' => '*',
            'phone' => '*',
            'mobile' => '*',
            'employed' => 0,
            'schoolQualification' => '*',
            'professionalQualification' => '*',
            'professionalQualificationFinalGrade' => '*',
            'academicDegree' => '*',
            'academicDegreeFinalGrade' => '*',
            'doctoralDegree' => '*',
            'doctoralDegreeFinalGrade' => '*',
            'previousKnowledge' => '*',
            'itKnowledge' => '*',
            'languageSkills' => new ObjectStorage(),
            'comment' => '*',
            'referrer' => '*',
            //TODO: Languageskills?
            //TODO: Files
        ];


        $threshold = new \DateTime();
        $threshold->sub(
            \DateInterval::createFromDateString($anonymizeLimit)
        );

        $count = $this->applicationRepository->countOldApplications($threshold);
        $this->outputLine(sprintf('Found %s old applications for anonymization.', $count));

        $counter = 0;

        foreach ($this->applicationRepository->findOldApplications($threshold) as $application) {
            foreach ($config as $property => $value) {
                $application->_setProperty($property, $value);
            }
            $application->setAnonymized(true);
            $this->applicationRepository->update($application);

            $counter++;

            if ($counter >= 20) {
                $this->applicationRepository->persistAll();
                $counter = 0;
            }

            $this->outputLine(sprintf('\tAnonymized application with uid %s.', $application->getUid()));
        }

        $this->applicationRepository->persistAll();

        $this->outputLine();
        $this->outputLine('Done.');
    }
}
