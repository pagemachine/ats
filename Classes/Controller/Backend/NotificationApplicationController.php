<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Message\RejectMessage;
use PAGEmachine\Ats\Message\ReplyMessage;
use PAGEmachine\Ats\Message\MessageInterface;
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
     * @param  RejectMessage $rejectMessage
     * @param  ReplyMessage $ReplyMessage
     * @param  string $messageType
     * @param  array $selected
     * @ignorevalidation $rejectMessage
     * @ignorevalidation $replyMessage
     * @return void
     */
    public function listAllAction(ApplicationFilter $filter = null, $resetFilter = false, RejectMessage $rejectMessage = null, ReplyMessage $replyMessage = null, $messageType = null, $selected = [])
    {
        if ($filter == null | $resetFilter === true) {
            $filter = new ApplicationFilter();
        }

        if ($messageType == 'reject') {
            $message = $rejectMessage;
        }

        if ($messageType == 'reply') {
            $message = $replyMessage;
        }

        if ($messageType != null && $messageType != 'null') {
            if ($message == null) {
                $application = $this->applicationRepository->findNotification($filter)[0];
                $message = $this->messageFactory->createMessage($messageType, $application);
            }

            $message->applyTextTemplate();
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findNotification($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter,
            'message' => $message,
            'messageType' => $messageType,
            'selected' => $selected,
        ]);
    }

    /**
     *
     *
     * @param  string $messageType
     * @param  array $selected
     * @param  \PAGEmachine\Ats\Message\MessageInterface $message
     * @ignorevalidation $message
     * @return void
     */
    public function newMassNotificationAction($messageType, $selected = [], MessageInterface $message = null){

        $filter = new ApplicationFilter();//remove;
        $selected = array_keys($selected, 1);
        if($message == null){
            $application = $this->applicationRepository->findNotification($filter)[0];//replace
            $message = $this->messageFactory->createMessage($messageType, $application);
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findNotification($filter),//replace
            'message' => $message,
            'messageType' => $messageType,
            'selected' => $selected,
        ]);
    }

    /**
     * Sends the Notifications in the desired way (mail or pdf)
     *
     * @param  RejectMessage $rejectMessage
     * @param  ReplyMessage $ReplyMessage
     * @param  string $messageType
     * @param  array $selected
     * @ignorevalidation $rejectMessage
     * @ignorevalidation $replyMessage
     * @return void
     */
    public function sendMassNotificationAction(RejectMessage $rejectMessage = null, ReplyMessage $replyMessage = null, $messageType = null, $selected = [])
    {
        $uids = array_keys($selected, 1);
        $messages = [];

        if ($messageType == 'reject') {
            $message = $rejectMessage;
        }

        if ($messageType == 'reply') {
            $message = $replyMessage;
        }

        foreach ($uids as $uid) {
            $filepath = '';
            $fileName = '';

            $application = $this->applicationRepository->findByUid($uid);
            $message->setApplication($application);
            $message->setRenderedBody(null);

            $this->applicationRepository->updateAndLog(
                $message->getApplication(),
                $messageType,
                [
                    'subject' => $message->getSubject(),
                    'sendType' => $message->getSendType(),
                    'cc' => $message->getCc(),
                    'bcc' => $message->getBcc(),
                    'message' => $message->getRenderedBody(),
                ]
            );

            if ($message->getSendType() == 'mail') {
                $message->send();
                usleep('100');
            } else {
                $fileName = PdfService::getInstance()->generateRandomFilename();
                $filePath = $message->generatePdf($fileName);
            }

            $messages[] = ['filePath' => $filePath, 'fileName' => $fileName, 'message' => clone $message];
        }
        $this->view->assignMultiple([
            'messages' => $messages,
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
}
