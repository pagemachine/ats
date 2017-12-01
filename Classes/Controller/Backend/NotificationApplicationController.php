<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Message\MassMessageContainer;
use PAGEmachine\Ats\Service\PdfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * NotificationApplicationController
 * Used in the "Mass Notification" Module
 */
class NotificationApplicationController extends ApplicationController
{
    /**
     * Action URLs for the action menu
     *
     * @var array
     */
    protected $menuUrls = [];

    /**
     * Forwards to the first allowed action (since some could be disallowed by role)
     *
     * @return void
     */
    public function initializeIndexAction()
    {

        $this->forward("listAll");
    }

    /**
     * Lists applications for the mass notification in the backend module
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
            'applications' => $this->applicationRepository->findNotification($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter,
            'messageTypes' => $this->messageFactory->getMessageTypes(),
        ]);
    }


    /**
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>  $applications
     * @param  \PAGEmachine\Ats\Message\MassMessageContainer $messageContainer
     * @param  int $messageType
     * @validate $applications NotEmpty
     * @ignorevalidation $messageContainer
     * @return void
     */
    public function newMassNotificationAction($applications, MassMessageContainer $messageContainer = null, $messageType = null)
    {
        if ($messageContainer === null) {
            $messageContainer = $this->messageFactory->createMassMessageContainer($messageType, $applications);
        }

        $this->view->assignMultiple([
            'applications' => $applications,
            'messageContainer' => $messageContainer,
            'messageType' => $messageType,
            'placeholders' => $this->messageFactory->getMessageTypes()[$messageType],
        ]);
    }

    /**
     * Sends the Notifications in the desired way (mail or pdf)
     *
     * @param  \PAGEmachine\Ats\Message\MassMessageContainer $messageContainer
     * @ignorevalidation $messageContainer
     * @return void
     */
    public function sendMassNotificationAction(MassMessageContainer $messageContainer)
    {

        $messageContainer->send();

        foreach ($messageContainer->getMessages() as $message) {
            $this->applicationRepository->updateAndLog(
                $message->getApplication(),
                $messageType,
                [
                    'subject' => $message->getSubject(),
                    'sendType' => $message->getSendType(),
                    'cc' => $message->getCc(),
                    'bcc' => $message->getBcc(),
                    'message' => $message->getRenderedBody(),
                    'placeholders' => $this->messageFactory->getMessageTypes()[$messageType],
                ]
            );
        }

        $this->view->assignMultiple([
            'messageContainer' => $messageContainer,
        ]);
    }

    /**
     * Downloads the pdf in the filePath
     *
     * @param  string $filePath
     * @param  string $filename
     * @return void
     */
    public function downloadPdfAction($filePath, $fileName)
    {
        PdfService::getInstance()->downloadPdf($filePath, $fileName);
    }

    /**
     *
     * @return void
     */
    protected function fixMessageContainerMapping()
    {
        $propertyMappingConfiguration = $this->arguments->getArgument("messageContainer")->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->forProperty("applications")->allowAllProperties();
        $propertyMappingConfiguration->forProperty("applications.*")->allowAllProperties();
    }
}
