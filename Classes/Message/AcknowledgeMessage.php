<?php
namespace PAGEmachine\Ats\Message;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class AcknowledgeMessage extends AbstractMessage implements MessageInterface
{
    /**
     * @var int
     */
    protected $type = AbstractMessage::MESSAGE_ACKNOWLEDGE;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getName()
    {

        return "AcknowledgeMessage";
    }

    /**
     * ReplyMessage has no custom fields, return empty array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCustomFields()
    {
        return [];
    }
}
