<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\Note;
use PAGEmachine\Ats\Message\RejectMessage;
use PAGEmachine\Ats\Message\ReplyMessage;

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

        if($messageType == 'reject'){
            $message = $rejectMessage;
        }

        if($messageType == 'reply'){
            $message = $replyMessage;
        }

        if($messageType != null && $messageType != 'null') {
            if ($message == null) {
                $application = $this->applicationRepository->findNotification($filter)[0];
                $message = $this->messageFactory->createMessage( $messageType , $application);
            }

            $message->applyTextTemplate();
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findNotification($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter,
            'message' => $message,
            'messageType' => $messageType,
            'selected' => $selected
        ]);
    }
}
