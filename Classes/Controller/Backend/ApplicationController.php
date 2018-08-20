<?php
namespace PAGEmachine\Ats\Controller\Backend;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Application\ApplicationRating;
use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\FileReference;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Model\Note;
use PAGEmachine\Ats\Message\AcknowledgeMessage;
use PAGEmachine\Ats\Message\InviteMessage;
use PAGEmachine\Ats\Message\RejectMessage;
use PAGEmachine\Ats\Message\ReplyMessage;
use PAGEmachine\Ats\Property\TypeConverter\UploadedFileReferenceConverter;
use PAGEmachine\Ats\Service\DuplicationService;
use PAGEmachine\Ats\Service\ExtconfService;
use PAGEmachine\Ats\Traits\StaticCalling;
use PAGEmachine\Ats\Workflow\WorkflowManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ApplicationController
 */
class ApplicationController extends AbstractBackendController
{
    use StaticCalling;

    /**
     * @var PAGEmachine\Ats\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $applicationRepository;

    /**
     * @var PAGEmachine\Ats\Domain\Repository\JobRepository
     * @inject
     */
    protected $jobRepository;


    /**
     * @var \PAGEmachine\Ats\Message\MessageFactory
     * @inject
     */
    protected $messageFactory;


    /**
     * Action URLs for the action menu
     *
     * @var array
     */
    protected $menuUrls = [
        "listAll" => ["action" => "listAll", "label" => "be.label.AllApplications"],
        "listMine" => ["action" => "listMine", "label" => "be.label.MyApplications"],
    ];

    /**
     * Builds the backend docheader menu with actions
     *
     * @return void
     */
    public function buildMenu()
    {
        if (!array_key_exists($this->request->getControllerActionName(), $this->menuUrls)) {
            return;
        }

        $menuRegistry = $this->getMenuRegistry();

        $uriBuilder = $this->controllerContext->getUriBuilder();

        $menu = $menuRegistry->makeMenu()
            ->setIdentifier("actions");

        foreach ($this->menuUrls as $url) {
            //If extbase_acl is loaded, reduce menu urls to the ones actually allowed
            if (ExtensionManagementUtility::isLoaded("extbase_acl")) {
                if (!\Pagemachine\ExtbaseAcl\Manager\ActionAccessManager::getInstance()->isActionAllowed(static::class, $url['action'])) {
                    continue;
                }
            }

            $isActive = $this->request->getControllerActionName() === $url['action'] ? true : false;
            $uri = $uriBuilder
                ->reset()
                ->uriFor($url['action'], [], $this->request->getControllerName(), null, null);
            $menuItem = $menu->makeMenuItem()
                ->setHref($uri)
                ->setTitle($this->callStatic(LocalizationUtility::class, 'translate', $url['label'], 'ats'))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
        }

        $menuRegistry->addMenu($menu);
    }

    /**
     * Testing helper class
     *
     * @return MenuRegistry
     * @codeCoverageIgnore
     */
    public function getMenuRegistry()
    {
        return $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry();
    }

    /**
     * Forwards to the first allowed action (since some could be disallowed by role)
     *
     * @return void
     */
    public function initializeIndexAction()
    {

        $this->forward("listMine");
    }

    /**
     * Dummy action (never used, but needed to forward)
     * @codeCoverageIgnore
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * Redirect if a object is no longer available.
     * This is important if this extension is used in combination with pagemachine/extbase-acl
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @throws \Exception|\TYPO3\CMS\Extbase\Property\Exception
    */
    public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response)
    {
        try {
            parent::processRequest($request, $response);
        } catch (\TYPO3\CMS\Extbase\Property\Exception $e) {
            if ($e instanceof \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException) {
                $this->redirect('index');
            } else {
                throw $e;
            }
        }
    }

    /**
     * Sets the proper type converter options for message objects
     *
     * @return void
     */
    public function initializeAction()
    {

        if ($this->request->hasArgument('message')) {
            $this->arguments->getArgument('message')
                ->getPropertyMappingConfiguration()
                ->forProperty('dateTime')
                ->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d H:i');

            $this->arguments->getArgument('message')
                ->getPropertyMappingConfiguration()
                ->forProperty('confirmDate')
                ->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d');
        }
        if ($this->request->hasArgument('application')) {
            $this->arguments->getArgument('application')
                ->getPropertyMappingConfiguration()
                ->forProperty('birthday')
                ->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d');

            $uploadConfiguration = ExtconfService::getInstance()->getUploadConfiguration();

            $this->arguments->getArgument('application')
                ->getPropertyMappingConfiguration()
                ->forProperty('files.999')
                ->setTypeConverterOptions(
                    UploadedFileReferenceConverter::class,
                    [
                        UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => $uploadConfiguration['uploadFolder'],
                        UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_CONFLICT_MODE => $uploadConfiguration['conflictMode'],
                        UploadedFileReferenceConverter::CONFIGURATION_FILE_EXTENSIONS => $uploadConfiguration['allowedFileExtensions'],
                    ]
                );
        }
    }

    /**
     * Lists applications in backend module
     *
     * @param  ApplicationFilter $filter
     * @param  bool $resetFilter
     * @return void
     */
    public function listAllAction(ApplicationFilter $filter = null, $resetFilter = false)
    {

        if ($filter == null | $resetFilter === true) {
            $filter = new ApplicationFilter();
        }

        $this->view->assignMultiple([
            'exceededApplications' => $this->applicationRepository->findDeadlineExceeded($this->settings['deadlineTime'], null, $filter),
            'newApplications' => $this->applicationRepository->findNew($this->settings['deadlineTime'], null, $filter),
            'progressApplications' => $this->applicationRepository->findInProgress($this->settings['deadlineTime'], null, $filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter,
        ]);
    }

    /**
     * Lists my applications
     *
     * @param  ApplicationFilter $filter
     * @param  bool $resetFilter
     * @return void
     */
    public function listMineAction(ApplicationFilter $filter = null, $resetFilter = false)
    {

        if ($filter == null | $resetFilter === true) {
            $filter = new ApplicationFilter();
        }

        $this->view->assignMultiple([
            'exceededApplications' => $this->applicationRepository->findDeadlineExceeded($this->settings['deadlineTime'], $GLOBALS['BE_USER'], $filter),
            'newApplications' => $this->applicationRepository->findNew($this->settings['deadlineTime'], $GLOBALS['BE_USER'], $filter),
            'progressApplications' => $this->applicationRepository->findInProgress($this->settings['deadlineTime'], $GLOBALS['BE_USER'], $filter),
            'jobs' => $this->jobRepository->findByBackendUser($GLOBALS['BE_USER']),
            'filter' => $filter,
        ]);
    }

    /**
     * Shows an application and lists all additional possible actions
     * @param  Application $application
     * @param  bool $print
     * @return void
     */
    public function showAction(Application $application, $print = false)
    {
        $this->view->assign('print', $print);
        $this->view->assign('application', $application);
    }

    /**
     *
     * @return void
     */
    public function initializeEditAction()
    {

        $this->fixDynamicFieldPropertyMapping();
    }

    /**
     * Backend edit action for applications
     *
     * @param  Application $application
     * @ignorevalidation $application
     * @return void
     */
    public function editAction(Application $application)
    {
        $this->view->assignMultiple([
            'application' => $application,
            'jobs' => $this->jobRepository->findAll(),
        ]);
    }

    /**
     *
     * @return void
     */
    public function initializeUpdateAction()
    {

        $this->fixDynamicFieldPropertyMapping();
    }

    /**
     * Backend update action for applications
     *
     * @param  Application $application
     * @return void
     */
    public function updateAction(Application $application)
    {

        $this->applicationRepository->updateAndLog($application, 'edit');
        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.update.ok', 'ats'));
        $this->redirect("edit", null, null, ["application" => $application]);
    }


    /**
     * Change workflow (status)
     *
     * @param  Application $application
     * @return void
     */
    public function editStatusAction(Application $application)
    {

        $constants = WorkflowManager::getInstance()->getPlaces();

        $this->view->assign('statusOptions', $constants);
        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * Update workflow (status)
     *
     * @param  Application $application
     * @param  Note $note
     * @ignorevalidation $note
     * @return void
     */
    public function updateStatusAction(Application $application, Note $note)
    {

        if (!empty($note->getDetails())) {
            $application->addNote($note);
        }

        $this->applicationRepository->updateAndLog(
            $application,
            'workflow',
            [
                'status' => $application->getStatus()->__toString(),
                'note' => $note->getDetails(),
            ]
        );
        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.updateStatus.ok', 'ats'));
        $this->redirect("editStatus", null, null, ["application" => $application]);
    }

    /**
     * ratingPerso action
     *
     * @param  Application $application
     * @return void
     */
    public function ratingPersoAction(Application $application)
    {

        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
        $this->view->assign('ratingOptions', ApplicationRating::getFlippedConstants());
    }

    /**
     * rating action
     *
     * @param  Application $application
     * @return void
     */
    public function ratingAction(Application $application)
    {

        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
        $this->view->assign('ratingOptions', ApplicationRating::getFlippedConstants());
    }

    /**
     * SubmitRating action
     *
     * @param Note $note
     * @param  Application $application
     * @param  string $forwardAction
     * @ignorevalidation $note
     * @return void
     */
    public function addRatingAction(Note $note, Application $application, $forwardAction)
    {

        if (!empty($note->getDetails())) {
            $application->addNote($note);
        }

        if ($forwardAction == "rating") {
            $this->applicationRepository->updateAndLog(
                $application,
                "rating",
                [
                    "rating" => $application->getRating()->__toString(),
                    "note" => $note->getDetails(),
                ]
            );
        } else {
            $this->applicationRepository->updateAndLog(
                $application,
                "ratingPerso",
                [
                    "ratingPerso" => $application->getRatingPerso()->__toString(),
                    "note" => $note->getDetails(),
                ]
            );
        }

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.rating.ok', 'ats'));
        $this->redirect($forwardAction, null, null, ["application" => $application]);
    }

    /**
     * notes action - lists all notes for a given application
     *
     * @param  Application $application
     * @return void
     */
    public function notesAction(Application $application)
    {

        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * SubmitNote action
     *
     * @param Note $note
     * @param  Application $application
     * @return void
     */
    public function addNoteAction(Note $note, Application $application)
    {

        $application->addNote($note);

        $this->applicationRepository->updateAndLog(
            $application,
            "note",
            [
                "note" => ($note->getIsInternal() ? "(internal)" : $note->getDetails()),
            ]
        );

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.note.ok', 'ats'));
        $this->redirect("notes", null, null, ["application" => $application]);
    }

    /**
     * Closes an application (with reason) (status change)
     *
     * @param  Application $application
     * @return void
     */
    public function closeAction(Application $application)
    {

        $constants = ApplicationStatus::getConstantsForCompletion();

        $this->view->assign('statusOptions', $constants);

        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * Confirm close action
     *
     * @param  Application $application
     * @ignorevalidation $note
     * @param  Note        $note
     * @return void
     */
    public function confirmCloseAction(Application $application, Note $note)
    {

        if (!empty($note->getDetails())) {
            $application->addNote($note);
        }

        $this->applicationRepository->updateAndLog(
            $application,
            "close",
            [
                "status" => $application->getStatus(),
            ]
        );
        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.close.ok', 'ats'));
        $this->redirect("index");
    }

    /**
     * Form for mail/pdf reply text creation

     * @param  ReplyMessage $message
     * @param  Application $application
     * @ignorevalidation $message
     * @return void
     */
    public function replyAction(ReplyMessage $message = null, Application $application = null)
    {

        if ($message == null) {
            $message = $this->messageFactory->createMessage("reply", $application);
        }

        $message->applyTextTemplate();

        $this->view->assignMultiple([
            'message' => $message,
            'application' => $message->getApplication(),
        ]);
    }

    /**
     * Sends the reply in the desired way (mail or pdf)
     *
     * @param ReplyMessage $message
     *
     * @return void
     */
    public function sendReplyAction(ReplyMessage $message)
    {

        $this->applicationRepository->updateAndLog(
            $message->getApplication(),
            'reply',
            [
                'subject' => $message->getRenderedSubject(),
                'sendType' => $message->getSendType(),
                'cc' => $message->getCc(),
                'bcc' => $message->getBcc(),
                'message' => $message->getRenderedBody(),
            ]
        );

        $message->send();

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.reply.ok', 'ats'));
        $this->redirect("show", null, null, ['application' => $message->getApplication()]);
    }

    /**
     * Form for mail/pdf invite text creation

     * @param  ReplyMessage $message
     * @param  Application $application
     * @ignorevalidation $message
     * @return void
     */
    public function inviteAction(InviteMessage $message = null, Application $application = null)
    {

        if ($message == null) {
            $message = $this->messageFactory->createMessage("invite", $application);
        }

        $message->applyTextTemplate();

        $this->view->assignMultiple([
            'message' => $message,
            'application' => $message->getApplication(),
        ]);
    }

    /**
     * Sends the reply in the desired way (mail or pdf)
     *
     * @param ReplyMessage $message
     *
     * @return void
     */
    public function sendInvitationAction(InviteMessage $message)
    {

        $this->applicationRepository->updateAndLog(
            $message->getApplication(),
            'invite',
            [
                'subject' => $message->getRenderedSubject(),
                'sendType' => $message->getSendType(),
                'cc' => $message->getCc(),
                'bcc' => $message->getBcc(),
                'message' => $message->getRenderedBody(),
            ]
        );

        $message->send();

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.invite.ok', 'ats'));
        $this->redirect("show", null, null, ['application' => $message->getApplication()]);
    }

    /**
     * Form for mail/pdf invite text creation

     * @param  ReplyMessage $message
     * @param  Application $application
     * @ignorevalidation $message
     * @return void
     */
    public function acknowledgeAction(AcknowledgeMessage $message = null, Application $application = null)
    {

        if ($message == null) {
            $message = $this->messageFactory->createMessage("acknowledge", $application);
        }

        $message->applyTextTemplate();

        $this->view->assignMultiple([
            'message' => $message,
            'application' => $message->getApplication(),
        ]);
    }

    /**
     * Sends the reply in the desired way (mail or pdf)
     *
     * @param ReplyMessage $message
     *
     * @return void
     */
    public function sendAcknowledgementAction(AcknowledgeMessage $message)
    {

        $this->applicationRepository->updateAndLog(
            $message->getApplication(),
            'acknowledge',
            [
                'subject' => $message->getRenderedSubject(),
                'sendType' => $message->getSendType(),
                'cc' => $message->getCc(),
                'bcc' => $message->getBcc(),
                'message' => $message->getRenderedBody(),
            ]
        );

        $message->send();

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.acknowledge.ok', 'ats'));
        $this->redirect("show", null, null, ['application' => $message->getApplication()]);
    }

    /**
     * Form for mail/pdf reject text creation

     * @param  RejectMessage $message
     * @param  Application $application
     * @ignorevalidation $message
     * @return void
     */
    public function rejectAction(RejectMessage $message = null, Application $application = null)
    {

        if ($message == null) {
            $message = $this->messageFactory->createMessage("reject", $application);
        }

        $message->applyTextTemplate();

        $this->view->assignMultiple([
            'message' => $message,
            'application' => $message->getApplication(),
        ]);
    }

    /**
     * Sends the reject in the desired way (mail or pdf)
     *
     * @param RejectMessage $message
     *
     * @return void
     */
    public function sendRejectionAction(RejectMessage $message)
    {

        $this->applicationRepository->updateAndLog(
            $message->getApplication(),
            'reject',
            [
                'subject' => $message->getRenderedSubject(),
                'sendType' => $message->getSendType(),
                'cc' => $message->getCc(),
                'bcc' => $message->getBcc(),
                'message' => $message->getRenderedBody(),
            ]
        );

        $message->send();

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.reject.ok', 'ats'));
        $this->redirect("show", null, null, ['application' => $message->getApplication()]);
    }

    /**
     * Back to Personal Department view
     *
     * @param  Application  $application
     * @return void
     */
    public function backToPersoAction(Application $application)
    {
        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * Send back to Personal Department
     *
     * @param  Application $application
     * @param  Note $note
     * @ignorevalidation $note
     * @return void
     */
    public function sendBackToPersoAction(Application $application, Note $note)
    {

        if (!empty($note->getDetails())) {
            $application->addNote($note);
        }

        $this->applicationRepository->updateAndLog(
            $application,
            "backToPerso",
            [
                "note" => $note->getDetails(),
            ]
        );

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.backToPerso.ok', 'ats'));
        $this->redirect("index");
    }

    /**
     * Action for history
     *
     * @param  Application $application
     * @return void
     */
    public function historyAction(Application $application)
    {

        $this->view->assign('application', $application);
    }

    /**
     * Cloning preparation action
     *
     * @param  Application $application
     * @return void
     */
    public function cloneAction(Application $application)
    {

        $this->view->assign('application', $application);
        $this->view->assign('jobs', $this->jobRepository->findAll());
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * Actual cloning
     *
     * @param  Application $application
     * @param  Job $job
     * @param  Note $note
     * @ignorevalidation $note
     * @return void
     */
    public function confirmCloneAction(Application $application, Job $job, Note $note)
    {

        $clone = DuplicationService::getInstance()->duplicateObject($application);

        $clone->setJob($job);
        $clone->setStatus(ApplicationStatus::cast(ApplicationStatus::NEW_APPLICATION));

        if (!empty($note->getDetails())) {
            $clone->addNote($note);
        }

        $this->applicationRepository->addOrUpdate($clone);

        $this->applicationRepository->updateAndLog(
            $application,
            "cloned",
            [
                "note" => $note->getDetails(),
                "clone" => $clone->getUid(),
            ]
        );

        $this->applicationRepository->updateAndLog(
            $clone,
            "clone",
            [
                "note" => $note->getDetails(),
                "source" => $application->getUid(),
            ]
        );

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.clone.ok', 'ats', [$application->getUid(), $clone->getUid()]));

        $this->redirect("show", null, null, ['application' => $clone]);
    }

    /**
     * Saves upload and forwards back to edit
     *
     * @param  Application $application
     * @return void
     */
    public function saveUploadAction(Application $application)
    {
        $this->applicationRepository->addOrUpdate($application);

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.upload.ok', 'ats'));
        $this->forward("edit", null, null, ['application' => $application->getUid()]);
    }

    /**
     * @param  Application $application
     * @param  FileReference $file
     * @return void
     */
    public function removeUploadAction(Application $application, FileReference $file)
    {

        $application->removeFile($file);

        $this->applicationRepository->updateAndLog(
            $application,
            "removeUpload",
            [
                "file" => $file->getOriginalResource()->getName(),
            ]
        );

        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.removeUpload.ok', 'ats', [$file->getOriginalResource()->getName()]));

        $this->forward("edit", null, null, ["application" => $application->getUid()]);
    }

    public function newAction(Application $application = null)
    {
        $this->view->assign('jobs', $this->jobRepository->findAll());
        $this->view->assign('application', $application);
    }

    /**
     *
     * @return void
     */
    public function initializeCreateAction()
    {

        $this->fixDynamicFieldPropertyMapping();
    }

    /**
     * Backend update action for applications
     *
     * @param  Application $application
     * @return void
     */
    public function createAction(Application $application)
    {

        $application->submit();

        $this->applicationRepository->addOrUpdate($application);

        $this->applicationRepository->updateAndLog($application, 'new');
        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.create.ok', 'ats'));
        $this->redirect("edit", null, null, ["application" => $application]);
    }

    /**
     * Adds PropertyMappingConfiguration for JS-added fields like the language skills
     *
     * @return void
     */
    protected function fixDynamicFieldPropertyMapping()
    {
        $propertyMappingConfiguration = $this->arguments->getArgument("application")->getPropertyMappingConfiguration();

        $propertyMappingConfiguration->forProperty("languageSkills")->allowAllProperties();
        $propertyMappingConfiguration->forProperty("languageSkills.*")->allowProperties("language");
        $propertyMappingConfiguration->forProperty("languageSkills.*")->allowProperties("level");
        $propertyMappingConfiguration->forProperty("languageSkills.*")->allowProperties("textLanguage");
        $propertyMappingConfiguration->allowCreationForSubProperty("languageSkills.*");
    }
}
