<?php
namespace PAGEmachine\Ats\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */


use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Property\TypeConverter\UploadedFileReferenceConverter;
use PAGEmachine\Ats\Service\AuthenticationService;
use PAGEmachine\Ats\Service\ExtconfService;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Controller\Exception\RequiredArgumentMissingException;

/**
 * AbstractApplicationController - handles access protection and redirects
 */
class AbstractApplicationController extends ActionController
{
    /**
     * @var ApplicationRepository
     */
    protected $applicationRepository = null;


    /**
     * @var AuthenticationService
    */
    protected $authenticationService;

    /**
     * @param  ApplicationRepository $applicationRepository
     */
    public function injectApplicationRepository(ApplicationRepository $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * @param  AuthenticationService $authenticationService
     */
    public function injectAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * This is called before the form action and checks if a valid FE User is logged in.
     *
     * @return void
     */
    public function initializeAction()
    {
        // Merge TS and FlexForm settings
        $this->settings = TyposcriptService::getInstance()->mergeFlexFormAndTypoScriptSettings($this->settings);


        if ($this->request->hasArgument('application')) {
            $this->setPropertyMappingConfigurationForApplication();
            $this->loadValidationSettings();
        }

        $groupid = !empty($this->settings['feUserGroup']) ? $this->settings['feUserGroup'] : null;
        if (!$this->authenticationService->isUserAuthenticatedAndHasGroup($groupid) && $this->settings['loginPage']) {
            $arguments = $this->buildArgumentsForLoginHandling();

            //Create url to login page and send arguments
            $loginUri = $this->uriBuilder->reset()
                ->setTargetPageUid($this->settings['loginPage'])
                ->setArguments($arguments)
                ->build();

            $this->redirectToUri($loginUri);
        }

        if (!$this->settings['loginPage'] && $this->request->hasArgument("application")) {
            $GLOBALS['TSFE']->fe_user->setKey('ses', 'Application', (int)$this->request->getArgument("application"));
            $GLOBALS["TSFE"]->storeSessionData();
        }
    }

    /**
     * Builds argument array to hand over when login redirect happens
     * Applications are not tracked. If there is a job and login user, the repository can reconstruct the application in progress
     *
     * @return array $arguments
     */
    protected function buildArgumentsForLoginHandling()
    {

        $job = null;

        if ($this->request->hasArgument("job")) {
            $job = $this->request->getArgument("job");
        } elseif ($this->request->hasArgument("application")) {
            $application = $this->request->getArgument("application");

            if (is_array($application) && array_key_exists("job", $application)) {
                $job = $application["job"];
            } elseif (is_numeric($application)) {
                $applicationObject = $this->applicationRepository->findByUid($application);

                $job = $applicationObject->getJob();
            }
        }
        if ($job === null) {
            throw new RequiredArgumentMissingException('Required argument "job" is not set for ' . $this->request->getControllerObjectName() . '->' . $this->request->getControllerActionName() . '.', 1298012500);
        }

        //Build forward and return url for login
        $arguments = [
            "return_url" => $this->uriBuilder->setCreateAbsoluteUri(true)->uriFor("form", ["job" => $job], "Application\\Form"),
            "referrer" => $this->uriBuilder->reset()->setCreateAbsoluteUri(true)->uriFor("show", ["job" => $job], "Job"),
        ];

        return $arguments;
    }

    /**
     * @return void
     */
    protected function setPropertyMappingConfigurationForApplication()
    {
        $mappingConfiguration = $this->arguments->getArgument('application')->getPropertyMappingConfiguration();

        $mappingConfiguration->forProperty('birthday')
            ->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d');

        $uploadConfiguration = ExtconfService::getInstance()->getUploadConfiguration();

        $mappingConfiguration->forProperty('files.999')
            ->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                [
                    UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => $uploadConfiguration['uploadFolder'],
                    UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_CONFLICT_MODE => $uploadConfiguration['conflictMode'],
                    UploadedFileReferenceConverter::CONFIGURATION_FILE_EXTENSIONS => $uploadConfiguration['allowedFileExtensions'],
                ]
            );

        $mappingConfiguration->forProperty("languageSkills")->allowAllProperties();
        $mappingConfiguration->forProperty("languageSkills.*")->allowProperties("language", "level", "textLanguage");
        $mappingConfiguration->allowCreationForSubProperty('languageSkills.*');
    }

    /**
     * Loads validation settings into settings array to pass on to fluid
     *
     * @return void
     */
    protected function loadValidationSettings()
    {
        $this->settings['validation'] = TyposcriptService::getInstance()->getFrameworkConfiguration()['mvc']['validation'][$this->arguments->getArgument('application')->getDataType()];
    }
}
