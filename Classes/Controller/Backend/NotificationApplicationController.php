<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Message\MessageInterface;
use PAGEmachine\Ats\Service\PdfService;
use TYPO3\CMS\Extbase\Property\TypeConverter\ObjectConverter;

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

    public function initializeNewMassNotificationAction()
    {
        $this->fixMessageContainerMapping();
    }


    /**
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>  $applications
     * @param  \PAGEmachine\Ats\Message\MessageInterface $message
     * @param  int $messageType
     * @validate $applications NotEmpty
     * @return void
     */
    public function newMassNotificationAction($applications, MessageInterface $message = null, $messageType = null)
    {
        if ($message == null) {
            $message = $this->messageFactory->createMessageFromConstantType($messageType, $applications->current());
        }

        $message->applyTextTemplate();

        $this->view->assignMultiple([
            'applications' => $applications,
            'message' => $message,
            'type' => get_class($message),
            'placeholders' => $this->messageFactory->getMessageTypes()[$message->getType()],
        ]);
    }

    public function initializeSendMassNotificationAction()
    {
        $this->fixMessageContainerMapping();
    }

    /**
     * Sends the Notifications in the desired way (mail or pdf)
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>  $applications
     * @param  \PAGEmachine\Ats\Message\MessageInterface $message
     * @return void
     */
    public function sendMassNotificationAction($applications, MessageInterface $message)
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->request, __METHOD__, 8, defined('TYPO3_cliMode') || defined('TYPO3_REQUESTTYPE') && (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI));
        $messageContainer = $this->messageFactory->createContainerFromMessage($message, $applications);

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
                    'placeholders' => $this->messageFactory->getMessageTypes()[$message->getType()],
                ]
            );
        }

        $this->redirect('result', null, null, ['sendType' => $message->getSendType(), 'results' => $messageContainer->getResults()]);
    }

    /**
     * Shows the results of the sending action
     *
     * @param  string $sendType
     * @param  array $results
     * @return void
     */
    public function resultAction($sendType, $results)
    {
        $this->view->assignMultiple([
            'sendType' => $sendType,
            'results' => $results,
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

        if ($this->request->hasArgument('message')) {
            $propertyMappingConfiguration = $this->arguments->getArgument("message")->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->setTypeConverterOption(ObjectConverter::class, ObjectConverter::CONFIGURATION_OVERRIDE_TARGET_TYPE_ALLOWED, true);
        }
    }
}
