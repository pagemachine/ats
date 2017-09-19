<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationC;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * QualificationsController (Third Step)
 */
class QualificationsController extends AbstractApplicationController
{

    /**
     * applicationCRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationCRepository
     * @inject
     */
    protected $repository = NULL;

    /**
     * @param  ApplicationC $application
     * @ignorevalidation $application
     * @return void
     */
    public function editQualificationsAction(ApplicationC $application) {

        $this->view->assign("application", $application);
    }

    /**
     *
     * @return void
     */
    public function initializeAction() {

        $propertyMappingConfiguration = $this->arguments->getArgument("application")->getPropertyMappingConfiguration();

        $propertyMappingConfiguration->forProperty("languageSkills")->allowAllProperties();
        $propertyMappingConfiguration->forProperty("languageSkills.*")->allowProperties("language", "level", "textLanguage");
        $propertyMappingConfiguration->allowCreationForSubProperty("languageSkills.*");

    }


    /**
     *
     * @param  ApplicationC $application
     * @return void
     */
    public function updateQualificationsAction(ApplicationC $application) {

    	$this->repository->addOrUpdate($application);
        $this->forward("editAdditionalData", "Application\\AdditionalData", null, ['application' => $application->getUid()]);

    }



}
