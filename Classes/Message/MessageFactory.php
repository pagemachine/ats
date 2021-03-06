<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Message\AbstractMessage;
use PAGEmachine\Ats\Message\MassMessageContainer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class MessageFactory implements SingletonInterface
{
    protected $messageTypes = [
        'reply' => ReplyMessage::class,
        'invite' => InviteMessage::class,
        'acknowledge' => AcknowledgeMessage::class,
        'reject' => RejectMessage::class,
    ];

    /**
     *
     * @var array
     */
    protected $messageTypeConstants = [
        AbstractMessage::MESSAGE_REPLY => 'reply',
        AbstractMessage::MESSAGE_INVITE => 'invite',
        AbstractMessage::MESSAGE_ACKNOWLEDGE => 'acknowledge',
        AbstractMessage::MESSAGE_REJECT => 'reject',
    ];

    /**
     * @var array
     */
    protected $messageNames = [
        AbstractMessage::MESSAGE_REPLY => 'ReplyMessage',
        AbstractMessage::MESSAGE_INVITE => 'InviteMessage',
        AbstractMessage::MESSAGE_ACKNOWLEDGE => 'AcknowledgeMessage',
        AbstractMessage::MESSAGE_REJECT => 'RejectMessage',
    ];

    /**
     * @var ObjectManager $objectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }


    /**
     * Returns message types
     *
     * @return array
     */
    public function getMessageTypes()
    {
        return $this->messageTypeConstants;
    }

    /**
     * Returns message names
     *
     * @return array
     */
    public function getMessageNames()
    {
        return $this->messageNames;
    }

    /**
     * Creates a new message for the given type
     *
     * @param  string                    $type
     * @param  Application               $application
     * @param  array $backendUser
     * @return MessageInterface
     */
    public function createMessage($type, Application $application)
    {

        if (empty($this->messageTypes[$type])) {
            throw new UndefinedMessageException('There is no MessageInterface implementation for type "' . $type . '".', 1489678614);
        }

        $message = $this->objectManager->get($this->messageTypes[$type]);

        $message->setApplication($application);

        // Use this signal to modify messages before they are passed to the form (for default input etc.)
        $signalSlotDispatcher = $this->objectManager->get(Dispatcher::class);
        list($message) = $signalSlotDispatcher->dispatch(__CLASS__, 'afterMessageCreated', [$message]);

        return $message;
    }

    /**
     * Creates a message originating from a constant type (integer)
     *
     * @param  int      $constantType
     * @param  Application $application
     * @return MessageInterface
     */
    public function createMessageFromConstantType($constantType, Application $application)
    {
        $type = $this->messageTypeConstants[$constantType];
        return $this->createMessage($type, $application);
    }


    /**
     * Creates a container from given message
     *
     * @param  MessageInterface $message
     * @param  array            $applications
     * @return MassMessageContainer $messageContainer
     */
    public function createContainerFromMessage(MessageInterface $message, $applications = [])
    {
        $messageContainer = $this->objectManager->get(MassMessageContainer::class);

        $messageContainer->setTemplateMessage($message);
        $messageContainer->setApplications($applications);

        return $messageContainer;
    }
}
