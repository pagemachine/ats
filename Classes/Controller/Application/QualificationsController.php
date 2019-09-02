<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationC;

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
    protected $repository = null;

    /**
     * @var \PAGEmachine\Ats\Domain\Repository\LanguageRepository
     * @inject
     */
    protected $languageRepository = null;

    /**
     * @param  ApplicationC $application
     * @ignorevalidation $application
     * @return void
     */
    public function editQualificationsAction(ApplicationC $application)
    {
        if (!empty($this->settings['allowedStaticLanguages'])) {
            $languageUids = explode(',', $this->settings['allowedStaticLanguages']);
            $languages = $this->languageRepository->findLanguagesByUids($languageUids);
        } else {
            $languages = $this->languageRepository->findAll();
        }
        $this->view->assign('languages', $languages);
        $this->view->assign("application", $application);
    }

    /**
     *
     * @param  ApplicationC $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function updateQualificationsAction(ApplicationC $application)
    {

        $this->repository->addOrUpdate($application);
        $this->forward("editAdditionalData", "Application\\AdditionalData", null, ['application' => $application->getUid()]);
    }
}
