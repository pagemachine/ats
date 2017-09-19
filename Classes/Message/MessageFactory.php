<?php
namespace PAGEmachine\Ats\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use TYPO3\CMS\Core\SingletonInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class MessageFactory implements SingletonInterface {

    protected $messageTypes = [
        'reply' => ReplyMessage::class,
        'invite' => InviteMessage::class,
        'acknowledge' => AcknowledgeMessage::class,
        'reject' => RejectMessage::class
    ];

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * Creates a new message for the given type
     *
     * @param  string                    $type
     * @param  Application               $application
     * @param  array $backendUser
     * @return MessageInterface
     */
    public function createMessage($type, Application $application) {

        if (empty($this->messageTypes[$type])) {

            throw new UndefinedMessageException('There is no MessageInterface implementation for type "' . $type . '".', 1489678614);
        }

        $message = $this->objectManager->get($this->messageTypes[$type]);

        $message->setApplication($application);

        return $message;
    }

}
