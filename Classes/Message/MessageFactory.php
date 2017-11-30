<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Message\AbstractMessage;
use PAGEmachine\Ats\Message\MassMessageContainer;
use TYPO3\CMS\Core\SingletonInterface;

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
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

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
     * Constructs a new messagecontainer
     *
     * @param string $messageType
     * @param array<\PAGEmachine\Ats\Domain\Model\Application> $applications
     */
    public function createMassMessageContainer($messageType, $applications = [])
    {
        $messageContainer = $this->objectManager->get(MassMessageContainer::class);

        $messageContainer->setType($messageType);

        $messageContainer->setApplication($applications->current());
        $messageContainer->setApplications($applications);

        // $applications->rewind();
        // if (!empty($applications)) {
        //     foreach ($applications as $application) {
        //         $message = $this->objectManager->get($this->messageTypes[$messageType]);
        //         $message->setApplication($application);

        //         $messageContainer->addMessage($message);
        //     }
        // }

        return $messageContainer;
    }
}
