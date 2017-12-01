<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Message\MessageInterface;
use PAGEmachine\Ats\Service\PdfService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * MassMessageContainer
 * For mass sendings. Holds multiple messages
 */
class MassMessageContainer extends AbstractMessage implements MessageInterface
{
    /**
     *
     * @var \PAGEmachine\Ats\Message\MessageFactory
     * @inject
     */
    protected $messageFactory;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return $this->messageFactory->getMessageNames()[$this->type];
    }

    /**
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
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

    public function buildMessages()
    {
        $this->messages = [];
        foreach ($this->applications as $application) {
            $message = $this->messageFactory->createMessageFromConstantType($this->type, $application);

            $message->setSubject($this->getSubject());
            $message->setSendType($this->getSendType());
            $message->setBody($this->getBody());
            $message->setCc($this->getCc());
            $message->setBcc($this->getBcc());

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
}
