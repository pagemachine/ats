<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\ExtconfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * SubmitController (Last Step)
 */
class SubmitController extends AbstractApplicationController
{
    /**
     * applicationRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $repository = null;

    /**
     * @var \PAGEmachine\Ats\Message\MessageFactory
     * @inject
     */
    protected $messageFactory;

    /**
     * @param  Application $application
     * @ignorevalidation $application
     * @return void
     */
    public function showSummaryAction(Application $application)
    {
        $this->view->assign("application", $application);
    }

    /**
     * @param  Application $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function submitAction(Application $application)
    {
        $application->setApplicationType(1);
        $application->submit();

        $this->repository->updateAndLog(
            $application,
            'new'
        );

        if (ExtconfService::getInstance()->getSendAutoAcknowledge()) {
            $message = $this->messageFactory->createMessage("acknowledge", $application);
            $message->setUseBackendUserCredentials(false);

            if ($message->applyAutoAcknowledgeTemplate()) {
                $this->repository->updateAndLog(
                    $message->getApplication(),
                    'autoAcknowledge',
                    [
                        'subject' => $message->getRenderedSubject(),
                        'sendType' => $message->getSendType(),
                        'cc' => $message->getCc(),
                        'bcc' => $message->getBcc(),
                        'message' => $message->getRenderedBody(),
                    ]
                );

                $message->send();
            }
        }

        if (intval($this->settings['afterSubmitPage']) > 0) {
            $this->redirect(null, null, null, null, intval($this->settings['afterSubmitPage']));
        } else {
            $this->redirect("submitted", null, null, ['application' => $application]);
        }
    }

    /**
     * @param  Application $application
     * @return void
     */
    public function submittedAction(Application $application)
    {

        $this->view->assign("application", $application);
    }
}
