<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Service\PdfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * MassMessageContainer
 * For mass sendings. Holds multiple messages
 */
class MassMessageContainer
{
    /**
     * @var MessageInterface
     */
    protected $templateMessage;

    /**
     * @return MessageInterface
     */
    public function getTemplateMessage()
    {
        return $this->templateMessage;
    }

    /**
     * @param MessageInterface $templateMessage
     * @return void
     */
    public function setTemplateMessage($templateMessage)
    {
        $this->templateMessage = $templateMessage;
    }


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>
     */
    protected $applications;

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application> $applications
     * @return void
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
    }


    /**
     * @var array<MessageInterface>
     */
    protected $messages = null;

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param array $messages
     * @return void
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * Builds the actual messages from the dummy
     *
     * @return void
     */
    public function buildMessages()
    {
        $this->messages = [];
        foreach ($this->applications as $application) {
            $message = clone $this->templateMessage;

            $message->setApplication($application);

            $this->messages[] = $message;
        }
    }

    /**
     * Sends all messages in this container
     *
     * @return void
     */
    public function send()
    {
        if ($this->messages === null) {
            $this->buildMessages();
        }
        if (!empty($this->messages)) {
            foreach ($this->messages as $message) {
                if ($message->getSendType() == 'mail') {
                    $message->send();
                    usleep('100');
                } else {
                    $fileName = PdfService::getInstance()->generateRandomFilename();
                    $message->generatePdf($fileName);
                }
            }
        }
    }

    public function getResults()
    {
        $results = [];
        foreach ($this->messages as $message) {
            if ($message->getSendType() == 'mail') {
                $results[] = [
                    'mail' => $message->getApplication()->getEmail(),
                    'name' => $message->getApplication()->getSurname(),
                ];
            } else {
                $results[] = [
                    'name' => $message->getApplication()->getSurname(),
                    'filepath' => $message->getPdfFilePath(),
                    'filename' => $message->getFilename(),
                ];
            }
        }

        return $results;
    }
}
